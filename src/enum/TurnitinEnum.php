<?php
/**
 * Created by PhpStorm.
 * User: Ning
 * CreateTime: 2026/4/28 15:27
 */

namespace NzhC\Turnitin\enum;

class TurnitinEnum
{
    const DUPLICATE_OPTION_HTTP_CODE = 409;

    const WEBHOOK_UTL_EXIST = 'A webhook with the same URL already exist. Either update or delete the existing webhook.';

    const WEBHOOK_SETTING_FAILED = 'Webhook is not configured. Please run the command to configure it first.';

    const SUBMISSION_HEADER_TYPE = 'SUBMISSION';

    const CANNOT_OPEN_OR_WRITING_FILE = 'Cannot open file for writing: ';

    const FAILED_CREATE_DIRECTORY = 'Failed to create directory';

    const UPLOAD_FILE_HEADER_TYPE = 'UPLOAD_FILE';

    const DOWNLOAD_PDF_HEADER_TYPE = 'DOWNLOAD_PDF';

    const VIEWER_URL_HEADER_TYPE = 'VIEWER_URL';

    const REGISTRATION_WEBHOOKS_HEADER_TYPE = 'REGISTRATION_WEBHOOKS';
    const WEBHOOKS_LISTS_HEADER_TYPE = 'WEBHOOKS_LISTS';

    const CREATE_REPORT_HEADER_TYPE = 'CREATE_REPORT';

    const CREATE_PDF_REPORT_HEADER_TYPE = 'CREATE_PDF_REPORT';

    const WEBHOOKS_DEL_HEADER_TYPE = 'WEBHOOKS_DEL';

    const REQUEST_SERVICE_CALL_FAILED = 'Request service call failed, please try again later.';

    const REQUEST_LINK_ACQUISITION_FAILED = 'Request link acquisition failed, please try again later.';

    const JSON_ENCODE_FAILED = 'JSON encode failed';

    const SIGNATURE_VERIFICATION_FAILED = 'Signature verification failed';

    const INVALID_JSON_RESPONSE = 'Invalid JSON response: ';

    const FILE_NOT_FOUND = 'File not found: ';

    const FILE_READ_FAILED = 'File read failed: ';

    const FILE_OPEN_FAILED = 'File open failed: ';

    const FIELD_REQUIRED_FOR_CHECK = '%s is required for similarity check.';
}