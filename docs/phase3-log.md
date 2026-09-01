# Phase 3 - Interface de saisie orientee source

## 2026-09-02

- Ajout d'une route HTML `documents.extract` pour ouvrir l'interface de saisie source-first.
- Ajout d'une route provisoire `documents.storeExtraction` qui accuse reception de la soumission en JSON, sans persistance a ce stade.
- Creation d'une vue split screen : visionneuse documentaire a gauche, formulaire d'extraction a droite.
- Ajout d'une visionneuse Vanilla JS avec zoom centre sur le curseur, panning souris et recentrage.
- Ajout d'un formulaire structure pour `document[...]`, `evenement[...]`, `ai[...]` et `assertions[index][...]`, directement exploitable par PHP via `$_POST`.
- Ajout d'un bloc IA prepare avec textarea et mock `console.log`, sans appel externe pour l'instant.
- Ajout dynamique des personnes citees avec nom, prenom, variante orthographique, role, role GEDCOM, age mentionne et presence/en vie.
- Ajout d'un scan SVG factice public pour tester la visionneuse sans dependance externe.
