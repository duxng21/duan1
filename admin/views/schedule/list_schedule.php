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
                        <h2 class="content-header-title float-left mb-0">Danh sách lịch khởi hành</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">
            <!-- Thông báo -->
            <?php require_once __DIR__ . '/../core/alert.php'; ?>

            <section id="basic-datatable">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">
                                    <?= $tour ? 'Lịch khởi hành: ' . htmlspecialchars($tour['tour_name']) : 'Tất cả lịch khởi hành' ?>
                                </h4>
                                <div>
                                    <a href="?act=xem-lich-theo-thang" class="btn btn-info btn-sm mr-1">
                                        <i class="feather icon-calendar"></i> Xem lịch tháng
                                    </a>
                                    <a href="?act=them-lich-khoi-hanh<?= $tour ? '&tour_id=' . $tour['tour_id'] : '' ?>"
                                        class="btn btn-primary btn-sm">
                                        <i class="feather icon-plus"></i> Thêm lịch mới
                                    </a>
                                </div>
                            </div>
                            <div class="card-content">
                                <div class="card-body card-dashboard">
                                    <!-- Bộ lọc -->
                                    <?php if (!$tour): ?>
                                        <div class="row mb-3">
                                            <div class="col-md-4">
                                                <form method="GET" action="">
                                                    <input type="hidden" name="act" value="danh-sach-lich-khoi-hanh">
                                                    <label class="mr-2">Lọc theo tour:</label>
                                                    <select name="tour_id" class="form-control"
                                                        onchange="this.form.submit()">
                                                        <option value="">Tất cả tour</option>
                                                        <?php foreach ($tours as $t): ?>
                                                            <option value="<?= $t['tour_id'] ?>">
                                                                <?= htmlspecialchars($t['tour_name']) ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </form>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Tour</th>
                                                    <th>Ngày khởi hành</th>
                                                    <th>Ngày kết thúc</th>
                                                    <th>Điểm tập trung</th>
                                                    <th>Số người</th>
                                                    <th>Giá (VNĐ)</th>
                                                    <th>Trạng thái</th>
                                                    <th>Hành động</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($schedules)): ?>
                                                    <?php foreach ($schedules as $schedule): ?>
                                                        <tr>
                                                            <td><?= $schedule['schedule_id'] ?></td>
                                                            <td>
                                                                <strong><?= htmlspecialchars($schedule['tour_name'] ?? '') ?></strong><br>
                                                                <small
                                                                    class="text-muted"><?= htmlspecialchars($schedule['tour_code'] ?? '') ?></small>
                                                            </td>
                                                            <td><?= date('d/m/Y', strtotime($schedule['departure_date'])) ?>
                                                            </td>
                                                            <td><?= $schedule['return_date'] ? date('d/m/Y', strtotime($schedule['return_date'])) : '-' ?>
                                                            </td>
                                                            <td>
                                                                <?= htmlspecialchars($schedule['meeting_point'] ?? '') ?><br>
                                                                <small
                                                                    class="text-muted"><?= $schedule['meeting_time'] ?? '' ?></small>
                                                            </td>
                                                            <td>
                                                                <span class="badge badge-info">
                                                                    <?= $schedule['total_guests'] ?? 0 ?> /
                                                                    <?= $schedule['max_participants'] ?? 0 ?>
                                                                </span>
                                                            </td>
                                                            <td>
                                                                NL:
                                                                <?= number_format($schedule['price_adult'], 0, ',', '.') ?><br>
                                                                TE: <?= number_format($schedule['price_child'], 0, ',', '.') ?>
                                                            </td>
                                                            <td>
                                                                <?php
                                                                $statusClass = match ($schedule['status']) {
                                                                    'Open' => 'badge-success',
                                                                    'Full' => 'badge-warning',
                                                                    'Confirmed' => 'badge-primary',
                                                                    'In Progress' => 'badge-info',
                                                                    'Completed' => 'badge-secondary',
                                                                    'Cancelled' => 'badge-danger',
                                                                    default => 'badge-light'
                                                                };
                                                                $statusText = match ($schedule['status']) {
                                                                    'Open' => 'Mở',
                                                                    'Full' => 'Đầy',
                                                                    'Confirmed' => 'Đã xác nhận',
                                                                    'In Progress' => 'Đang diễn ra',
                                                                    'Completed' => 'Hoàn thành',
                                                                    'Cancelled' => 'Đã hủy',
                                                                    default => $schedule['status']
                                                                };
                                                                ?>
                                                                <span
                                                                    class="badge <?= $statusClass ?>"><?= $statusText ?></span>
                                                            </td>
                                                            <td>
                                                                <a href="?act=chi-tiet-lich-khoi-hanh&id=<?= $schedule['schedule_id'] ?>"
                                                                    class="btn btn-info btn-sm" title="Chi tiết & Phân công">
                                                                    <i class="feather icon-eye"></i>
                                                                </a>
                                                                <?php if ($schedule['status'] !== 'In Progress'): ?>
                                                                    <a href="?act=sua-lich-khoi-hanh&id=<?= $schedule['schedule_id'] ?>"
                                                                        class="btn btn-warning btn-sm" title="Sửa">
                                                                        <i class="feather icon-edit"></i>
                                                                    </a>
                                                                <?php else: ?>
                                                                    <button class="btn btn-secondary btn-sm"
                                                                        title="Không thể sửa khi tour đang diễn ra" disabled>
                                                                        <i class="feather icon-lock"></i>
                                                                    </button>
                                                                <?php endif; ?>
                                                                <a href="?act=xuat-bao-cao-lich&id=<?= $schedule['schedule_id'] ?>"
                                                                    class="btn btn-success btn-sm" title="Xuất báo cáo">
                                                                    <i class="feather icon-printer"></i>
                                                                </a>
                                                                <?php if ($schedule['status'] !== 'In Progress'): ?>
                                                                    <a onclick="return confirm('Xóa lịch này?')"
                                                                        href="?act=xoa-lich-khoi-hanh&id=<?= $schedule['schedule_id'] ?>"
                                                                        class="btn btn-danger btn-sm" title="Xóa">
                                                                        <i class="feather icon-trash"></i>
                                                                    </a>
                                                                <?php else: ?>
                                                                    <button class="btn btn-secondary btn-sm"
                                                                        title="Không thể xóa khi tour đang diễn ra" disabled>
                                                                        <i class="feather icon-trash"></i>
                                                                    </button>
                                                                <?php endif; ?>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="9" class="text-center text-muted">Chưa có lịch khởi
                                                            hành nào</td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
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
<!-- END: Content-->
<?php require_once __DIR__ . '/../core/footer.php'; ?>