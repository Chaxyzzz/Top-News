<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuditLogService
{
    /**
     * Log a security or administrative action.
     */
    public function log(
        string $action,
        mixed $description = null,
        mixed $entityType = null,
        mixed $entityId = null,
        mixed $user = null,
        ?array $metadata = null
    ): ?AuditLog {
        try {
            $finalEntityType = is_string($entityType) ? $entityType : null;
            $finalEntityId = is_numeric($entityId) ? (int) $entityId : null;
            $finalDescription = null;
            $actor = $user instanceof User ? $user : null;
            $meta = is_array($metadata) ? $metadata : null;

            // Handle when $description is passed as a Model (positional: log($action, $model, $desc, $actor, $metadata))
            if ($description instanceof Model) {
                $finalEntityType = get_class($description);
                $finalEntityId = (int) $description->getKey();
                $finalDescription = is_string($entityType) ? $entityType : null;
                $actor = $entityId instanceof User ? $entityId : ($user instanceof User ? $user : null);
                $meta = is_array($user) ? $user : (is_array($metadata) ? $metadata : null);
            } elseif (is_string($description)) {
                $finalDescription = $description;
                if ($entityId instanceof User) {
                    $actor = $entityId;
                }
                if (is_array($user)) {
                    $meta = $user;
                }
            }

            $userId = $actor?->id ?? Auth::id();
            $request = request();

            return AuditLog::create([
                'user_id' => $userId,
                'action' => $action,
                'entity_type' => $finalEntityType,
                'entity_id' => $finalEntityId,
                'description' => $finalDescription,
                'ip_address' => $request?->ip(),
                'user_agent' => $request ? substr((string) $request->userAgent(), 0, 500) : null,
                'metadata' => $meta,
                'created_at' => now(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('Failed to write audit log: '.$e->getMessage(), [
                'action' => $action,
            ]);

            return null;
        }
    }
}
