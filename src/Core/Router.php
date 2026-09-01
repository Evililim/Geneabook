<?php

declare(strict_types=1);

namespace App\Core;

use App\Controllers\DocumentController;
use App\Controllers\HomeController;
use App\Controllers\IndividuController;
use Throwable;

final class Router
{
    /**
     * @var array<string, array{class: class-string, method: string, format: string}>
     */
    private array $routes = [
        'home' => ['class' => HomeController::class, 'method' => 'index', 'format' => 'html'],
        'documents.index' => ['class' => DocumentController::class, 'method' => 'index', 'format' => 'html'],
        'documents.api.index' => ['class' => DocumentController::class, 'method' => 'apiIndex', 'format' => 'json'],
        'individus.show' => ['class' => IndividuController::class, 'method' => 'show', 'format' => 'html'],
        'individus.api.show' => ['class' => IndividuController::class, 'method' => 'apiShow', 'format' => 'json'],
    ];

    public function dispatch(array $query): void
    {
        $action = (string) ($query['action'] ?? 'home');
        $route = $this->routes[$action] ?? null;

        if ($route === null) {
            $this->emitError('Route not found.', 404, $this->wantsJson($query));
            return;
        }

        try {
            $controller = new $route['class']();
            $method = $route['method'];
            $controller->$method($query);
        } catch (Throwable $throwable) {
            $this->emitError($throwable->getMessage(), 500, $route['format'] === 'json');
        }
    }

    private function wantsJson(array $query): bool
    {
        if (($query['format'] ?? null) === 'json') {
            return true;
        }

        $accept = (string) ($_SERVER['HTTP_ACCEPT'] ?? '');

        return str_contains($accept, 'application/json');
    }

    private function emitError(string $message, int $statusCode, bool $asJson): void
    {
        http_response_code($statusCode);

        if ($asJson) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['error' => $message], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
            return;
        }

        echo '<!doctype html><meta charset="utf-8"><title>Geneabook</title>';
        echo '<h1>Geneabook</h1>';
        echo '<p>' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</p>';
    }
}
