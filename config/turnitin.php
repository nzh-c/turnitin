<?php

/**
 * Turnitin Config (Framework agnostic)
 */

if (!function_exists('turnitin_env')) {

    function turnitin_env($key, $default = null)
    {
        if (function_exists('env')) {
            return env($key, $default);
        }

        $value = getenv($key);
        if ($value !== false) {
            return $value;
        }

        return $default;
    }
}

return [

    'base_url' => turnitin_env(
        'TURNITIN_BASE_URL',
        'https://crossref-22835.tii-sandbox.com/'
    ),

    'api_key' => turnitin_env('TURNITIN_API_KEY'),

    'x_turnitin_integration_name' => turnitin_env('TURNITIN_INTEGRATION_NAME'),

    'x_turnitin_integration_version' => turnitin_env('TURNITIN_INTEGRATION_VERSION'),

    'owner_id' => turnitin_env('TURNITIN_OWNER_ID'),

    'owner_default_permission_set' => turnitin_env('TURNITIN_OWNER_DEFAULT_PERMISSION_SET'),

    'submitter_default_permission_set' => turnitin_env('TURNITIN_SUBMITTER_DEFAULT_PERMISSION_SET'),

    'signing_secret' => turnitin_env('TURNITIN_SIGNING_SECRET'),

    'callback' => turnitin_env('TURNITIN_CALLBACK'),

    'webhook_description' => turnitin_env(
        'TURNITIN_WEBHOOK_DESCRIPTION',
        'oss webhook'
    ),

    'event_types' =>turnitin_env(
        'TURNITIN_EVENT_TYPES',
        'SUBMISSION_COMPLETE,SIMILARITY_COMPLETE,SIMILARITY_UPDATED,PDF_STATUS,GROUP_ATTACHMENT_COMPLETE'
    ),

    'webhook_settings' => turnitin_env('TURNITIN_WEBHOOK_SETTINGS'),
];