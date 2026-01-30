<?php
declare(strict_types=1);

namespace App\Services;

use App\Repositories\AuditRepository;
use App\Core\Auth;

final class AuditService
{
    public function log(string $action, ?int $userId = null, array $meta = []): void
    {
        $actorId = $userId ?? (Auth::user()['id'] ?? null);
        (new AuditRepository())->log($action, $actorId, $meta);
    }
}
