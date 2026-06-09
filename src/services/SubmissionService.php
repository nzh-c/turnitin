<?php
/**
 * Created by PhpStorm.
 * User: Ning
 * CreateTime: 2026/4/28 15:49
 */

namespace NzhC\Turnitin\services;

use DateTime;
use DateTimeZone;
use Exception;
use NzhC\Turnitin\enum\TurnitinEnum;
use NzhC\Turnitin\http\HttpClient;
use NzhC\Turnitin\lib\TurnitinParameterException;

class SubmissionService extends BaseService
{
    private HttpClient $http;

    private string $submissionUrl = 'api/v1/submissions';
    public function __construct(HttpClient $http,array $config = [])
    {
        parent::__construct($config);
        parent::checkWebhookSetting();
        $this->http = $http;
    }

    /**
     * @notes createSubmission
     * @param string $title
     * @param array $authors
     * @param array $submitter
     * @param array $customData
     * @return array
     * @throws Exception
     * @author n
     * @date 2026/4/28
     */
    public function createSubmission(string $title,array $authors, array $submitter,array $customData = []): array
    {
        $createSimilarityRequestUrl = $this->config['base_url'] ? $this->config['base_url']. $this->submissionUrl: '';

        $this->validateField(compact('title','createSimilarityRequestUrl'));

        $bodyData = $this->buildData($title,$authors,$submitter,$customData);
        $headers = $this->getHeaders(TurnitinEnum::SUBMISSION_HEADER_TYPE);

        $returnData = $this->http->curlRequest($createSimilarityRequestUrl,'POST',$headers,$bodyData);

        return $returnData['data'] ?? [];
    }

    /**
     * @throws Exception
     */
    private function buildData(string $title,
                               array $authors,
                               array $submitter,
                               array $customData = []): array
    {
        $dt = new DateTime('now', new DateTimeZone('UTC'));

        $owner = $authors[0]['id'] ?? null;

        return [
            "owner"                            => $owner,
            "title"                            => $title,
            "owner_default_permission_set"     => $this->config["owner_default_permission_set"] ?? null,
            "submitter_default_permission_set" => $this->config["submitter_default_permission_set"] ?? null,
            "metadata"                         => [
                "owners"=>$authors,
                "submitter"=>$submitter,
                "original_submitted_time"=> $dt->format('Y-m-d\TH:i:s.v\Z'),
                "custom"=> json_encode($customData)
            ]
        ];
    }
}