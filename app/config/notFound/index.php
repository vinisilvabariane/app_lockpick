<?php
$basePath = isset($_SERVER['APP_BASE_PATH']) ? (string)$_SERVER['APP_BASE_PATH'] : '';
$globalStylePath = __DIR__ . '/../../../public/css/global/style.css';
$globalStyleVersion = file_exists($globalStylePath) ? filemtime($globalStylePath) : time();
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lockpick | Pagina nao encontrada</title>
    <link rel="icon" type="image/png" href="<?= $basePath ?>/public/img/com-fundo-maior.png" sizes="512x512">
    <link rel="apple-touch-icon" href="<?= $basePath ?>/public/img/com-fundo-maior.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@300;400;500;700&display=swap">
    <link rel="stylesheet" href="<?= $basePath ?>/public/css/global/style.css?v=<?= $globalStyleVersion ?>">
    <style>
        body.notfound-page {
            min-height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: radial-gradient(circle at 20% 20%, rgba(185, 3, 15, 0.08), rgba(255, 255, 255, 0) 35%),
                radial-gradient(circle at 80% 80%, rgba(22, 25, 23, 0.12), rgba(255, 255, 255, 0) 35%),
                #fff;
            padding: 24px;
        }

        .notfound-standalone {
            width: min(760px, 100%);
            text-align: center;
        }

        .notfound-standalone .system-title {
            font-size: clamp(4rem, 18vw, 8rem);
            line-height: 1;
            margin-bottom: 12px;
            color: var(--color3);
        }
    </style>
</head>

<body class="notfound-page">
    <main class="notfound-standalone">
        <div class="card shadow-sm register-config-card">
            <div class="card-body p-4 p-md-5">
                <h1 class="system-title">404</h1>
                <p class="h4 mb-2">Pagina nao encontrada</p>
                <p class="text-muted mb-4">O endereco acessado nao existe ou foi movido.</p>
                <div class="d-flex flex-wrap justify-content-center gap-2">
                    <a href="<?= $basePath ?>/home" class="btn btn-primary">
                        <i class="bi bi-house-door me-2"></i>Voltar para Home
                    </a>
                </div>
            </div>
        </div>
    </main>

    <?php include_once __DIR__ . '/../../../includes/dependencies.php'; ?>
</body>

</html>