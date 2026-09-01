# Architecture backend PHP natif

```text
Geneabook/
├── assets/
│   └── scans/              # Scans et medias documentaires conserves localement.
├── config/
│   └── database.php        # Configuration PostgreSQL via variables d'environnement.
├── database/
│   └── schema.sql          # Schema relationnel PostgreSQL.
├── public/
│   ├── index.php           # Point d'entree unique et front controller.
│   └── assets/             # Assets publics servis par le serveur web.
├── src/
│   ├── autoload.php        # Autoload maison du namespace App.
│   ├── Controllers/        # Controleurs MVC simplifies.
│   ├── Core/               # Routeur, base controller, connexion PDO.
│   └── Models/             # Acces metier et persistence PostgreSQL.
└── views/                  # Templates HTML classiques.
```

Le serveur web doit pointer vers `public/`. Les scans originaux restent hors racine publique dans `assets/scans/`; ils pourront etre servis plus tard via un controleur verifiant les droits et les metadonnees du document.
