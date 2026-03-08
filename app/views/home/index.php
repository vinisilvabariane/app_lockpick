<?php
$basePath = isset($_SERVER['APP_BASE_PATH']) ? (string)$_SERVER['APP_BASE_PATH'] : '';
$homeScriptPath = __DIR__ . '/../../../public/js/home/script.js';
$homeScriptVersion = file_exists($homeScriptPath) ? filemtime($homeScriptPath) : time();
$globalStylePath = __DIR__ . '/../../../public/css/global/style.css';
$globalStyleVersion = file_exists($globalStylePath) ? filemtime($globalStylePath) : time();
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lockpick</title>
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

<body class="home-page">
<?php include_once __DIR__ . '/../../../includes/navbar.php'; ?>

<button class="home-right-bubble" id="toggleAside" type="button" title="Informacoes do sistema">
    <i class="bi bi-info-circle"></i>
</button>

<main id="main-content">
    <header class="main-header fade-in-up">
        <div class="home-header-brand">
            <img
                    class="home-hero-logo"
                    src="<?= $basePath ?>/public/img/sem-fundo-maior.png"
                    alt="Logo Lockpick"
            >
            <h1 class="system-title">
                Lockpick
            </h1>
        </div>
        <p class="system-subtitle">
            Plataforma unificada para controle de usuarios em multiplos bancos de dados. Gerencie
            acessos e registros de forma centralizada e segura.
        </p>
    </header>
    <section class="fade-in-up">
        <div class="card stat-card overview-carousel mb-4 text-center">
            <div class="card-body p-0">
                <div id="lockpickOverview" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-indicators mb-0">
                        <button type="button" data-bs-target="#lockpickOverview" data-bs-slide-to="0"
                                class="active"></button>
                        <button type="button" data-bs-target="#lockpickOverview" data-bs-slide-to="1"></button>
                        <button type="button" data-bs-target="#lockpickOverview" data-bs-slide-to="2"></button>
                    </div>
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <div class="overview-slide">
                                <h3>O que o sistema faz?</h3>
                                <p>Uma plataforma para gerenciar o ciclo de vida de usuarios com padrao, seguranca e
                                    rastreabilidade.</p>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <div class="overview-slide">
                                <h3>Para quem foi feito</h3>
                                <p>Times de TI, seguranca e governanca que precisam centralizar cadastro, revisao e
                                    revogacao de acesso.</p>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <div class="overview-slide">
                                <h3>Como funciona</h3>
                                <p>Solicite, valide e registre cada etapa em um unico fluxo, reduzindo falhas manuais e
                                    retrabalho.</p>
                            </div>
                        </div>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#lockpickOverview"
                            data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#lockpickOverview"
                            data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>
                </div>
            </div>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card stat-card h-100">
                    <div class="card-body p-4">
                        <div class="stat-card-icon bg-primary-subtle text-primary">
                            <i class="bi bi-diagram-3"></i>
                        </div>
                        <h5 class="mb-2">Fluxo Unificado</h5>
                        <p class="text-muted mb-0">Todos os passos de gestao de usuarios em um unico lugar, com processo
                            claro para toda a equipe.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card h-100">
                    <div class="card-body p-4">
                        <div class="stat-card-icon bg-success-subtle text-success">
                            <i class="bi bi-journal-check"></i>
                        </div>
                        <h5 class="mb-2">Padrao Operacional</h5>
                        <p class="text-muted mb-0">As mesmas regras e checkpoints em cada cadastro, facilitando
                            auditoria e conformidade interna.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card h-100">
                    <div class="card-body p-4">
                        <div class="stat-card-icon bg-warning-subtle text-warning">
                            <i class="bi bi-lightning-charge"></i>
                        </div>
                        <h5 class="mb-2">Execucao Simples</h5>
                        <p class="text-muted mb-0">Interface objetiva para iniciar tarefas, acompanhar status e manter
                            historico organizado.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="card stat-card mt-4">
            <div class="card-body p-4 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <h4 class="mb-1">Comece pelo cadastro de usuario</h4>
                    <p class="text-muted mb-0">Inicie um novo registro e siga o fluxo padrao do Lockpick.</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="<?= $basePath ?>/create" class="btn btn-primary">
                        <i class="bi bi-person-plus me-2"></i>Cadastrar Usuario
                    </a>
                    <button type="button" class="btn btn-outline-primary" id="openAsideInfo">
                        <i class="bi bi-info-circle me-2"></i>Sobre o Sistema
                    </button>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include_once __DIR__ . '/../../../includes/footer.php'; ?>
<?php include_once __DIR__ . '/../../../includes/infoAside.php'; ?>
<?php include_once __DIR__ . '/../../../includes/dependencies.php'; ?>

<script src="<?= $basePath ?>/public/js/home/script.js?v=<?= $homeScriptVersion ?>"></script>
</body>

</html>




