<!DOCTYPE html>
<html class="loading" lang="vi" data-textdirection="ltr">

<head>
    <meta charset="UTF-8">
    <title>Đăng ký tài khoản</title>
    <link rel="stylesheet" type="text/css" href="admin/views/assetz/app-assets/vendors/css/vendors.min.css">
    <link rel="stylesheet" type="text/css" href="admin/views/assetz/app-assets/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="admin/views/assetz/app-assets/css/bootstrap-extended.css">
    <link rel="stylesheet" type="text/css" href="admin/views/assetz/app-assets/css/colors.css">
    <link rel="stylesheet" type="text/css" href="admin/views/assetz/app-assets/css/components.css">
    <link rel="stylesheet" type="text/css" href="admin/views/assetz/app-assets/css/pages/authentication.css">
</head>

<body
    class="vertical-layout vertical-menu-modern 1-column navbar-floating footer-static bg-full-screen-image blank-page"
    data-menu="vertical-menu-modern" data-col="1-column">
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row"></div>
            <div class="content-body">
                <section class="row flexbox-container">
                    <div class="col-xl-8 col-11 d-flex justify-content-center">
                        <div class="card bg-authentication rounded-0 mb-0">
                            <div class="row m-0">
                                <div class="col-lg-6 d-lg-block d-none text-center align-self-center px-1 py-0">
                                    <img src="admin/views/assetz/app-assets/images/pages/register.png" alt="brand" />
                                </div>
                                <div class="col-lg-6 col-12 p-0">
                                    <div class="card rounded-0 mb-0 px-2">
                                        <div class="card-header pb-1">
                                            <div class="card-title">
                                                <h4 class="mb-0">Tạo tài khoản</h4>
                                            </div>
                                        </div>
                                        <div class="px-2 small text-muted mb-1">Chỉ ADMIN mới đăng ký tài khoản.</div>
                                        <?php require_once __DIR__ . '/../core/alert.php'; ?>
                                        <div class="card-content">
                                            <div class="card-body pt-1">
                                                <form action="?act=do-register" method="POST" autocomplete="off">
                                                    <fieldset
                                                        class="form-label-group form-group position-relative has-icon-left">
                                                        <input type="text" name="username" class="form-control"
                                                            id="reg-username" placeholder="Username" required>
                                                        <div class="form-control-position"><i
                                                                class="feather icon-user"></i></div>
                                                        <label for="reg-username">Username</label>
                                                    </fieldset>
                                                    <fieldset
                                                        class="form-label-group form-group position-relative has-icon-left">
                                                        <input type="text" name="full_name" class="form-control"
                                                            id="reg-fullname" placeholder="Họ tên" required>
                                                        <div class="form-control-position"><i
                                                                class="feather icon-users"></i></div>
                                                        <label for="reg-fullname">Họ tên</label>
                                                    </fieldset>
                                                    <fieldset
                                                        class="form-label-group form-group position-relative has-icon-left">
                                                        <input type="email" name="email" class="form-control"
                                                            id="reg-email" placeholder="Email">
                                                        <div class="form-control-position"><i
                                                                class="feather icon-mail"></i></div>
                                                        <label for="reg-email">Email</label>
                                                    </fieldset>
                                                    <div class="form-row">
                                                        <div class="col-md-6">
                                                            <fieldset
                                                                class="form-label-group position-relative has-icon-left">
                                                                <input type="password" name="password"
                                                                    class="form-control" id="reg-pass"
                                                                    placeholder="Mật khẩu" required>
                                                                <div class="form-control-position"><i
                                                                        class="feather icon-lock"></i></div>
                                                                <label for="reg-pass">Mật khẩu</label>
                                                            </fieldset>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <fieldset
                                                                class="form-label-group position-relative has-icon-left">
                                                                <input type="password" name="password_confirmation"
                                                                    class="form-control" id="reg-pass2"
                                                                    placeholder="Xác nhận" required>
                                                                <div class="form-control-position"><i
                                                                        class="feather icon-lock"></i></div>
                                                                <label for="reg-pass2">Xác nhận</label>
                                                            </fieldset>
                                                        </div>
                                                    </div>
                                                    <div class="form-row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="role_code">Vai trò</label>
                                                                <select name="role_code" id="role_code"
                                                                    class="form-control">
                                                                    <option value="GUIDE">GUIDE (Hướng dẫn viên)
                                                                    </option>
                                                                    <option value="ADMIN">ADMIN</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="staff_id">Staff ID (nếu là HDV)</label>
                                                                <input type="number" name="staff_id" id="staff_id"
                                                                    class="form-control" placeholder="VD: 12">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <button type="submit" class="btn btn-primary btn-block"><i
                                                            class="feather icon-save"></i> Tạo tài khoản</button>
                                                    <a href="?act=login" class="btn btn-outline-secondary btn-block"><i
                                                            class="feather icon-arrow-left"></i> Quay lại đăng nhập</a>
                                                </form>
                                            </div>
                                        </div>
                                        <div class="p-2 text-center">
                                            <small class="text-muted">Hệ thống sẽ khóa tài khoản nếu sai mật khẩu quá
                                                nhiều.</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
    <script src="admin/views/assetz/app-assets/vendors/js/vendors.min.js"></script>
    <script src="admin/views/assetz/app-assets/js/core/app-menu.js"></script>
    <script src="admin/views/assetz/app-assets/js/core/app.js"></script>
    <script src="admin/views/assetz/app-assets/js/scripts/components.js"></script>
</body>

</html>