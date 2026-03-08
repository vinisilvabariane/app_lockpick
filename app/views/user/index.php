<?php
$basePath = isset($_SERVER['APP_BASE_PATH']) ? (string)$_SERVER['APP_BASE_PATH'] : '';
$userScriptPath = __DIR__ . '/../../../public/js/user/script.js';
$userScriptVersion = file_exists($userScriptPath) ? filemtime($userScriptPath) : time();
$globalStylePath = __DIR__ . '/../../../public/css/global/style.css';
$globalStyleVersion = file_exists($globalStylePath) ? filemtime($globalStylePath) : time();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lockpick | Cadastrar Usuarios</title>
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

<body class="user-page">
<?php include_once __DIR__ . '/../../../includes/navbar.php'; ?>

<main id="main-content">
    <header class="main-header fade-in-up">
        <h1 class="system-title">Cadastro de usuarios</h1>
        <p class="system-subtitle">Digite o nome do usuario e o sistema preenche valores padrao, exibindo apenas os campos obrigatorios.</p>
    </header>

    <section class="mt-4">
        <div class="card shadow-sm register-config-card">
            <div class="card-body">
                <h2 class="h5 mb-3">Dados base do usuario</h2>
                <div class="row g-3 mb-4">
                    <div class="col-md-8 col-lg-6">
                        <label for="master-user-name" class="form-label">Nome do usuario</label>
                        <input type="text"
                               class="form-control"
                               id="master-user-name"
                               placeholder="Ex.: Joao da Silva"
                               autocomplete="off">
                        <small class="text-muted d-block mt-2">Esse nome sera usado para sugerir login, nome completo e outros campos comuns.</small>
                    </div>
                    <div class="col-md-4 col-lg-3 align-self-end">
                        <button type="button" class="btn btn-outline-secondary w-100" id="btn-reapply-defaults">
                            Reaplicar padroes
                        </button>
                    </div>
                </div>

                <h2 class="h5 mb-3">Bancos cadastrados no SQLite</h2>
                <p class="text-muted mb-3">Marque os bancos que deseja manipular e gere os inputs automaticamente.</p>
                <div id="database-list" class="row g-2 mb-3"></div>
                <div class="d-flex gap-2 mb-4">
                    <button type="button" class="btn btn-outline-primary" id="btn-refresh-databases">
                        Atualizar lista
                    </button>
                    <button type="button" class="btn btn-primary" id="btn-load-selected">
                        Montar inputs dos selecionados
                    </button>
                </div>
                <div id="required-summary" class="alert alert-info d-none"></div>
                <div id="user-dynamic-forms" class="d-flex flex-column gap-3"></div>
            </div>
        </div>
    </section>
</main>

<?php include_once __DIR__ . '/../../../includes/footer.php'; ?>
<?php include_once __DIR__ . '/../../../includes/dependencies.php'; ?>
<script src="<?= $basePath ?>/public/js/user/script.js?v=<?= $userScriptVersion ?>"></script>
</body>
</html>
