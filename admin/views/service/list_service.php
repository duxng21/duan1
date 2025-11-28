<?php require_once './views/core/header.php'; ?>
<?php require_once './views/core/menu.php'; ?>

<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <h2 class="content-header-title">Quản lý dịch vụ</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="?act=/">Trang chủ</a></li>
                        <li class="breadcrumb-item active">Danh sách dịch vụ</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="content-body">
            <?php require_once './views/core/alert.php'; ?>

            <!-- Search & Filter -->
            <section>
                <div class="card">
                    <div class="card-header"><h4 class="card-title">Tìm kiếm & Lọc</h4></div>
                    <div class="card-body">
                        <form method="GET">
                            <input type="hidden" name="act" value="danh-sach-dich-vu">
                            <div class="row">
                                <div class="col-md-3">
                                    <label>Loại dịch vụ</label>
                                    <select class="form-control" name="service_type">
                                        <option value="">-- Tất cả --</option>
                                        <option value="Restaurant" <?= ($_GET['service_type'] ?? '') == 'Restaurant' ? 'selected' : '' ?>>Nhà hàng</option>
                                        <option value="Hotel" <?= ($_GET['service_type'] ?? '') == 'Hotel' ? 'selected' : '' ?>>Khách sạn</option>
                                        <option value="Vehicle" <?= ($_GET['service_type'] ?? '') == 'Vehicle' ? 'selected' : '' ?>>Phương tiện</option>
                                        <option value="Flight" <?= ($_GET['service_type'] ?? '') == 'Flight' ? 'selected' : '' ?>>Vé máy bay</option>
                                        <option value="Entrance" <?= ($_GET['service_type'] ?? '') == 'Entrance' ? 'selected' : '' ?>>Vé tham quan</option>
                                        <option value="Insurance" <?= ($_GET['service_type'] ?? '') == 'Insurance' ? 'selected' : '' ?>>Bảo hiểm</option>
                                        <option value="Other" <?= ($_GET['service_type'] ?? '') == 'Other' ? 'selected' : '' ?>>Khác</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label>Đối tác</label>
                                    <select class="form-control" name="partner_id">
                                        <option value="">-- Tất cả --</option>
                                        <?php foreach ($partners as $p): ?>
                                            <option value="<?= $p['partner_id'] ?>" <?= ($_GET['partner_id'] ?? '') == $p['partner_id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($p['partner_name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label>Trạng thái</label>
                                    <select class="form-control" name="status">
                                        <option value="">-- Tất cả --</option>
                                        <option value="1" <?= ($_GET['status'] ?? '') === '1' ? 'selected' : '' ?>>Hoạt động</option>
                                        <option value="0" <?= ($_GET['status'] ?? '') === '0' ? 'selected' : '' ?>>Ngừng HĐ</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label>Tìm kiếm</label>
                                    <input type="text" class="form-control" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" placeholder="Tên dịch vụ...">
                                </div>
                                <div class="col-md-1">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-primary btn-block"><i class="feather icon-search"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </section>

            <!-- Services List -->
            <section>
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Danh sách dịch vụ (<?= count($services) ?>)</h4>
                        <a href="?act=them-dich-vu" class="btn btn-primary"><i class="feather icon-plus"></i> Thêm dịch vụ</a>
                    </div>
                    <div class="card-content">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>STT</th>
                                        <th>Tên dịch vụ</th>
                                        <th>Loại</th>
                                        <th>Đối tác</th>
                                        <th>Giá</th>
                                        <th>Đơn vị</th>
                                        <th>Đánh giá</th>
                                        <th>Trạng thái</th>
                                        <th>Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($services)): ?>
                                        <tr><td colspan="9" class="text-center">Không có dữ liệu</td></tr>
                                    <?php else: ?>
                                        <?php
                                        $serviceTypeLabels = [
                                            'Restaurant' => 'Nhà hàng', 'Hotel' => 'Khách sạn', 
                                            'Vehicle' => 'Phương tiện', 'Flight' => 'Vé máy bay',
                                            'Entrance' => 'Vé tham quan', 'Insurance' => 'Bảo hiểm', 'Other' => 'Khác'
                                        ];
                                        ?>
                                        <?php foreach ($services as $index => $service): ?>
                                            <tr>
                                                <td><?= $index + 1 ?></td>
                                                <td><strong><?= htmlspecialchars($service['service_name']) ?></strong></td>
                                                <td><?= $serviceTypeLabels[$service['service_type']] ?? $service['service_type'] ?></td>
                                                <td><?= htmlspecialchars($service['partner_name'] ?? 'N/A') ?></td>
                                                <td><?= number_format($service['unit_price'], 0) ?> đ</td>
                                                <td><?= htmlspecialchars($service['unit']) ?></td>
                                                <td><span class="badge badge-warning"><i class="feather icon-star"></i> <?= number_format($service['rating'], 1) ?></span></td>
                                                <td>
                                                    <?php if ($service['status'] == 1): ?>
                                                        <span class="badge badge-success">Hoạt động</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-secondary">Ngừng HĐ</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="btn-group">
                                                        <a href="?act=sua-dich-vu&id=<?= $service['service_id'] ?>" class="btn btn-sm btn-primary" title="Sửa">
                                                            <i class="feather icon-edit"></i>
                                                        </a>
                                                        <a href="?act=xoa-dich-vu&id=<?= $service['service_id'] ?>" class="btn btn-sm btn-danger" 
                                                           onclick="return confirm('Xóa dịch vụ này?')" title="Xóa">
                                                            <i class="feather icon-trash-2"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<?php require_once './views/core/footer.php'; ?>
