<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title><?= htmlspecialchars($title ?? 'Individu', ENT_QUOTES, 'UTF-8') ?></title>
</head>
<body>
    <h1><?= htmlspecialchars($title ?? 'Individu', ENT_QUOTES, 'UTF-8') ?></h1>
    <pre><?= htmlspecialchars(json_encode([
        'individu' => $individu ?? null,
        'assertions' => $assertions ?? [],
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8') ?></pre>
</body>
</html>
