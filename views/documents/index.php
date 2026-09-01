<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title><?= htmlspecialchars($title ?? 'Documents', ENT_QUOTES, 'UTF-8') ?></title>
</head>
<body>
    <h1>Documents</h1>
    <pre><?= htmlspecialchars(json_encode($documents ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8') ?></pre>
</body>
</html>
