<?php
/**
 * Created by PhpStorm.
 * User: Ning
 * CreateTime: 2026/4/30 11:11
 */

namespace NzhC\Turnitin\services;

use NzhC\Turnitin\enum\TurnitinEnum;
use NzhC\Turnitin\http\HttpClient;

class SimilarityReportService extends BaseService
{
    private HttpClient $http;

    private string $url = 'api/v1/submissions/%s/similarity';

    public function __construct(HttpClient $http,array $config = [])
    {
        parent::__construct($config);
        parent::checkWebhookSetting();
        $this->http = $http;
    }

    public function create(string $similarityCheckId)
    {
        $reportUrl = sprintf($this->url, $similarityCheckId);
        $createReportUrl = $this->config['base_url'] ? $this->config['base_url'].$reportUrl : '';

        $this->validateField(compact('createReportUrl'));

        $headers = $this->getHeaders(TurnitinEnum::CREATE_REPORT_HEADER_TYPE);
        $bodyData = $this->buildData();

        $returnData = $this->http->curlRequest($createReportUrl,'PUT',$headers,$bodyData);

        return $returnData['data'] ?? [];
    }

    private function buildData():array
    {
        return [
            'indexing_settings'=>[
                'add_to_index' => true
            ],
            'generation_settings' => [
                'search_repositories' => [
                    'INTERNET',
                    'SUBMITTED_WORK',
                    'PUBLICATION',
                    'CROSSREF',
                    'CROSSREF_POSTED_CONTENT',
                ],
                'auto_exclude_self_matching_scope' => 'ALL',
                'priority' => 'HIGH',
            ],
            'view_settings' => [
                'exclude_quotes' => true,
                'exclude_bibliography' => true,
                'exclude_citations' => false,
                'exclude_abstract' => false,
                'exclude_methods' => false,
                'exclude_custom_sections' => false,
                'exclude_preprints' => false,
                'exclude_small_matches' => 8,
                'exclude_internet' => false,
                'exclude_publications' => false,
                'exclude_crossref' => false,
                'exclude_crossref_posted_content' => false,
                'exclude_submitted_works' => false,
            ],
        ];
    }
}