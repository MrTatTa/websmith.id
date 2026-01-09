<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$layananPages = ['jasa-tugas.php', 'jasa-laprak.php', 'it-consultant.php', 'servis-laptop.php'];
$isLayananActive = in_array($currentPage, $layananPages);
$akunPages = ['account-setting.php', 'setting-notification.php'];
$isAkunActive = in_array($currentPage, $akunPages);
$dataPages = ['kategori-jasa.php', 'layanan.php'];
$isDataActive = in_array($currentPage, $dataPages);
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
        <li class="menu-item <?= $isLayananActive ? 'active open' : '' ?>">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-layout"></i>
                <div class="text-truncate" data-i18n="Layouts">Pusat Layanan</div>
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
                    <a href="servis-laptop.html" class="menu-link">
                        <div class="text-truncate" data-i18n="Without navbar">Servis Laptop/PC</div>
                    </a>
                </li>
                <li class="menu-item <?= ($currentPage == 'it-consultant.php') ? 'active' : '' ?>">
                    <a href="it-consultant.html" class="menu-link">
                        <div class="text-truncate" data-i18n="Fluid">IT Consultant</div>
                    </a>
                </li>
            </ul>
        </li>
        <li class="menu-item <?= $isAkunActive ? 'active open' : '' ?>">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-dock-top"></i>
                <div class="text-truncate" data-i18n="User Account">User Account</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item <?= ($currentPage == 'account-setting.php') ? 'active' : '' ?>">
                    <a href="account-setting.php" class="menu-link">
                        <div class="text-truncate" data-i18n="Account">Account</div>
                    </a>
                </li>
                <li class="menu-item <?= ($currentPage == 'setting-notification.php') ? 'active' : '' ?>">
                    <a href="setting-notification.php" class="menu-link">
                        <div class="text-truncate" data-i18n="Notification">Notification</div>
                    </a>
                </li>
            </ul>
        </li>

        <!-- Apps & Pages -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">DATA SECTION</span>
        </li>
        <!-- Pages -->
        <li class="menu-item <?= $isDataActive ? 'active open' : '' ?>">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-pie-chart-alt-2"></i>
                <div class="text-truncate" data-i18n="User Account">Manajemen Data</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item <?= ($currentPage == 'kategori-jasa.php') ? 'active' : '' ?>">
                    <a href="kategori-jasa.php" class="menu-link">
                        <div class="text-truncate" data-i18n="Kategori Jasa">Kategori Jasa</div>
                    </a>
                </li>
                <li class="menu-item <?= ($currentPage == 'layanan.php') ? 'active' : '' ?>">
                    <a href="layanan.php" class="menu-link">
                        <div class="text-truncate" data-i18n="Layanan">Layanan</div>
                    </a>
                </li>
            </ul>
        </li>
    </ul>
</aside>