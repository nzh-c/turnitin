<?php
/**
 * Created by PhpStorm.
 * User: Ning
 * CreateTime: 2026/4/30 11:43
 */

namespace NzhC\Turnitin\services;

use NzhC\Turnitin\enum\TurnitinEnum;
use NzhC\Turnitin\http\HttpClient;

class WebHookService extends BaseService
{
    private HttpClient $http;

    private string $webHookApi = 'api/v1/webhooks';

    public function __construct(HttpClient $http)
    {
        parent::__construct();
        $this->http = $http;
    }

    /**
     * @notes registration
     * @return array
     * @author n
     * @date 2026/4/30
     */
    public function registration():array
    {
        try{
            $createWebhookUrl = $this->config['base_url'] ? $this->config['base_url'].$this->webHookApi : '';

            $this->validateField(compact('createWebhookUrl'));

            $headers = $this->getHeaders(TurnitinEnum::REGISTRATION_WEBHOOKS_HEADER_TYPE);
            $bodyData = $this->buildData();

            $returnData = $this->http->curlRequest($createWebhookUrl,'POST',$headers,$bodyData);
            $data['success'] = true;
            $data['message'] = 'Registration Succeeded';
            $data['webhook_id'] = $returnData['data']['id'] ?? null;
            return $data;
        }catch (\Exception $exception){
            $data['success'] = false;
            $data['message'] = $exception->getMessage();
            return $data;
        }

    }

    /**
     * @notes list
     * @return array
     * @author n
     * @date 2026/4/30
     */
    public function list():array
    {
        try{
            $listUrl = $this->config['base_url'] ? $this->config['base_url'].$this->webHookApi : '';

            $this->validateField(compact('listUrl'));

            $headers = $this->getHeaders(TurnitinEnum::WEBHOOKS_LISTS_HEADER_TYPE);

            return $this->http->curlRequest($listUrl,'GET',$headers);
        }catch (\Exception $exception){
            $data['success'] = false;
            $data['message'] = $exception->getMessage();
            return $data;
        }
    }

    /**
     * @notes delete
     * @param string $id
     * @return array
     * @author n
     * @date 2026/4/30
     */
    public function delete(string $id):array
    {
        try{
            $delUrl = $this->config['base_url'] ? $this->config['base_url'].$this->webHookApi.'/'.$id : '';

            $this->validateField(compact('delUrl'));

            $headers = $this->getHeaders(TurnitinEnum::WEBHOOKS_LISTS_HEADER_TYPE);

            $this->http->curlRequest($delUrl,'DELETE',$headers);

            $data['success'] = true;
            $data['message'] = 'Delete Succeeded';
            return $data;
        }catch (\Exception $exception){
            $data['success'] = false;
            $data['message'] = $exception->getMessage();
            return $data;
        }
    }

    /**
     * @throws Exception
     */
    private function buildData(): array
    {
        $event = $this->config['event_types'] ? explode(',',$this->config['event_types']) : [];
        return [
            "signing_secret"                   => isset($this->config["signing_secret"]) && !empty($this->config["signing_secret"])
                                                  ? base64_encode($this->config["signing_secret"]) : null,
            "description"                            => $this->config["webhook_description"],
            "url"     => $this->config["callback"] ?? null,
            "event_types" => $event
        ];
    }
}