<?php require_once __DIR__ . '/../core/header.php'; ?>
<?php require_once __DIR__ . '/../core/menu.php'; ?>
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">Danh sách báo giá</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">
            <?php require_once __DIR__ . '/../core/alert.php'; ?>

            <!-- Thống kê -->
            <div class="row">
                <!-- Total Quotes -->
                <div class="col-lg-3 col-sm-6">
                    <div class="card">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <h3 class="font-weight-bolder mb-0"><?= $stats['total_quotes'] ?? 0 ?></h3>
                                <span>Tổng báo giá</span>
                            </div>
                            <div class="avatar bg-light-primary p-50">
                                <span class="avatar-content">
                                    <i class="feather icon-file-text font-medium-5"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Value -->
                <div class="col-lg-3 col-sm-6">
                    <div class="card">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <h3 class="font-weight-bolder mb-0"><?= number_format($stats['total_value'] ?? 0) ?>
                                </h3>
                                <span>Tổng giá trị (VNĐ)</span>
                            </div>
                            <div class="avatar bg-light-success p-50">
                                <span class="avatar-content">
                                    <i class="feather icon-dollar-sign font-medium-5"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- By Status -->
                <?php
                $statusColors = [
                    'Đang chờ' => 'warning',
                    'Đã chấp nhận' => 'success',
                    'Đã từ chối' => 'danger',
                    'Hết hạn' => 'secondary'
                ];
                $statusIcons = [
                    'Đang chờ' => 'clock',
                    'Đã chấp nhận' => 'check-circle',
                    'Đã từ chối' => 'x-circle',
                    'Hết hạn' => 'alert-circle'
                ];

                $displayCount = 0;
                foreach ($stats['by_status'] ?? [] as $status => $data):
                    if ($displayCount >= 2)
                        break; // Only show first 2 status cards
                    $color = $statusColors[$status] ?? 'info';
                    $icon = $statusIcons[$status] ?? 'file-text';
                    $displayCount++;
                    ?>
                    <div class="col-lg-3 col-sm-6">
                        <div class="card">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div>
                                    <h3 class="font-weight-bolder mb-0"><?= $data['total'] ?? 0 ?></h3>
                                    <span><?= htmlspecialchars($status) ?></span>
                                </div>
                                <div class="avatar bg-light-<?= $color ?> p-50">
                                    <span class="avatar-content">
                                        <i class="feather icon-<?= $icon ?> font-medium-5"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Tất cả báo giá</h4>
                    <div>
                        <a href="?act=tao-bao-gia" class="btn btn-primary btn-sm">
                            <i class="feather icon-plus"></i> Tạo báo giá mới
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Bộ lọc -->
                    <form method="GET" class="mb-2">
                        <input type="hidden" name="act" value="danh-sach-bao-gia" />
                        <div class="row">
                            <div class="col-md-4">
                                <select name="status" class="form-control" onchange="this.form.submit()">
                                    <option value="">Tất cả trạng thái</option>
                                    <option value="Đang chờ" <?= ($filters['status'] === 'Đang chờ' ? 'selected' : '') ?>>
                                        Đang chờ</option>
                                    <option value="Đã chấp nhận" <?= ($filters['status'] === 'Đã chấp nhận' ? 'selected' : '') ?>>Đã chấp nhận</option>
                                    <option value="Đã từ chối" <?= ($filters['status'] === 'Đã từ chối' ? 'selected' : '') ?>>Đã từ chối</option>
                                    <option value="Hết hạn" <?= ($filters['status'] === 'Hết hạn' ? 'selected' : '') ?>>Hết
                                        hạn</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <input type="text" name="search" value="<?= htmlspecialchars($filters['search']) ?>"
                                    class="form-control" placeholder="Tìm theo tên, email, số điện thoại..." />
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary"><i class="feather icon-search"></i>
                                    Tìm</button>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tour</th>
                                    <th>Khách hàng</th>
                                    <th>Ngày KH</th>
                                    <th>Tổng tiền</th>
                                    <th>Trạng thái</th>
                                    <th>Ngày tạo</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($quotes)): ?>
                                    <?php foreach ($quotes as $q): ?>
                                        <tr>
                                            <td><?= $q['quote_id'] ?></td>
                                            <td>
                                                <strong><?= htmlspecialchars($q['tour_name']) ?></strong><br>
                                                <small class="text-muted"><?= htmlspecialchars($q['tour_code']) ?></small>
                                            </td>
                                            <td>
                                                <?= htmlspecialchars($q['customer_name']) ?><br>
                                                <small class="text-muted"><?= htmlspecialchars($q['customer_phone']) ?></small>
                                            </td>
                                            <td><?= $q['departure_date'] ? date('d/m/Y', strtotime($q['departure_date'])) : '-' ?>
                                            </td>
                                            <td><?= number_format($q['total_amount'], 0, ',', '.') ?> đ</td>
                                            <td>
                                                <?php
                                                switch ($q['status']) {
                                                    case 'Đang chờ':
                                                        $badge = 'badge-warning';
                                                        break;
                                                    case 'Đã chấp nhận':
                                                        $badge = 'badge-success';
                                                        break;
                                                    case 'Đã từ chối':
                                                        $badge = 'badge-danger';
                                                        break;
                                                    case 'Hết hạn':
                                                        $badge = 'badge-secondary';
                                                        break;
                                                    default:
                                                        $badge = 'badge-light';
                                                        break;
                                                }
                                                ?>
                                                <span class="badge <?= $badge ?>"><?= htmlspecialchars($q['status']) ?></span>
                                            </td>
                                            <td><?= date('d/m/Y', strtotime($q['created_at'])) ?></td>
                                            <td>
                                                <a href="?act=xem-bao-gia&id=<?= $q['quote_id'] ?>" class="btn btn-info btn-sm"
                                                    title="Xem chi tiết">
                                                    <i class="feather icon-eye"></i>
                                                </a>
                                                <a href="?act=xuat-bao-gia&id=<?= $q['quote_id'] ?>&format=pdf" target="_blank"
                                                    class="btn btn-success btn-sm" title="In/PDF">
                                                    <i class="feather icon-printer"></i>
                                                </a>
                                                <a onclick="return confirm('Xóa báo giá này?')"
                                                    href="?act=xoa-bao-gia&id=<?= $q['quote_id'] ?>"
                                                    class="btn btn-danger btn-sm" title="Xóa">
                                                    <i class="feather icon-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center text-muted">Chưa có báo giá nào</td>
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
<?php require_once __DIR__ . '/../core/footer.php'; ?>