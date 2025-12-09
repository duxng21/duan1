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
                            <i class="feather icon-users"></i> Danh sách khách
                        </h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="?act=danh-sach-booking">Booking</a></li>
                                <li class="breadcrumb-item active">Danh sách khách</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-header-right text-md-right col-md-3 col-12">
                <!-- Actions -->
                <div class="btn-group">
                    <a href="?act=xuat-danh-sach-doan&booking_id=<?= $booking_id ?? '' ?>&schedule_id=<?= $schedule_id ?? '' ?>"
                        class="btn btn-primary" target="_blank">
                        <i class="feather icon-printer"></i> In danh sách
                    </a>
                    <a href="?act=bao-cao-doan&booking_id=<?= $booking_id ?? '' ?>&schedule_id=<?= $schedule_id ?? '' ?>"
                        class="btn btn-info">
                        <i class="feather icon-bar-chart"></i> Báo cáo
                    </a>
                    <a href="?act=xuat-danh-sach-da-check-in&booking_id=<?= $booking_id ?? '' ?>&schedule_id=<?= $schedule_id ?? '' ?>"
                        class="btn btn-success">
                        <i class="feather icon-download"></i> Xuất đã check-in
                    </a>
                </div>
            </div>
        </div>

        <div class="content-body">
            <!-- Booking Info -->
            <?php if (isset($booking)): ?>
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
                                <strong>Khách hàng:</strong>
                                <?= htmlspecialchars($booking['customer_name'] ?? $booking['organization_name'] ?? 'N/A') ?>
                            </div>
                            <div class="col-md-3">
                                <strong>Trạng thái:</strong>
                                <span class="badge badge-info"><?= $booking['status'] ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Summary Statistics -->
            <div class="row">
                <div class="col-lg-3 col-sm-6 col-12">
                    <div class="card">
                        <div class="card-header d-flex align-items-start pb-0">
                            <div>
                                <h2 class="text-bold-700 mb-0"><?= $summary['total_guests'] ?? 0 ?></h2>
                                <p>Tổng khách</p>
                            </div>
                            <div class="avatar bg-rgba-primary p-50 m-0">
                                <div class="avatar-content">
                                    <i class="feather icon-users text-primary font-medium-5"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6 col-12">
                    <div class="card">
                        <div class="card-header d-flex align-items-start pb-0">
                            <div>
                                <h2 class="text-bold-700 mb-0 text-success"><?= $summary['checked_in'] ?? 0 ?></h2>
                                <p>Đã check-in</p>
                            </div>
                            <div class="avatar bg-rgba-success p-50 m-0">
                                <div class="avatar-content">
                                    <i class="feather icon-check-circle text-success font-medium-5"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6 col-12">
                    <div class="card">
                        <div class="card-header d-flex align-items-start pb-0">
                            <div>
                                <h2 class="text-bold-700 mb-0 text-danger"><?= $summary['no_show'] ?? 0 ?></h2>
                                <p>Vắng mặt</p>
                            </div>
                            <div class="avatar bg-rgba-danger p-50 m-0">
                                <div class="avatar-content">
                                    <i class="feather icon-x-circle text-danger font-medium-5"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6 col-12">
                    <div class="card">
                        <div class="card-header d-flex align-items-start pb-0">
                            <div>
                                <h2 class="text-bold-700 mb-0 text-warning"><?= $summary['room_assigned'] ?? 0 ?></h2>
                                <p>Đã phân phòng</p>
                            </div>
                            <div class="avatar bg-rgba-warning p-50 m-0">
                                <div class="avatar-content">
                                    <i class="feather icon-home text-warning font-medium-5"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Guest List -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title"><i class="feather icon-list"></i> Danh sách khách (<?= count($guests) ?>)
                    </h4>
                </div>
                <div class="card-content">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th width="40">STT</th>
                                        <th>Họ tên</th>
                                        <th>CMND/CCCD</th>
                                        <th>Giới tính</th>
                                        <th>Năm sinh</th>
                                        <th>SĐT</th>
                                        <th>Loại</th>
                                        <th>Phòng</th>
                                        <th>Check-in</th>
                                        <th width="180">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($guests)): ?>
                                        <?php $stt = 1; ?>
                                        <?php foreach ($guests as $guest): ?>
                                            <tr>
                                                <td><?= $stt++ ?></td>
                                                <td>
                                                    <strong><?= htmlspecialchars($guest['full_name']) ?></strong>
                                                </td>
                                                <td><?= htmlspecialchars($guest['id_number'] ?? '') ?></td>
                                                <td>
                                                    <span class="badge badge-light">N/A</span>
                                                </td>
                                                <td>
                                                    <?php if (!empty($guest['date_of_birth'])): ?>
                                                        <?= date('Y', strtotime($guest['date_of_birth'])) ?>
                                                    <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if (!empty($guest['phone'])): ?>
                                                        <a href="tel:<?= htmlspecialchars($guest['phone']) ?>">
                                                            <?= htmlspecialchars($guest['phone']) ?>
                                                        </a>
                                                    <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <span class="badge badge-info">Khách</span>
                                                </td>
                                                <td>
                                                    <span class="badge badge-secondary">Chưa phân</span>
                                                </td>
                                                <td>
                                                    <span class="badge badge-warning">Chưa check-in</span>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <button type="button" class="btn btn-success" title="Check-in"
                                                            onclick="alert('Check-in thành công!')">
                                                            <i class="feather icon-check"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-warning" title="Phân phòng"
                                                            onclick="alert('Phân phòng cho ' + '<?= htmlspecialchars($guest['full_name']) ?>')">
                                                            <i class="feather icon-home"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-info" title="Xem ghi chú">
                                                            <i class="feather icon-message-square"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="10" class="text-center text-muted py-4">
                                                <i class="feather icon-inbox"></i> Chưa có khách nào trong danh sách
                                            </td>
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
</div>
<!-- END: Content-->

<?php require_once __DIR__ . '/../core/footer.php'; ?>