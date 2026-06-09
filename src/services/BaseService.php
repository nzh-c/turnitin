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

class BaseService
{
    protected array $config = [];

    public function __construct(array $config = [])
    {
        $this->configBuild($config);
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    protected function checkWebhookSetting()
    {
        if (!isset($this->config['webhook_settings']) || empty($this->config['webhook_settings']))
        {
            throw new TurnitinRuntimeException(TurnitinEnum::WEBHOOK_SETTING_FAILED);
        }
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
            case TurnitinEnum::GET_SUBMISSION_INFO_TYPE:
            case TurnitinEnum::GET_REPORT_INFO_TYPE:
            case TurnitinEnum::REGISTRATION_WEBHOOKS_HEADER_TYPE:
            case TurnitinEnum::WEBHOOKS_LISTS_HEADER_TYPE:
            case TurnitinEnum::CREATE_REPORT_HEADER_TYPE:
            case TurnitinEnum::WEBHOOKS_DEL_HEADER_TYPE:
            case TurnitinEnum::CREATE_PDF_REPORT_HEADER_TYPE:
            case TurnitinEnum::VIEWER_URL_HEADER_TYPE:
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

    protected function configBuild(array $config = []):void
    {
        $defaultConfig = require __DIR__ . '/../../config/turnitin.php';

        $this->config = array_merge(
            $defaultConfig,
            $this->getFrameworkConfig(),
            $config
        );
    }

    protected function getFrameworkConfig(): array
    {
        if (class_exists(\think\facade\Config::class)) {

            try {

                if (
                    class_exists(\think\App::class)
                    && strpos(\think\App::VERSION, '6.') === 0
                ) {
                    return \think\facade\Config::get('turnitin') ?? [];
                }

                return \think\facade\Config::get('turnitin.') ?? [];

            } catch (\Throwable $e) {
                return [];
            }
        }

        if (function_exists('config')) {

            try {
                return config('turnitin', []);
            } catch (\Throwable $e) {
                return [];
            }
        }

        return [];
    }
}