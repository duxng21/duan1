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
                        <h2 class="content-header-title float-left mb-0">Chi tiết nhân sự</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="?act=danh-sach-nhan-su">Danh sách nhân sự</a></li>
                                <li class="breadcrumb-item active"><?= htmlspecialchars($staff['full_name']) ?></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">
            <section id="staff-detail">
                <div class="row">
                    <!-- Thông tin cơ bản -->
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Thông tin cá nhân</h4>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Họ tên:</strong></td>
                                            <td><?= htmlspecialchars($staff['full_name']) ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Loại:</strong></td>
                                            <td>
                                                <?php
                                                $typeClass = match ($staff['staff_type']) {
                                                    'Guide' => 'badge-primary',
                                                    'Driver' => 'badge-info',
                                                    'Support' => 'badge-warning',
                                                    'Manager' => 'badge-success',
                                                    default => 'badge-light'
                                                };
                                                $typeName = match ($staff['staff_type']) {
                                                    'Guide' => 'Hướng dẫn viên',
                                                    'Driver' => 'Tài xế',
                                                    'Support' => 'Hỗ trợ',
                                                    'Manager' => 'Quản lý',
                                                    default => $staff['staff_type']
                                                };
                                                ?>
                                                <span class="badge <?= $typeClass ?>"><?= $typeName ?></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Điện thoại:</strong></td>
                                            <td><?= htmlspecialchars($staff['phone'] ?? 'N/A') ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Email:</strong></td>
                                            <td><?= htmlspecialchars($staff['email'] ?? 'N/A') ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>CMND/CCCD:</strong></td>
                                            <td><?= htmlspecialchars($staff['id_card'] ?? 'N/A') ?></td>
                                        </tr>
                                        <?php if ($staff['license_number']): ?>
                                            <tr>
                                                <td><strong>Bằng lái:</strong></td>
                                                <td><?= htmlspecialchars($staff['license_number']) ?></td>
                                            </tr>
                                        <?php endif; ?>
                                        <tr>
                                            <td><strong>Kinh nghiệm:</strong></td>
                                            <td><?= $staff['experience_years'] ?> năm</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Ngôn ngữ:</strong></td>
                                            <td><?= htmlspecialchars($staff['languages'] ?? 'N/A') ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Trạng thái:</strong></td>
                                            <td>
                                                <span
                                                    class="badge <?= $staff['status'] ? 'badge-success' : 'badge-secondary' ?>">
                                                    <?= $staff['status'] ? 'Đang làm việc' : 'Nghỉ việc' ?>
                                                </span>
                                            </td>
                                        </tr>
                                    </table>

                                    <?php if ($staff['notes']): ?>
                                        <div class="mt-2">
                                            <strong>Ghi chú:</strong>
                                            <p><?= nl2br(htmlspecialchars($staff['notes'])) ?></p>
                                        </div>
                                    <?php endif; ?>

                                    <div class="mt-3">
                                        <a href="?act=sua-nhan-su&id=<?= $staff['staff_id'] ?>"
                                            class="btn btn-primary btn-block">
                                            <i class="feather icon-edit"></i> Chỉnh sửa
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Lịch làm việc -->
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Lịch làm việc</h4>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <!-- Bộ lọc ngày -->
                                    <form method="GET" action="" class="mb-3">
                                        <input type="hidden" name="act" value="chi-tiet-nhan-su">
                                        <input type="hidden" name="id" value="<?= $staff['staff_id'] ?>">
                                        <div class="row">
                                            <div class="col-md-5">
                                                <label>Từ ngày:</label>
                                                <input type="date" class="form-control" name="from_date"
                                                    value="<?= $from_date ?>">
                                            </div>
                                            <div class="col-md-5">
                                                <label>Đến ngày:</label>
                                                <input type="date" class="form-control" name="to_date"
                                                    value="<?= $to_date ?>">
                                            </div>
                                            <div class="col-md-2">
                                                <label>&nbsp;</label>
                                                <button type="submit" class="btn btn-primary btn-block">
                                                    <i class="feather icon-search"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </form>

                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Ngày khởi hành</th>
                                                    <th>Tour</th>
                                                    <th>Vai trò</th>
                                                    <th>Trạng thái</th>
                                                    <th>Hành động</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($schedules)): ?>
                                                    <?php foreach ($schedules as $schedule): ?>
                                                        <tr>
                                                            <td>
                                                                <?= date('d/m/Y', strtotime($schedule['departure_date'])) ?>
                                                                <br>
                                                                <small class="text-muted">
                                                                    <?= $schedule['return_date'] ? date('d/m/Y', strtotime($schedule['return_date'])) : '' ?>
                                                                </small>
                                                            </td>
                                                            <td><?= htmlspecialchars($schedule['tour_name']) ?></td>
                                                            <td><?= htmlspecialchars($schedule['role'] ?? '') ?></td>
                                                            <td>
                                                                <?php
                                                                $statusClass = match ($schedule['status']) {
                                                                    'Open' => 'badge-success',
                                                                    'Full' => 'badge-warning',
                                                                    'Confirmed' => 'badge-primary',
                                                                    'Completed' => 'badge-secondary',
                                                                    'Cancelled' => 'badge-danger',
                                                                    default => 'badge-light'
                                                                };
                                                                ?>
                                                                <span
                                                                    class="badge <?= $statusClass ?>"><?= $schedule['status'] ?></span>
                                                            </td>
                                                            <td>
                                                                <a href="?act=chi-tiet-lich-khoi-hanh&id=<?= $schedule['schedule_id'] ?>"
                                                                    class="btn btn-info btn-sm">
                                                                    <i class="feather icon-eye"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="5" class="text-center text-muted">
                                                            Không có lịch làm việc trong khoảng thời gian này
                                                        </td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>

                                    <?php if (!empty($schedules)): ?>
                                        <div class="alert alert-info mt-3">
                                            <strong>Tổng số lịch:</strong> <?= count($schedules) ?> lịch khởi hành
                                        </div>
                                    <?php endif; ?>
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