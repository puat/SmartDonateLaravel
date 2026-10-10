<?php

namespace App\Services;

use App\Models\AuditLog;

class AuditService
{
    public function record(string $action, string $module, ?int $recordId, string $description): AuditLog
    {
        return AuditLog::create(['user_id' => auth()->id(), 'action' => $action, 'module' => $module, 'record_id' => $recordId, 'description' => $description, 'ip_address' => request()?->ip(), 'user_agent' => request()?->userAgent()]);
    }
}
