<?php require_once __DIR__ . '/../core/header.php'; ?>
<?php require_once __DIR__ . '/../core/menu.php'; ?>

<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">Thêm đối tác mới</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="?act=/">Trang chủ</a></li>
                                <li class="breadcrumb-item"><a href="?act=danh-sach-doi-tac">Đối tác</a></li>
                                <li class="breadcrumb-item active">Thêm mới</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-body">
            <?php require_once __DIR__ . '/../core/alert.php'; ?>

            <form action="?act=luu-doi-tac" method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-8">
                        <!-- Thông tin cơ bản -->
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Thông tin cơ bản</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label for="supplier_name">Tên đối tác <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="supplier_name" id="supplier_name"
                                                class="form-control" required placeholder="VD: Khách sạn Mường Thanh">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="supplier_code">Mã đối tác</label>
                                            <input type="text" name="supplier_code" id="supplier_code"
                                                class="form-control" placeholder="VD: KS-MT-001">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="supplier_type">Loại đối tác <span class="text-danger">*</span></label>
                                    <select name="supplier_type" id="supplier_type" class="form-control" required>
                                        <option value="">-- Chọn loại --</option>
                                        <option value="Hotel">🏨 Khách sạn</option>
                                        <option value="Restaurant">🍽️ Nhà hàng</option>
                                        <option value="Transport">🚌 Vận chuyển</option>
                                        <option value="Guide">👤 Hướng dẫn viên</option>
                                        <option value="Activity">🎭 Hoạt động/Vui chơi</option>
                                        <option value="Insurance">🛡️ Bảo hiểm</option>
                                        <option value="Other">📦 Khác</option>
                                    </select>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="contact_person">Người liên hệ</label>
                                            <input type="text" name="contact_person" id="contact_person"
                                                class="form-control" placeholder="Họ tên">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="phone">Điện thoại</label>
                                            <input type="tel" name="phone" id="phone" class="form-control"
                                                placeholder="0987654321">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email">Email</label>
                                            <input type="email" name="email" id="email" class="form-control"
                                                placeholder="email@example.com">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="website">Website</label>
                                            <input type="url" name="website" id="website" class="form-control"
                                                placeholder="https://...">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="address">Địa chỉ</label>
                                    <textarea name="address" id="address" rows="2" class="form-control"
                                        placeholder="Địa chỉ đầy đủ"></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="notes">Ghi chú</label>
                                    <textarea name="notes" id="notes" rows="3" class="form-control"
                                        placeholder="Thông tin bổ sung..."></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Hợp đồng -->
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Thông tin hợp đồng</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="contract_number">Số hợp đồng</label>
                                            <input type="text" name="contract_number" id="contract_number"
                                                class="form-control" placeholder="HĐ-2025-001">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="contract_start_date">Ngày bắt đầu</label>
                                            <input type="date" name="contract_start_date" id="contract_start_date"
                                                class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="contract_end_date">Ngày kết thúc</label>
                                            <input type="date" name="contract_end_date" id="contract_end_date"
                                                class="form-control">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="contract_file">File hợp đồng (PDF, DOC, DOCX)</label>
                                    <div class="custom-file">
                                        <input type="file" name="contract_file" id="contract_file"
                                            class="custom-file-input" accept=".pdf,.doc,.docx">
                                        <label class="custom-file-label" for="contract_file">Chọn file...</label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="payment_terms">Điều khoản thanh toán</label>
                                    <textarea name="payment_terms" id="payment_terms" rows="2" class="form-control"
                                        placeholder="VD: Thanh toán 50% trước tour, 50% sau tour..."></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="cancellation_policy">Chính sách hủy</label>
                                    <textarea name="cancellation_policy" id="cancellation_policy" rows="2"
                                        class="form-control"
                                        placeholder="VD: Hủy trước 7 ngày không tính phí..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <!-- Trạng thái & Đánh giá -->
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Trạng thái</h4>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="status">Trạng thái</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="1" selected>Hoạt động</option>
                                        <option value="0">Ngừng hoạt động</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="rating">Đánh giá (0-5 sao)</label>
                                    <input type="number" name="rating" id="rating" class="form-control" min="0" max="5"
                                        step="0.1" value="0" placeholder="0.0">
                                    <small class="text-muted">Đánh giá chất lượng dịch vụ của đối tác</small>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="card">
                            <div class="card-body">
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="feather icon-save"></i> Lưu đối tác
                                </button>
                                <a href="?act=danh-sach-doi-tac" class="btn btn-secondary btn-block">
                                    <i class="feather icon-x"></i> Hủy bỏ
                                </a>
                            </div>
                        </div>

                        <!-- Hướng dẫn -->
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">💡 Hướng dẫn</h4>
                            </div>
                            <div class="card-body">
                                <small class="text-muted">
                                    <ul class="pl-3">
                                        <li>Nhập đầy đủ thông tin liên hệ</li>
                                        <li>Upload file hợp đồng nếu có</li>
                                        <li>Điền rõ điều khoản thanh toán và chính sách hủy</li>
                                        <li>Đánh giá chất lượng để dễ quản lý</li>
                                    </ul>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Update file input label when file is selected
    document.querySelector('.custom-file-input').addEventListener('change', function (e) {
        var fileName = e.target.files[0].name;
        var label = e.target.nextElementSibling;
        label.innerText = fileName;
    });
</script>

<?php require_once __DIR__ . '/../core/footer.php'; ?>