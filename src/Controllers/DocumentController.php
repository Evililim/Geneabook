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

    public function extract(array $query): void
    {
        $this->render('documents/extract', [
            'title' => 'Saisie orientee source',
            'scanPath' => $query['scan'] ?? '/assets/sample-acte.svg',
        ]);
    }

    public function storeExtraction(array $query): void
    {
        $this->json([
            'status' => 'received',
            'document' => $_POST['document'] ?? [],
            'evenement' => $_POST['evenement'] ?? [],
            'assertions_count' => count($_POST['assertions'] ?? []),
        ], 202);
    }

    public function apiIndex(array $query): void
    {
        $documents = (new Document())->latest();

        $this->json([
            'data' => $documents,
        ]);
    }
}
