<?php
/**
 * Created by PhpStorm.
 * User: Ning
 * CreateTime: 2026/4/9 18:29
 */

namespace NzhC\Turnitin;

use NzhC\Turnitin\enum\TurnitinEnum;
use NzhC\Turnitin\exception\TurnitinSignatureException;
use NzhC\Turnitin\http\HttpClient;
use NzhC\Turnitin\services\PdfReportService;
use NzhC\Turnitin\services\ReportDownloadService;
use NzhC\Turnitin\services\SimilarityReportService;
use NzhC\Turnitin\services\SubmissionService;
use NzhC\Turnitin\services\UploadFileService;

class Turnitin
{
    private HttpClient $http;

    public function __construct()
    {
        $this->http = new HttpClient();
    }

    /**
     * @notes signatureVerification
     * @param string $secret
     * @param string $rawBody php://input 进行获取
     * @param string $signature
     * @return bool
     * @author n
     * @date 2026/4/30
     */
    public function signatureVerification(string $secret,string $rawBody,string $signature): bool
    {
        $calculatedSignature = hash_hmac('sha256', $rawBody, $secret);

        if (!hash_equals($calculatedSignature, $signature)) {
            throw new TurnitinSignatureException(TurnitinEnum::SIGNATURE_VERIFICATION_FAILED);
        }

        return true;
    }

    /**
     * @notes createSubmission
     * @param $title
     * @param array $customData
     * @return array
     * @throws \Exception
     * @author n
     * @date 2026/4/28
     */
    public function createSubmission($title,array $customData = []): array
    {
        $submissionService = new SubmissionService($this->http);
        return $submissionService->createSubmission($title,$customData);
    }

    /**
     * @notes uploadFile
     * @param string $fileName
     * @param string $filePath
     * @param string $similarityCheckId
     * @return array|mixed
     * @author n
     * @date 2026/4/30
     */
    public function uploadFile(string $fileName,string $filePath,string $similarityCheckId)
    {
        $uploadFileService = new UploadFileService($this->http);
        return $uploadFileService->uploadFile($fileName,$filePath,$similarityCheckId);
    }

    /**
     * @notes createReport
     * @param string $similarityCheckId
     * @return array|mixed
     * @author n
     * @date 2026/4/30
     */
    public function createReport(string $similarityCheckId)
    {
        $createReportService = new SimilarityReportService($this->http);
        return $createReportService->create($similarityCheckId);
    }

    /**
     * @notes createReportPdf
     * @param string $similarityCheckId
     * @return array|mixed
     * @author n
     * @date 2026/4/30
     */
    public function createPdfReport(string $similarityCheckId)
    {
        $pfeReportService = new PdfReportService($this->http);
        return $pfeReportService->create($similarityCheckId);
    }

    /**
     * @notes downloadPdfReport
     * @param string $savePath
     * @param string $similarityCheckId
     * @param string $pdfId
     * @return array|mixed
     * @author n
     * @date 2026/4/30
     */
    public function downloadPdfReport(string $savePath,string $similarityCheckId,string $pdfId)
    {
        $uploadFileService = new ReportDownloadService($this->http);
        return $uploadFileService->download($savePath,$similarityCheckId,$pdfId);
    }
}