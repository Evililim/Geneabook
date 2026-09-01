<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($title ?? 'Saisie orientee source', ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="stylesheet" href="/assets/source-entry.css">
</head>
<body>
    <main class="source-workspace">
        <section class="viewer-panel" aria-label="Visionneuse documentaire">
            <header class="panel-header">
                <div>
                    <p class="eyebrow">Document</p>
                    <h1>Visionneuse</h1>
                </div>
                <button class="icon-button" type="button" data-reset-viewer title="Recentrer l'image">&olarr;</button>
            </header>

            <div class="document-viewer" data-document-viewer>
                <img
                    src="<?= htmlspecialchars($scanPath ?? '/assets/sample-acte.svg', ENT_QUOTES, 'UTF-8') ?>"
                    alt="Scan de l'acte"
                    class="document-image"
                    data-document-image
                    draggable="false"
                >
            </div>
        </section>

        <section class="form-panel" aria-label="Formulaire d'extraction">
            <header class="panel-header">
                <div>
                    <p class="eyebrow">Extraction</p>
                    <h2>Acte et assertions</h2>
                </div>
            </header>

            <form class="source-form" method="post" action="/index.php?action=documents.storeExtraction" enctype="multipart/form-data">
                <fieldset>
                    <legend>Document</legend>
                    <div class="field-grid">
                        <label>
                            ID document
                            <input type="number" name="document[id]" min="1" inputmode="numeric">
                        </label>
                        <label>
                            Nouveau scan
                            <input type="file" name="document[scan]" accept="image/*,.pdf">
                        </label>
                        <label class="wide">
                            Titre ou cote
                            <input type="text" name="document[titre]" autocomplete="off">
                        </label>
                    </div>
                </fieldset>

                <fieldset>
                    <legend>Evenement principal</legend>
                    <div class="field-grid">
                        <label>
                            Type
                            <select name="evenement[type]" required>
                                <option value="naissance">Naissance</option>
                                <option value="mariage">Mariage</option>
                                <option value="deces">Deces</option>
                                <option value="bapteme">Bapteme</option>
                                <option value="sepulture">Sepulture</option>
                                <option value="recensement">Recensement</option>
                                <option value="autre">Autre</option>
                            </select>
                        </label>
                        <label>
                            Tag GEDCOM
                            <input type="text" name="evenement[gedcom_tag]" value="BIRT" maxlength="16" required>
                        </label>
                        <label>
                            Date lue
                            <input type="text" name="evenement[date_texte]" placeholder="ex. vers 1821">
                        </label>
                        <label>
                            Precision date
                            <select name="evenement[qualite_date]">
                                <option value="exacte">Exacte</option>
                                <option value="intervalle">Intervalle</option>
                                <option value="environ">Environ</option>
                                <option value="avant">Avant</option>
                                <option value="apres">Apres</option>
                                <option value="incomplete">Incomplete</option>
                                <option value="inconnue">Inconnue</option>
                            </select>
                        </label>
                        <label class="wide">
                            Lieu
                            <input type="text" name="evenement[lieu_texte]" autocomplete="off">
                        </label>
                        <label>
                            Precision lieu
                            <select name="evenement[precision_lieu]">
                                <option value="numero">Numero</option>
                                <option value="rue">Rue</option>
                                <option value="quartier">Quartier</option>
                                <option value="commune" selected>Commune</option>
                                <option value="departement">Departement</option>
                                <option value="region">Region</option>
                                <option value="pays">Pays</option>
                                <option value="inconnue">Inconnue</option>
                            </select>
                        </label>
                    </div>
                </fieldset>

                <fieldset>
                    <legend>IA</legend>
                    <label>
                        Transcription brute
                        <textarea name="ai[raw_text]" rows="5"></textarea>
                    </label>
                    <button type="button" class="secondary-button" data-ai-parser>Parser avec l'IA</button>
                </fieldset>

                <fieldset>
                    <legend>Acteurs de l'acte</legend>
                    <div class="assertions-toolbar">
                        <button type="button" class="secondary-button" data-add-assertion>Ajouter une personne citee</button>
                    </div>
                    <div class="assertions-list" data-assertions-list></div>
                </fieldset>

                <div class="form-actions">
                    <button type="submit" class="primary-button">Enregistrer l'extraction</button>
                </div>
            </form>
        </section>
    </main>

    <template id="assertion-row-template">
        <article class="assertion-row" data-assertion-row>
            <div class="assertion-row__header">
                <strong data-assertion-title>Personne citee</strong>
                <button type="button" class="icon-button" data-remove-assertion title="Retirer cette personne">&times;</button>
            </div>
            <div class="field-grid">
                <label>
                    Nom
                    <input type="text" data-name="nom" autocomplete="family-name">
                </label>
                <label>
                    Prenom
                    <input type="text" data-name="prenom" autocomplete="given-name">
                </label>
                <label class="wide">
                    Variante orthographique
                    <input type="text" data-name="variante_nom" autocomplete="off">
                </label>
                <label>
                    Role
                    <select data-name="role">
                        <option value="Sujet">Sujet</option>
                        <option value="Pere">Pere</option>
                        <option value="Mere">Mere</option>
                        <option value="Conjoint">Conjoint</option>
                        <option value="Temoin">Temoin</option>
                        <option value="Declarant">Declarant</option>
                        <option value="Officiant">Officiant</option>
                        <option value="Autre">Autre</option>
                    </select>
                </label>
                <label>
                    Role GEDCOM
                    <input type="text" data-name="gedcom_role" placeholder="CHIL, HUSB, WIFE...">
                </label>
                <label>
                    Age mentionne
                    <input type="text" data-name="age_mentionne" placeholder="ex. 32 ans">
                </label>
                <label class="checkbox-field">
                    <input type="checkbox" data-name="inference_vie_calculee" value="1">
                    Present / En vie
                </label>
            </div>
        </article>
    </template>

    <script src="/assets/source-entry.js" defer></script>
</body>
</html>
