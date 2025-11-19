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

            <!-- Thông tin cơ bản -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Thông tin lịch khởi hành</h4>
                    <a href="?act=sua-lich-khoi-hanh&id=<?= $schedule['schedule_id'] ?>" class="btn btn-warning btn-sm">
                        <i class="feather icon-edit"></i> Sửa thông tin
                    </a>
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
                                            <?= $schedule['total_guests'] ?? 0 ?> /
                                            <?= $schedule['max_participants'] ?? 0 ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Trạng thái:</th>
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
                                        <span class="badge <?= $statusClass ?>"><?= $schedule['status'] ?></span>
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
                </div>
            </div>

            <!-- Tab Navigation -->
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="staff-tab" data-toggle="tab" href="#staff" role="tab">
                        <i class="feather icon-users"></i> Nhân sự (<?= count($staff) ?>)
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
                <!-- Tab Nhân sự -->
                <div class="tab-pane active" id="staff" role="tabpanel">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Phân công nhân sự</h4>
                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal"
                                data-target="#addStaffModal">
                                <i class="feather icon-user-plus"></i> Phân công nhân sự
                            </button>
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
                                                        $typeLabel = match ($s['staff_type']) {
                                                            'Guide' => '<span class="badge badge-primary">Hướng dẫn viên</span>',
                                                            'Driver' => '<span class="badge badge-info">Tài xế</span>',
                                                            'Support' => '<span class="badge badge-success">Hỗ trợ</span>',
                                                            'Manager' => '<span class="badge badge-warning">Quản lý</span>',
                                                            default => $s['staff_type']
                                                        };
                                                        echo $typeLabel;
                                                        ?>
                                                    </td>
                                                    <td><?= htmlspecialchars($s['role'] ?? '-') ?></td>
                                                    <td><?= htmlspecialchars($s['phone'] ?? '') ?></td>
                                                    <td><?= htmlspecialchars($s['languages'] ?? '') ?></td>
                                                    <td>
                                                        <button class="btn btn-sm btn-danger"
                                                            onclick="if(confirm('Xóa nhân sự khỏi lịch?')) location.href='?act=xoa-nhan-su-khoi-lich&schedule_id=<?= $schedule['schedule_id'] ?>&staff_id=<?= $s['staff_id'] ?>'">
                                                            <i class="feather icon-x"></i>
                                                        </button>
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
                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal"
                                data-target="#addServiceModal">
                                <i class="feather icon-plus"></i> Thêm dịch vụ
                            </button>
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
                                                        $typeLabel = match ($serv['service_type']) {
                                                            'Hotel' => '<span class="badge badge-primary">Khách sạn</span>',
                                                            'Restaurant' => '<span class="badge badge-success">Nhà hàng</span>',
                                                            'Transport' => '<span class="badge badge-info">Xe</span>',
                                                            'Flight' => '<span class="badge badge-warning">Máy bay</span>',
                                                            'Insurance' => '<span class="badge badge-secondary">Bảo hiểm</span>',
                                                            default => $serv['service_type']
                                                        };
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
                                                        <button class="btn btn-sm btn-danger"
                                                            onclick="if(confirm('Xóa dịch vụ khỏi lịch?')) location.href='?act=xoa-dich-vu-khoi-lich&schedule_id=<?= $schedule['schedule_id'] ?>&service_id=<?= $serv['service_id'] ?>'">
                                                            <i class="feather icon-x"></i>
                                                        </button>
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
                <a href="?act=xuat-bao-cao-lich&id=<?= $schedule['schedule_id'] ?>" class="btn btn-success">
                    <i class="feather icon-printer"></i> Xuất báo cáo
                </a>
            </div>
        </div>
    </div>
</div>

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
                            <?php foreach ($allStaff as $s): ?>
                                <option value="<?= $s['staff_id'] ?>">
                                    <?= htmlspecialchars($s['full_name']) ?>
                                    (<?= match ($s['staff_type']) {
                                        'Guide' => 'HDV',
                                        'Driver' => 'Tài xế',
                                        'Support' => 'Hỗ trợ',
                                        'Manager' => 'Quản lý',
                                        default => $s['staff_type']
                                    } ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="role">Vai trò trong tour này</label>
                        <input type="text" name="role" id="role" class="form-control"
                            placeholder="VD: Hướng dẫn viên chính, Tài xế phụ...">
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