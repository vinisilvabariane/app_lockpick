<?php
$basePath = isset($_SERVER['APP_BASE_PATH']) ? (string)$_SERVER['APP_BASE_PATH'] : '';
$currentRoute = isset($_SERVER['APP_CURRENT_ROUTE']) ? (string)$_SERVER['APP_CURRENT_ROUTE'] : '/';
$homeUrl = ($basePath !== '' ? $basePath : '') . '/home';
$registerUrl = ($basePath !== '' ? $basePath : '') . '/register';
$userUrl = ($basePath !== '' ? $basePath : '') . '/user';
$configurateUrl = ($basePath !== '' ? $basePath : '') . '/configurate';
$resetUrl = ($basePath !== '' ? $basePath : '') . '/reset';
?>
<div class="nav-bubble-wrapper">
    <button class="navbar-brand-circle" type="button" aria-label="Navegacao">
        <i class="bi bi-chevron-up"></i>
    </button>
    <nav id="sidebar">
        <div class="nav flex-column">
            <a href="<?= $homeUrl ?>"
                class="sidebar-item <?= ($currentRoute === '/' || $currentRoute === '/home') ? 'active' : '' ?>"
                title="Home">
                <i class="bi bi-house-door"></i>
                <span>Home</span>
                <div class="tooltip-text">Home</div>
            </a>
            <a href="<?= $registerUrl ?>"
                class="sidebar-item <?= $currentRoute === '/register' ? 'active' : '' ?>"
                title="Banco de Dados">
                <i class="bi bi-database"></i>
                <span>Cadastrar Banco de dados</span>
                <div class="tooltip-text">Cadastrar Banco de Dados</div>
            </a>
            <a href="<?= $configurateUrl ?>"
                class="sidebar-item <?= $currentRoute === '/configurate' ? 'active' : '' ?>"
                title="Configurações">
                <i class="bi bi-gear"></i>
                <span>Configurações</span>
                <div class="tooltip-text">Configurações</div>
            </a>
            <a href="<?= $userUrl ?>"
                class="sidebar-item <?= $currentRoute === '/user' ? 'active' : '' ?>"
                title="Usuarios">
                <i class="bi bi-person-plus"></i>
                <span>Cadastrar Usuarios</span>
                <div class="tooltip-text">Cadastrar Usuarios</div>
            </a>
            <a href="<?= $resetUrl ?>"
                class="sidebar-item <?= $currentRoute === '/reset' ? 'active' : '' ?>"
                title="Resetar">
                <i class="bi bi-arrow-repeat"></i>
                <span>Resetar</span>
                <div class="tooltip-text">Resetar</div>
            </a>
        </div>
    </nav>
</div>