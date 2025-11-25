<?php require_once __DIR__ . '/../core/header.php'; ?>
<?php require_once __DIR__ . '/../core/menu.php'; ?>
<?php 
// Restore form data if validation failed
$formData = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_data']);
?>
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">Tạo báo giá mới</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">
            <?php require_once __DIR__ . '/../core/alert.php'; ?>

            <form method="POST" action="?act=luu-bao-gia" id="quoteForm">
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Thông tin tour & khách hàng</h4>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Tour <span class="text-danger">*</span></label>
                                    <select name="tour_id" class="form-control" required>
                                        <option value="">-- Chọn tour --</option>
                                        <?php foreach ($tours as $t): ?>
                                            <option value="<?= $t['tour_id'] ?>" <?= ($selectedTour && $selectedTour['tour_id'] == $t['tour_id'] ? 'selected' : '') ?>>
                                                <?= htmlspecialchars($t['tour_name']) ?>
                                                (<?= htmlspecialchars($t['code']) ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Ngày khởi hành dự kiến</label>
                                    <input type="date" name="departure_date" class="form-control" />
                                </div>

                                <hr>

                                <div class="form-group">
                                    <label>Tên khách hàng <span class="text-danger">*</span></label>
                                    <input type="text" name="customer_name" class="form-control" required value="<?= htmlspecialchars($formData['customer_name'] ?? '') ?>" />
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Email</label>
                                            <input type="email" name="customer_email" class="form-control" value="<?= htmlspecialchars($formData['customer_email'] ?? '') ?>" />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Số điện thoại</label>
                                            <input type="text" name="customer_phone" class="form-control" value="<?= htmlspecialchars($formData['customer_phone'] ?? '') ?>" />
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Địa chỉ</label>
                                    <textarea name="customer_address" class="form-control" rows="2"><?= htmlspecialchars($formData['customer_address'] ?? '') ?></textarea>
                                </div>

                                <hr>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Số người lớn</label>
                                            <input type="number" name="num_adults" class="form-control" min="0"
                                                value="0" />
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Số trẻ em</label>
                                            <input type="number" name="num_children" class="form-control" min="0"
                                                value="0" />
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Số em bé</label>
                                            <input type="number" name="num_infants" class="form-control" min="0"
                                                value="0" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Dịch vụ bổ sung (tùy chọn)</h4>
                            </div>
                            <div class="card-body">
                                <div id="optionsContainer">
                                    <div class="row mb-2">
                                        <div class="col-md-6">
                                            <input type="text" name="option_name[]" class="form-control"
                                                placeholder="Tên dịch vụ" />
                                        </div>
                                        <div class="col-md-3">
                                            <input type="number" name="option_price[]" class="form-control"
                                                placeholder="Giá" min="0" />
                                        </div>
                                        <div class="col-md-3">
                                            <input type="number" name="option_quantity[]" class="form-control"
                                                placeholder="SL" min="1" value="1" />
                                        </div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="addOption()">
                                    <i class="feather icon-plus"></i> Thêm dịch vụ
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Giá & chiết khấu</h4>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Giá căn bản <span class="text-danger">*</span></label>
                                    <input type="number" name="base_price" class="form-control" required min="0" step="0.01" value="<?= htmlspecialchars($formData['base_price'] ?? '') ?>" />
                                    <small class="form-text text-muted">Nhập giá tour cho toàn bộ đoàn</small>
                                </div>

                                <div class="form-group">
                                    <label>Loại chiết khấu</label>
                                    <select name="discount_type" class="form-control">
                                        <option value="none">Không</option>
                                        <option value="percent">Phần trăm (%)</option>
                                        <option value="fixed">Số tiền cố định</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Giá trị chiết khấu</label>
                                    <input type="number" name="discount_value" class="form-control" min="0" step="0.01"
                                        value="0" />
                                </div>

                                <div class="form-group">
                                    <label>Phụ phí</label>
                                    <input type="number" name="additional_fees" class="form-control" min="0" step="0.01"
                                        value="0" />
                                </div>

                                <div class="form-group">
                                    <label>Thuế (%)</label>
                                    <input type="number" name="tax_percent" class="form-control" min="0" max="100"
                                        step="0.01" value="0" />
                                </div>

                                <div class="form-group">
                                    <label>Thời hạn báo giá (ngày)</label>
                                    <input type="number" name="validity_days" class="form-control" min="1" value="7" />
                                </div>

                                <div class="form-group">
                                    <label>Ghi chú nội bộ</label>
                                    <textarea name="internal_notes" class="form-control" rows="3"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-body text-center">
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="feather icon-save"></i> Tạo báo giá
                                </button>
                                <a href="?act=danh-sach-bao-gia" class="btn btn-outline-secondary btn-block">
                                    Hủy
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>

<script>
    function addOption() {
        const container = document.getElementById('optionsContainer');
        const row = document.createElement('div');
        row.className = 'row mb-2';
        row.innerHTML = `
    <div class="col-md-6">
      <input type="text" name="option_name[]" class="form-control" placeholder="Tên dịch vụ" />
    </div>
    <div class="col-md-3">
      <input type="number" name="option_price[]" class="form-control" placeholder="Giá" min="0" />
    </div>
    <div class="col-md-3">
      <input type="number" name="option_quantity[]" class="form-control" placeholder="SL" min="1" value="1" />
    </div>
  `;
        container.appendChild(row);
    }
</script>

<?php require_once __DIR__ . '/../core/footer.php'; ?>