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
                        <h2 class="content-header-title float-left mb-0">Chi tiết lịch khởi hành & Phân công</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">
            <!-- Thông báo -->
            <?php require_once __DIR__ . '/../core/alert.php'; ?>

            <?php if (isset($_SESSION['success']) && strpos($_SESSION['success'], 'thành công') !== false): ?>
                <div class="alert alert-info alert-dismissible fade show mb-2" role="alert">
                    <i class="feather icon-info"></i>
                    <strong>Tiếp theo:</strong> Hãy thêm danh sách khách trong đoàn bằng cách click nút "Quản lý danh sách
                    đoàn" dưới đây.
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <!-- Thông tin cơ bản -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Thông tin lịch khởi hành</h4>
                    <?php if (isAdmin()): ?>
                        <div class="btn-group">
                            <a href="?act=quan-ly-danh-sach-doan&schedule_id=<?= $schedule['schedule_id'] ?>"
                                class="btn btn-info btn-sm mr-1">
                                <i class="feather icon-users"></i> Quản lý danh sách đoàn
                            </a>
                            <?php if ($schedule['status'] === 'In Progress'): ?>
                                <span class="badge badge-warning">
                                    <i class="feather icon-lock"></i> Đang diễn ra - Không thể chỉnh sửa
                                </span>
                            <?php else: ?>
                                <a href="?act=sua-lich-khoi-hanh&id=<?= $schedule['schedule_id'] ?>"
                                    class="btn btn-warning btn-sm">
                                    <i class="feather icon-edit"></i> Sửa lịch
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Tour:</th>
                                    <td><strong><?= htmlspecialchars($schedule['tour_name']) ?></strong></td>
                                </tr>
                                <tr>
                                    <th>Mã tour:</th>
                                    <td><?= htmlspecialchars($schedule['tour_code'] ?? '') ?></td>
                                </tr>
                                <tr>
                                    <th>Ngày khởi hành:</th>
                                    <td><?= date('d/m/Y', strtotime($schedule['departure_date'])) ?></td>
                                </tr>
                                <tr>
                                    <th>Ngày kết thúc:</th>
                                    <td><?= $schedule['return_date'] ? date('d/m/Y', strtotime($schedule['return_date'])) : '-' ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Điểm tập trung:</th>
                                    <td><?= htmlspecialchars($schedule['meeting_point'] ?? '') ?></td>
                                </tr>
                                <tr>
                                    <th>Giờ tập trung:</th>
                                    <td><?= $schedule['meeting_time'] ?? '-' ?></td>
                                </tr>
                                <tr>
                                    <th>Số người:</th>
                                    <td>
                                        <span class="badge badge-info">
                                            <?= ((int) ($numAdults ?? 0) + (int) ($numChildren ?? 0) + (int) ($numInfants ?? 0)) ?>
                                            /
                                            <?= $schedule['max_participants'] ?? 0 ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Trạng thái:</th>
                                    <td>
                                        <?php
                                        switch ($schedule['status']) {
                                            case 'Open':
                                                $statusClass = 'badge-success';
                                                $statusText = 'Mở đặt';
                                                break;
                                            case 'Full':
                                                $statusClass = 'badge-warning';
                                                $statusText = 'Đầy';
                                                break;
                                            case 'Confirmed':
                                                $statusClass = 'badge-primary';
                                                $statusText = 'Đã xác nhận';
                                                break;
                                            case 'In Progress':
                                                $statusClass = 'badge-info';
                                                $statusText = 'Đang diễn ra';
                                                break;
                                            case 'Completed':
                                                $statusClass = 'badge-secondary';
                                                $statusText = 'Hoàn thành';
                                                break;
                                            case 'Cancelled':
                                                $statusClass = 'badge-danger';
                                                $statusText = 'Đã hủy';
                                                break;
                                            default:
                                                $statusClass = 'badge-light';
                                                $statusText = $schedule['status'];
                                                break;
                                        }
                                        ?>
                                        <span class="badge <?= $statusClass ?>"><?= $statusText ?></span>

                                        <?php if (isAdmin()): ?>
                                            <div class="btn-group ml-2" role="group">
                                                <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle"
                                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="feather icon-edit-2"></i> Đổi trạng thái
                                                </button>
                                                <div class="dropdown-menu">
                                                    <?php if ($schedule['status'] !== 'In Progress'): ?>
                                                        <a class="dropdown-item" href="#"
                                                            onclick="return confirmChangeStatus(<?= $schedule['schedule_id'] ?>, 'In Progress', 'Đang diễn ra')">
                                                            <i class="feather icon-play-circle text-info"></i> Bắt đầu tour
                                                        </a>
                                                    <?php endif; ?>
                                                    <?php if ($schedule['status'] === 'In Progress'): ?>
                                                        <a class="dropdown-item" href="#"
                                                            onclick="return confirmChangeStatus(<?= $schedule['schedule_id'] ?>, 'Completed', 'Hoàn thành')">
                                                            <i class="feather icon-check-circle text-success"></i> Hoàn thành
                                                            tour
                                                        </a>
                                                    <?php endif; ?>
                                                    <?php if ($schedule['status'] !== 'Cancelled' && $schedule['status'] !== 'In Progress'): ?>
                                                        <a class="dropdown-item" href="#"
                                                            onclick="return confirmChangeStatus(<?= $schedule['schedule_id'] ?>, 'Cancelled', 'Hủy')">
                                                            <i class="feather icon-x-circle text-danger"></i> Hủy tour
                                                        </a>
                                                    <?php endif; ?>
                                                    <div class="dropdown-divider"></div>
                                                    <a class="dropdown-item" href="#"
                                                        onclick="return confirmChangeStatus(<?= $schedule['schedule_id'] ?>, 'Open', 'Mở đặt')">
                                                        <i class="feather icon-unlock text-success"></i> Mở đặt
                                                    </a>
                                                    <a class="dropdown-item" href="#"
                                                        onclick="return confirmChangeStatus(<?= $schedule['schedule_id'] ?>, 'Confirmed', 'Đã xác nhận')">
                                                        <i class="feather icon-check text-primary"></i> Xác nhận
                                                    </a>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <?php if ($schedule['notes']): ?>
                        <div class="alert alert-info">
                            <strong>Ghi chú:</strong> <?= nl2br(htmlspecialchars($schedule['notes'])) ?>
                        </div>
                    <?php endif; ?>

                    <!-- Tóm tắt giá & tổng tạm tính -->
                    <div class="card mt-1">
                        <div class="card-header p-1">
                            <h5 class="card-title mb-0">Giá & Tổng tạm tính</h5>
                        </div>
                        <div class="card-body p-1">
                            <div class="row">
                                <div class="col-md-8">
                                    <table class="table table-sm mb-0">
                                        <tr>
                                            <th style="width:25%">Giá NL (Người lớn):</th>
                                            <td><span
                                                    class="badge badge-primary"><?= number_format((float) ($schedule['price_adult'] ?? 0), 0, ',', '.') ?>
                                                    đ</span></td>
                                            <th style="width:20%">Giá TE (Trẻ em):</th>
                                            <td><span
                                                    class="badge badge-info"><?= number_format((float) ($schedule['price_child'] ?? 0), 0, ',', '.') ?>
                                                    đ</span></td>
                                        </tr>
                                        <tr>
                                            <th>Số NL dự kiến:</th>
                                            <td><?= (int) ($numAdults ?? 0) ?></td>
                                            <th>Số TE dự kiến:</th>
                                            <td><?= (int) ($numChildren ?? 0) ?></td>
                                        </tr>
                                        <tr>
                                            <th>Em bé dự kiến:</th>
                                            <td><?= (int) ($numInfants ?? 0) ?></td>
                                            <th>Tổng khách:</th>
                                            <td><?= ((int) ($numAdults ?? 0) + (int) ($numChildren ?? 0) + (int) ($numInfants ?? 0)) ?>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-4 text-right">
                                    <div class="h6 mb-0">Tổng tạm tính:</div>
                                    <div class="h4 text-success mb-0">
                                        <?= number_format((float) ($estimatedTotal ?? 0), 0, ',', '.') ?> đ
                                    </div>
                                    <?php if (isAdmin()): ?>
                                        <a class="btn btn-sm btn-outline-primary mt-50"
                                            href="?act=tao-booking-tu-lich&schedule_id=<?= $schedule['schedule_id'] ?>">
                                            <i class="feather icon-file-plus"></i> Tạo booking từ lịch này
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                function confirmChangeStatus(scheduleId, newStatus, statusName) {
                    if (confirm('Bạn có chắc chắn muốn chuyển trạng thái sang "' + statusName + '"?')) {
                        var form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '?act=thay-doi-trang-thai-tour';

                        var scheduleInput = document.createElement('input');
                        scheduleInput.type = 'hidden';
                        scheduleInput.name = 'schedule_id';
                        scheduleInput.value = scheduleId;
                        form.appendChild(scheduleInput);

                        var statusInput = document.createElement('input');
                        statusInput.type = 'hidden';
                        statusInput.name = 'status';
                        statusInput.value = newStatus;
                        form.appendChild(statusInput);

                        document.body.appendChild(form);
                        form.submit();
                    }
                    return false;
                }
            </script>

            <!-- Tab Navigation -->
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="members-tab" data-toggle="tab" href="#members" role="tab">
                        <i class="feather icon-users"></i> Danh sách đoàn (<?= count($groupMembers ?? []) ?>)
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="staff-tab" data-toggle="tab" href="#staff" role="tab">
                        <i class="feather icon-user-check"></i> Nhân sự (<?= count($staff) ?>)
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="services-tab" data-toggle="tab" href="#services" role="tab">
                        <i class="feather icon-package"></i> Dịch vụ (<?= count($services) ?>)
                    </a>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content">
                <!-- Tab Danh sách đoàn -->
                <div class="tab-pane active" id="members" role="tabpanel">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Danh sách thành viên trong đoàn</h4>
                            <?php if (isAdmin()): ?>
                                <a href="?act=quan-ly-danh-sach-doan&schedule_id=<?= $schedule['schedule_id'] ?>"
                                    class="btn btn-primary btn-sm">
                                    <i class="feather icon-edit"></i> Quản lý danh sách
                                </a>
                            <?php endif; ?>
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
                                                    <td><strong><?= htmlspecialchars($member['full_name']) ?></strong></td>
                                                    <td><?= htmlspecialchars($member['phone'] ?? '-') ?></td>
                                                    <td><?= htmlspecialchars($member['email'] ?? '-') ?></td>
                                                    <td><?= htmlspecialchars($member['id_number'] ?? '-') ?></td>
                                                    <td><?= $member['date_of_birth'] ? date('d/m/Y', strtotime($member['date_of_birth'])) : '-' ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-info">
                                    <i class="feather icon-info"></i> Chưa có thành viên nào trong danh sách đoàn.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Tab Nhân sự -->
                <div class="tab-pane" id="staff" role="tabpanel">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Phân công nhân sự</h4>
                            <?php if (isAdmin() && count($staff) < 1): ?>
                                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal"
                                    data-target="#addStaffModal">
                                    <i class="feather icon-user-plus"></i> Phân công nhân sự
                                </button>
                            <?php elseif (isAdmin() && count($staff) >= 1): ?>
                                <span class="badge badge-success">
                                    <i class="feather icon-check"></i> Đã phân công (tối đa 1 nhân sự/tour)
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($staff)): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Họ tên</th>
                                                <th>Loại nhân sự</th>
                                                <th>Vai trò</th>
                                                <th>Điện thoại</th>
                                                <th>Ngôn ngữ</th>
                                                <th>Hành động</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($staff as $s): ?>
                                                <tr>
                                                    <td><strong><?= htmlspecialchars($s['full_name']) ?></strong></td>
                                                    <td>
                                                        <?php
                                                        switch ($s['staff_type']) {
                                                            case 'Guide':
                                                                $typeLabel = '<span class="badge badge-primary">Hướng dẫn viên</span>';
                                                                break;
                                                            case 'Driver':
                                                                $typeLabel = '<span class="badge badge-info">Tài xế</span>';
                                                                break;
                                                            case 'Support':
                                                                $typeLabel = '<span class="badge badge-success">Hỗ trợ</span>';
                                                                break;
                                                            case 'Manager':
                                                                $typeLabel = '<span class="badge badge-warning">Quản lý</span>';
                                                                break;
                                                            default:
                                                                $typeLabel = $s['staff_type'];
                                                                break;
                                                        }
                                                        echo $typeLabel;
                                                        ?>
                                                    </td>
                                                    <td><?= htmlspecialchars($s['role'] ?? '-') ?></td>
                                                    <td><?= htmlspecialchars($s['phone'] ?? '') ?></td>
                                                    <td><?= htmlspecialchars($s['languages'] ?? '') ?></td>
                                                    <td>
                                                        <?php if (isAdmin()): ?>
                                                            <button class="btn btn-sm btn-danger"
                                                                onclick="if(confirm('Xóa nhân sự khỏi lịch?')) location.href='?act=xoa-nhan-su-khoi-lich&schedule_id=<?= $schedule['schedule_id'] ?>&staff_id=<?= $s['staff_id'] ?>'">
                                                                <i class="feather icon-x"></i>
                                                            </button>
                                                        <?php endif; ?>
                                                        <?php if (isGuide() && isset($_SESSION['staff_id']) && $_SESSION['staff_id'] == $s['staff_id'] && !empty($s['check_in_time'])): ?>
                                                            <span class="badge badge-success mt-1">Đã check-in:
                                                                <?= date('d/m H:i', strtotime($s['check_in_time'])) ?></span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <p class="text-muted">Chưa phân công nhân sự nào.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Tab Dịch vụ -->
                <div class="tab-pane" id="services" role="tabpanel">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Dịch vụ được phân bổ</h4>
                            <?php if (isAdmin()): ?>
                                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal"
                                    data-target="#addServiceModal">
                                    <i class="feather icon-plus"></i> Thêm dịch vụ
                                </button>
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($services)): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Tên dịch vụ</th>
                                                <th>Loại</th>
                                                <th>Nhà cung cấp</th>
                                                <th>Số lượng</th>
                                                <th>Đơn giá</th>
                                                <th>Thành tiền</th>
                                                <th>Ghi chú</th>
                                                <th>Hành động</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $totalCost = 0;
                                            foreach ($services as $serv):
                                                $totalCost += ($serv['quantity'] * $serv['unit_price']);
                                                ?>
                                                <tr>
                                                    <td><strong><?= htmlspecialchars($serv['service_name']) ?></strong></td>
                                                    <td>
                                                        <?php
                                                        switch ($serv['service_type']) {
                                                            case 'Hotel':
                                                                $typeLabel = '<span class="badge badge-primary">Khách sạn</span>';
                                                                break;
                                                            case 'Restaurant':
                                                                $typeLabel = '<span class="badge badge-success">Nhà hàng</span>';
                                                                break;
                                                            case 'Transport':
                                                                $typeLabel = '<span class="badge badge-info">Xe</span>';
                                                                break;
                                                            case 'Flight':
                                                                $typeLabel = '<span class="badge badge-warning">Máy bay</span>';
                                                                break;
                                                            case 'Insurance':
                                                                $typeLabel = '<span class="badge badge-secondary">Bảo hiểm</span>';
                                                                break;
                                                            default:
                                                                $typeLabel = $serv['service_type'];
                                                                break;
                                                        }
                                                        echo $typeLabel;
                                                        ?>
                                                    </td>
                                                    <td><?= htmlspecialchars($serv['provider_name'] ?? '') ?></td>
                                                    <td><?= $serv['quantity'] ?></td>
                                                    <td><?= number_format($serv['unit_price'], 0, ',', '.') ?> đ</td>
                                                    <td><strong><?= number_format($serv['quantity'] * $serv['unit_price'], 0, ',', '.') ?>
                                                            đ</strong></td>
                                                    <td><?= htmlspecialchars($serv['notes'] ?? '-') ?></td>
                                                    <td>
                                                        <?php if (isAdmin()): ?>
                                                            <button class="btn btn-sm btn-danger"
                                                                onclick="if(confirm('Xóa dịch vụ khỏi lịch?')) location.href='?act=xoa-dich-vu-khoi-lich&schedule_id=<?= $schedule['schedule_id'] ?>&service_id=<?= $serv['service_id'] ?>'">
                                                                <i class="feather icon-x"></i>
                                                            </button>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                            <tr class="table-info">
                                                <td colspan="5" class="text-right"><strong>Tổng chi phí dịch vụ:</strong>
                                                </td>
                                                <td colspan="3"><strong><?= number_format($totalCost, 0, ',', '.') ?>
                                                        đ</strong></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <p class="text-muted">Chưa có dịch vụ nào.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-3">
                <a href="?act=danh-sach-lich-khoi-hanh" class="btn btn-secondary">
                    <i class="feather icon-arrow-left"></i> Quay lại
                </a>
                <?php if (isAdmin()): ?>
                    <a href="?act=xuat-bao-cao-lich&id=<?= $schedule['schedule_id'] ?>" class="btn btn-success">
                        <i class="feather icon-printer"></i> Xuất báo cáo
                    </a>
                <?php endif; ?>
                <?php if (isGuide()): ?>
                    <!-- HDV Check-in -->
                    <form action="?act=hdv-checkin" method="POST" style="display:inline-block;">
                        <input type="hidden" name="schedule_id" value="<?= $schedule['schedule_id'] ?>">
                        <button type="submit" class="btn btn-info">
                            <i class="feather icon-map-pin"></i> Check-in
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php if (isGuide()): ?>
    <!-- Nhật ký hành trình cho HDV -->
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-body">
                <div class="card mt-2">
                    <div class="card-header">
                        <h4 class="card-title">Nhật ký hành trình</h4>
                    </div>
                    <div class="card-body">
                        <form action="?act=hdv-luu-nhat-ky" method="POST">
                            <input type="hidden" name="schedule_id" value="<?= $schedule['schedule_id'] ?>">
                            <div class="form-group">
                                <label for="log_text">Ghi chép mới</label>
                                <textarea name="log_text" id="log_text" rows="3" class="form-control"
                                    placeholder="Ví dụ: Đã đón đoàn tại điểm tập trung, thời tiết tốt..."></textarea>
                            </div>
                            <button class="btn btn-primary" type="submit"><i class="feather icon-save"></i> Lưu nhật
                                ký</button>
                        </form>
                        <hr>
                        <h5>Nhật ký gần đây</h5>
                        <?php if (!empty($journeyLogs)): ?>
                            <div class="table-responsive">
                                <table class="table table-sm table-striped">
                                    <thead>
                                        <tr>
                                            <th>Thời gian</th>
                                            <th>HDV</th>
                                            <th>Nội dung</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($journeyLogs as $log): ?>
                                            <tr>
                                                <td style="width:140px;">
                                                    <?= date('d/m/Y H:i', strtotime($log['created_at'])) ?>
                                                </td>
                                                <td style="width:160px;">
                                                    <?= htmlspecialchars($log['full_name'] ?? 'HDV') ?>
                                                </td>
                                                <td><?= nl2br(htmlspecialchars($log['log_text'])) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-muted mb-0">Chưa có nhật ký nào.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Modal Phân công Nhân sự -->
<div class="modal fade" id="addStaffModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Phân công nhân sự</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="?act=phan-cong-nhan-su" method="POST">
                <input type="hidden" name="schedule_id" value="<?= $schedule['schedule_id'] ?>">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="staff_id">Chọn nhân viên <span class="text-danger">*</span></label>
                        <select name="staff_id" id="staff_id" class="form-control" required>
                            <option value="">-- Chọn nhân viên --</option>
                            <?php foreach ($allStaff as $s):
                                switch ($s['staff_type']) {
                                    case 'Guide':
                                        $typeShort = 'HDV';
                                        break;
                                    case 'Driver':
                                        $typeShort = 'Tài xế';
                                        break;
                                    case 'Support':
                                        $typeShort = 'Hỗ trợ';
                                        break;
                                    case 'Manager':
                                        $typeShort = 'Quản lý';
                                        break;
                                    default:
                                        $typeShort = $s['staff_type'];
                                        break;
                                }
                                ?>
                                <option value="<?= $s['staff_id'] ?>">
                                    <?= htmlspecialchars($s['full_name']) ?>
                                    (<?= $typeShort ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="role">Vai trò trong tour này <span class="text-danger">*</span></label>
                        <select name="role" id="role" class="form-control" required>
                            <option value="">-- Chọn vai trò --</option>
                            <option value="Chính">Chính</option>
                            <option value="Phụ">Phụ</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">Phân công</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Thêm Dịch vụ -->
<div class="modal fade" id="addServiceModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm dịch vụ</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="?act=phan-bo-dich-vu" method="POST">
                <input type="hidden" name="schedule_id" value="<?= $schedule['schedule_id'] ?>">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="service_id">Chọn dịch vụ <span class="text-danger">*</span></label>
                        <select name="service_id" id="service_id" class="form-control" required>
                            <option value="">-- Chọn dịch vụ --</option>
                            <?php foreach ($allServices as $serv): ?>
                                <option value="<?= $serv['service_id'] ?>" data-price="<?= $serv['default_price'] ?>">
                                    <?= htmlspecialchars($serv['service_name']) ?>
                                    (<?= $serv['service_type'] ?> - <?= htmlspecialchars($serv['provider_name'] ?? '') ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="quantity">Số lượng <span class="text-danger">*</span></label>
                                <input type="number" name="quantity" id="quantity" class="form-control" value="1"
                                    min="1" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="unit_price">Đơn giá (VNĐ) <span class="text-danger">*</span></label>
                                <input type="number" name="unit_price" id="unit_price" class="form-control" value="0"
                                    min="0" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="service_notes">Ghi chú</label>
                        <textarea name="notes" id="service_notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">Thêm dịch vụ</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Auto-fill giá khi chọn dịch vụ
    document.getElementById('service_id').addEventListener('change', function () {
        const selectedOption = this.options[this.selectedIndex];
        const defaultPrice = selectedOption.getAttribute('data-price');
        if (defaultPrice) {
            document.getElementById('unit_price').value = defaultPrice;
        }
    });
</script>

<!-- END: Content-->
<?php require_once __DIR__ . '/../core/footer.php'; ?>