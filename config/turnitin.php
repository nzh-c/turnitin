<?php
/**
 * Created by PhpStorm.
 * User: Ning
 * CreateTime: 2026/4/9 18:13
 */

return [
    'base_url' => env('TURNITIN_ASE_URL','https://crossref-22835.tii-sandbox.com/'),
    'api_key' => env('TURNITIN_API_KEY'),
    'x_turnitin_integration_name' => env('TURNITIN_INTEGRATION_NAME'),
    'x_turnitin_integration_version' => env('TURNITIN_INTEGRATION_VERSION'),
    'owner_id' => env('TURNITIN_OWNER_ID'),
    'owner_default_permission_set' => env('TURNITIN_OWNER_DEFAULT_PERMISSION_SET'),
    'submitter_default_permission_set' => env('TURNITIN_SUBMITTER_DEFAULT_PERMISSION_SET'),
    'given_name' => env('TURNITIN_GIVEN_NAME'),
    'family_name' => env('TURNITIN_FAMILY_NAME'),
    'email' => env('TURNITIN_EMAIL'),
    'signing_secret' => env('TURNITIN_SIGNING_SECRET'),
    'callback' => env('TURNITIN_CALLBACK'),
    'webhook_description' => env('TURNITIN_WEBHOOK_DESCRIPTION','oss webhook'),
    'event_types' => env('TURNITIN_EVENT_TYPES','SUBMISSION_COMPLETE,SIMILARITY_COMPLETE,SIMILARITY_UPDATED,PDF_STATUS,GROUP_ATTACHMENT_COMPLETE'),
    'webhook_settings' => env('TURNITIN_WEBHOOK_SETTINGS'),
];