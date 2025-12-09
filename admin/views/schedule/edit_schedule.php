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
                        <h2 class="content-header-title float-left mb-0">Chỉnh sửa lịch khởi hành</h2>
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
                                    <form action="?act=cap-nhat-lich-khoi-hanh&id=<?= $schedule['schedule_id'] ?>"
                                        method="POST">
                                        <div class="form-group">
                                            <label for="tour_id">Tour <span class="text-danger">*</span></label>
                                            <select name="tour_id" id="tour_id" class="form-control" required>
                                                <option value="">-- Chọn tour --</option>
                                                <?php if (!empty($tours)): ?>
                                                    <?php foreach ($tours as $tour): ?>
                                                        <option value="<?= $tour['tour_id'] ?>"
                                                            <?= ($schedule['tour_id'] == $tour['tour_id']) ? 'selected' : '' ?>>
                                                            <?= htmlspecialchars($tour['tour_name']) ?> (<?= $tour['code'] ?>)
                                                        </option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <!-- Thông tin khách liên hệ -->
                                                    <h5 class="mt-2 mb-1">Thông tin khách/đơn vị liên hệ</h5>
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="customer_name">Họ tên</label>
                                                                <input type="text" name="customer_name"
                                                                    id="customer_name" class="form-control"
                                                                    value="<?= htmlspecialchars($schedule['customer_name'] ?? '') ?>"
                                                                    placeholder="VD: Nguyễn Văn A">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="customer_phone">SĐT</label>
                                                                <input type="tel" name="customer_phone"
                                                                    id="customer_phone" class="form-control"
                                                                    value="<?= htmlspecialchars($schedule['customer_phone'] ?? '') ?>"
                                                                    placeholder="VD: 0901234567">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="customer_email">Email</label>
                                                                <input type="email" name="customer_email"
                                                                    id="customer_email" class="form-control"
                                                                    value="<?= htmlspecialchars($schedule['customer_email'] ?? '') ?>"
                                                                    placeholder="VD: khach@congty.com">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <label for="departure_date">Ngày khởi hành <span
                                                            class="text-danger">*</span></label>
                                                    <input type="date" name="departure_date" id="departure_date"
                                                        class="form-control" value="<?= $schedule['departure_date'] ?>"
                                                        required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="return_date">Ngày kết thúc</label>
                                                    <input type="date" name="return_date" id="return_date"
                                                        class="form-control"
                                                        value="<?= $schedule['return_date'] ?? '' ?>">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-8">
                                                <div class="form-group">
                                                    <label for="meeting_point">Điểm tập trung</label>
                                                    <input type="text" name="meeting_point" id="meeting_point"
                                                        class="form-control"
                                                        value="<?= htmlspecialchars($schedule['meeting_point'] ?? '') ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="meeting_time">Giờ tập trung</label>
                                                    <input type="time" name="meeting_time" id="meeting_time"
                                                        class="form-control"
                                                        value="<?= $schedule['meeting_time'] ?? '' ?>">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="max_participants">Số người tối đa</label>
                                            <input type="number" name="max_participants" id="max_participants"
                                                class="form-control" value="<?= $schedule['max_participants'] ?? 0 ?>"
                                                min="1">
                                        </div>

                                        <!-- Số lượng khách -->
                                        <h5 class="mt-3 mb-2">Số lượng khách dự kiến</h5>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="num_adults">Người lớn (NL)</label>
                                                    <input type="number" name="num_adults" id="num_adults"
                                                        class="form-control" value="<?= $schedule['num_adults'] ?? 0 ?>"
                                                        min="0">
                                                    <small class="text-muted">Từ 12 tuổi trở lên</small>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="num_children">Trẻ em (TE)</label>
                                                    <input type="number" name="num_children" id="num_children"
                                                        class="form-control"
                                                        value="<?= $schedule['num_children'] ?? 0 ?>" min="0">
                                                    <small class="text-muted">Từ 6-11 tuổi</small>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="num_infants">Em bé</label>
                                                    <input type="number" name="num_infants" id="num_infants"
                                                        class="form-control"
                                                        value="<?= $schedule['num_infants'] ?? 0 ?>" min="0">
                                                    <small class="text-muted">Dưới 6 tuổi</small>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="price_adult">Giá người lớn (VNĐ) <span
                                                            class="text-danger">*</span></label>
                                                    <input type="number" name="price_adult" id="price_adult"
                                                        class="form-control"
                                                        value="<?= $schedule['price_adult'] ?? 0 ?>" min="0" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="price_child">Giá trẻ em (VNĐ)</label>
                                                    <input type="number" name="price_child" id="price_child"
                                                        class="form-control"
                                                        value="<?= $schedule['price_child'] ?? 0 ?>" min="0">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="status">Trạng thái</label>
                                            <select name="status" id="status" class="form-control">
                                                <option value="Open" <?= ($schedule['status'] == 'Open') ? 'selected' : '' ?>>Mở đặt tour</option>
                                                <option value="Confirmed" <?= ($schedule['status'] == 'Confirmed') ? 'selected' : '' ?>>Đã xác nhận</option>
                                                <option value="Full" <?= ($schedule['status'] == 'Full') ? 'selected' : '' ?>>Đã đầy chỗ</option>
                                                <option value="Completed" <?= ($schedule['status'] == 'Completed') ? 'selected' : '' ?>>Hoàn thành</option>
                                                <option value="Cancelled" <?= ($schedule['status'] == 'Cancelled') ? 'selected' : '' ?>>Đã hủy</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="notes">Ghi chú</label>
                                            <textarea name="notes" id="notes" class="form-control"
                                                rows="3"><?= htmlspecialchars($schedule['notes'] ?? '') ?></textarea>
                                        </div>

                                        <div class="alert alert-warning">
                                            <i class="feather icon-alert-triangle"></i>
                                            <strong>Cảnh báo:</strong> Thay đổi ngày khởi hành có thể ảnh hưởng đến lịch
                                            của nhân sự đã phân công.
                                        </div>

                                        <div class="alert alert-info">
                                            <i class="feather icon-info"></i>
                                            <strong>Tự động đồng bộ:</strong> Khi bạn cập nhật lịch, tất cả thông tin
                                            booking liên quan sẽ tự động cập nhật (giá, điểm tập trung, giờ tập trung,
                                            v.v.)
                                        </div>

                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary mr-1">
                                                <i class="feather icon-save"></i> Cập nhật
                                            </button>
                                            <a href="?act=chi-tiet-lich-khoi-hanh&id=<?= $schedule['schedule_id'] ?>"
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
            </section>
        </div>
    </div>
</div>

<script>
    // Validate ngày kết thúc phải sau ngày khởi hành
    document.getElementById('return_date').addEventListener('change', function () {
        const departureDate = document.getElementById('departure_date').value;
        const returnDate = this.value;

        if (departureDate && returnDate && returnDate < departureDate) {
            alert('Ngày kết thúc phải sau ngày khởi hành!');
            this.value = '';
        }
    });

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
</script>

<!-- END: Content-->
<?php require_once __DIR__ . '/../core/footer.php'; ?>