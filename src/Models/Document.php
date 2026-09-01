<?php

declare(strict_types=1);

namespace App\Models;

final class Document extends BaseModel
{
    public function create(array $data): int
    {
        $sql = <<<'SQL'
            INSERT INTO documents (
                type,
                titre,
                chemin_fichier,
                media_mime_type,
                media_hash_sha256,
                archive_nom,
                archive_cote,
                archive_url,
                date_document_texte,
                date_document_min,
                date_document_max,
                lieu_conservation,
                transcription,
                notes,
                gedcom_source_ref,
                gedcom_media_ref
            ) VALUES (
                :type,
                :titre,
                :chemin_fichier,
                :media_mime_type,
                :media_hash_sha256,
                :archive_nom,
                :archive_cote,
                :archive_url,
                :date_document_texte,
                :date_document_min,
                :date_document_max,
                :lieu_conservation,
                :transcription,
                :notes,
                :gedcom_source_ref,
                :gedcom_media_ref
            )
            RETURNING id
        SQL;

        $statement = $this->db->prepare($sql);
        $statement->execute([
            'type' => $data['type'],
            'titre' => $data['titre'] ?? null,
            'chemin_fichier' => $data['chemin_fichier'],
            'media_mime_type' => $data['media_mime_type'] ?? null,
            'media_hash_sha256' => $data['media_hash_sha256'] ?? null,
            'archive_nom' => $data['archive_nom'] ?? null,
            'archive_cote' => $data['archive_cote'] ?? null,
            'archive_url' => $data['archive_url'] ?? null,
            'date_document_texte' => $data['date_document_texte'] ?? null,
            'date_document_min' => $data['date_document_min'] ?? null,
            'date_document_max' => $data['date_document_max'] ?? null,
            'lieu_conservation' => $data['lieu_conservation'] ?? null,
            'transcription' => $data['transcription'] ?? null,
            'notes' => $data['notes'] ?? null,
            'gedcom_source_ref' => $data['gedcom_source_ref'] ?? null,
            'gedcom_media_ref' => $data['gedcom_media_ref'] ?? null,
        ]);

        return (int) $statement->fetchColumn();
    }

    public function find(int $id): ?array
    {
        $statement = $this->db->prepare('SELECT * FROM documents WHERE id = :id');
        $statement->execute(['id' => $id]);
        $document = $statement->fetch();

        return $document === false ? null : $document;
    }

    public function latest(int $limit = 20): array
    {
        $statement = $this->db->prepare(
            'SELECT * FROM documents ORDER BY date_ajout DESC, id DESC LIMIT :limit'
        );
        $statement->bindValue('limit', $limit, \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }
}
