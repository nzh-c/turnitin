<?php
/**
 * Created by PhpStorm.
 * User: Ning
 * CreateTime: 2026/4/28 14:34
 */

namespace NzhC\Turnitin\http;

use NzhC\Turnitin\enum\TurnitinEnum;
use NzhC\Turnitin\exception\TurnitinRequestException;

class HttpClient
{
    public function __construct(){}

    /**
     * @notes download
     * @param string $url
     * @param string $savePath
     * @param array $headers
     * @param int $maxRetries
     * @return array
     * @author n
     * @date 2026/4/30
     */
    public function download(string $url, string $savePath, array $headers = [], int $maxRetries = 3): array
    {
        $requestId = uniqid('download_', true);
        $dir = dirname($savePath);

        if (!is_dir($dir) && !mkdir($dir, 0777, true) && !is_dir($dir)) {
            throw new TurnitinRequestException(TurnitinEnum::FAILED_CREATE_DIRECTORY.$dir);
        }

        $tmpPath = $savePath . '.tmp';

        $lastError = null;

        for ($i = 1; $i <= $maxRetries; $i++) {

            $fp = fopen($tmpPath, 'wb');
            if ($fp === false) {
                throw new TurnitinRequestException(TurnitinEnum::CANNOT_OPEN_OR_WRITING_FILE.$tmpPath);
            }

            $ch = curl_init($url);

            curl_setopt_array($ch, [
                CURLOPT_FILE => $fp,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_TIMEOUT => 60,
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_FAILONERROR => false,
            ]);

            curl_exec($ch);

            $curlErrno = curl_errno($ch);
            $curlError = curl_error($ch);
            $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            curl_close($ch);
            fclose($fp);

            if ($curlErrno === 0 && $httpCode >= 200 && $httpCode < 300) {
                rename($tmpPath, $savePath);

                return [
                    'success' => true,
                    'http_code' => $httpCode,
                    'file' => $savePath,
                    'request_id' => $requestId,
                ];
            }


            $lastError = [
                'attempt' => $i,
                'curl_errno' => $curlErrno,
                'curl_error' => $curlError,
                'http_code' => $httpCode,
                'request_id' => $requestId,
            ];

            if (file_exists($tmpPath)) {
                unlink($tmpPath);
            }

            $retryable = (
                $curlErrno !== 0 ||
                $httpCode >= 500 ||
                $httpCode === 0
            );

            if (!$retryable) {
                break;
            }

            if ($i < $maxRetries) {
                usleep(100000 * pow(2, $i));
            }
        }

        $responseMsg = null;

        if ( !empty($lastError[ 'response']) ) {
            $errResponse = json_decode($lastError['response'], true);
            $responseMsg = $errResponse['message'] ?? null;
        }

        throw new TurnitinRequestException($responseMsg ?? TurnitinEnum::REQUEST_SERVICE_CALL_FAILED, $lastError,0,null,$lastError['http_code'] ?? 500);
    }

    /**
     * @notes curlRequest
     * @param string $baseUrl
     * @param string $method
     * @param array $headers
     * @param array $data
     * @param string $filePath
     * @param int $maxRetries
     * @return array
     * @author n
     * @date 2026/4/28
     */
    public function curlRequest(string $baseUrl
                                , string $method = 'GET'
                                ,array $headers = []
                                ,array $data = []
                                , string $filePath = ''
                                ,int $maxRetries = 3): array
    {
        $lastError = null;

        $requestId = uniqid('turnitin_', true);

        $method = strtoupper($method);

        for ($i = 1; $i <= $maxRetries; $i++) {

            $ch = curl_init();

            curl_setopt_array($ch, [
                CURLOPT_URL => $baseUrl,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_SSL_VERIFYHOST => 2,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_CONNECTTIMEOUT => 5,
                CURLOPT_CUSTOMREQUEST => $method,
            ]);

            if ($filePath) {
                if (!is_file($filePath)) {
                    throw new TurnitinRequestException(TurnitinEnum::FILE_NOT_FOUND. $filePath);
                }

                $fp = fopen($filePath, 'rb');

                if ($fp === false) {
                    throw new TurnitinRequestException(TurnitinEnum::FILE_OPEN_FAILED.$filePath);
                }

                curl_setopt($ch, CURLOPT_UPLOAD, true);
                curl_setopt($ch, CURLOPT_INFILE, $fp);
                curl_setopt($ch, CURLOPT_INFILESIZE, filesize($filePath));
            } elseif (in_array($method, ['POST','PUT','PATCH'])) {
                $body = json_encode($data, JSON_UNESCAPED_UNICODE);

                if ($body === false) {
                    throw new TurnitinRequestException(TurnitinEnum::JSON_ENCODE_FAILED);
                }

                curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
            }

            $response = curl_exec($ch);

            if ($response === false) {
                $response = null;
            }

            $curl_errno = curl_errno($ch);
            $curl_error = curl_error($ch);
            $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            curl_close($ch);

            $result = null;

            if (!empty($response)) {
                $result = json_decode($response, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    $result = $response;
                }
            }

            if ($curl_errno === 0 && $httpCode >= 200 && $httpCode < 300) {
                return [
                    'http_code' => $httpCode,
                    'data' => $result,
                    'raw' => $response,
                    'success' => true,
                ];
            }

            $lastError = [
                'attempt' => $i,
                'curl_errno' => $curl_errno,
                'curl_error' => $curl_error,
                'http_code' => $httpCode,
                'response' => $response,
                'request_id' => $requestId,
            ];

            $retryable = ( $curl_errno !== 0 || $httpCode >= 500 );

            if (!$retryable) {
                break;
            }

            if ($i < $maxRetries) {
                usleep(100000 * pow(2, $i));
            }
        }

        $responseMsg = null;

        if ( !empty($lastError[ 'response']) ) {
            $errResponse = json_decode($lastError['response'], true);
            $responseMsg = $errResponse['message'] ?? null;
        }

        throw new TurnitinRequestException($responseMsg ?? TurnitinEnum::REQUEST_SERVICE_CALL_FAILED, $lastError,0,null,$lastError['http_code'] ?? 500);
    }
}