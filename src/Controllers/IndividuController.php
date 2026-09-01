<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Individu;

final class IndividuController extends Controller
{
    public function show(array $query): void
    {
        $id = (int) ($query['id'] ?? 0);
        $model = new Individu();
        $individu = $model->find($id);

        if ($individu === null) {
            $this->render('error', ['message' => 'Individu not found.'], 404);
            return;
        }

        $this->render('individus/show', [
            'title' => 'Individu #' . $id,
            'individu' => $individu,
            'assertions' => $model->assertions($id),
        ]);
    }

    public function apiShow(array $query): void
    {
        $id = (int) ($query['id'] ?? 0);
        $model = new Individu();
        $individu = $model->find($id);

        if ($individu === null) {
            $this->json(['error' => 'Individu not found.'], 404);
            return;
        }

        $this->json([
            'data' => [
                'individu' => $individu,
                'assertions' => $model->assertions($id),
            ],
        ]);
    }
}
