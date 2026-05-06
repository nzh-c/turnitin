<?php
/**
 * Created by PhpStorm.
 * User: Ning
 * CreateTime: 2026/4/30 16:53
 */

namespace NzhC\Turnitin\services;

use NzhC\Turnitin\enum\TurnitinEnum;
use NzhC\Turnitin\http\HttpClient;

class PdfReportService extends BaseService
{
    private HttpClient $http;

    private string $url = 'api/v1/submissions/%s/similarity/pdf';

    public function __construct(HttpClient $http)
    {
        parent::__construct();
        $this->http = $http;
    }

    public function create(string $similarityCheckId,string $locale = '')
    {
        $pdfUrl = sprintf($this->url, $similarityCheckId);
        $createPdfUrl = $this->config['base_url'] ? $this->config['base_url'].$pdfUrl : '';

        $this->validateField(compact('createPdfUrl','similarityCheckId'));

        $headers = $this->getHeaders(TurnitinEnum::CREATE_PDF_REPORT_HEADER_TYPE);

        $bodyData = $this->buildData($locale);

        $returnData = $this->http->curlRequest($createPdfUrl,'POST',$headers,$bodyData);

        return $returnData['data'] ?? [];
    }

    private function buildData(string $locale = ''):array
    {
        return [
            'locale' => $locale ?: 'en',
        ];
    }
}