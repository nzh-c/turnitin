<?php
/**
 * Created by PhpStorm.
 * User: Ning
 * CreateTime: 2026/4/28 15:46
 */

namespace NzhC\Turnitin\services;

use NzhC\Turnitin\enum\TurnitinEnum;
use NzhC\Turnitin\exception\TurnitinParameterException;
use NzhC\Turnitin\exception\TurnitinRuntimeException;
use think\App;
use think\facade\Config;

class BaseService
{
    protected array $config;

    public function __construct()
    {
        $this->configBuild();

        if (!isset($this->config['webhook_settings']) || empty($this->config['webhook_settings']))
        {
            throw new TurnitinRuntimeException(TurnitinEnum::WEBHOOK_SETTING_FAILED);
        }
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    private function camelToWords(string $input): string
    {
        $words = preg_split('/(?=[A-Z])/', $input);
        $words = array_map(function ($word) {
            return ucfirst(strtolower($word));
        }, $words);

        return implode(' ', $words);
    }

    public function validateField(array $field): bool
    {
        foreach ($field as $key => $value) {
            if (empty($value)) {
                $showKey = $this->camelToWords($key);
                throw new TurnitinParameterException(sprintf(TurnitinEnum::FIELD_REQUIRED_FOR_CHECK,$showKey));
            }
        }

        return true;
    }

    protected function getHeaders(string $type,?array $data = null):array
    {
        $headers = [];

        $defaultHeaders = [
            'Authorization: Bearer ' . ($this->config['api_key'] ?? ''),
            'X-Turnitin-Integration-Name: ' . ($this->config['x_turnitin_integration_name'] ?? ''),
            'X-Turnitin-Integration-Version: ' . ($this->config['x_turnitin_integration_version'] ?? '')
        ];

        switch ($type) {
            case TurnitinEnum::SUBMISSION_HEADER_TYPE:
            case TurnitinEnum::REGISTRATION_WEBHOOKS_HEADER_TYPE:
            case TurnitinEnum::WEBHOOKS_LISTS_HEADER_TYPE:
            case TurnitinEnum::CREATE_REPORT_HEADER_TYPE:
            case TurnitinEnum::WEBHOOKS_DEL_HEADER_TYPE:
            case TurnitinEnum::CREATE_PDF_REPORT_HEADER_TYPE:
                $headers = array_merge($defaultHeaders, [
                    'Content-Type:application/json'
                ], $data ?? []);
                break;
            case TurnitinEnum::UPLOAD_FILE_HEADER_TYPE:
                $headers = array_merge($defaultHeaders, [
                    'Content-Type:application/octet-stream'
                ],$data ?? []);
                break;
            case TurnitinEnum::DOWNLOAD_PDF_HEADER_TYPE:
                $headers = $defaultHeaders;
                break;
        }

        return $headers;
    }

    protected function configBuild():void
    {
        $this->config = require __DIR__.'/../../config/turnitin.php';

//        if (strpos(App::VERSION, '6.') === 0) {
//            $this->config = array_merge($this->config, Config::get('turnitin') ?? []);
//        } else {
//            $this->config = array_merge($this->config, Config::get('turnitin.') ?? []);
//        }
    }
}