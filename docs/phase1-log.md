# Phase 1 - Schéma PostgreSQL

## 2026-09-02

- Création d'un schéma initial orienté source : `documents` reste le point d'entrée documentaire, et les faits exploitables passent par `evenements` + `assertions`.
- Modélisation des assertions multiples : plusieurs événements/assertions peuvent coexister pour un même fait, avec conservation de la source, du rôle, de l'âge mentionné, de la qualité de preuve et du niveau de confiance.
- Conservation des dates historiques incertaines : texte original dans `date_texte`, plage normalisée dans `date_min`/`date_max`, qualification dans `qualite_date`.
- Ajout d'une surcharge de valeur primante via `faits_primants`, pour permettre une sélection manuelle sans supprimer les hypothèses concurrentes.
- Ajout des variantes orthographiques avec une contrainte garantissant une seule variante primante par individu.
- Préparation GEDCOM 7 : champs `gedcom_tag`, `gedcom_type`, `gedcom_role`, références source/média et types de noms.
- Préparation D3.js : table `preferences_affichage` pour une souche d'affichage volatile distincte de la `souche_base`.
