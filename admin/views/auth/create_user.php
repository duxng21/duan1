<?php require_once __DIR__ . '/../core/header.php'; ?>
<?php require_once __DIR__ . '/../core/menu.php'; ?>
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <h2 class="content-header-title">Tạo User Mới</h2>
            </div>
        </div>
        <div class="content-body">
            <?php require_once __DIR__ . '/../core/alert.php'; ?>
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Thông tin tài khoản</h4>
                </div>
                <div class="card-body">
                    <form action="?act=luu-user" method="POST" autocomplete="off">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Username <span class="text-danger">*</span></label>
                                    <input type="text" name="username" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Họ tên <span class="text-danger">*</span></label>
                                    <input type="text" name="full_name" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" name="email" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Staff ID (nếu là HDV)</label>
                                    <input type="number" name="staff_id" class="form-control" placeholder="VD: 12">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Mật khẩu <span class="text-danger">*</span></label>
                                    <input type="password" name="password" class="form-control" required minlength="8">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Xác nhận mật khẩu <span class="text-danger">*</span></label>
                                    <input type="password" name="password_confirmation" class="form-control" required
                                        minlength="8">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Vai trò <span class="text-danger">*</span></label>
                                    <select name="role_id" class="form-control" required>
                                        <option value="">-- Chọn role --</option>
                                        <?php foreach ($roles as $r): ?>
                                            <option value="<?= $r['role_id'] ?>"><?= $r['role_code'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 d-flex align-items-center">
                                <div class="alert alert-secondary w-100 small mb-0">
                                    <strong>Lưu ý:</strong> Mật khẩu tối thiểu 8 ký tự. Nên đổi mật khẩu sau khi tạo nếu
                                    là tài khoản quan trọng.
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-primary"><i class="feather icon-save"></i> Lưu user</button>
                        <a href="?act=danh-sach-user" class="btn btn-secondary ml-1"><i
                                class="feather icon-arrow-left"></i> Quay lại</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../core/footer.php'; ?>