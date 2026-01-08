<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$layananPages = ['jasa-tugas.php', 'jasa-laprak.php', 'servis-pc.php', 'it-consultant.php'];
$isLayananActive = in_array($currentPage, $layananPages);
?>


<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="index.php" class="app-brand-link">
            <!-- logo taruh sini
               ##logo -->
            <span class="app-brand-text demo menu-text fw-bold ms-2">WebSmith</span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="bx bx-chevron-left d-block d-xl-none align-middle"></i>
        </a>
    </div>

    <div class="menu-divider mt-0"></div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <!-- Dashboards -->
        <li class="menu-item <?= ($currentPage == 'index.php') ? 'active' : '' ?>">
            <a href="index.php" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-smile"></i>
                <div class="text-truncate" data-i18n="Dashboards">Dashboards</div>
            </a>
        </li>

        <!-- Layouts -->
        <li class="menu-item <?= $isLayananActive ? 'active' : '' ?>">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-layout"></i>
                <div class="text-truncate" data-i18n="Layouts">Layanan</div>
            </a>

            <ul class="menu-sub">
                <li class="menu-item <?= ($currentPage == 'jasa-tugas.php') ? 'active' : '' ?>">
                    <a href="jasa-tugas.php" class="menu-link">
                        <div class="text-truncate" data-i18n="Without menu">Jasa Tugas</div>
                    </a>
                </li>
                <li class="menu-item <?= ($currentPage == 'jasa-laprak.php') ? 'active' : '' ?>">
                    <a href="jasa-laprak.php" class="menu-link">
                        <div class="text-truncate" data-i18n="Without menu">Jasa Laprak</div>
                    </a>
                </li>
                <li class="menu-item <?= ($currentPage == 'servis-laptop.php') ? 'active' : '' ?>">
                    <a href="servis-laptop.php" class="menu-link">
                        <div class="text-truncate" data-i18n="Without navbar">Servis Laptop/PC</div>
                    </a>
                </li>
                <li class="menu-item <?= ($currentPage == 'it-consultant.php') ? 'active' : '' ?>">
                    <a href="it-consultant.php" class="menu-link">
                        <div class="text-truncate" data-i18n="Fluid">IT Consultant</div>
                    </a>
                </li>
            </ul>
        </li>

        <!-- Apps & Pages -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">DATA SECTION</span>
        </li>
        <!-- Pages -->
        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-dock-top"></i>
                <div class="text-truncate" data-i18n="User Account">User Account</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="pages-account-settings-account.html" class="menu-link">
                        <div class="text-truncate" data-i18n="Account">Account</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="pages-account-settings-notifications.html" class="menu-link">
                        <div class="text-truncate" data-i18n="Notifications">Notifications</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="pages-account-settings-connections.html" class="menu-link">
                        <div class="text-truncate" data-i18n="Connections">Connections</div>
                    </a>
                </li>
            </ul>
        </li>
        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-lock-open-alt"></i>
                <div class="text-truncate" data-i18n="Authentications">Authentications</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="auth-login-basic.html" class="menu-link" target="_blank">
                        <div class="text-truncate" data-i18n="Basic">Login</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="auth-register-basic.html" class="menu-link" target="_blank">
                        <div class="text-truncate" data-i18n="Basic">Register</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="auth-forgot-password-basic.html" class="menu-link" target="_blank">
                        <div class="text-truncate" data-i18n="Basic">Forgot Password</div>
                    </a>
                </li>
            </ul>
        </li>
        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-cube-alt"></i>
                <div class="text-truncate" data-i18n="Misc">Misc</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="pages-misc-error.html" class="menu-link">
                        <div class="text-truncate" data-i18n="Error">Error</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="pages-misc-under-maintenance.html" class="menu-link">
                        <div class="text-truncate" data-i18n="Under Maintenance">Under Maintenance</div>
                    </a>
                </li>
            </ul>
        </li>
    </ul>
</aside>