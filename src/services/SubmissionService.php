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
    public function __construct(HttpClient $http)
    {
        parent::__construct();
        $this->http = $http;
    }

    /**
     * @notes createSubmission
     * @param string $title
     * @param array $customData
     * @return array
     * @throws Exception
     * @author n
     * @date 2026/4/28
     */
    public function createSubmission(string $title,array $customData = []): array
    {
        $createSimilarityRequestUrl = $this->config['base_url'] ? $this->config['base_url']. $this->submissionUrl: '';

        $this->validateField(compact('title','createSimilarityRequestUrl'));

        $bodyData = $this->buildData($title,$customData);
        $headers = $this->getHeaders(TurnitinEnum::SUBMISSION_HEADER_TYPE);

        $returnData = $this->http->curlRequest($createSimilarityRequestUrl,'POST',$headers,$bodyData);

        return $returnData['data'] ?? [];
    }

    /**
     * @throws Exception
     */
    private function buildData(string $title, array $customData = []): array
    {
        $dt = new DateTime('now', new DateTimeZone('UTC'));
        return [
            "owner"                            => $this->config["owner_id"] ?? null,
            "title"                            => $title,
            "owner_default_permission_set"     => $this->config["owner_default_permission_set"] ?? null,
            "submitter_default_permission_set" => $this->config["submitter_default_permission_set"] ?? null,
            "metadata"                         => [
                "owners"=>[
                            [
                                "id"=>$this->config["owner_id"] ?? null,
                                "given_name"=>$this->config["given_name"] ?? null,
                                "family_name"=> $this->config["family_name"] ?? null,
                                "email"=> $this->config["email"] ?? null,
                            ]
                        ],
                "original_submitted_time"=> $dt->format('Y-m-d\TH:i:s.v\Z'),
                "custom"=> json_encode($customData)
            ]
        ];
    }
}