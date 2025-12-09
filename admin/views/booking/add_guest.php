<?php require_once __DIR__ . '/../core/header.php'; ?>
<?php require_once __DIR__ . '/../core/menu.php'; ?>
<?php require_once __DIR__ . '/../core/alert.php'; ?>

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
                            <i class="feather icon-user-plus"></i> Thêm khách mới
                        </h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="?act=danh-sach-booking">Booking</a></li>
                                <li class="breadcrumb-item"><a href="?act=danh-sach-khach&booking_id=<?= $booking['booking_id'] ?>">Danh sách khách</a></li>
                                <li class="breadcrumb-item active">Thêm khách</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-body">
            <!-- Booking Info -->
            <div class="card">
                <div class="card-body">
                    <h4><i class="feather icon-info"></i> Thông tin Booking</h4>
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Mã Booking:</strong> #<?= $booking['booking_id'] ?>
                        </div>
                        <div class="col-md-3">
                            <strong>Tour:</strong> <?= htmlspecialchars($booking['tour_name']) ?>
                        </div>
                        <div class="col-md-3">
                            <strong>Ngày khởi hành:</strong> <?= date('d/m/Y', strtotime($booking['tour_date'])) ?>
                        </div>
                        <div class="col-md-3">
                            <strong>Trạng thái:</strong>
                            <span class="badge badge-info"><?= $booking['status'] ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form thêm khách -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title"><i class="feather icon-edit"></i> Thông tin khách mới</h4>
                </div>
                <div class="card-content">
                    <div class="card-body">
                        <form method="POST" action="?act=luu-khach" class="form">
                            <input type="hidden" name="booking_id" value="<?= $booking['booking_id'] ?>">
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="full_name">Họ tên <span class="text-danger">*</span></label>
                                        <input type="text" id="full_name" class="form-control" name="full_name" 
                                               placeholder="Nhập họ và tên đầy đủ" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="id_card">CMND/CCCD</label>
                                        <input type="text" id="id_card" class="form-control" name="id_card" 
                                               placeholder="Số chứng minh thư hoặc căn cước">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="birth_date">Ngày sinh</label>
                                        <input type="date" id="birth_date" class="form-control" name="birth_date" 
                                               max="<?= date('Y-m-d') ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="gender">Giới tính</label>
                                        <select id="gender" class="form-control" name="gender">
                                            <option value="Male">Nam</option>
                                            <option value="Female">Nữ</option>
                                            <option value="Other">Khác</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="is_adult">Loại khách</label>
                                        <select id="is_adult" class="form-control" name="is_adult">
                                            <option value="1">Người lớn</option>
                                            <option value="0">Trẻ em</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="phone">Số điện thoại</label>
                                        <input type="tel" id="phone" class="form-control" name="phone" 
                                               placeholder="Số điện thoại liên hệ">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="email" id="email" class="form-control" name="email" 
                                               placeholder="Địa chỉ email">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="address">Địa chỉ</label>
                                        <textarea id="address" class="form-control" name="address" rows="2" 
                                                  placeholder="Địa chỉ thường trú"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="payment_status">Trạng thái thanh toán</label>
                                        <select id="payment_status" class="form-control" name="payment_status">
                                            <option value="Pending">Chờ thanh toán</option>
                                            <option value="Paid">Đã thanh toán</option>
                                            <option value="Refunded">Đã hoàn tiền</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="special_needs">Yêu cầu đặc biệt</label>
                                        <textarea id="special_needs" class="form-control" name="special_needs" rows="3" 
                                                  placeholder="Ghi chú các yêu cầu đặc biệt: ăn chay, dị ứng, khuyết tật, v.v."></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="btn btn-success">
                                    <i class="feather icon-check"></i> Lưu thông tin
                                </button>
                                <a href="?act=danh-sach-khach&booking_id=<?= $booking['booking_id'] ?>" 
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
</div>
<!-- END: Content-->

<script>
// Auto-set is_adult based on birth_date
document.getElementById('birth_date').addEventListener('change', function() {
    const birthDate = new Date(this.value);
    const today = new Date();
    const age = today.getFullYear() - birthDate.getFullYear();
    const monthDiff = today.getMonth() - birthDate.getMonth();
    
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
        age--;
    }
    
    const isAdultSelect = document.getElementById('is_adult');
    if (age >= 18) {
        isAdultSelect.value = '1';
    } else if (age >= 0) {
        isAdultSelect.value = '0';
    }
});

// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    const fullName = document.getElementById('full_name').value.trim();
    
    if (!fullName) {
        e.preventDefault();
        alert('Vui lòng nhập họ tên!');
        document.getElementById('full_name').focus();
        return false;
    }
    
    const phone = document.getElementById('phone').value.trim();
    if (phone && !/^[0-9+\-\s()]{10,15}$/.test(phone)) {
        e.preventDefault();
        alert('Số điện thoại không đúng định dạng!');
        document.getElementById('phone').focus();
        return false;
    }
    
    return true;
});
</script>

<?php require_once __DIR__ . '/../core/footer.php'; ?>