<?php require_once __DIR__ . '/../core/header.php'; ?>
<?php require_once __DIR__ . '/../core/menu.php'; ?>

<!-- BEGIN: Content-->
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">
                            <i class="feather icon-plus-circle"></i> Thêm ghi chú đặc biệt
                        </h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="?act=/">Trang chủ</a></li>
                                <li class="breadcrumb-item"><a
                                        href="?act=danh-sach-khach&booking_id=<?= $_GET['booking_id'] ?? '' ?>">Danh
                                        sách khách</a></li>
                                <li class="breadcrumb-item active">Thêm ghi chú</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-body">
            <!-- Thông báo -->
            <?php require_once __DIR__ . '/../core/alert.php'; ?>

            <!-- Thông tin khách -->
            <div class="card">
                <div class="card-body">
                    <h5><i class="feather icon-user"></i> Thông tin khách hàng</h5>
                    <div class="row">
                        <div class="col-md-4">
                            <strong>Họ tên:</strong> <?= htmlspecialchars($guest['full_name']) ?>
                        </div>
                        <div class="col-md-4">
                            <strong>Điện thoại:</strong> <?= htmlspecialchars($guest['phone'] ?? 'N/A') ?>
                        </div>
                        <div class="col-md-4">
                            <strong>Email:</strong> <?= htmlspecialchars($guest['email'] ?? 'N/A') ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form thêm ghi chú -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Thông tin ghi chú đặc biệt</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="?act=them-ghi-chu">
                        <input type="hidden" name="guest_id" value="<?= $guest['guest_id'] ?>">
                        <input type="hidden" name="booking_id" value="<?= $_GET['booking_id'] ?>">
                        <input type="hidden" name="return_url"
                            value="<?= htmlspecialchars($_GET['return_url'] ?? '?act=danh-sach-khach&booking_id=' . $_GET['booking_id']) ?>">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Loại ghi chú <span class="text-danger">*</span></label>
                                    <select name="note_type" class="form-control" required>
                                        <option value="">-- Chọn loại --</option>
                                        <option value="Dietary">🍽️ Ăn uống (ăn chay, kiêng thức ăn...)</option>
                                        <option value="Medical">💊 Y tế (bệnh lý, thuốc...)</option>
                                        <option value="Allergy">⚠️ Dị ứng (thức ăn, thuốc, môi trường...)</option>
                                        <option value="Mobility">♿ Di chuyển (khó khăn vận động...)</option>
                                        <option value="Other">📝 Yêu cầu khác</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Mức độ ưu tiên <span class="text-danger">*</span></label>
                                    <select name="priority_level" class="form-control" required>
                                        <option value="Medium" selected>Trung bình</option>
                                        <option value="Low">Thấp</option>
                                        <option value="High">Cao (Khẩn cấp)</option>
                                    </select>
                                    <small class="form-text text-muted">
                                        Cao: Cần xử lý ngay | Trung bình: Quan trọng | Thấp: Ghi nhận
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Nội dung ghi chú <span class="text-danger">*</span></label>
                            <textarea name="note_content" class="form-control" rows="5" required
                                placeholder="Nhập chi tiết yêu cầu đặc biệt của khách...&#10;&#10;Ví dụ:&#10;- Ăn chay trường, không sử dụng hành tỏi&#10;- Dị ứng hải sản, tôm cua&#10;- Đang dùng thuốc tim mạch, cần nghỉ ngơi nhiều&#10;- Khó di chuyển, cần hỗ trợ xe lăn"></textarea>
                        </div>

                        <div class="alert alert-info">
                            <i class="feather icon-info"></i>
                            <strong>Lưu ý:</strong> Sau khi lưu, hệ thống sẽ tự động gửi thông báo đến HDV và bộ phận
                            hậu cần để chuẩn bị dịch vụ phù hợp.
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="feather icon-save"></i> Lưu ghi chú
                            </button>
                            <a href="<?= htmlspecialchars($_GET['return_url'] ?? '?act=danh-sach-khach&booking_id=' . $_GET['booking_id']) ?>"
                                class="btn btn-secondary">
                                <i class="feather icon-x"></i> Hủy
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END: Content-->

<?php require_once __DIR__ . '/../core/footer.php'; ?>