<?php
$basePath = isset($_SERVER['APP_BASE_PATH']) ? (string)$_SERVER['APP_BASE_PATH'] : '';
$configurateScriptPath = __DIR__ . '/../../../public/js/configurate/script.js';
$configurateScriptVersion = file_exists($configurateScriptPath) ? filemtime($configurateScriptPath) : time();
$globalStylePath = __DIR__ . '/../../../public/css/global/style.css';
$globalStyleVersion = file_exists($globalStylePath) ? filemtime($globalStylePath) : time();
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lockpick | Configuracoes</title>
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

<body class="configurate-page">
    <?php include_once __DIR__ . '/../../../includes/navbar.php'; ?>

    <main id="main-content">
        <header class="main-header fade-in-up">
            <h1 class="system-title">Configuracao de Tabelas</h1>
            <p class="system-subtitle">Defina obrigatoriedade, automacao e valores padrao para cada campo das tabelas cadastradas.</p>
        </header>

        <section class="mt-4">
            <div class="row g-4">
                <div class="col-lg-12">
                    <div class="card shadow-sm register-config-card h-100">
                        <div class="card-body p-4">
                            <h2 class="h5 mb-3">Selecionar tabela cadastrada</h2>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label for="cfg-registry-select" class="form-label">Tabela cadastrada</label>
                                    <select id="cfg-registry-select" class="form-select">
                                        <option value="">Selecione uma tabela...</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label d-block">&nbsp;</label>
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-outline-primary" id="btn-refresh-registries">
                                            Atualizar tabelas
                                        </button>
                                        <button type="button" class="btn btn-primary" id="btn-load-configuration">
                                            Carregar configuracao
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div id="cfg-selected-summary" class="alert alert-info d-none mb-3"></div>

                            <form id="cfg-form" class="row g-3 d-none">
                                <div class="col-md-6">
                                    <label for="cfg-email-domain" class="form-label">Dominio padrao de e-mail</label>
                                    <input type="text" id="cfg-email-domain" class="form-control" placeholder="exemplo.com.br">
                                </div>
                                <div class="col-md-6">
                                    <label for="cfg-email-prefix-source" class="form-label">Coluna para prefixo do e-mail</label>
                                    <select id="cfg-email-prefix-source" class="form-select">
                                        <option value="">Selecione...</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <div class="table-responsive">
                                        <table class="table table-sm align-middle cfg-columns-table">
                                            <thead>
                                        <tr>
                                            <th>Campo</th>
                                            <th>Tipo</th>
                                            <th>Inserir valor manual</th>
                                            <th>Inserir valor Default</th>
                                            <th>Inserir valor automatico</th>
                                            <th>Inserir como Nulo</th>
                                            <th>Default</th>
                                            <th>Origem da Informacao</th>
                                        </tr>
                                            </thead>
                                            <tbody id="cfg-columns-tbody"></tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-12 d-flex gap-2">
                                    <button type="button" class="btn btn-primary" id="btn-save-configuration">
                                        <i class="bi bi-save me-2"></i>Salvar configuracao da tabela
                                    </button>
                                    <button type="button" class="btn btn-outline-primary" id="btn-reset-configuration">
                                        Restaurar carregado
                                    </button>
                                </div>
                            </form>

                            <div id="cfg-empty-state" class="alert alert-warning mb-0">
                                Selecione uma tabela cadastrada para configurar regras dos campos.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include_once __DIR__ . '/../../../includes/footer.php'; ?>
    <?php include_once __DIR__ . '/../../../includes/dependencies.php'; ?>
    <script src="<?= $basePath ?>/public/js/configurate/script.js?v=<?= $configurateScriptVersion ?>"></script>
</body>

</html>
