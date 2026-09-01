<?php

declare(strict_types=1);

namespace App\Models;

final class Individu extends BaseModel
{
    public function create(array $data = []): int
    {
        $statement = $this->db->prepare(
            'INSERT INTO individus (souche_base, notes_globales, gedcom_xref)
             VALUES (:souche_base, :notes_globales, :gedcom_xref)
             RETURNING id'
        );
        $statement->execute([
            'souche_base' => (bool) ($data['souche_base'] ?? false),
            'notes_globales' => $data['notes_globales'] ?? null,
            'gedcom_xref' => $data['gedcom_xref'] ?? null,
        ]);

        return (int) $statement->fetchColumn();
    }

    public function find(int $id): ?array
    {
        $statement = $this->db->prepare('SELECT * FROM individus WHERE id = :id');
        $statement->execute(['id' => $id]);
        $individu = $statement->fetch();

        return $individu === false ? null : $individu;
    }

    public function assertions(int $id): array
    {
        $statement = $this->db->prepare(
            'SELECT a.*, e.type AS evenement_type, e.gedcom_tag, e.date_texte, e.lieu_texte, d.titre AS document_titre
             FROM assertions a
             INNER JOIN evenements e ON e.id = a.evenement_id
             INNER JOIN documents d ON d.id = a.document_id
             WHERE a.individu_id = :id
             ORDER BY e.date_min NULLS LAST, e.id, a.id'
        );
        $statement->execute(['id' => $id]);

        return $statement->fetchAll();
    }
}
