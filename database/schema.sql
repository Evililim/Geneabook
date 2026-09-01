-- Geneabook - Phase 1
-- Schema PostgreSQL oriente source, assertions multiples et compatibilite GEDCOM 7.

BEGIN;

CREATE EXTENSION IF NOT EXISTS pg_trgm;

CREATE TYPE document_type AS ENUM (
    'acte_naissance',
    'acte_mariage',
    'acte_deces',
    'acte_bapteme',
    'acte_sepulture',
    'recensement',
    'contrat',
    'jugement',
    'militaire',
    'notarial',
    'photo',
    'correspondance',
    'autre'
);

CREATE TYPE precision_lieu AS ENUM (
    'numero',
    'rue',
    'quartier',
    'commune',
    'departement',
    'region',
    'pays',
    'inconnue'
);

CREATE TYPE qualite_date AS ENUM (
    'exacte',
    'intervalle',
    'environ',
    'avant',
    'apres',
    'incomplete',
    'inconnue'
);

CREATE TYPE qualite_preuve AS ENUM (
    'directe',
    'indirecte',
    'negative',
    'deduite',
    'incertaine'
);

CREATE TABLE individus (
    id BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    souche_base BOOLEAN NOT NULL DEFAULT FALSE,
    notes_globales TEXT,
    gedcom_xref VARCHAR(64) UNIQUE,
    created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE TABLE documents (
    id BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    type document_type NOT NULL,
    titre TEXT,
    chemin_fichier TEXT NOT NULL,
    media_mime_type VARCHAR(128),
    media_hash_sha256 CHAR(64),
    archive_nom TEXT,
    archive_cote TEXT,
    archive_url TEXT,
    date_document_texte TEXT,
    date_document_min DATE,
    date_document_max DATE,
    lieu_conservation TEXT,
    transcription TEXT,
    notes TEXT,
    gedcom_source_ref VARCHAR(64),
    gedcom_media_ref VARCHAR(64),
    date_ajout TIMESTAMPTZ NOT NULL DEFAULT now(),
    created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT now(),
    CONSTRAINT documents_date_range_chk CHECK (
        date_document_min IS NULL
        OR date_document_max IS NULL
        OR date_document_min <= date_document_max
    ),
    CONSTRAINT documents_media_hash_sha256_chk CHECK (
        media_hash_sha256 IS NULL
        OR media_hash_sha256 ~ '^[0-9a-f]{64}$'
    )
);

CREATE TABLE evenements (
    id BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    type VARCHAR(32) NOT NULL,
    gedcom_tag VARCHAR(16) NOT NULL,
    gedcom_type TEXT,
    date_texte TEXT,
    date_min DATE,
    date_max DATE,
    qualite_date qualite_date NOT NULL DEFAULT 'inconnue',
    lieu_texte TEXT,
    precision_lieu precision_lieu NOT NULL DEFAULT 'inconnue',
    lat NUMERIC(9, 6),
    lng NUMERIC(9, 6),
    certitude_lieu SMALLINT NOT NULL DEFAULT 0,
    statut_verifie BOOLEAN NOT NULL DEFAULT FALSE,
    notes TEXT,
    created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT now(),
    CONSTRAINT evenements_date_range_chk CHECK (
        date_min IS NULL
        OR date_max IS NULL
        OR date_min <= date_max
    ),
    CONSTRAINT evenements_lat_chk CHECK (lat IS NULL OR lat BETWEEN -90 AND 90),
    CONSTRAINT evenements_lng_chk CHECK (lng IS NULL OR lng BETWEEN -180 AND 180),
    CONSTRAINT evenements_certitude_lieu_chk CHECK (certitude_lieu BETWEEN 0 AND 100),
    CONSTRAINT evenements_gedcom_tag_chk CHECK (gedcom_tag ~ '^[A-Z0-9_]{1,16}$')
);

CREATE TABLE assertions (
    id BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    document_id BIGINT NOT NULL REFERENCES documents(id) ON DELETE RESTRICT,
    evenement_id BIGINT NOT NULL REFERENCES evenements(id) ON DELETE CASCADE,
    individu_id BIGINT NOT NULL REFERENCES individus(id) ON DELETE CASCADE,
    role VARCHAR(64) NOT NULL,
    gedcom_role VARCHAR(64),
    age_mentionne TEXT,
    age_min SMALLINT,
    age_max SMALLINT,
    qualite_preuve qualite_preuve NOT NULL DEFAULT 'directe',
    confiance SMALLINT NOT NULL DEFAULT 50,
    inference_vie_calculee BOOLEAN NOT NULL DEFAULT FALSE,
    citation_originale TEXT,
    notes TEXT,
    created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT now(),
    CONSTRAINT assertions_age_range_chk CHECK (
        age_min IS NULL
        OR age_max IS NULL
        OR age_min <= age_max
    ),
    CONSTRAINT assertions_age_min_chk CHECK (age_min IS NULL OR age_min BETWEEN 0 AND 130),
    CONSTRAINT assertions_age_max_chk CHECK (age_max IS NULL OR age_max BETWEEN 0 AND 130),
    CONSTRAINT assertions_confiance_chk CHECK (confiance BETWEEN 0 AND 100),
    CONSTRAINT assertions_unique_role_per_event_doc_person UNIQUE (
        document_id,
        evenement_id,
        individu_id,
        role
    )
);

CREATE TABLE variantes_noms (
    id BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    individu_id BIGINT NOT NULL REFERENCES individus(id) ON DELETE CASCADE,
    document_id BIGINT REFERENCES documents(id) ON DELETE RESTRICT,
    nom TEXT NOT NULL,
    prenom TEXT,
    particule TEXT,
    suffixe TEXT,
    surnom TEXT,
    gedcom_name_type VARCHAR(32),
    est_primant BOOLEAN NOT NULL DEFAULT FALSE,
    notes TEXT,
    created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

-- Une seule variante de nom primante par individu.
CREATE UNIQUE INDEX variantes_noms_unq_primant
    ON variantes_noms (individu_id)
    WHERE est_primant;

-- Surcharge manuelle optionnelle de la valeur primante pour un fait GEDCOM donne.
CREATE TABLE faits_primants (
    id BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    individu_id BIGINT NOT NULL REFERENCES individus(id) ON DELETE CASCADE,
    gedcom_tag VARCHAR(16) NOT NULL,
    evenement_id BIGINT NOT NULL REFERENCES evenements(id) ON DELETE RESTRICT,
    assertion_id BIGINT REFERENCES assertions(id) ON DELETE SET NULL,
    force_manuellement BOOLEAN NOT NULL DEFAULT TRUE,
    raison TEXT,
    created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT now(),
    CONSTRAINT faits_primants_gedcom_tag_chk CHECK (gedcom_tag ~ '^[A-Z0-9_]{1,16}$'),
    CONSTRAINT faits_primants_unique_fact UNIQUE (individu_id, gedcom_tag)
);

-- Gestion de la souche d'affichage volatile pour la navigation D3.js.
CREATE TABLE preferences_affichage (
    id BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    cle VARCHAR(128) NOT NULL UNIQUE,
    individu_id BIGINT REFERENCES individus(id) ON DELETE SET NULL,
    valeur_json JSONB NOT NULL DEFAULT '{}'::jsonb,
    updated_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE INDEX idx_documents_type ON documents (type);
CREATE INDEX idx_documents_date_range ON documents (date_document_min, date_document_max);
CREATE INDEX idx_documents_archive ON documents (archive_nom, archive_cote);
CREATE INDEX idx_documents_media_hash ON documents (media_hash_sha256);

CREATE INDEX idx_evenements_type ON evenements (type);
CREATE INDEX idx_evenements_gedcom_tag ON evenements (gedcom_tag);
CREATE INDEX idx_evenements_date_range ON evenements (date_min, date_max);
CREATE INDEX idx_evenements_lieu_trgm ON evenements USING GIN (lieu_texte gin_trgm_ops);
CREATE INDEX idx_evenements_geo ON evenements (lat, lng);

CREATE INDEX idx_assertions_document ON assertions (document_id);
CREATE INDEX idx_assertions_evenement ON assertions (evenement_id);
CREATE INDEX idx_assertions_individu ON assertions (individu_id);
CREATE INDEX idx_assertions_role ON assertions (role);
CREATE INDEX idx_assertions_inference_vie ON assertions (inference_vie_calculee);

CREATE INDEX idx_variantes_noms_individu ON variantes_noms (individu_id);
CREATE INDEX idx_variantes_noms_document ON variantes_noms (document_id);
CREATE INDEX idx_variantes_noms_nom_trgm ON variantes_noms USING GIN (nom gin_trgm_ops);
CREATE INDEX idx_variantes_noms_prenom_trgm ON variantes_noms USING GIN (prenom gin_trgm_ops);

CREATE INDEX idx_faits_primants_individu ON faits_primants (individu_id);
CREATE INDEX idx_faits_primants_evenement ON faits_primants (evenement_id);
CREATE INDEX idx_preferences_affichage_individu ON preferences_affichage (individu_id);

COMMIT;
