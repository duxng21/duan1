<?php require_once __DIR__ . '/../core/header.php'; ?>
<?php require_once __DIR__ . '/../core/menu.php'; ?>
<?php require_once __DIR__ . '/../core/alert.php'; ?>
<!-- BEGIN: Content-->
<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">Chỉnh sửa Booking</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="?act=danh-sach-booking">Danh sách booking</a></li>
                                <li class="breadcrumb-item"><a href="?act=chi-tiet-booking&id=<?= $booking['booking_id'] ?>">Chi tiết</a></li>
                                <li class="breadcrumb-item active">Chỉnh sửa</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">
            <section id="edit-booking">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Thông tin booking #<?= $booking['booking_id'] ?></h4>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <form action="?act=cap-nhat-booking&id=<?= $booking['booking_id'] ?>" method="POST" id="bookingForm">

                                        <!-- Loại Booking -->
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label>Loại booking <span class="text-danger">*</span></label>
                                                    <div class="d-flex">
                                                        <div class="custom-control custom-radio mr-3">
                                                            <input type="radio" id="type_personal" name="booking_type"
                                                                value="Cá nhân" class="custom-control-input" 
                                                                <?= ($booking['booking_type'] == 'Cá nhân') ? 'checked' : '' ?>
                                                                onchange="toggleBookingType()">
                                                            <label class="custom-control-label" for="type_personal">
                                                                <i class="feather icon-user"></i> Khách lẻ (1-2 người)
                                                            </label>
                                                        </div>
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" id="type_group" name="booking_type"
                                                                value="Đoàn" class="custom-control-input"
                                                                <?= ($booking['booking_type'] == 'Đoàn') ? 'checked' : '' ?>
                                                                onchange="toggleBookingType()">
                                                            <label class="custom-control-label" for="type_group">
                                                                <i class="feather icon-users"></i> Đoàn (Công ty/Tổ chức)
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <!-- Tour -->
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="tour_id">Tour <span class="text-danger">*</span></label>
                                                    <select name="tour_id" id="tour_id" class="form-control" required>
                                                        <option value="">-- Chọn tour --</option>
                                                        <?php foreach ($tours as $tour): ?>
                                                            <option value="<?= $tour['tour_id'] ?>"
                                                                <?= ($booking['tour_id'] == $tour['tour_id']) ? 'selected' : '' ?>>
                                                                <?= htmlspecialchars($tour['tour_name']) ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Ngày khởi hành -->
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="tour_date">Ngày khởi hành</label>
                                                    <input type="date" name="tour_date" id="tour_date"
                                                        class="form-control" 
                                                        value="<?= $booking['tour_date'] ?? '' ?>"
                                                        onchange="loadScheduleData()">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Thông tin lịch khởi hành (tự động lấy từ schedule) -->
                                        <?php if (!empty($booking['schedule_id'])): ?>
                                        <div class="card border-left-info mt-3 mb-3">
                                            <div class="card-header bg-light">
                                                <h5 class="card-title mb-0">
                                                    <i class="feather icon-map"></i> Thông tin lịch khởi hành (từ lịch trình)
                                                </h5>
                                                <small class="text-muted d-block mt-1">Các thông tin này được lấy trực tiếp từ lịch khởi hành</small>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <p><strong>Điểm tập trung:</strong> <?= htmlspecialchars($booking['schedule_meeting_point'] ?? 'N/A') ?></p>
                                                        <p><strong>Giờ tập trung:</strong> <?= $booking['schedule_meeting_time'] ?? 'N/A' ?></p>
                                                        <p><strong>Số chỗ tối đa:</strong> <span class="badge badge-info"><?= $booking['schedule_max_participants'] ?? 0 ?></span></p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <p><strong>Trạng thái lịch:</strong> <span class="badge badge-<?php
                                                            $ss = $booking['schedule_status'] ?? 'Open';
                                                            echo ($ss == 'Open' ? 'success' : ($ss == 'Full' ? 'warning' : ($ss == 'Confirmed' ? 'primary' : 'secondary')));
                                                        ?>"><?= $booking['schedule_status'] ?? 'Open' ?></span></p>
                                                        <p><strong>Giá lịch:</strong> NL: <?= number_format($booking['schedule_price_adult'] ?? 0, 0, ',', '.') ?> đ | TE: <?= number_format($booking['schedule_price_child'] ?? 0, 0, ',', '.') ?> đ</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endif; ?>

                                        <!-- Thông tin khách hàng (Cá nhân) -->
                                        <div id="customer-section" style="display: <?= ($booking['booking_type'] == 'Cá nhân') ? 'block' : 'none' ?>">
                                            <h5 class="mt-2 mb-1"><i class="feather icon-user"></i> Thông tin khách hàng</h5>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="customer_id">Khách hàng <span class="text-danger customer-required">*</span></label>
                                                        <select name="customer_id" id="customer_id" class="form-control">
                                                            <option value="">-- Chọn khách hàng --</option>
                                                            <?php foreach ($customers as $customer): ?>
                                                                <option value="<?= $customer['customer_id'] ?>"
                                                                    <?= ($booking['customer_id'] == $customer['customer_id']) ? 'selected' : '' ?>>
                                                                    <?= htmlspecialchars($customer['full_name']) ?> - <?= htmlspecialchars($customer['phone']) ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Thông tin đoàn -->
                                        <div id="group-section" style="display: <?= ($booking['booking_type'] == 'Đoàn') ? 'block' : 'none' ?>">
                                            <h5 class="mt-2 mb-1"><i class="feather icon-briefcase"></i> Thông tin công ty/tổ chức</h5>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="organization_name">Tên công ty/tổ chức <span class="text-danger group-required">*</span></label>
                                                        <input type="text" name="organization_name" id="organization_name" 
                                                            class="form-control" value="<?= htmlspecialchars($booking['organization_name'] ?? '') ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="contact_name">Người liên hệ <span class="text-danger group-required">*</span></label>
                                                        <input type="text" name="contact_name" id="contact_name" 
                                                            class="form-control" value="<?= htmlspecialchars($booking['contact_name'] ?? '') ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="contact_phone">SĐT liên hệ <span class="text-danger group-required">*</span></label>
                                                        <input type="tel" name="contact_phone" id="contact_phone" 
                                                            class="form-control" value="<?= htmlspecialchars($booking['contact_phone'] ?? '') ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="contact_email">Email liên hệ</label>
                                                        <input type="email" name="contact_email" id="contact_email" 
                                                            class="form-control" value="<?= htmlspecialchars($booking['contact_email'] ?? '') ?>">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Số lượng khách -->
                                        <h5 class="mt-2 mb-1"><i class="feather icon-users"></i> Số lượng khách</h5>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="num_adults">Người lớn <span class="text-danger">*</span></label>
                                                    <input type="text" id="num_adults_display" 
                                                        class="form-control" value="<?= $booking['num_adults'] ?>" readonly>
                                                    <input type="hidden" name="num_adults" id="num_adults" value="<?= $booking['num_adults'] ?>">
                                                    <small class="text-muted">Từ 12 tuổi trở lên</small>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="num_children">Trẻ em</label>
                                                    <input type="text" id="num_children_display" 
                                                        class="form-control" value="<?= $booking['num_children'] ?>" readonly>
                                                    <input type="hidden" name="num_children" id="num_children" value="<?= $booking['num_children'] ?>">
                                                    <small class="text-muted">Từ 6-11 tuổi</small>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="num_infants">Em bé</label>
                                                    <input type="text" id="num_infants_display" 
                                                        class="form-control" value="<?= $booking['num_infants'] ?>" readonly>
                                                    <input type="hidden" name="num_infants" id="num_infants" value="<?= $booking['num_infants'] ?>">
                                                    <small class="text-muted">Dưới 6 tuổi</small>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Yêu cầu đặc biệt -->
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="special_requests"><i class="feather icon-message-square"></i> Yêu cầu đặc biệt</label>
                                                    <textarea name="special_requests" id="special_requests" 
                                                        class="form-control" rows="3"><?= htmlspecialchars($booking['special_requests'] ?? '') ?></textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Tổng tiền & Trạng thái -->
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="total_amount">Tổng tiền <span class="text-danger">*</span></label>
                                                    <input type="number" name="total_amount" id="total_amount" 
                                                        class="form-control" required value="<?= $booking['total_amount'] ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="status">Trạng thái</label>
                                                    <select name="status" id="status" class="form-control">
                                                        <option value="Giữ chỗ" <?= ($booking['status'] == 'Giữ chỗ') ? 'selected' : '' ?>>Giữ chỗ</option>
                                                        <option value="Đã đặt cọc" <?= ($booking['status'] == 'Đã đặt cọc') ? 'selected' : '' ?>>Đã đặt cọc</option>
                                                        <option value="Đã thanh toán" <?= ($booking['status'] == 'Đã thanh toán') ? 'selected' : '' ?>>Đã thanh toán</option>
                                                        <option value="Đã hủy" <?= ($booking['status'] == 'Đã hủy') ? 'selected' : '' ?>>Đã hủy</option>
                                                        <option value="Đã hoàn thành" <?= ($booking['status'] == 'Đã hoàn thành') ? 'selected' : '' ?>>Đã hoàn thành</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Dịch vụ bổ sung -->
                                        <?php if (!empty($bookingDetails)): ?>
                                        <h5 class="mt-2 mb-1"><i class="feather icon-package"></i> Dịch vụ bổ sung</h5>
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Dịch vụ</th>
                                                        <th>Số lượng</th>
                                                        <th>Đơn giá</th>
                                                        <th>Thành tiền</th>
                                                        <th>Thao tác</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($bookingDetails as $detail): ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars($detail['service_name']) ?></td>
                                                        <td><?= $detail['quantity'] ?></td>
                                                        <td><?= number_format($detail['unit_price'], 0, ',', '.') ?> đ</td>
                                                        <td><?= number_format($detail['quantity'] * $detail['unit_price'], 0, ',', '.') ?> đ</td>
                                                        <td>
                                                            <a href="?act=xoa-booking-detail&id=<?= $detail['detail_id'] ?>&booking_id=<?= $booking['booking_id'] ?>" 
                                                               class="btn btn-sm btn-danger"
                                                               onclick="return confirm('Xoa dich vu nay?')">
                                                                <i class="feather icon-trash"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <?php endif; ?>

                                        <div class="alert alert-warning mt-3">
                                            <i class="feather icon-alert-triangle"></i>
                                            <strong>Lưu ý:</strong> Thay đổi thông tin booking có thể ảnh hưởng đến lịch trình và thanh toán.
                                        </div>

                                        <div class="form-group mt-3">
                                            <button type="submit" class="btn btn-primary btn-lg">
                                                <i class="feather icon-save"></i> Cập nhật booking
                                            </button>
                                            <a href="?act=chi-tiet-booking&id=<?= $booking['booking_id'] ?>" class="btn btn-secondary btn-lg">
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
<!-- END: Content-->

<script>
    function toggleBookingType() {
        const isGroup = document.getElementById('type_group').checked;
        document.getElementById('customer-section').style.display = isGroup ? 'none' : 'block';
        document.getElementById('group-section').style.display = isGroup ? 'block' : 'none';

        // Toggle required
        const customerSelect = document.getElementById('customer_id');
        customerSelect.required = !isGroup;
        
        // Xóa giá trị customer_id khi chọn Đoàn
        if (isGroup) {
            customerSelect.value = '';
        }
        
        document.getElementById('organization_name').required = isGroup;
        document.getElementById('contact_name').required = isGroup;
        document.getElementById('contact_phone').required = isGroup;
    }

    // Hàm lấy dữ liệu lịch khởi hành từ API khi thay đổi ngày
    function loadScheduleData() {
        const tourId = document.getElementById('tour_id').value;
        const tourDate = document.getElementById('tour_date').value;
        
        if (!tourId || !tourDate) {
            return;
        }
        
        // AJAX call to fetch schedule data
        fetch('?act=api-schedule-data&tour_id=' + encodeURIComponent(tourId) + '&tour_date=' + encodeURIComponent(tourDate))
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data) {
                    const schedule = data.data;
                    console.log('Schedule data:', schedule);
                    
                    // Cập nhật hiển thị thông tin lịch
                    // Reload page để hiển thị thông tin lịch mới
                    location.reload(); // Reload to show updated schedule information
                }
            })
            .catch(err => console.error('Error loading schedule:', err));
    }

    // Khởi tạo trạng thái form khi load
    document.addEventListener('DOMContentLoaded', function() {
        toggleBookingType();
        
        // Validation trước khi submit
        document.getElementById('bookingForm').addEventListener('submit', function(e) {
            const isGroup = document.getElementById('type_group').checked;
            const customerId = document.getElementById('customer_id').value;
            const tourId = document.getElementById('tour_id').value;
            const numAdults = parseInt(document.getElementById('num_adults').value) || 0;
            
            if (!tourId) {
                alert('Vui lòng chọn tour!');
                e.preventDefault();
                return false;
            }
            
            if (numAdults < 1) {
                alert('Số người lớn phải >= 1!');
                e.preventDefault();
                return false;
            }
            
            if (!isGroup && !customerId) {
                alert('Vui lòng chọn khách hàng!');
                e.preventDefault();
                return false;
            }
            
            if (isGroup) {
                const orgName = document.getElementById('organization_name').value.trim();
                const contactName = document.getElementById('contact_name').value.trim();
                const contactPhone = document.getElementById('contact_phone').value.trim();
                
                if (!orgName || !contactName || !contactPhone) {
                    alert('Vui lòng điền đầy đủ thông tin công ty/tổ chức!');
                    e.preventDefault();
                    return false;
                }
            }
            
            return true;
        });
    });
</script>

<?php require_once __DIR__ . '/../core/footer.php'; ?>
