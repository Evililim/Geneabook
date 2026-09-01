# Phase 2 - Architecture backend PHP natif

## 2026-09-02

- Creation d'une arborescence MVC simplifiee : `public/`, `src/Core/`, `src/Controllers/`, `src/Models/`, `config/`, `views/`, `assets/scans/`.
- Ajout d'un autoloader PSR-4 minimal maison pour le namespace `App`.
- Ajout d'une configuration PostgreSQL par variables d'environnement avec valeurs locales par defaut.
- Ajout d'un singleton `App\Core\Database` base sur PDO, configure en exceptions strictes, fetch associatif, prepares natifs et encodage client PostgreSQL UTF8.
- Ajout d'un routeur minimaliste via `public/index.php`, base sur `?action=...`, capable de produire HTML ou JSON.
- Ajout des premiers modeles `Document`, `Individu` et `Assertion`.
- Ajout d'une methode transactionnelle `Assertion::createEventWithAssertions()` pour creer un evenement et plusieurs assertions en bloc atomique.
- Ajout de vues HTML tres minimales uniquement pour valider le flux MVC, sans developper l'interface utilisateur finale.
