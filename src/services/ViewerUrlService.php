<?php
/**
 * Created by PhpStorm.
 * User: Ning
 * CreateTime: 2026/6/3 16:13
 */

namespace NzhC\Turnitin\services;

use NzhC\Turnitin\enum\TurnitinEnum;
use NzhC\Turnitin\http\HttpClient;

class ViewerUrlService extends BaseService
{
    private HttpClient $http;

    private string $url = 'api/v1/submissions/%s/viewer-url';

    public function __construct(HttpClient $http,array $config = [])
    {
        parent::__construct($config);
        parent::checkWebhookSetting();
        $this->http = $http;
    }

    public function create(string $similarityCheckId,string $locale = '')
    {
        $reportUrl = sprintf($this->url, $similarityCheckId);
        $createViewerUrl = $this->config['base_url'] ? $this->config['base_url'].$reportUrl : '';

        $this->validateField(compact('createViewerUrl'));

        $headers = $this->getHeaders(TurnitinEnum::VIEWER_URL_HEADER_TYPE);
        $bodyData = $this->buildData($locale);

        $returnData = $this->http->curlRequest($createViewerUrl,'POST',$headers,$bodyData);

        return $returnData['data'] ?? [];
    }

    private function buildData($locale):array
    {
        return [
            "viewer_user_id" => $this->config["owner_id"] ?? null,
            "locale"=>  $locale ?: 'en',
            "viewer_default_permission_set" => 'EDITOR'
        ];
    }
}