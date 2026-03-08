<?php
$basePath = isset($_SERVER['APP_BASE_PATH']) ? (string)$_SERVER['APP_BASE_PATH'] : '';
$registerScriptPath = __DIR__ . '/../../../public/js/register/script.js';
$registerScriptVersion = file_exists($registerScriptPath) ? filemtime($registerScriptPath) : time();
$globalStylePath = __DIR__ . '/../../../public/css/global/style.css';
$globalStyleVersion = file_exists($globalStylePath) ? filemtime($globalStylePath) : time();
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lockpick | Cadastrar Banco de Dados</title>
    <link rel="icon" type="image/png" href="<?= $basePath ?>/public/img/com-fundo-maior.png" sizes="512x512">
    <link rel="apple-touch-icon" href="<?= $basePath ?>/public/img/com-fundo-maior.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@300;400;500;700&display=swap">
    <link rel="stylesheet" href="<?= $basePath ?>/public/css/global/style.css?v=<?= $globalStyleVersion ?>">
</head>

<body class="register-page">
    <?php include_once __DIR__ . '/../../../includes/navbar.php'; ?>

    <main id="main-content">
        <header class="main-header fade-in-up">
            <h1 class="system-title">Configuracao de banco de dados</h1>
            <p class="system-subtitle">Cadastre o schema uma vez e reaproveite no fluxo dinamico de usuarios.</p>
        </header>
        <section class="mt-4">
            <div class="row g-4">
                <div class="col-lg-12">
                    <div class="card shadow-sm register-config-card h-100">
                        <div class="card-body p-4">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                                <h2 class="h5 mb-0">Salvar configuracao no SQLite</h2>
                            </div>
                            <form id="db-config-form" class="row g-3">
                                <div class="col-md-6">
                                    <label for="database-name" class="form-label">Nome do banco</label>
                                    <input type="text" class="form-control" id="database-name" name="database_name"
                                        placeholder="Ex.: ERP_PRD" required>
                                </div>
                                <div class="col-12">
                                    <label for="sql-definition" class="form-label">DDL SQL (CREATE TABLE)</label>
                                    <textarea id="sql-definition" name="sql_definition" class="form-control" rows="12"
                                        placeholder="Cole aqui o CREATE TABLE completo" required></textarea>
                                    <div class="form-text">Cole o CREATE TABLE do banco de origem. O sistema vai extrair colunas e
                                        indices automaticamente.
                                    </div>
                                </div>
                                <div class="col-12 d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-save me-2"></i>Salvar configuracao
                                    </button>
                                </div>
                            </form>
                            <pre id="db-config-feedback" class="mt-3 p-3 bg-light border rounded small mb-0"></pre>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <?php include_once __DIR__ . '/../../../includes/footer.php'; ?>
    <?php include_once __DIR__ . '/../../../includes/dependencies.php'; ?>
    <script src="<?= $basePath ?>/public/js/register/script.js?v=<?= $registerScriptVersion ?>"></script>
</body>

</html>