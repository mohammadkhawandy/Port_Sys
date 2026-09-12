<?php

namespace App\Libraries;

use App\Models\ActivityLog;

class ActivityLogger
{
    public function log(
        string $action,
        string $description,
        ?string $entityType = null,
        ?int $entityId = null,
        array $metadata = []
    ): void {
        try {
            (new ActivityLog())->insert([
                'user_id' => session()->get('userId') ? (int) session()->get('userId') : null,
                'action' => $action,
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'description' => mb_substr($description, 0, 500),
                'metadata' => $metadata === [] ? null : json_encode($metadata, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'ip_address' => service('request')->getIPAddress(),
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $e) {
            log_message('debug', 'Activity log skipped: {message}', ['message' => $e->getMessage()]);
        }
    }
}
