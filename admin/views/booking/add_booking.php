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
                        <h2 class="content-header-title float-left mb-0">Tạo Booking Mới</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="?act=danh-sach-booking">Danh sách booking</a></li>
                                <li class="breadcrumb-item active">Tạo mới</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">
            <section id="add-booking">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Thông tin booking</h4>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <form action="?act=luu-booking" method="POST" id="bookingForm">

                                        <?php if (isset($prefill) && is_array($prefill)): ?>
                                            <input type="hidden" name="schedule_id"
                                                value="<?= (int) $prefill['schedule_id'] ?>">
                                            <input type="hidden" id="override_price_adult"
                                                value="<?= (float) $prefill['price_adult'] ?>">
                                            <input type="hidden" id="override_price_child"
                                                value="<?= (float) $prefill['price_child'] ?>">
                                        <?php endif; ?>

                                        <!-- Loại Booking -->
                                        <div class="col-md-6 d-flex align-items-center">
                                            <div class="custom-control custom-checkbox mt-2">
                                                <input type="checkbox" class="custom-control-input" id="allow_overbook"
                                                    name="allow_overbook" value="1">
                                                <label class="custom-control-label" for="allow_overbook">
                                                    Cho phép vượt số chỗ (Admin)
                                                </label>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label>Loại booking <span class="text-danger">*</span></label>
                                                    <div class="d-flex">
                                                        <div class="custom-control custom-radio mr-3">
                                                            <input type="radio" id="type_personal" name="booking_type"
                                                                value="Cá nhân" class="custom-control-input" checked
                                                                onchange="toggleBookingType()">
                                                            <label class="custom-control-label" for="type_personal">
                                                                <i class="feather icon-user"></i> Khách lẻ (1-2 người)
                                                            </label>
                                                        </div>
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" id="type_group" name="booking_type"
                                                                value="Đoàn" class="custom-control-input"
                                                                onchange="toggleBookingType()">
                                                            <label class="custom-control-label" for="type_group">
                                                                <i class="feather icon-users"></i> Đoàn (Công ty/Tổ
                                                                chức)
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <!-- Lịch khởi hành -->
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="schedule_id">Chọn lịch khởi hành <span class="text-danger">*</span></label>
                                                    <select name="schedule_id" id="schedule_id" class="form-control" required
                                                        onchange="updateScheduleInfo()">
                                                        <option value="">-- Chọn lịch khởi hành --</option>
                                                        <?php foreach ($schedules as $sch): ?>
                                                            <option value="<?= $sch['schedule_id'] ?>" 
                                                                <?= (isset($prefill) && (int)$prefill['schedule_id'] === (int)$sch['schedule_id']) ? 'selected' : '' ?>
                                                                data-tour-name="<?= htmlspecialchars($sch['tour_name']) ?>"
                                                                data-tour-code="<?= htmlspecialchars($sch['tour_code']) ?>"
                                                                data-departure="<?= $sch['departure_date'] ?>"
                                                                data-return="<?= $sch['return_date'] ?>"
                                                                data-price-adult="<?= $sch['price_adult'] ?>"
                                                                data-price-child="<?= $sch['price_child'] ?>"
                                                                data-available="<?= $sch['available_slots'] ?>"
                                                                data-meeting-point="<?= htmlspecialchars($sch['meeting_point']) ?>"
                                                                data-meeting-time="<?= $sch['meeting_time'] ?>"
                                                                data-num-adults="<?= (int) ($sch['num_adults'] ?? 0) ?>"
                                                                data-num-children="<?= (int) ($sch['num_children'] ?? 0) ?>"
                                                                data-num-infants="<?= (int) ($sch['num_infants'] ?? 0) ?>">
                                                                [<?= date('d/m/Y', strtotime($sch['departure_date'])) ?>] 
                                                                <?= htmlspecialchars($sch['tour_name']) ?> 
                                                                - NL: <?= number_format($sch['price_adult'], 0, ',', '.') ?>đ
                                                                / TE: <?= number_format($sch['price_child'], 0, ',', '.') ?>đ
                                                                (Còn <?= $sch['available_slots'] ?> chỗ)
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <small class="text-muted">Chọn lịch khởi hành cụ thể với giá và ngày đã được xác định</small>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Thông tin lịch được chọn (Read-only từ schedule) -->
                                        <div id="schedule-info" class="alert alert-info" style="display:none;">
                                            <h6 class="mb-1"><i class="feather icon-info"></i> Thông tin lịch khởi hành (từ hợp đồng):</h6>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <strong>Tour:</strong> <span id="info-tour-name"></span><br>
                                                    <strong>Mã:</strong> <span id="info-tour-code"></span><br>
                                                    <strong>Khởi hành:</strong> <span id="info-departure"></span>
                                                </div>
                                                <div class="col-md-4">
                                                    <strong>Kết thúc:</strong> <span id="info-return"></span><br>
                                                    <strong>Giá NL:</strong> <span id="info-price-adult"></span> đ<br>
                                                    <strong>Giá TE:</strong> <span id="info-price-child"></span> đ
                                                </div>
                                                <div class="col-md-4">
                                                    <strong>Người lớn:</strong> <span id="info-num-adults"></span><br>
                                                    <strong>Trẻ em:</strong> <span id="info-num-children"></span><br>
                                                    <strong>Em bé:</strong> <span id="info-num-infants"></span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Thông tin khách hàng (Cá nhân) -->
                                        <div id="customer-section">
                                            <h5 class="mt-2 mb-1"><i class="feather icon-user"></i> Thông tin khách hàng
                                            </h5>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="customer_id">Khách hàng <span
                                                                class="text-danger customer-required">*</span></label>
                                                        <select name="customer_id" id="customer_id"
                                                            class="form-control">
                                                            <option value="">-- Chọn khách hàng --</option>
                                                            <?php foreach ($customers as $customer): ?>
                                                                <option value="<?= $customer['customer_id'] ?>">
                                                                    <?= htmlspecialchars($customer['full_name']) ?> -
                                                                    <?= htmlspecialchars($customer['phone']) ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Thông tin đoàn -->
                                        <div id="group-section" style="display:none;">
                                            <h5 class="mt-2 mb-1"><i class="feather icon-briefcase"></i> Thông tin công
                                                ty/tổ chức</h5>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="organization_name">Tên công ty/tổ chức <span
                                                                class="text-danger group-required">*</span></label>
                                                        <input type="text" name="organization_name"
                                                            id="organization_name" class="form-control"
                                                            placeholder="VD: Công ty TNHH ABC">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="contact_name">Người liên hệ <span
                                                                class="text-danger group-required">*</span></label>
                                                        <input type="text" name="contact_name" id="contact_name"
                                                            class="form-control" placeholder="Họ tên">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="contact_phone">SĐT liên hệ <span
                                                                class="text-danger group-required">*</span></label>
                                                        <input type="tel" name="contact_phone" id="contact_phone"
                                                            class="form-control" placeholder="0987654321">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="contact_email">Email liên hệ</label>
                                                        <input type="email" name="contact_email" id="contact_email"
                                                            class="form-control" placeholder="email@company.com">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Số lượng khách (Read-only từ schedule) -->
                                        <h5 class="mt-2 mb-1"><i class="feather icon-users"></i> Số lượng khách (theo hợp đồng lịch)</h5>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="num_adults_display">Người lớn</label>
                                                    <input type="text" id="num_adults_display"
                                                        class="form-control" readonly>
                                                    <input type="hidden" name="num_adults" id="num_adults">
                                                    <small class="text-muted">Từ 12 tuổi trở lên</small>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="num_children_display">Trẻ em</label>
                                                    <input type="text" id="num_children_display"
                                                        class="form-control" readonly>
                                                    <input type="hidden" name="num_children" id="num_children">
                                                    <small class="text-muted">Từ 6-11 tuổi</small>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="num_infants_display">Em bé</label>
                                                    <input type="text" id="num_infants_display"
                                                        class="form-control" readonly>
                                                    <input type="hidden" name="num_infants" id="num_infants">
                                                    <small class="text-muted">Dưới 6 tuổi</small>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Yêu cầu đặc biệt -->
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="special_requests"><i
                                                            class="feather icon-message-square"></i> Yêu cầu đặc
                                                        biệt</label>
                                                    <textarea name="special_requests" id="special_requests"
                                                        class="form-control" rows="3"
                                                        placeholder="VD: Ăn chay, dị ứng thực phẩm, xe lăn, yêu cầu phòng riêng..."></textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Tổng tiền & Trạng thái -->
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="total_amount">Tổng tiền <span
                                                            class="text-danger">*</span></label>
                                                    <input type="number" name="total_amount" id="total_amount"
                                                        class="form-control" required readonly>
                                                    <small class="text-muted">Tự động tính dựa trên số lượng
                                                        khách</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="status">Trạng thái</label>
                                                    <select name="status" id="status" class="form-control">
                                                        <option value="Giữ chỗ" selected>Giữ chỗ</option>
                                                        <option value="Đã đặt cọc">Đã đặt cọc</option>
                                                        <option value="Đã thanh toán">Đã thanh toán</option>
                                                        <option value="Đã hoàn thành">Đã hoàn thành</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Dịch vụ bổ sung -->
                                        <h5 class="mt-2 mb-1"><i class="feather icon-package"></i> Dịch vụ bổ sung
                                            (không bắt buộc)</h5>
                                        <div id="services-container">
                                            <div class="row service-row mb-2">
                                                <div class="col-md-5">
                                                    <input type="text" name="service_name[]" class="form-control"
                                                        placeholder="Tên dịch vụ (VD: Bảo hiểm, Vé tham quan thêm...)">
                                                </div>
                                                <div class="col-md-3">
                                                    <input type="number" name="quantity[]" class="form-control"
                                                        placeholder="Số lượng" min="1" value="1">
                                                </div>
                                                <div class="col-md-3">
                                                    <input type="number" name="unit_price[]" class="form-control"
                                                        placeholder="Đơn giá" min="0">
                                                </div>
                                                <div class="col-md-1">
                                                    <button type="button" class="btn btn-success btn-sm"
                                                        onclick="addServiceRow()">
                                                        <i class="feather icon-plus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="alert alert-info mt-3">
                                            <h5><i class="feather icon-info"></i> Lưu ý:</h5>
                                            <ul class="mb-0">
                                                <li>Hệ thống sẽ kiểm tra thông tin booking trước khi tạo</li>
                                                <li>Booking sẽ được xác nhận tạm thời, cần duyệt bởi admin</li>
                                                <li>Đoàn đông người vui lòng liên hệ trước để đảm bảo chỗ</li>
                                            </ul>
                                        </div>

                                        <div class="form-group mt-3">
                                            <button type="submit" class="btn btn-primary btn-lg">
                                                <i class="feather icon-save"></i> Tạo booking & Xác nhận tạm thời
                                            </button>
                                            <a href="?act=danh-sach-booking" class="btn btn-secondary btn-lg">
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
    // Initialize when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        const scheduleSelect = document.getElementById('schedule_id');

        // If schedule prefilled, just render it
        if (scheduleSelect && scheduleSelect.value) {
            updateScheduleInfo();
            return;
        }

        // Auto-select first available schedule to show dữ liệu cho người dùng
        if (scheduleSelect && scheduleSelect.options.length > 1) {
            scheduleSelect.selectedIndex = 1; // skip placeholder
            updateScheduleInfo();
        }
    });

    function toggleBookingType() {
        const isGroup = document.getElementById('type_group').checked;
        document.getElementById('customer-section').style.display = isGroup ? 'none' : 'block';
        document.getElementById('group-section').style.display = isGroup ? 'block' : 'none';

        // Toggle required
        const customerSelect = document.getElementById('customer_id');
        customerSelect.required = !isGroup;

        // Xóa giá trị customer_id khi chọn Đoàn để tránh gửi empty string
        if (isGroup) {
            customerSelect.value = '';
        }

        document.getElementById('organization_name').required = isGroup;
        document.getElementById('contact_name').required = isGroup;
        document.getElementById('contact_phone').required = isGroup;
    }

    function updateScheduleInfo() {
        const scheduleSelect = document.getElementById('schedule_id');
        const selectedOption = scheduleSelect.options[scheduleSelect.selectedIndex];
        
        if (!selectedOption.value) {
            document.getElementById('schedule-info').style.display = 'none';
            return;
        }

        // Hiển thị thông tin lịch (đặc biệt là số lượng khách)
        document.getElementById('info-tour-name').textContent = selectedOption.getAttribute('data-tour-name');
        document.getElementById('info-tour-code').textContent = selectedOption.getAttribute('data-tour-code');
        document.getElementById('info-departure').textContent = new Date(selectedOption.getAttribute('data-departure')).toLocaleDateString('vi-VN');
        document.getElementById('info-return').textContent = new Date(selectedOption.getAttribute('data-return')).toLocaleDateString('vi-VN');
        document.getElementById('info-price-adult').textContent = new Intl.NumberFormat('vi-VN').format(selectedOption.getAttribute('data-price-adult'));
        document.getElementById('info-price-child').textContent = new Intl.NumberFormat('vi-VN').format(selectedOption.getAttribute('data-price-child'));
        
        // Lấy số lượng khách từ schedule (từ data attributes)
        const numAdults = parseInt(selectedOption.getAttribute('data-num-adults') || 0);
        const numChildren = parseInt(selectedOption.getAttribute('data-num-children') || 0);
        const numInfants = parseInt(selectedOption.getAttribute('data-num-infants') || 0);
        
        // Gán vào fields display (readonly)
        document.getElementById('num_adults_display').value = numAdults;
        document.getElementById('num_children_display').value = numChildren;
        document.getElementById('num_infants_display').value = numInfants;
        
        // Gán vào hidden fields để submit
        document.getElementById('num_adults').value = numAdults;
        document.getElementById('num_children').value = numChildren;
        document.getElementById('num_infants').value = numInfants;
        
        // Hiển thị thông tin trong schedule-info box
        document.getElementById('info-num-adults').textContent = numAdults;
        document.getElementById('info-num-children').textContent = numChildren;
        document.getElementById('info-num-infants').textContent = numInfants;
        
        document.getElementById('schedule-info').style.display = 'block';
        
        // Tính toán tổng tiền
        calculateTotal();
    }

    function calculateTotal() {
        const scheduleSelect = document.getElementById('schedule_id');
        const selectedOption = scheduleSelect.options[scheduleSelect.selectedIndex];
        
        if (!selectedOption.value) {
            document.getElementById('total_amount').value = 0;
            return;
        }
        
        const priceAdult = parseFloat(selectedOption.getAttribute('data-price-adult')) || 0;
        const priceChild = parseFloat(selectedOption.getAttribute('data-price-child')) || 0;

        const numAdults = parseInt(document.getElementById('num_adults').value) || 0;
        const numChildren = parseInt(document.getElementById('num_children').value) || 0;
        const numInfants = parseInt(document.getElementById('num_infants').value) || 0;

        // Tính: người lớn = price_adult, trẻ em = price_child, em bé = 10% price_child
        const total = (numAdults * priceAdult) + (numChildren * priceChild) + (numInfants * priceChild * 0.1);

        document.getElementById('total_amount').value = Math.round(total);
    }    function addServiceRow() {
        const container = document.getElementById('services-container');
        const newRow = document.createElement('div');
        newRow.className = 'row service-row mb-2';
        newRow.innerHTML = `
        <div class="col-md-5">
            <input type="text" name="service_name[]" class="form-control" placeholder="Tên dịch vụ">
        </div>
        <div class="col-md-3">
            <input type="number" name="quantity[]" class="form-control" placeholder="Số lượng" min="1" value="1">
        </div>
        <div class="col-md-3">
            <input type="number" name="unit_price[]" class="form-control" placeholder="Đơn giá" min="0">
        </div>
        <div class="col-md-1">
            <button type="button" class="btn btn-danger btn-sm" onclick="removeServiceRow(this)">
                <i class="feather icon-minus"></i>
            </button>
        </div>
    `;
        container.appendChild(newRow);
    }

    function removeServiceRow(btn) {
        btn.closest('.service-row').remove();
    }

    // Auto-calculate on page load
    document.addEventListener('DOMContentLoaded', function () {
        // Nếu đã có schedule được chọn sẵn, hiển thị info
        const scheduleSelect = document.getElementById('schedule_id');
        if (scheduleSelect.value) {
            updateScheduleInfo();
        }

        // Thêm validation trước khi submit
        document.getElementById('bookingForm').addEventListener('submit', function (e) {
            const isGroup = document.getElementById('type_group').checked;
            const customerId = document.getElementById('customer_id').value;
            const scheduleId = document.getElementById('schedule_id').value;
            const numAdults = parseInt(document.getElementById('num_adults').value) || 0;

            // Validate schedule
            if (!scheduleId) {
                alert('Vui lòng chọn lịch khởi hành!');
                e.preventDefault();
                return false;
            }

            // Validate số người lớn (phải > 0 từ schedule)
            if (numAdults < 1) {
                alert('Lịch khởi hành phải có ít nhất 1 người lớn!');
                e.preventDefault();
                return false;
            }

            // Validate customer_id cho booking cá nhân
            if (!isGroup && !customerId) {
                alert('Vui lòng chọn khách hàng!');
                e.preventDefault();
                return false;
            }

            // Validate thông tin đoàn
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