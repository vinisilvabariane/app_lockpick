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
    <title>Lockpick | Reset de Configuracoes</title>
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

<body class="reset-page">
<?php include_once __DIR__ . '/../../../includes/navbar.php'; ?>

<main id="main-content">
    <header class="main-header fade-in-up">
        <h1 class="system-title">Reset</h1>
        <p class="system-subtitle">Centralize a limpeza de configuracoes para reiniciar fluxos com seguranca.</p>
    </header>

    <section class="mt-4">
        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card shadow-sm register-config-card h-100">
                    <div class="card-body p-4">
                        <h2 class="h5 mb-3">Solicitar reset de cadastro</h2>
                        <form class="row g-3">
                            <div class="col-md-6">
                                <label for="reset-database" class="form-label">Banco alvo</label>
                                <input type="text" class="form-control" id="reset-database" placeholder="Ex.: ERP_PRD">
                            </div>
                            <div class="col-md-6">
                                <label for="reset-table" class="form-label">Tabela alvo</label>
                                <input type="text" class="form-control" id="reset-table" placeholder="Ex.: usuarios">
                            </div>
                            <div class="col-12">
                                <label for="reset-reason" class="form-label">Motivo</label>
                                <textarea class="form-control" id="reset-reason" rows="4" placeholder="Descreva o motivo do reset"></textarea>
                            </div>
                            <div class="col-12">
                                <label for="reset-confirm" class="form-label">Confirmacao</label>
                                <input type="text" class="form-control" id="reset-confirm" placeholder="Digite RESET para confirmar">
                                <div class="form-text">Apenas design de interface nesta etapa. Integracao de backend pode ser conectada depois.</div>
                            </div>
                            <div class="col-12 d-flex gap-2">
                                <button type="button" class="btn btn-primary">
                                    <i class="bi bi-arrow-clockwise me-2"></i>Solicitar reset
                                </button>
                                <button type="button" class="btn btn-outline-primary">
                                    Limpar campos
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card shadow-sm register-config-card h-100">
                    <div class="card-body p-4">
                        <h2 class="h5 mb-3">Checklist rapido</h2>
                        <ul class="mb-0">
                            <li>Valide se o banco/tabela estao corretos.</li>
                            <li>Informe motivo auditavel para o reset.</li>
                            <li>Confirme impacto com o time responsavel.</li>
                            <li>Execute o processo fora de horario critico.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include_once __DIR__ . '/../../../includes/footer.php'; ?>
<?php include_once __DIR__ . '/../../../includes/dependencies.php'; ?>
</body>

</html>
