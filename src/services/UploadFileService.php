<?php
/**
 * Created by PhpStorm.
 * User: Ning
 * CreateTime: 2026/4/28 18:07
 */

namespace NzhC\Turnitin\services;

use NzhC\Turnitin\enum\TurnitinEnum;
use NzhC\Turnitin\http\HttpClient;

class UploadFileService extends BaseService
{
    private HttpClient $http;

    private string $uploadApi = 'api/v1/submissions/%s/original';

    public function __construct(HttpClient $http,array $config = [])
    {
        parent::__construct($config);
        parent::checkWebhookSetting();
        $this->http = $http;
    }

    public function uploadFile(string $checkFileName,string $checkFilePath,string $similarityCheckId)
    {

        $uploadUrl = sprintf($this->uploadApi, $similarityCheckId);

        $uploadSimilarityFileUrl = $this->config['base_url'] ? $this->config['base_url'].$uploadUrl : '';

        $this->validateField(compact('checkFileName','checkFilePath','similarityCheckId','uploadSimilarityFileUrl'));

        $headers = $this->getHeaders(TurnitinEnum::UPLOAD_FILE_HEADER_TYPE,[
            'Content-Disposition:'.'inline; filename="'.$checkFileName.'"'
        ]);

        $returnData = $this->http->curlRequest($uploadSimilarityFileUrl,'PUT',$headers,[],$checkFilePath);

        return $returnData['data'] ?? [];
    }
}