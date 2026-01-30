<?php
namespace App\Services;
class MailService {
    public static function send(string $to, string $subject, string $html): bool {
        $headers = "MIME-Version: 1.0\r\nContent-type: text/html; charset=utf-8\r\n";
        $config = require __DIR__ . '/../Config/config.php';
        $from = $config['mail']['from'];
        $headers .= "From: " . $from . "\r\n";
        $ok = @mail($to, $subject, $html, $headers);
        $dir = __DIR__ . '/../../storage/emails';
        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }
        $name = $dir . '/' . date('Ymd_His') . '_' . preg_replace('/[^a-z0-9]/i', '_', $to) . '.html';
        file_put_contents($name, $html);
        return $ok;
    }
}
