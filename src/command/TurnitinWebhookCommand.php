<?php
/**
 * Created by PhpStorm.
 * User: Ning
 * CreateTime: 2026/4/30 13:22
 */

namespace NzhC\Turnitin\command;

use hzh\exception\CustomException;
use NzhC\Turnitin\http\HttpClient;
use NzhC\Turnitin\services\WebHookService;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;

class TurnitinWebhookCommand extends Command
{
    private string $type;

    public function configure()
    {
        $this->setName('turnitin:webhook')
             ->addArgument('type', Argument::OPTIONAL , "webhook type: create | list | del")
             ->addOption('id', null, Option::VALUE_OPTIONAL, 'webhook id')
             ->setDescription('Turnitin Webhook Command');
    }

    public function execute(Input $input, Output $output)
    {
        $this->setType($input,$output);
        $httpClient = new HttpClient();
        $service = new WebhookService($httpClient);

        switch ($this->type) {
            case 'create':
                $this->create($service,$output);
                break;
            case 'list':
                $this->list($service,$output);
                break;
            case 'del':
                $this->del($service,$output,$input);
                break;
        }

    }

    private function del(WebHookService $service,Output $output,Input $input)
    {
        $id = $input->getOption('id');
        if (empty($id)) {
            $output->error('id is required for delete');
            return;
        }
        $data = $service->delete($id);
        $data['success'] ? $output->info('Delete successful.') : $output->error($data['message']);

    }

    private function list(WebHookService $service,Output $output)
    {
        $data = $service->list();
        if ($data['success']) {
            if (is_array($data['data']) && !empty($data['data'])) {
                $output->writeln('<info>Webhook registered as follows:</info>');
                foreach ($data['data'] as $item) {
                    $output->writeln('<info>ID:</info>'.$item['id'] ?? 'unknown id');
                }
            }else{
                $output->writeln('<info>Webhook not registered yet</info>');
            }
        }else{
            $output->writeln('<info>'.$data['message'].'</info>');
        }
    }

    private function create(WebHookService $service,Output $output)
    {
        $data = $service->registration();
        if ($data['success']) {
            $output->info($data['message']);
            $output->info('ID:'.$data['webhook_id'] ?? 'unknown id');
            $this->setFlag($output);
        }else{
            $output->error($data['message']);
        }
    }

    private function setFlag(Output $output)
    {
        $path = app()->getAppPath().'..'.DIRECTORY_SEPARATOR.'.env';
        if (file_exists($path)
            && strpos(file_get_contents($path), 'WEBHOOK_SETTINGS')
        ) {
            $output->error('TURNITIN_WEBHOOK_SETTINGS is exists');
        } else {

            $content = file_get_contents($path);

            if ($content === false) {
                $output->error('Failed to read .env file');
                return;
            }

            if (strpos($content, 'WEBHOOK_SETTINGS=1') !== false) {
                return;
            }

            $pattern = '/(\[TURNITIN\][^\n]*\n)/';

            if (preg_match($pattern, $content, $matches, PREG_OFFSET_CAPTURE)) {

                $insertPos = $matches[0][1] + strlen($matches[0][0]);

                $newContent = substr($content, 0, $insertPos)
                    . "WEBHOOK_SETTINGS=1\n"
                    . substr($content, $insertPos);

            } else {
                $newContent = rtrim($content) . "\n\n[TURNITIN]\nWEBHOOK_SETTINGS=1\n";
            }

            file_put_contents($path, $newContent);

            $output->info('TURNITIN_WEBHOOK_SETTINGS has created');
        }
    }

    private function setType(Input $input, Output $output):void
    {
        $type = trim($input->getArgument('type') ?? '');
        $typeList = ['create','list','del'];

        if (empty($type) || !in_array($type, $typeList)) {
            $output->error('webhook type error');
            exit();
        }

        $this->type = $type;
    }
}