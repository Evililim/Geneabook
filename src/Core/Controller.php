<?php

declare(strict_types=1);

namespace App\Core;

abstract class Controller
{
    protected function render(string $view, array $data = [], int $statusCode = 200): void
    {
        http_response_code($statusCode);

        extract($data, EXTR_SKIP);

        $viewPath = dirname(__DIR__, 2) . '/views/' . $view . '.php';
        if (!is_file($viewPath)) {
            $this->json(['error' => 'View not found.'], 500);
            return;
        }

        require $viewPath;
    }

    protected function json(array $payload, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');

        echo json_encode($payload, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
    }
}
