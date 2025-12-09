<?php
/**
 * View: Chi tiết tour dành cho HDV
 * Use Case 1: Bước 4, 5
 * Hiển thị: thông tin chung, thời gian, danh sách địa điểm, hoạt động theo ngày, hình ảnh, nhiệm vụ
 */
?>
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
                        <h2 class="content-header-title float-left mb-0">
                            <i class="feather icon-map"></i>
                            <?= htmlspecialchars($tour['tour_name'] ?? 'Chi tiết tour') ?>
                            <span
                                class="badge badge-<?= $schedule['status'] == 'In Progress' ? 'warning' : ($schedule['status'] == 'Completed' ? 'success' : 'primary') ?> ml-1"><?= htmlspecialchars($schedule['status'] ?? 'Open') ?></span>
                        </h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="?act=hdv-lich-cua-toi">Lịch của tôi</a>
                                </li>
                                <li class="breadcrumb-item active">Chi tiết tour
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-header-right text-md-right col-md-3 col-12 d-md-block d-none">
                <div class="form-group breadcrum-right">
                    <a href="?act=view-tour-journal&schedule_id=<?= $schedule_id ?>"
                        class="btn btn-primary btn-sm mr-1"><i class="feather icon-book-open"></i> Nhật ký</a>
                    <a href="?act=view-tour-feedback&schedule_id=<?= $schedule_id ?>"
                        class="btn btn-info btn-sm mr-1"><i class="feather icon-message-circle"></i> Feedback</a>
                    <a href="?act=hdv-lich-cua-toi" class="btn btn-secondary btn-sm"><i
                            class="feather icon-arrow-left"></i> Quay lại</a>
                </div>
            </div>
        </div>

        <div class="content-body">

            <!-- Thẻ thông tin chung (Use Case 1 - Bước 4b) -->
            <section id="tour-info">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title mb-0">
                                    <i class="feather icon-info"></i> Thông tin Tour
                                </h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-xl-3 col-md-6 mb-2">
                                        <div class="form-group mb-0">
                                            <label class="form-label text-muted small">Mã tour</label>
                                            <p class="mb-0"><span
                                                    class="badge badge-light-primary"><?= htmlspecialchars($tour['code'] ?? 'N/A') ?></span>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-md-6 mb-2">
                                        <div class="form-group mb-0">
                                            <label class="form-label text-muted small">Tên tour</label>
                                            <p class="mb-0">
                                                <strong><?= htmlspecialchars($tour['tour_name'] ?? '') ?></strong>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-md-6 mb-2">
                                        <div class="form-group mb-0">
                                            <label class="form-label text-muted small">Loại tour</label>
                                            <p class="mb-0"><span
                                                    class="badge badge-info"><?= htmlspecialchars($tour['category_name'] ?? 'Chưa xác định') ?></span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <hr class="my-2">
                                <div class="row">
                                    <div class="col-md-4 mb-2">
                                        <div class="form-group mb-0">
                                            <label class="form-label text-muted small"><i
                                                    class="feather icon-calendar"></i> Ngày khởi hành</label>
                                            <p class="mb-0">
                                                <strong><?= date('d/m/Y H:i', strtotime($schedule['departure_date'])) ?></strong>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <div class="form-group mb-0">
                                            <label class="form-label text-muted small"><i
                                                    class="feather icon-calendar"></i> Ngày kết thúc</label>
                                            <p class="mb-0">
                                                <strong><?= date('d/m/Y', strtotime($schedule['return_date'] ?? $schedule['departure_date'])) ?></strong>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <div class="form-group mb-0">
                                            <label class="form-label text-muted small">Thời lượng</label>
                                            <p class="mb-0">
                                                <span class="badge badge-light-success"><?= $total_days ?> ngày</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <hr class="my-2">
                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <div class="form-group mb-0">
                                            <label class="form-label text-muted small">
                                                <i class="feather icon-map-pin"></i> Điểm tập trung
                                            </label>
                                            <p class="mb-0">
                                                <?= htmlspecialchars($schedule['meeting_point'] ?? 'Chưa xác định') ?>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <div class="form-group mb-0">
                                            <label class="form-label text-muted small">
                                                <i class="feather icon-clock"></i> Thời gian tập trung
                                            </label>
                                            <p class="mb-0">
                                                <span
                                                    class="badge badge-warning"><?= htmlspecialchars($schedule['meeting_time'] ?? 'Chưa xác định') ?></span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Tabs: Lịch trình, Ảnh, Nhiệm vụ, Chính sách -->
            <section id="tour-details">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <ul class="nav nav-tabs nav-justified" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="itinerary-tab" data-bs-toggle="tab"
                                        data-bs-target="#itinerary" type="button" role="tab">
                                        <i class="feather icon-map"></i> Lịch trình
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="gallery-tab" data-bs-toggle="tab"
                                        data-bs-target="#gallery" type="button" role="tab">
                                        <i class="feather icon-image"></i> Hình ảnh
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="tasks-tab" data-bs-toggle="tab" data-bs-target="#tasks"
                                        type="button" role="tab">
                                        <i class="feather icon-check-square"></i> Nhiệm vụ
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="policies-tab" data-bs-toggle="tab"
                                        data-bs-target="#policies" type="button" role="tab">
                                        <i class="feather icon-file-text"></i> Chính sách
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="staff-tab" data-bs-toggle="tab" data-bs-target="#staff"
                                        type="button" role="tab">
                                        <i class="feather icon-users"></i> Đội ngũ
                                    </button>
                                </li>
                            </ul>

                            <div class="tab-content">
                                <!-- Tab: Lịch trình -->
                                <div class="tab-pane fade show active" id="itinerary" role="tabpanel">
                                    <div class="card-body">
                                        <!-- Danh sách khách (groupMembers) embedded for HDV -->
                                        <div class="card mb-3 border-primary" style="border: 2px solid #7367f0;">
                                            <div class="card-header bg-light-primary">
                                                <h4 class="card-title mb-0"><i class="feather icon-users"></i> Danh sách
                                                    khách trong đoàn (<?= count($groupMembers ?? []) ?>)</h4>
                                            </div>
                                            <div class="card-body">
                                                <?php if (!empty($groupMembers)): ?>
                                                    <div class="table-responsive">
                                                        <table class="table table-striped">
                                                            <thead>
                                                                <tr>
                                                                    <th>#</th>
                                                                    <th>Họ tên</th>
                                                                    <th>SĐT</th>
                                                                    <th>Email</th>
                                                                    <th>CMND/CCCD</th>
                                                                    <th>Ngày sinh</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php foreach ($groupMembers as $index => $member): ?>
                                                                    <tr>
                                                                        <td><?= $index + 1 ?></td>
                                                                        <td><strong><?= htmlspecialchars($member['full_name']) ?></strong>
                                                                        </td>
                                                                        <td><?= htmlspecialchars($member['phone'] ?? '-') ?>
                                                                        </td>
                                                                        <td><?= htmlspecialchars($member['email'] ?? '-') ?>
                                                                        </td>
                                                                        <td><?= htmlspecialchars($member['id_number'] ?? '-') ?>
                                                                        </td>
                                                                        <td><?= $member['date_of_birth'] ? date('d/m/Y', strtotime($member['date_of_birth'])) : '-' ?>
                                                                        </td>
                                                                    </tr>
                                                                <?php endforeach; ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="alert alert-info">
                                                        <i class="feather icon-info"></i> Chưa có thành viên nào trong danh
                                                        sách đoàn.
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <a href="?act=hdv-diem-danh&schedule_id=<?= $schedule_id ?>"
                                                class="btn btn-success btn-sm">
                                                <i class="feather icon-check"></i> Điểm danh
                                            </a>
                                        </div>
                                        <?php if (!empty($itineraries)): ?>
                                            <?php foreach ($itineraries as $iti): ?>
                                                <div class="timeline-item mb-4">
                                                    <div class="timeline-marker bg-primary">
                                                        <i class="feather icon-circle text-white"></i>
                                                    </div>
                                                    <div class="timeline-content">
                                                        <h6 class="mb-1">
                                                            <strong>Ngày <?= $iti['day_number'] ?>:
                                                                <?= htmlspecialchars($iti['title']) ?></strong>
                                                        </h6>
                                                        <p class="text-muted small mb-2">
                                                            <i class="feather icon-calendar"></i>
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
                                                <i class="feather icon-alert-triangle"></i> Chưa có lịch trình nào
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Tab: Hình ảnh -->
                                <div class="tab-pane fade" id="gallery" role="tabpanel">
                                    <div class="card-body">
                                        <?php if (!empty($gallery)): ?>
                                            <div class="row">
                                                <?php foreach ($gallery as $img): ?>
                                                    <div class="col-xl-3 col-md-4 col-sm-6 mb-3">
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
                                                <i class="feather icon-image"></i> Chưa có hình ảnh nào
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Tab: Nhiệm vụ của tôi -->
                                <div class="tab-pane fade" id="tasks" role="tabpanel">
                                    <div class="card-body">
                                        <p class="text-muted">
                                            <i class="feather icon-info"></i>
                                            Danh sách công việc chi tiết của bạn được hiển thị tại
                                            <a href="?act=hdv-nhiem-vu-cua-toi&schedule_id=<?= $schedule_id ?>">trang
                                                Nhiệm vụ</a>
                                        </p>
                                        <a href="?act=hdv-nhiem-vu-cua-toi&schedule_id=<?= $schedule_id ?>"
                                            class="btn btn-primary">
                                            <i class="feather icon-check-square"></i> Xem danh sách nhiệm vụ
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tab: Chính sách -->
                        <div class="tab-pane fade" id="policies" role="tabpanel">
                            <div class="card-body">
                                <?php if (!empty($policies)): ?>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <h6><i class="feather icon-x-circle"></i> Chính sách hủy</h6>
                                            <div class="bg-light p-3 rounded">
                                                <?= nl2br(htmlspecialchars($policies['cancellation_policy'] ?? 'Chưa cập nhật')) ?>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <h6><i class="feather icon-refresh-cw"></i> Chính sách thay đổi</h6>
                                            <div class="bg-light p-3 rounded">
                                                <?= nl2br(htmlspecialchars($policies['change_policy'] ?? 'Chưa cập nhật')) ?>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <h6><i class="feather icon-credit-card"></i> Chính sách thanh toán</h6>
                                            <div class="bg-light p-3 rounded">
                                                <?= nl2br(htmlspecialchars($policies['payment_policy'] ?? 'Chưa cập nhật')) ?>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <h6><i class="feather icon-file-text"></i> Ghi chú</h6>
                                            <div class="bg-light p-3 rounded">
                                                <?= nl2br(htmlspecialchars($policies['note_policy'] ?? 'Chưa cập nhật')) ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-warning">
                                        <i class="feather icon-alert-triangle"></i> Chưa có chính sách nào
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div> <!-- Tab: Đội ngũ -->
                        <div class="tab-pane fade" id="staff" role="tabpanel">
                            <div class="card-body">
                                <?php if (!empty($assigned_staff)): ?>
                                    <div class="list-group list-group-flush">
                                        <?php foreach ($assigned_staff as $staff_member): ?>
                                            <div class="list-group-item">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <h6 class="mb-1">
                                                            <?= htmlspecialchars($staff_member['full_name']) ?>
                                                        </h6>
                                                        <p class="mb-0 small text-muted">
                                                            <strong>Vai trò:</strong>
                                                            <?= htmlspecialchars($staff_member['role'] ?? 'Chưa xác định') ?>
                                                        </p>
                                                    </div>
                                                    <div class="text-end">
                                                        <p class="mb-0 small">
                                                            <i class="feather icon-phone text-primary"></i>
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
                                        <i class="feather icon-users"></i> Chưa có đội ngũ nào được phân công
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
    </div>
    </section>
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

<?php require_once __DIR__ . '/../core/footer.php'; ?>