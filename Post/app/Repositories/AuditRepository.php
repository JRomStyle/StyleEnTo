<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Core\DB;
use App\Core\Session;

final class AuditRepository
{
    public function log(string $action, ?int $userId, array $meta = []): void
    {
        $stmt = DB::connection()->prepare('INSERT INTO audit_logs (action, user_id, ip_address, session_id, metadata, created_at) VALUES (:action, :user_id, :ip, :session_id, :metadata, NOW())');
        $stmt->execute([
            'action' => $action,
            'user_id' => $userId,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'session_id' => session_id(),
            'metadata' => json_encode($meta, JSON_UNESCAPED_UNICODE)
        ]);
    }
}
