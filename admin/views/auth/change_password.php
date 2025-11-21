<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Đổi mật khẩu</title>
    <link rel="stylesheet" type="text/css" href="admin/views/assetz/app-assets/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="admin/views/assetz/app-assets/css/bootstrap-extended.css">
    <link rel="stylesheet" type="text/css" href="admin/views/assetz/app-assets/css/colors.css">
    <link rel="stylesheet" type="text/css" href="admin/views/assetz/app-assets/css/components.css">
</head>

<body class="vertical-layout 1-column p-2">
    <div class="container">
        <h3 class="mb-2">Đổi mật khẩu</h3>
        <?php require_once __DIR__ . '/../core/alert.php'; ?>
        <form action="?act=luu-mat-khau-moi" method="POST" autocomplete="off" class="card p-2">
            <div class="form-group">
                <label for="old_password">Mật khẩu cũ</label>
                <input type="password" class="form-control" id="old_password" name="old_password" required>
            </div>
            <div class="form-group">
                <label for="new_password">Mật khẩu mới <small class="text-muted">(>=8 ký tự)</small></label>
                <input type="password" class="form-control" id="new_password" name="new_password" required
                    minlength="8">
            </div>
            <div class="form-group">
                <label for="confirm_password">Xác nhận mật khẩu mới</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
            </div>
            <button class="btn btn-primary"><i class="feather icon-save"></i> Lưu</button>
            <a href="index.php" class="btn btn-secondary ml-1">Quay lại</a>
        </form>
        <div class="mt-2 small text-muted">Khuyến nghị dùng mật khẩu mạnh: chữ hoa, thường, số và ký tự đặc biệt.</div>
    </div>
</body>

</html>