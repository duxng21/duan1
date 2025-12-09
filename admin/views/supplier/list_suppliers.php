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
                        <h2 class="content-header-title float-left mb-0">Quản lý đối tác</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="?act=/">Trang chủ</a></li>
                                <li class="breadcrumb-item active">Đối tác</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-body">
            <?php require_once __DIR__ . '/../core/alert.php'; ?>

            <!-- Statistics Cards -->
            <div class="row">
                <?php if (!empty($statistics)): ?>
                    <?php
                    $typeIcons = [
                        'Hotel' => 'home',
                        'Restaurant' => 'shopping-bag',
                        'Transport' => 'truck',
                        'Guide' => 'user',
                        'Activity' => 'star',
                        'Insurance' => 'shield',
                        'Other' => 'box'
                    ];
                    $typeColors = [
                        'Hotel' => 'primary',
                        'Restaurant' => 'warning',
                        'Transport' => 'success',
                        'Guide' => 'info',
                        'Activity' => 'danger',
                        'Insurance' => 'secondary',
                        'Other' => 'dark'
                    ];
                    foreach ($statistics as $stat):
                        $icon = $typeIcons[$stat['supplier_type']] ?? 'box';
                        $color = $typeColors[$stat['supplier_type']] ?? 'primary';
                        ?>
                        <div class="col-xl-2 col-md-4 col-sm-6">
                            <div class="card text-center">
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="avatar bg-rgba-<?= $color ?> p-50 mb-1">
                                            <div class="avatar-content">
                                                <i class="feather icon-<?= $icon ?> text-<?= $color ?> font-medium-5"></i>
                                            </div>
                                        </div>
                                        <h2 class="text-bold-700"><?= $stat['active'] ?>/<?= $stat['total'] ?></h2>
                                        <p class="mb-0"><?= htmlspecialchars($stat['supplier_type']) ?></p>
                                        <?php if ($stat['avg_rating'] > 0): ?>
                                            <small class="text-muted">⭐ <?= number_format($stat['avg_rating'], 1) ?></small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Filter & Search -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Tìm kiếm & lọc</h4>
                </div>
                <div class="card-content">
                    <div class="card-body">
                        <form method="GET" action="">
                            <input type="hidden" name="act" value="danh-sach-doi-tac">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Loại đối tác</label>
                                        <select name="supplier_type" class="form-control">
                                            <option value="">-- Tất cả --</option>
                                            <option value="Hotel" <?= ($filters['supplier_type'] ?? '') == 'Hotel' ? 'selected' : '' ?>>Khách sạn</option>
                                            <option value="Restaurant" <?= ($filters['supplier_type'] ?? '') == 'Restaurant' ? 'selected' : '' ?>>Nhà hàng</option>
                                            <option value="Transport" <?= ($filters['supplier_type'] ?? '') == 'Transport' ? 'selected' : '' ?>>Vận chuyển</option>
                                            <option value="Guide" <?= ($filters['supplier_type'] ?? '') == 'Guide' ? 'selected' : '' ?>>Hướng dẫn viên</option>
                                            <option value="Activity" <?= ($filters['supplier_type'] ?? '') == 'Activity' ? 'selected' : '' ?>>Hoạt động</option>
                                            <option value="Insurance" <?= ($filters['supplier_type'] ?? '') == 'Insurance' ? 'selected' : '' ?>>Bảo hiểm</option>
                                            <option value="Other" <?= ($filters['supplier_type'] ?? '') == 'Other' ? 'selected' : '' ?>>Khác</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Trạng thái</label>
                                        <select name="status" class="form-control">
                                            <option value="">-- Tất cả --</option>
                                            <option value="1" <?= ($filters['status'] ?? '') === '1' ? 'selected' : '' ?>>
                                                Hoạt động</option>
                                            <option value="0" <?= ($filters['status'] ?? '') === '0' ? 'selected' : '' ?>>
                                                Ngừng</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label>Tìm kiếm</label>
                                        <input type="text" name="search" class="form-control"
                                            placeholder="Tên, mã, người liên hệ..."
                                            value="<?= htmlspecialchars($filters['search'] ?? '') ?>">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <label class="d-block">&nbsp;</label>
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="feather icon-search"></i> Lọc
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Suppliers List -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Danh sách đối tác (<?= count($suppliers) ?>)</h4>
                    <div class="heading-elements">
                        <a href="?act=them-doi-tac" class="btn btn-primary">
                            <i class="feather icon-plus"></i> Thêm đối tác
                        </a>
                    </div>
                </div>
                <div class="card-content">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Mã</th>
                                    <th>Tên đối tác</th>
                                    <th>Loại</th>
                                    <th>Người liên hệ</th>
                                    <th>Điện thoại</th>
                                    <th>Email</th>
                                    <th>Đánh giá</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($suppliers)): ?>
                                    <tr>
                                        <td colspan="10" class="text-center text-muted">Không có dữ liệu</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($suppliers as $index => $supplier): ?>
                                        <tr>
                                            <td><?= $index + 1 ?></td>
                                            <td>
                                                <span class="badge badge-light-secondary">
                                                    <?= htmlspecialchars($supplier['supplier_code'] ?? 'N/A') ?>
                                                </span>
                                            </td>
                                            <td>
                                                <strong><?= htmlspecialchars($supplier['supplier_name']) ?></strong>
                                            </td>
                                            <td>
                                                <?php
                                                $typeLabels = [
                                                    'Hotel' => 'Khách sạn',
                                                    'Restaurant' => 'Nhà hàng',
                                                    'Transport' => 'Vận chuyển',
                                                    'Guide' => 'HDV',
                                                    'Activity' => 'Hoạt động',
                                                    'Insurance' => 'Bảo hiểm',
                                                    'Other' => 'Khác'
                                                ];
                                                $type = $supplier['supplier_type'];
                                                $color = $typeColors[$type] ?? 'primary';
                                                ?>
                                                <span class="badge badge-<?= $color ?>">
                                                    <?= $typeLabels[$type] ?? $type ?>
                                                </span>
                                            </td>
                                            <td><?= htmlspecialchars($supplier['contact_person'] ?? '-') ?></td>
                                            <td>
                                                <?php if (!empty($supplier['phone'])): ?>
                                                    <a href="tel:<?= $supplier['phone'] ?>">
                                                        <?= htmlspecialchars($supplier['phone']) ?>
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($supplier['email'])): ?>
                                                    <a href="mailto:<?= $supplier['email'] ?>">
                                                        <?= htmlspecialchars($supplier['email']) ?>
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($supplier['rating'] > 0): ?>
                                                    <span class="badge badge-light-warning">
                                                        ⭐ <?= number_format($supplier['rating'], 1) ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($supplier['status'] == 1): ?>
                                                    <span class="badge badge-success">Hoạt động</span>
                                                <?php else: ?>
                                                    <span class="badge badge-secondary">Ngừng</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="?act=xem-doi-tac&id=<?= $supplier['supplier_id'] ?>"
                                                        class="btn btn-sm btn-info" title="Xem chi tiết">
                                                        <i class="feather icon-eye"></i>
                                                    </a>
                                                    <?php if (isAdmin()): ?>
                                                        <a href="?act=sua-doi-tac&id=<?= $supplier['supplier_id'] ?>"
                                                            class="btn btn-sm btn-warning" title="Sửa">
                                                            <i class="feather icon-edit"></i>
                                                        </a>
                                                        <a href="?act=xoa-doi-tac&id=<?= $supplier['supplier_id'] ?>"
                                                            class="btn btn-sm btn-danger" title="Xóa"
                                                            onclick="return confirm('Bạn có chắc muốn xóa đối tác này?')">
                                                            <i class="feather icon-trash-2"></i>
                                                        </a>
                                                    <?php endif; ?>
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
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../core/footer.php'; ?>