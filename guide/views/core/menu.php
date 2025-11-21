<?php
function isActive($routes)
{
    $act = $_GET['act'] ?? '';

    // Trang default
    if ($act == '' || $act == '/') {
        $act = 'dashboard';
    }

    return in_array($act, (array) $routes);
}
?>
<!-- BEGIN: Main Menu-->
<div class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true">
    <div class="navbar-header">
        <ul class="nav navbar-nav flex-row">
            <li class="nav-item mr-auto"><a class="navbar-brand" href="/admin">
                    <div class="brand-logo"></div>
                    <h2 class="brand-text mb-0">Tour Guide</h2>
                </a></li>
            <li class="nav-item nav-toggle"><a class="nav-link modern-nav-toggle pr-0" data-toggle="collapse"><i
                        class="feather icon-x d-block d-xl-none font-medium-4 primary toggle-icon"></i><i
                        class="toggle-icon feather icon-disc font-medium-4 d-none d-xl-block collapse-toggle-icon primary"
                        data-ticon="icon-disc"></i></a></li>
        </ul>
    </div>
    <div class="shadow-bottom"></div>
    <div class="main-menu-content">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
            <li class="<?= isActive(['dashboard']) ? 'active' : '' ?> nav-item"><a href="/admin"><i
                        class="feather icon-home""></i><span class=" menu-title" data-i18n="Dashboard">Bảng điều
                        khiển</span></a>
            </li>
            <li class=" navigation-header"><span>CHỨC NĂNG</span>
            </li>
            <li class="<?= isActive(['tour-lich-lam-viec']) ?> nav-item"><a href="?act=tour-lich-lam-viec"><i
                        class="feather icon-calendar""></i><span class=" menu-title" data-i18n="Tour & Lịch Làm Việc">Tour & Lịch Làm Việc</span></a>
            </li>
            <li class="<?= isActive(['danh-sach-khach']) ?> nav-item"><a href="?act=danh-sach-khach"><i
                        class="feather icon-users""></i><span class=" menu-title" data-i18n="Danh sách khách">Danh sách khách</span></a>
            </li>
            <li class="<?= isActive(['nhat-ky-tour']) ?> nav-item"><a href="?act=nhat-ky-tour"><i
                        class="feather icon-file-text""></i><span class=" menu-title" data-i18n="Nhật ký Tour">Nhật ký Tour</span></a>
            </li>
            <li class="<?= isActive(['diem-danh-khach']) ?> nav-item"><a href="?act=diem-danh-khach"><i
                        class="feather icon-user-check""></i><span class=" menu-title" data-i18n="Điểm danh khách">Điểm danh khách</span></a>
            </li>
            <li class="<?= isActive(['yeu-cau-dac-biet']) ?> nav-item"><a href="?act=yeu-cau-dac-biet"><i
                        class="feather icon-alert-circle""></i><span class=" menu-title" data-i18n="Yêu cầu đặc biệt">Yêu cầu đặc biệt</span></a>
            </li>
            <li class="<?= isActive(['danh-gia-phan-hoi']) ?> nav-item"><a href="?act=danh-gia-phan-hoi"><i
                        class="feather icon-message-square""></i><span class=" menu-title" data-i18n="Đánh Giá & Phản Hồi">Đánh Giá & Phản Hồi</span></a>
            </li>
        </ul>
    </div>
</div>
<!-- END: Main Menu-->