<?php
/**
 * Created by PhpStorm.
 * User: Ning
 * CreateTime: 2026/4/30 17:02
 */

namespace NzhC\Turnitin\services;

use NzhC\Turnitin\enum\TurnitinEnum;
use NzhC\Turnitin\http\HttpClient;

class ReportDownloadService extends BaseService
{
    private HttpClient $http;

    private string $downloadApi = 'api/v1/submissions/%s/similarity/pdf/%s';

    public function __construct(HttpClient $http,array $config = [])
    {
        parent::__construct($config);
        parent::checkWebhookSetting();
        $this->http = $http;
    }

    public function download(string $pdfSavePath,string $similarityCheckId,string $similarityReportId): array
    {

        $downloadUrl = sprintf($this->downloadApi, $similarityCheckId,$similarityReportId);

        $downloadPdfUrl = $this->config['base_url'] ? $this->config['base_url'].$downloadUrl : '';

        $this->validateField(compact('downloadPdfUrl','pdfSavePath','similarityCheckId','similarityReportId'));

        $headers = $this->getHeaders(TurnitinEnum::DOWNLOAD_PDF_HEADER_TYPE);

        return $this->http->download($downloadPdfUrl,$pdfSavePath,$headers);
    }
}