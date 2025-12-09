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

                                        <!-- Thông tin khách liên hệ -->
                                        <h5 class="mt-2 mb-1">Thông tin khách/đơn vị liên hệ</h5>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="customer_name">Họ tên</label>
                                                    <input type="text" name="customer_name" id="customer_name"
                                                           class="form-control" placeholder="VD: Nguyễn Văn A">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="customer_phone">SĐT</label>
                                                    <input type="tel" name="customer_phone" id="customer_phone"
                                                           class="form-control" placeholder="VD: 0901234567">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="customer_email">Email</label>
                                                    <input type="email" name="customer_email" id="customer_email"
                                                           class="form-control" placeholder="VD: khach@congty.com">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="max_participants">Số người tối đa</label>
                                            <input type="number" name="max_participants" id="max_participants" 
                                                   class="form-control" placeholder="VD: 30" min="1" value="30">
                                        </div>

                                        <!-- Số lượng khách -->
                                        <h5 class="mt-3 mb-2">Số lượng khách dự kiến</h5>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="num_adults">Người lớn (NL)</label>
                                                    <input type="number" name="num_adults" id="num_adults" 
                                                           class="form-control" placeholder="VD: 25" min="0" value="0">
                                                    <small class="text-muted">Từ 12 tuổi trở lên</small>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="num_children">Trẻ em (TE)</label>
                                                    <input type="number" name="num_children" id="num_children" 
                                                           class="form-control" placeholder="VD: 5" min="0" value="0">
                                                    <small class="text-muted">Từ 6-11 tuổi</small>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="num_infants">Em bé</label>
                                                    <input type="number" name="num_infants" id="num_infants" 
                                                           class="form-control" placeholder="VD: 0" min="0" value="0">
                                                    <small class="text-muted">Dưới 6 tuổi</small>
                                                </div>
                                            </div>
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

                                        <!-- Lịch trình chi tiết của tour -->
                                        <div class="card border-left-primary mt-3" id="itinerary-section" style="display:none;">
                                            <div class="card-header bg-light">
                                                <h5 class="card-title mb-0">
                                                    <i class="feather icon-calendar"></i> Lịch trình chi tiết tour
                                                </h5>
                                            </div>
                                            <div class="card-body" id="itinerary-content">
                                                <!-- Itineraries will be loaded here dynamically -->
                                            </div>
                                        </div>

                                        <div class="alert alert-info">
                                            <i class="feather icon-info"></i>
                                            <strong>Lưu ý:</strong> Sau khi tạo lịch khởi hành, bạn có thể phân công nhân sự và dịch vụ ở trang chi tiết.
                                        </div>

                                        <!-- Hiển thị tóm tắt khách hàng -->
                                        <div id="guestSummaryCard" class="alert alert-secondary" style="display:none;">
                                            <h6 class="mb-2"><i class="feather icon-users"></i> Tóm tắt khách hàng hiện tại</h6>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <p><strong>Người lớn:</strong> <span id="adultCount">0</span></p>
                                                </div>
                                                <div class="col-md-4">
                                                    <p><strong>Trẻ em:</strong> <span id="childCount">0</span></p>
                                                </div>
                                                <div class="col-md-4">
                                                    <p><strong>Tổng cộng:</strong> <span id="totalGuests">0</span></p>
                                                </div>
                                            </div>
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
// Fetch guest summary and itineraries when tour is selected
document.getElementById('tour_id').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const tourId = this.value;
    
    if (!tourId) {
        document.getElementById('guestSummaryCard').style.display = 'none';
        document.getElementById('itinerary-section').style.display = 'none';
        return;
    }
    
    // AJAX call to fetch guest summary for this tour
    fetch('?act=api-guest-summary&tour_id=' + encodeURIComponent(tourId))
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('adultCount').textContent = data.adult_count || 0;
                document.getElementById('childCount').textContent = data.child_count || 0;
                document.getElementById('totalGuests').textContent = (data.adult_count || 0) + (data.child_count || 0);
                document.getElementById('guestSummaryCard').style.display = 'block';
            }
        })
        .catch(err => console.error('Error fetching guest summary:', err));

    // AJAX call to fetch tour itineraries
    fetch('?act=api-tour-itineraries&tour_id=' + encodeURIComponent(tourId))
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data && data.data.length > 0) {
                const container = document.getElementById('itinerary-content');
                container.innerHTML = '';
                
                data.data.forEach(itinerary => {
                    const dayCard = document.createElement('div');
                    dayCard.className = 'card border-left-info mb-2';
                    dayCard.innerHTML = `
                        <div class="card-body py-3">
                            <div class="row">
                                <div class="col-md-2">
                                    <h6>
                                        <span class="badge badge-info">Ngày ${itinerary.day_number}</span>
                                    </h6>
                                </div>
                                <div class="col-md-10">
                                    <h6 class="mb-1"><strong>${htmlEscape(itinerary.title)}</strong></h6>
                                    <p class="mb-1 text-muted">
                                        <small>${htmlEscape(itinerary.description || 'Không có mô tả')}</small>
                                    </p>
                                    ${itinerary.accommodation ? `
                                        <p class="mb-0">
                                            <small><strong>Chỗ ở:</strong> ${htmlEscape(itinerary.accommodation)}</small>
                                        </p>
                                    ` : ''}
                                </div>
                            </div>
                        </div>
                    `;
                    container.appendChild(dayCard);
                });
                
                document.getElementById('itinerary-section').style.display = 'block';
            } else {
                document.getElementById('itinerary-section').style.display = 'none';
            }
        })
        .catch(err => console.error('Error fetching itineraries:', err));
});

// Helper function to escape HTML
function htmlEscape(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Validate guest count updates
const guestFields = ['num_adults', 'num_children', 'num_infants'];
guestFields.forEach(field => {
    const element = document.getElementById(field);
    if (element) {
        element.addEventListener('change', function () {
            const adults = parseInt(document.getElementById('num_adults').value) || 0;
            const children = parseInt(document.getElementById('num_children').value) || 0;
            const infants = parseInt(document.getElementById('num_infants').value) || 0;
            const totalGuests = adults + children + infants;
            const maxParticipants = parseInt(document.getElementById('max_participants').value) || 0;

            if (totalGuests > maxParticipants) {
                const message = `Lỗi: Tổng số khách (${totalGuests}) vượt quá số chỗ tối đa (${maxParticipants})!`;
                alert(message);
                this.focus();
            }
        });
    }
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
