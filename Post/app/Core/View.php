<?php
declare(strict_types=1);

namespace App\Core;

final class View
{
    public static function render(string $view, array $data = []): string
    {
        $path = dirname(__DIR__) . '/Views/' . $view . '.php';
        if (!file_exists($path)) {
            throw new \RuntimeException('View not found');
        }
        extract($data, EXTR_SKIP);
        ob_start();
        require $path;
        return ob_get_clean();
    }
}
