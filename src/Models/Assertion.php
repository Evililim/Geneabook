<?php

declare(strict_types=1);

namespace App\Models;

use Throwable;

final class Assertion extends BaseModel
{
    /**
     * Cree un evenement et toutes ses assertions dans une transaction atomique.
     *
     * @param array<string, mixed> $eventData
     * @param array<int, array<string, mixed>> $assertionsData
     * @return array{evenement_id:int, assertion_ids:array<int, int>}
     */
    public function createEventWithAssertions(array $eventData, array $assertionsData): array
    {
        if ($assertionsData === []) {
            throw new \InvalidArgumentException('At least one assertion is required.');
        }

        $this->db->beginTransaction();

        try {
            $eventId = $this->insertEvent($eventData);
            $assertionIds = [];

            foreach ($assertionsData as $assertionData) {
                $assertionData['evenement_id'] = $eventId;
                $assertionIds[] = $this->insertAssertion($assertionData);
            }

            $this->db->commit();

            return [
                'evenement_id' => $eventId,
                'assertion_ids' => $assertionIds,
            ];
        } catch (Throwable $throwable) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $throwable;
        }
    }

    private function insertEvent(array $data): int
    {
        $sql = <<<'SQL'
            INSERT INTO evenements (
                type,
                gedcom_tag,
                gedcom_type,
                date_texte,
                date_min,
                date_max,
                qualite_date,
                lieu_texte,
                precision_lieu,
                lat,
                lng,
                certitude_lieu,
                statut_verifie,
                notes
            ) VALUES (
                :type,
                :gedcom_tag,
                :gedcom_type,
                :date_texte,
                :date_min,
                :date_max,
                :qualite_date,
                :lieu_texte,
                :precision_lieu,
                :lat,
                :lng,
                :certitude_lieu,
                :statut_verifie,
                :notes
            )
            RETURNING id
        SQL;

        $statement = $this->db->prepare($sql);
        $statement->execute([
            'type' => $data['type'],
            'gedcom_tag' => $data['gedcom_tag'],
            'gedcom_type' => $data['gedcom_type'] ?? null,
            'date_texte' => $data['date_texte'] ?? null,
            'date_min' => $data['date_min'] ?? null,
            'date_max' => $data['date_max'] ?? null,
            'qualite_date' => $data['qualite_date'] ?? 'inconnue',
            'lieu_texte' => $data['lieu_texte'] ?? null,
            'precision_lieu' => $data['precision_lieu'] ?? 'inconnue',
            'lat' => $data['lat'] ?? null,
            'lng' => $data['lng'] ?? null,
            'certitude_lieu' => $data['certitude_lieu'] ?? 0,
            'statut_verifie' => (bool) ($data['statut_verifie'] ?? false),
            'notes' => $data['notes'] ?? null,
        ]);

        return (int) $statement->fetchColumn();
    }

    private function insertAssertion(array $data): int
    {
        $sql = <<<'SQL'
            INSERT INTO assertions (
                document_id,
                evenement_id,
                individu_id,
                role,
                gedcom_role,
                age_mentionne,
                age_min,
                age_max,
                qualite_preuve,
                confiance,
                inference_vie_calculee,
                citation_originale,
                notes
            ) VALUES (
                :document_id,
                :evenement_id,
                :individu_id,
                :role,
                :gedcom_role,
                :age_mentionne,
                :age_min,
                :age_max,
                :qualite_preuve,
                :confiance,
                :inference_vie_calculee,
                :citation_originale,
                :notes
            )
            RETURNING id
        SQL;

        $statement = $this->db->prepare($sql);
        $statement->execute([
            'document_id' => $data['document_id'],
            'evenement_id' => $data['evenement_id'],
            'individu_id' => $data['individu_id'],
            'role' => $data['role'],
            'gedcom_role' => $data['gedcom_role'] ?? null,
            'age_mentionne' => $data['age_mentionne'] ?? null,
            'age_min' => $data['age_min'] ?? null,
            'age_max' => $data['age_max'] ?? null,
            'qualite_preuve' => $data['qualite_preuve'] ?? 'directe',
            'confiance' => $data['confiance'] ?? 50,
            'inference_vie_calculee' => (bool) ($data['inference_vie_calculee'] ?? false),
            'citation_originale' => $data['citation_originale'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        return (int) $statement->fetchColumn();
    }
}
