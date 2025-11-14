    <!-- BEGIN: Main Menu-->
    <div class="horizontal-menu-wrapper">
        <div class="header-navbar navbar-expand-sm navbar navbar-horizontal floating-nav navbar-dark navbar-without-dd-arrow navbar-shadow menu-border" role="navigation" data-menu="menu-wrapper">
            <div class="navbar-header">
                <ul class="nav navbar-nav flex-row">
                    <li class="nav-item mr-auto"><a class="navbar-brand" href="html/ltr/horizontal-menu-template-dark/index.html">
                            <div class="brand-logo"></div>
                            <h2 class="brand-text mb-0">Vuexy</h2>
                        </a></li>
                    <li class="nav-item nav-toggle"><a class="nav-link modern-nav-toggle pr-0" data-toggle="collapse"><i class="feather icon-x d-block d-xl-none font-medium-4 primary toggle-icon"></i><i class="toggle-icon feather icon-disc font-medium-4 d-none d-xl-block collapse-toggle-icon primary" data-ticon="icon-disc"></i></a></li>
                </ul>
            </div>
            <!-- Horizontal menu content-->
            <div class="navbar-container main-menu-content" data-menu="menu-container">
                <!-- include includes/mixins-->
                <ul class="nav navbar-nav" id="main-menu-navigation" data-menu="menu-navigation">
                    <li class="nav-item">
                        <a class="nav-link" href="/admin">
                            <i class="feather icon-home"></i>
                            <span data-i18n="Dashboard">Dashboard</span>
                        </a>
                    </li>
                    <li class="dropdown nav-item" data-menu="dropdown"><a class="dropdown-toggle nav-link" href="#" data-toggle="dropdown"><i class="feather icon-package"></i><span data-i18n="Apps">Quản lý Tour</span></a>
                        <ul class="dropdown-menu">
                            <li data-menu=""><a class="dropdown-item" href="?act=menu-tour" data-toggle="dropdown" data-i18n="Email"><i class="feather icon-grid"></i>Danh mục Tour</a>
                            </li>
                            <li data-menu=""><a class="dropdown-item" href="?act=list-tour" data-toggle="dropdown" data-i18n="Email"><i class="feather icon-file-text"></i>Danh sách Tour</a>
                            </li>
                        </ul>
                    </li>
                    <li class="dropdown nav-item" data-menu="dropdown"><a class="dropdown-toggle nav-link" href="#" data-toggle="dropdown"><i class="feather icon-calendar"></i><span data-i18n="Apps">Booking</span></a>
                        <ul class="dropdown-menu">
                            <li data-menu=""><a class="dropdown-item" href="menu_tour.php" data-toggle="dropdown" data-i18n="Email"><i class="feather icon-file-text"></i>Tạo booking</a>
                            </li>
                            <li data-menu=""><a class="dropdown-item" href="#" data-toggle="dropdown" data-i18n="Chat"><i class="feather icon-list"></i>Quản lý booking</a>
                            </li>
                        </ul>
                    </li>
                    <li class="dropdown nav-item" data-menu="dropdown"><a class="dropdown-toggle nav-link" href="#" data-toggle="dropdown"><i class="feather icon-more-horizontal"></i><span data-i18n="Apps">Khác</span></a>
                        <ul class="dropdown-menu">
                            <li data-menu=""><a class="dropdown-item" href="menu_tour.php" data-toggle="dropdown" data-i18n="Email"><i class="feather icon-users"></i>Quản lý nhân sự</a>
                            </li>
                            <li data-menu=""><a class="dropdown-item" href="#" data-toggle="dropdown" data-i18n="Chat"><i class="feather icon-clock"></i>Lịch Tour & Phân bổ</a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <!-- END: Main Menu-->