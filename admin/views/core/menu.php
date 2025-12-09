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

// Đếm thông báo chưa đọc cho HDV
$unread_notifications = 0;
if (function_exists('isGuide') && isGuide() && isset($_SESSION['staff_id'])) {
    $unread_notifications = getUnreadNotificationCount($_SESSION['staff_id']);
}
?>
<!-- BEGIN: Main Menu-->
<div class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true">
    <div class="navbar-header">
        <ul class="nav navbar-nav flex-row">
            <li class="nav-item mr-auto"><a class="navbar-brand" href="/admin">
                    <div class="brand-logo"></div>
                    <h2 class="brand-text mb-0">
                        <?php echo (function_exists('isGuide') && isGuide()) ? 'HDV Panel' : 'Admin Panel'; ?>
                    </h2>
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
            <li class=" navigation-header"><span>USERS</span>
            </li>
            <li class="<?= isActive(['danh-sach-user', 'tao-user']) ?> nav-item">
                <a href="#">
                    <i class="feather icon-users"></i>
                    <span class="menu-title" data-i18n="Quản lý User">Quản lý User</span>
                </a>

                <ul class="menu-content">
                    <li class="<?= isActive(['danh-sach-user']) ? 'active' : '' ?>">
                        <a href="?act=danh-sach-user">
                            <i class="feather icon-circle"></i>
                            <span class="menu-item" data-i18n="Users">Users</span>
                        </a>
                    </li>

                    <li class="<?= isActive(['tao-user']) ? 'active' : '' ?>">
                        <a href="?act=tao-user">
                            <i class="feather icon-circle"></i>
                            <span class="menu-item" data-i18n="Tạo User">Tạo User</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class=" navigation-header"><span>CHỨC NĂNG</span>
            </li>
            <li class="<?= isActive(['menu-tour', 'list-tour']) ?> nav-item"><a href="#"><i
                        class="feather icon-package"></i><span class="menu-title" data-i18n="Quản lý Tour">Quản lý
                        Tour</span></a>
                <ul class="menu-content">
                    <li class="<?= isActive(['menu-tour']) ? 'active' : '' ?>"><a href="?act=menu-tour"><i
                                class="feather icon-circle"></i><span class="menu-item" data-i18n="Danh mục Tour">Danh
                                mục Tour</span></a>
                    </li>
                    <li class="<?= isActive(['list-tour']) ? 'active' : '' ?>"><a href="?act=list-tour"><i
                                class="feather icon-circle"></i><span class="menu-item" data-i18n="Danh sách Tour">Danh
                                sách Tour</span></a>
                    </li>
                </ul>
            </li>
            <li
                class="<?= isActive(['danh-sach-nhan-su', 'them-nhan-su', 'chi-tiet-nhan-su', 'sua-nhan-su']) ?> nav-item">
                <a href="#"><i class="feather icon-users"></i><span class="menu-title" data-i18n="Quản lý nhân sự">Quản
                        lý nhân sự</span></a>
                <ul class="menu-content">
                    <li class="<?= isActive(['danh-sach-nhan-su']) ? 'active' : '' ?>"><a
                            href="?act=danh-sach-nhan-su"><i class="feather icon-circle"></i><span class="menu-item"
                                data-i18n="Danh sách nhân sự">Danh sách nhân sự</span></a>
                    </li>
                    <li class="<?= isActive(['them-nhan-su']) ? 'active' : '' ?>"><a href="?act=them-nhan-su"><i
                                class="feather icon-circle"></i><span class="menu-item" data-i18n="Thêm nhân sự">Thêm
                                nhân sự</span></a>
                    </li>
                </ul>
            </li>
            <li
                class="<?= isActive(['danh-sach-booking', 'them-booking', 'chi-tiet-booking', 'sua-booking']) ?> nav-item">
                <a href="#"><i class="feather icon-book-open"></i><span class="menu-title"
                        data-i18n="Quản lý Booking">Quản
                        lý Booking</span></a>
                <ul class="menu-content">
                    <li class="<?= isActive(['danh-sach-booking']) ? 'active' : '' ?>"><a
                            href="?act=danh-sach-booking"><i class="feather icon-circle"></i><span class="menu-item"
                                data-i18n="Danh sách booking">Danh sách booking</span></a>
                    </li>
                    <li class="<?= isActive(['them-booking']) ? 'active' : '' ?>"><a href="?act=them-booking"><i
                                class="feather icon-circle"></i><span class="menu-item" data-i18n="Tạo booking">Tạo
                                booking</span></a>
                    </li>
                </ul>
            </li>
            <li
                class="<?= isActive(['danh-sach-lich-khoi-hanh', 'them-lich-khoi-hanh', 'chi-tiet-lich-khoi-hanh', 'sua-lich-khoi-hanh', 'xem-lich-theo-thang']) ?> nav-item">
                <a href="#"><i class="feather icon-clock"></i><span class="menu-title" data-i18n="Lịch khởi hành">Lịch
                        khởi hành</span></a>
                <ul class="menu-content">
                    <li class="<?= isActive(['danh-sach-lich-khoi-hanh']) ? 'active' : '' ?>"><a
                            href="?act=danh-sach-lich-khoi-hanh"><i class="feather icon-circle"></i><span
                                class="menu-item" data-i18n="Danh sách lịch">Danh sách lịch</span></a>
                    </li>
                    <li class="<?= isActive(['them-lich-khoi-hanh']) ? 'active' : '' ?>"><a
                            href="?act=them-lich-khoi-hanh"><i class="feather icon-circle"></i><span class="menu-item"
                                data-i18n="Thêm lịch mới">Thêm lịch mới</span></a>
                    </li>
                    <li class="<?= isActive(['xem-lich-theo-thang']) ? 'active' : '' ?>"><a
                            href="?act=xem-lich-theo-thang"><i class="feather icon-circle"></i><span class="menu-item"
                                data-i18n="Xem lịch tháng">Xem lịch tháng</span></a>
                    </li>
                </ul>
            </li>

            <?php if (function_exists('isGuide') && isGuide()): ?>
                <!-- MENU CHO HƯỚNG DẪN VIÊN -->
                <li class=" navigation-header"><span>HƯỚNG DẪN VIÊN</span></li>

                <!-- Lịch tour -->
                <li class="<?= isActive(['hdv-lich-cua-toi', 'hdv-chi-tiet-tour', 'hdv-nhiem-vu-cua-toi']) ?> nav-item">
                    <a href="#"><i class="feather icon-map"></i><span class="menu-title">Lịch tour của tôi</span></a>
                    <ul class="menu-content">
                        <li class="<?= isActive(['hdv-lich-cua-toi']) ? 'active' : '' ?>">
                            <a href="?act=hdv-lich-cua-toi"><i class="feather icon-circle"></i><span class="menu-item">Danh
                                    sách tour</span></a>
                        </li>
                        <li class="<?= isActive(['hdv-xem-lich-thang']) ? 'active' : '' ?>">
                            <a href="?act=hdv-xem-lich-thang"><i class="feather icon-circle"></i><span class="menu-item">Xem
                                    lịch tháng</span></a>
                        </li>
                        <li class="<?= isActive(['hdv-nhiem-vu-cua-toi']) ? 'active' : '' ?>">
                            <a href="?act=hdv-lich-cua-toi"><i class="feather icon-circle"></i><span class="menu-item">Nhiệm
                                    vụ của tôi</span></a>
                        </li>
                    </ul>
                </li>


                <!-- Hồ sơ & Cài đặt -->
                <li class=" navigation-header"><span>CÁ NHÂN</span></li>

                <li class="<?= isActive(['hdv-ho-so']) ? 'active' : '' ?> nav-item">
                    <a href="?act=hdv-ho-so"><i class="feather icon-user"></i><span class="menu-title">Hồ sơ của
                            tôi</span></a>
                </li>

                <li class="<?= isActive(['hdv-thong-bao']) ? 'active' : '' ?> nav-item">
                    <a href="?act=hdv-thong-bao">
                        <i class="feather icon-bell"></i>
                        <span class="menu-title">Thông báo</span>
                        <?php if (!empty($unread_notifications)): ?>
                            <span class="badge badge-pill badge-danger float-right"><?= $unread_notifications ?></span>
                        <?php endif; ?>
                    </a>
                </li>

                <li class="<?= isActive(['hdv-tro-giup']) ? 'active' : '' ?> nav-item">
                    <a href="?act=hdv-tro-giup"><i class="feather icon-help-circle"></i><span class="menu-title">Trợ
                            giúp</span></a>
                </li>

                <li class="nav-item">
                    <a href="?act=doi-mat-khau"><i class="feather icon-lock"></i><span class="menu-title">Đổi mật
                            khẩu</span></a>
                </li>

            <?php else: ?>
                <!-- MENU CHO ADMIN/STAFF -->
                <div class="mt-1">
                    <a href="?act=danh-sach-user" class="btn btn-sm btn-outline-info"><i class="feather icon-users"></i>
                        Users</a>
                    <a href="?act=tao-user" class="btn btn-sm btn-outline-success"><i
                            class="feather icon-user-plus"></i></a>
                </div>
                <li class=" navigation-header"><span>CHỨC NĂNG</span></li>
                <li class="<?= isActive(['menu-tour', 'list-tour']) ?> nav-item"><a href="#"><i
                            class="feather icon-package"></i><span class="menu-title" data-i18n="Quản lý Tour">Quản lý
                            Tour</span></a>
                    <ul class="menu-content">
                        <li class="<?= isActive(['menu-tour']) ? 'active' : '' ?>"><a href="?act=menu-tour"><i
                                    class="feather icon-circle"></i><span class="menu-item" data-i18n="Danh mục Tour">Danh
                                    mục Tour</span></a>
                        </li>
                        <li class="<?= isActive(['list-tour']) ? 'active' : '' ?>"><a href="?act=list-tour"><i
                                    class="feather icon-circle"></i><span class="menu-item" data-i18n="Danh sách Tour">Danh
                                    sách Tour</span></a>
                        </li>
                    </ul>
                </li>
                <li
                    class="<?= isActive(['danh-sach-nhan-su', 'them-nhan-su', 'chi-tiet-nhan-su', 'sua-nhan-su']) ?> nav-item">
                    <a href="#"><i class="feather icon-users"></i><span class="menu-title" data-i18n="Quản lý nhân sự">Quản
                            lý nhân sự</span></a>
                    <ul class="menu-content">
                        <li class="<?= isActive(['danh-sach-nhan-su']) ? 'active' : '' ?>"><a
                                href="?act=danh-sach-nhan-su"><i class="feather icon-circle"></i><span class="menu-item"
                                    data-i18n="Danh sách nhân sự">Danh sách nhân sự</span></a>
                        </li>
                        <li class="<?= isActive(['them-nhan-su']) ? 'active' : '' ?>"><a href="?act=them-nhan-su"><i
                                    class="feather icon-circle"></i><span class="menu-item" data-i18n="Thêm nhân sự">Thêm
                                    nhân sự</span></a>
                        </li>
                    </ul>
                </li>
                <li
                    class="<?= isActive(['danh-sach-booking', 'them-booking', 'chi-tiet-booking', 'sua-booking']) ?> nav-item">
                    <a href="#"><i class="feather icon-book-open"></i><span class="menu-title"
                            data-i18n="Quản lý Booking">Quản
                            lý Booking</span></a>
                    <ul class="menu-content">
                        <li class="<?= isActive(['danh-sach-booking']) ? 'active' : '' ?>"><a
                                href="?act=danh-sach-booking"><i class="feather icon-circle"></i><span class="menu-item"
                                    data-i18n="Danh sách booking">Danh sách booking</span></a>
                        </li>
                        <li class="<?= isActive(['them-booking']) ? 'active' : '' ?>"><a href="?act=them-booking"><i
                                    class="feather icon-circle"></i><span class="menu-item" data-i18n="Tạo booking">Tạo
                                    booking</span></a>
                        </li>
                    </ul>
                </li>
                <li
                    class="<?= isActive(['danh-sach-lich-khoi-hanh', 'them-lich-khoi-hanh', 'chi-tiet-lich-khoi-hanh', 'sua-lich-khoi-hanh', 'xem-lich-theo-thang']) ?> nav-item">
                    <a href="#"><i class="feather icon-clock"></i><span class="menu-title" data-i18n="Lịch khởi hành">Lịch
                            khởi hành</span></a>
                    <ul class="menu-content">
                        <li class="<?= isActive(['danh-sach-lich-khoi-hanh']) ? 'active' : '' ?>"><a
                                href="?act=danh-sach-lich-khoi-hanh"><i class="feather icon-circle"></i><span
                                    class="menu-item" data-i18n="Danh sách lịch">Danh sách lịch</span></a>
                        </li>
                        <li class="<?= isActive(['them-lich-khoi-hanh']) ? 'active' : '' ?>"><a
                                href="?act=them-lich-khoi-hanh"><i class="feather icon-circle"></i><span class="menu-item"
                                    data-i18n="Thêm lịch mới">Thêm lịch mới</span></a>
                        </li>
                        <li class="<?= isActive(['xem-lich-theo-thang']) ? 'active' : '' ?>"><a
                                href="?act=xem-lich-theo-thang"><i class="feather icon-circle"></i><span class="menu-item"
                                    data-i18n="Xem lịch tháng">Xem lịch tháng</span></a>
                        </li>
                    </ul>
                </li>
                <?php if (function_exists('isAdmin') && isAdmin()): ?>
                    <li class="<?= isActive(['bao-cao-tour', 'bao-cao-doanh-thu']) ?> nav-item"><a href="#"><i
                                class="feather icon-bar-chart-2"></i><span class="menu-title" data-i18n="Báo cáo">Báo
                                cáo</span></a>
                        <ul class="menu-content">
                            <li class="<?= isActive(['bao-cao-tour']) ? 'active' : '' ?>"><a href="?act=bao-cao-tour"><i
                                        class="feather icon-circle"></i><span class="menu-item" data-i18n="Báo cáo tour">Báo cáo
                                        tour</span></a></li>
                            <li class="<?= isActive(['bao-cao-doanh-thu']) ? 'active' : '' ?>"><a
                                    href="?act=bao-cao-doanh-thu"><i class="feather icon-circle"></i><span class="menu-item"
                                        data-i18n="Báo cáo doanh thu">Báo cáo
                                        doanh thu</span></a></li>
                        </ul>
                    </li>
                    <li class="<?= isActive(['danh-sach-bao-gia', 'tao-bao-gia', 'xem-bao-gia']) ?> nav-item"><a href="#"><i
                                class="feather icon-file-text"></i><span class="menu-title" data-i18n="Báo giá">Báo
                                giá</span></a>
                        <ul class="menu-content">
                            <li class="<?= isActive(['danh-sach-bao-gia']) ? 'active' : '' ?>"><a
                                    href="?act=danh-sach-bao-gia"><i class="feather icon-circle"></i><span class="menu-item"
                                        data-i18n="Danh sách báo giá">Danh sách báo giá</span></a></li>
                            <li class="<?= isActive(['tao-bao-gia']) ? 'active' : '' ?>"><a href="?act=tao-bao-gia"><i
                                        class="feather icon-circle"></i><span class="menu-item" data-i18n="Tạo báo giá">Tạo báo
                                        giá</span></a></li>
                        </ul>
                    </li>
                <?php endif; ?>

                <?php if (!function_exists('isGuide') || !isGuide()): ?>
                    <!-- Menu User Management (ẩn với HDV) -->
                    <li class="<?= isActive(['danh-sach-user', 'tao-user']) ?> nav-item"><a href="#"><i
                                class="feather icon-user"></i><span class="menu-title" data-i18n="Quản lý User">Quản lý
                                User</span></a>
                        <ul class="menu-content">
                            <li class="<?= isActive(['danh-sach-user']) ? 'active' : '' ?>"><a href="?act=danh-sach-user"><i
                                        class="feather icon-circle"></i><span class="menu-item">Danh sách User</span></a></li>
                            <li class="<?= isActive(['tao-user']) ? 'active' : '' ?>"><a href="?act=tao-user"><i
                                        class="feather icon-circle"></i><span class="menu-item">Tạo User</span></a></li>
                        </ul>
                    </li>
                <?php endif; ?>
            <?php endif; ?> <!-- End else for isGuide check -->
        </ul>
    </div>
</div>
<!-- END: Main Menu-->
