<?php
/**
 * View: Chi tiết tour dành cho HDV
 * Use Case 1: Bước 4, 5
 * Hiển thị: thông tin chung, thời gian, danh sách địa điểm, hoạt động theo ngày, hình ảnh, nhiệm vụ
 */
?>
<?php require_once './views/core/header.php'; ?>
<?php require_once './views/core/menu.php'; ?>

<div class="container-fluid">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="?act=hdv-lich-cua-toi">Lịch của tôi</a>
                        </li>
                        <li class="breadcrumb-item active">Chi tiết tour</li>
                    </ol>
                </nav>
                <h1 class="page-title"><?= htmlspecialchars($tour['tour_name'] ?? 'Chưa xác định') ?></h1>
                <p class="text-muted">Mã tour: <strong><?= htmlspecialchars($tour['code'] ?? '') ?></strong></p>
            </div>
            <div class="col-auto">
                <a href="?act=hdv-lich-cua-toi" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
                <a href="?act=hdv-xuat-lich&schedule_id=<?= $schedule_id ?>&format=pdf"
                    class="btn btn-danger">
                    <i class="fas fa-file-pdf"></i> Xuất PDF
                </a>
                <a href="?act=hdv-xuat-lich&schedule_id=<?= $schedule_id ?>&format=excel"
                    class="btn btn-success">
                    <i class="fas fa-file-excel"></i> Xuất Excel
                </a>
            </div>
        </div>
    </div>

    <!-- Thẻ thông tin chung (Use Case 1 - Bước 4b) -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle"></i> Thông tin chung
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label text-muted">Mã tour</label>
                                <p class="mb-0">
                                    <strong><?= htmlspecialchars($tour['code'] ?? 'N/A') ?></strong>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label text-muted">Tên tour</label>
                                <p class="mb-0">
                                    <strong><?= htmlspecialchars($tour['tour_name'] ?? '') ?></strong>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label text-muted">Loại tour</label>
                                <p class="mb-0">
                                    <span class="badge bg-info">
                                        <?= htmlspecialchars($tour['category_name'] ?? 'Chưa xác định') ?>
                                    </span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label text-muted">Tổng số ngày</label>
                                <p class="mb-0">
                                    <strong><?= $total_days ?> ngày</strong>
                                </p>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label text-muted">
                                    <i class="fas fa-calendar-alt"></i> Ngày khởi hành
                                </label>
                                <p class="mb-0">
                                    <strong><?= date('d/m/Y H:i', strtotime($schedule['departure_date'])) ?></strong>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label text-muted">
                                    <i class="fas fa-calendar-check"></i> Ngày kết thúc
                                </label>
                                <p class="mb-0">
                                    <strong><?= date('d/m/Y', strtotime($schedule['return_date'] ?? $schedule['departure_date'])) ?></strong>
                                </p>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label text-muted">
                                    <i class="fas fa-map-marker-alt"></i> Điểm tập trung
                                </label>
                                <p class="mb-0">
                                    <?= htmlspecialchars($schedule['meeting_point'] ?? 'Chưa xác định') ?>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label text-muted">
                                    <i class="fas fa-clock"></i> Thời gian tập trung
                                </label>
                                <p class="mb-0">
                                    <?= htmlspecialchars($schedule['meeting_time'] ?? 'Chưa xác định') ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs: Lịch trình, Ảnh, Nhiệm vụ, Chính sách -->
    <div class="row">
        <div class="col-12">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="itinerary-tab" data-bs-toggle="tab"
                        data-bs-target="#itinerary" type="button" role="tab">
                        <i class="fas fa-map"></i> Lịch trình
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="gallery-tab" data-bs-toggle="tab"
                        data-bs-target="#gallery" type="button" role="tab">
                        <i class="fas fa-images"></i> Hình ảnh
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tasks-tab" data-bs-toggle="tab"
                        data-bs-target="#tasks" type="button" role="tab">
                        <i class="fas fa-tasks"></i> Nhiệm vụ của tôi
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="policies-tab" data-bs-toggle="tab"
                        data-bs-target="#policies" type="button" role="tab">
                        <i class="fas fa-file-contract"></i> Chính sách
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="staff-tab" data-bs-toggle="tab"
                        data-bs-target="#staff" type="button" role="tab">
                        <i class="fas fa-people"></i> Đội ngũ
                    </button>
                </li>
            </ul>

            <div class="tab-content">
                <!-- Tab: Lịch trình -->
                <div class="tab-pane fade show active" id="itinerary" role="tabpanel">
                    <div class="card border-top-0 rounded-0">
                        <div class="card-body">
                            <?php if (!empty($itineraries)): ?>
                                <?php foreach ($itineraries as $iti): ?>
                                <div class="timeline-item mb-4">
                                    <div class="timeline-marker bg-primary">
                                        <i class="fas fa-circle text-white"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">
                                            <strong>Ngày <?= $iti['day_number'] ?>: <?= htmlspecialchars($iti['title']) ?></strong>
                                        </h6>
                                        <p class="text-muted small mb-2">
                                            <i class="fas fa-calendar-alt"></i>
                                            <?php
                                            $day_date = clone $departure;
                                            $day_date->modify('+' . ($iti['day_number'] - 1) . ' days');
                                            echo $day_date->format('d/m/Y (l)');
                                            ?>
                                        </p>
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <p class="mb-2">
                                                    <strong>Hoạt động:</strong><br>
                                                    <?= nl2br(htmlspecialchars($iti['description'] ?? '')) ?>
                                                </p>
                                                <?php if (!empty($iti['accommodation'])): ?>
                                                <p class="mb-0">
                                                    <strong>Nơi ở:</strong><br>
                                                    <?= htmlspecialchars($iti['accommodation']) ?>
                                                </p>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i> Chưa có lịch trình nào
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Tab: Hình ảnh -->
                <div class="tab-pane fade" id="gallery" role="tabpanel">
                    <div class="card border-top-0 rounded-0">
                        <div class="card-body">
                            <?php if (!empty($gallery)): ?>
                            <div class="row g-3">
                                <?php foreach ($gallery as $img): ?>
                                <div class="col-md-3 col-sm-6">
                                    <div class="card border shadow-sm">
                                        <img src="<?= htmlspecialchars($img['file_path']) ?>"
                                            class="card-img-top" alt="Tour image"
                                            style="height: 200px; object-fit: cover; cursor: pointer;"
                                            data-bs-toggle="modal" data-bs-target="#imageModal"
                                            onclick="showImage('<?= htmlspecialchars($img['file_path']) ?>')">
                                        <div class="card-body text-center small">
                                            <?php if ($img['is_featured']): ?>
                                            <span class="badge bg-success">Ảnh chính</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php else: ?>
                            <div class="alert alert-warning">
                                <i class="fas fa-image"></i> Chưa có hình ảnh nào
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Tab: Nhiệm vụ của tôi -->
                <div class="tab-pane fade" id="tasks" role="tabpanel">
                    <div class="card border-top-0 rounded-0">
                        <div class="card-body">
                            <p class="text-muted">
                                <i class="fas fa-info-circle"></i>
                                Danh sách công việc chi tiết của bạn được hiển thị tại
                                <a href="?act=hdv-nhiem-vu-cua-toi&schedule_id=<?= $schedule_id ?>">trang Nhiệm vụ</a>
                            </p>
                            <a href="?act=hdv-nhiem-vu-cua-toi&schedule_id=<?= $schedule_id ?>"
                                class="btn btn-primary">
                                <i class="fas fa-tasks"></i> Xem danh sách nhiệm vụ
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Tab: Chính sách -->
                <div class="tab-pane fade" id="policies" role="tabpanel">
                    <div class="card border-top-0 rounded-0">
                        <div class="card-body">
                            <?php if (!empty($policies)): ?>
                            <div class="row">
                                <div class="col-md-6">
                                    <h6><i class="fas fa-ban"></i> Chính sách hủy</h6>
                                    <div class="bg-light p-3 rounded mb-3">
                                        <?= nl2br(htmlspecialchars($policies['cancellation_policy'] ?? 'Chưa cập nhật')) ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h6><i class="fas fa-exchange-alt"></i> Chính sách thay đổi</h6>
                                    <div class="bg-light p-3 rounded mb-3">
                                        <?= nl2br(htmlspecialchars($policies['change_policy'] ?? 'Chưa cập nhật')) ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <h6><i class="fas fa-credit-card"></i> Chính sách thanh toán</h6>
                                    <div class="bg-light p-3 rounded mb-3">
                                        <?= nl2br(htmlspecialchars($policies['payment_policy'] ?? 'Chưa cập nhật')) ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h6><i class="fas fa-sticky-note"></i> Ghi chú</h6>
                                    <div class="bg-light p-3 rounded mb-3">
                                        <?= nl2br(htmlspecialchars($policies['note_policy'] ?? 'Chưa cập nhật')) ?>
                                    </div>
                                </div>
                            </div>
                            <?php else: ?>
                            <div class="alert alert-warning">
                                <i class="fas fa-file-contract"></i> Chưa có chính sách nào
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Tab: Đội ngũ -->
                <div class="tab-pane fade" id="staff" role="tabpanel">
                    <div class="card border-top-0 rounded-0">
                        <div class="card-body">
                            <?php if (!empty($assigned_staff)): ?>
                            <div class="list-group">
                                <?php foreach ($assigned_staff as $staff_member): ?>
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">
                                                <?= htmlspecialchars($staff_member['full_name']) ?>
                                            </h6>
                                            <p class="mb-0 small text-muted">
                                                <strong>Vai trò:</strong> <?= htmlspecialchars($staff_member['role'] ?? 'Chưa xác định') ?>
                                            </p>
                                        </div>
                                        <div class="text-end">
                                            <p class="mb-0 small">
                                                <i class="fas fa-phone text-primary"></i>
                                                <?= htmlspecialchars($staff_member['phone'] ?? 'N/A') ?>
                                            </p>
                                            <p class="mb-0 small text-muted">
                                                <span class="badge bg-info">
                                                    <?= htmlspecialchars($staff_member['staff_type'] ?? 'N/A') ?>
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php else: ?>
                            <div class="alert alert-warning">
                                <i class="fas fa-users"></i> Chưa có đội ngũ nào được phân công
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Xem ảnh lớn -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Hình ảnh tour</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="largeImage" src="" alt="Large image" style="max-width: 100%; height: auto;">
            </div>
        </div>
    </div>
</div>

<script>
function showImage(src) {
    document.getElementById('largeImage').src = src;
}
</script>

<?php require_once './views/core/footer.php'; ?>
