<?php
class Controller
{
    protected function render($view, $data = [])
    {
        extract($data);
        $viewFile = __DIR__ . '/../app/views/' . $view . '.php';
        require __DIR__ . '/../app/views/layouts/main.php';
    }
    protected function redirect($path)
    {
        $config = require __DIR__ . '/../config/config.php';
        header('Location: ' . $config['app']['base_url'] . $path);
    }
}

