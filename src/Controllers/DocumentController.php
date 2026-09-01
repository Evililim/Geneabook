<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Document;

final class DocumentController extends Controller
{
    public function index(array $query): void
    {
        $documents = (new Document())->latest();

        $this->render('documents/index', [
            'title' => 'Documents',
            'documents' => $documents,
        ]);
    }

    public function apiIndex(array $query): void
    {
        $documents = (new Document())->latest();

        $this->json([
            'data' => $documents,
        ]);
    }
}
