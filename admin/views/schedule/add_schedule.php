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
                        <h2 class="content-header-title float-left mb-0">Thêm lịch khởi hành</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">
            <!-- Thông báo -->
            <?php require_once __DIR__ . '/../core/alert.php'; ?>

            <section id="basic-vertical-layouts">
                <div class="row match-height">
                    <div class="col-md-8 col-12 mx-auto">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Thông tin lịch khởi hành</h4>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <form action="?act=luu-lich-khoi-hanh" method="POST">
                                        <div class="form-group">
                                            <label for="tour_id">Chọn Tour <span class="text-danger">*</span></label>
                                            <select name="tour_id" id="tour_id" class="form-control" required>
                                                <option value="">-- Chọn tour --</option>
                                                <?php if (!empty($tours)): ?>
                                                    <?php foreach ($tours as $tour): ?>
                                                        <option value="<?= $tour['tour_id'] ?>" 
                                                            <?= (isset($tour_id) && $tour_id == $tour['tour_id']) ? 'selected' : '' ?>>
                                                            <?= htmlspecialchars($tour['tour_name']) ?> (<?= $tour['code'] ?>)
                                                        </option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="departure_date">Ngày khởi hành <span class="text-danger">*</span></label>
                                                    <input type="date" name="departure_date" id="departure_date" 
                                                           class="form-control" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="return_date">Ngày kết thúc</label>
                                                    <input type="date" name="return_date" id="return_date" 
                                                           class="form-control">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-8">
                                                <div class="form-group">
                                                    <label for="meeting_point">Điểm tập trung</label>
                                                    <input type="text" name="meeting_point" id="meeting_point" 
                                                           class="form-control" placeholder="VD: Số 1 Bà Triệu, Hà Nội">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="meeting_time">Giờ tập trung</label>
                                                    <input type="time" name="meeting_time" id="meeting_time" 
                                                           class="form-control" value="07:00">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="max_participants">Số người tối đa</label>
                                            <input type="number" name="max_participants" id="max_participants" 
                                                   class="form-control" placeholder="VD: 30" min="1" value="30">
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="price_adult">Giá người lớn (VNĐ) <span class="text-danger">*</span></label>
                                                    <input type="number" name="price_adult" id="price_adult" 
                                                           class="form-control" placeholder="VD: 5000000" min="0" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="price_child">Giá trẻ em (VNĐ)</label>
                                                    <input type="number" name="price_child" id="price_child" 
                                                           class="form-control" placeholder="VD: 3500000" min="0">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="status">Trạng thái</label>
                                            <select name="status" id="status" class="form-control">
                                                <option value="Open" selected>Mở đặt tour</option>
                                                <option value="Confirmed">Đã xác nhận</option>
                                                <option value="Full">Đã đầy chỗ</option>
                                                <option value="Cancelled">Đã hủy</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="notes">Ghi chú</label>
                                            <textarea name="notes" id="notes" class="form-control" rows="3" 
                                                      placeholder="Ghi chú về lịch khởi hành..."></textarea>
                                        </div>

                                        <div class="alert alert-info">
                                            <i class="feather icon-info"></i>
                                            <strong>Lưu ý:</strong> Sau khi tạo lịch khởi hành, bạn có thể phân công nhân sự và dịch vụ ở trang chi tiết.
                                        </div>

                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary mr-1">
                                                <i class="feather icon-save"></i> Lưu lịch khởi hành
                                            </button>
                                            <a href="?act=danh-sach-lich-khoi-hanh" class="btn btn-secondary">
                                                <i class="feather icon-x"></i> Hủy
                                            </a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<script>
// Tự động set ngày kết thúc khi chọn tour
document.getElementById('tour_id').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    // Có thể thêm logic để tự động tính ngày kết thúc dựa trên duration của tour
});

// Validate ngày kết thúc phải sau ngày khởi hành
document.getElementById('return_date').addEventListener('change', function() {
    const departureDate = document.getElementById('departure_date').value;
    const returnDate = this.value;
    
    if (departureDate && returnDate && returnDate < departureDate) {
        alert('Ngày kết thúc phải sau ngày khởi hành!');
        this.value = '';
    }
});
</script>

<!-- END: Content-->
<?php require_once __DIR__ . '/../core/footer.php'; ?>
