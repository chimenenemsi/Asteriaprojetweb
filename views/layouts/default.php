<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Monta', ENT_QUOTES, 'UTF-8') ?></title>
    <style>
        body {
            margin: 0;
            font-family: "Segoe UI", Arial, sans-serif;
            background: #f6f8fb;
            color: #1d2433;
        }

        .wrap {
            max-width: 900px;
            margin: 0 auto;
            padding: 80px 24px;
        }

        .panel {
            background: #ffffff;
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 20px 45px rgba(16, 24, 40, 0.08);
        }

        a.button {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 22px;
            border-radius: 999px;
            background: #1f7a5c;
            color: #ffffff;
            text-decoration: none;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="panel">
            <?= $content ?>
        </div>
    </div>
</body>
</html>
