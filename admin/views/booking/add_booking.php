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

                                        <!-- Loại Booking -->
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
                                            <!-- Tour -->
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="tour_id">Tour <span class="text-danger">*</span></label>
                                                    <select name="tour_id" id="tour_id" class="form-control" required
                                                        onchange="updateTourPrice()">
                                                        <option value="">-- Chọn tour --</option>
                                                        <?php foreach ($tours as $tour): ?>
                                                            <option value="<?= $tour['tour_id'] ?>"
                                                                data-price-adult="<?= $tour['price_adult'] ?>"
                                                                data-price-child="<?= $tour['price_child'] ?>"
                                                                data-duration="<?= $tour['duration_days'] ?>">
                                                                <?= htmlspecialchars($tour['tour_name']) ?>
                                                                <?php if ($tour['price_adult'] > 0): ?>
                                                                    - <?= number_format($tour['price_adult'], 0, ',', '.') ?>
                                                                    đ/NL
                                                                <?php endif; ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Ngày khởi hành mong muốn -->
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="tour_date">Ngày khởi hành mong muốn</label>
                                                    <input type="date" name="tour_date" id="tour_date"
                                                        class="form-control" min="<?= date('Y-m-d') ?>">
                                                    <small class="text-muted">Hệ thống sẽ kiểm tra chỗ trống</small>
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

                                        <!-- Số lượng khách -->
                                        <h5 class="mt-2 mb-1"><i class="feather icon-users"></i> Số lượng khách</h5>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="num_adults">Người lớn <span
                                                            class="text-danger">*</span></label>
                                                    <input type="number" name="num_adults" id="num_adults"
                                                        class="form-control" min="1" value="1" required
                                                        onchange="calculateTotal()">
                                                    <small class="text-muted">Từ 12 tuổi trở lên</small>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="num_children">Trẻ em</label>
                                                    <input type="number" name="num_children" id="num_children"
                                                        class="form-control" min="0" value="0"
                                                        onchange="calculateTotal()">
                                                    <small class="text-muted">Từ 6-11 tuổi</small>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="num_infants">Em bé</label>
                                                    <input type="number" name="num_infants" id="num_infants"
                                                        class="form-control" min="0" value="0"
                                                        onchange="calculateTotal()">
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
                                                        <option value="Chờ xác nhận" selected>Chờ xác nhận</option>
                                                        <option value="Đã đặt cọc">Đã đặt cọc</option>
                                                        <option value="Hoàn tất">Hoàn tất</option>
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
                                                <li>Hệ thống sẽ tự động kiểm tra chỗ trống nếu bạn chọn ngày khởi hành
                                                </li>
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

    function updateTourPrice() {
        calculateTotal();
    }

    function calculateTotal() {
        const tourSelect = document.getElementById('tour_id');
        const selectedOption = tourSelect.options[tourSelect.selectedIndex];
        const priceAdult = parseFloat(selectedOption.getAttribute('data-price-adult')) || 0;
        const priceChild = parseFloat(selectedOption.getAttribute('data-price-child')) || 0;

        const numAdults = parseInt(document.getElementById('num_adults').value) || 0;
        const numChildren = parseInt(document.getElementById('num_children').value) || 0;
        const numInfants = parseInt(document.getElementById('num_infants').value) || 0;

        // Tính: người lớn = price_adult, trẻ em = price_child, em bé = 10% price_child
        const total = (numAdults * priceAdult) + (numChildren * priceChild) + (numInfants * priceChild * 0.1);

        document.getElementById('total_amount').value = Math.round(total);
    }

    function addServiceRow() {
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
        calculateTotal();

        // Thêm validation trước khi submit
        document.getElementById('bookingForm').addEventListener('submit', function (e) {
            const isGroup = document.getElementById('type_group').checked;
            const customerId = document.getElementById('customer_id').value;
            const tourId = document.getElementById('tour_id').value;
            const numAdults = parseInt(document.getElementById('num_adults').value) || 0;

            // Validate tour
            if (!tourId) {
                alert('Vui lòng chọn tour!');
                e.preventDefault();
                return false;
            }

            // Validate số người lớn
            if (numAdults < 1) {
                alert('Số người lớn phải >= 1!');
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