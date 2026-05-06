<?php
/**
 * Created by PhpStorm.
 * User: Ning
 * CreateTime: 2026/4/30 13:21
 */

namespace NzhC\Turnitin;

use NzhC\Turnitin\command\TurnitinWebhookCommand;
use think\Service;

class TurnitinService extends Service
{
    public function boot()
    {
        $this->commands(TurnitinWebhookCommand::class);
    }
}