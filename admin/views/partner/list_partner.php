<?php require_once './views/core/header.php'; ?>
<?php require_once './views/core/menu.php'; ?>

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
                                <li class="breadcrumb-item active">Danh sách đối tác</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-body">
            <!-- Alert messages -->
            <?php require_once './views/core/alert.php'; ?>

            <!-- Search & Filter -->
            <section>
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Tìm kiếm & Lọc</h4>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="">
                            <input type="hidden" name="act" value="danh-sach-doi-tac">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Loại đối tác</label>
                                        <select class="form-control" name="partner_type">
                                            <option value="">-- Tất cả --</option>
                                            <option value="Restaurant" <?= isset($_GET['partner_type']) && $_GET['partner_type'] == 'Restaurant' ? 'selected' : '' ?>>Nhà hàng</option>
                                            <option value="Hotel" <?= isset($_GET['partner_type']) && $_GET['partner_type'] == 'Hotel' ? 'selected' : '' ?>>Khách sạn</option>
                                            <option value="Transportation" <?= isset($_GET['partner_type']) && $_GET['partner_type'] == 'Transportation' ? 'selected' : '' ?>>Vận tải</option>
                                            <option value="Airline" <?= isset($_GET['partner_type']) && $_GET['partner_type'] == 'Airline' ? 'selected' : '' ?>>Hàng không</option>
                                            <option value="Other" <?= isset($_GET['partner_type']) && $_GET['partner_type'] == 'Other' ? 'selected' : '' ?>>Khác</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Trạng thái</label>
                                        <select class="form-control" name="status">
                                            <option value="">-- Tất cả --</option>
                                            <option value="1" <?= isset($_GET['status']) && $_GET['status'] == '1' ? 'selected' : '' ?>>Hoạt động</option>
                                            <option value="0" <?= isset($_GET['status']) && $_GET['status'] == '0' ? 'selected' : '' ?>>Ngừng hoạt động</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Tìm kiếm</label>
                                        <input type="text" class="form-control" name="search" 
                                               value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" 
                                               placeholder="Tên, người liên hệ, SĐT, email...">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="feather icon-search"></i> Tìm kiếm
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </section>

            <!-- Partners List -->
            <section>
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Danh sách đối tác (<?= count($partners) ?>)</h4>
                        <a href="?act=them-doi-tac" class="btn btn-primary">
                            <i class="feather icon-plus"></i> Thêm đối tác
                        </a>
                    </div>
                    <div class="card-content">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>STT</th>
                                        <th>Tên đối tác</th>
                                        <th>Loại</th>
                                        <th>Người liên hệ</th>
                                        <th>Điện thoại</th>
                                        <th>Email</th>
                                        <th>Đánh giá</th>
                                        <th>Trạng thái</th>
                                        <th>Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($partners)): ?>
                                        <tr>
                                            <td colspan="9" class="text-center">Không có dữ liệu</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($partners as $index => $partner): ?>
                                            <tr>
                                                <td><?= $index + 1 ?></td>
                                                <td><strong><?= htmlspecialchars($partner['partner_name']) ?></strong></td>
                                                <td>
                                                    <?php
                                                    $typeLabels = [
                                                        'Restaurant' => 'Nhà hàng',
                                                        'Hotel' => 'Khách sạn',
                                                        'Transportation' => 'Vận tải',
                                                        'Airline' => 'Hàng không',
                                                        'Other' => 'Khác'
                                                    ];
                                                    echo $typeLabels[$partner['partner_type']] ?? $partner['partner_type'];
                                                    ?>
                                                </td>
                                                <td><?= htmlspecialchars($partner['contact_person'] ?? 'N/A') ?></td>
                                                <td><?= htmlspecialchars($partner['phone'] ?? 'N/A') ?></td>
                                                <td><?= htmlspecialchars($partner['email'] ?? 'N/A') ?></td>
                                                <td>
                                                    <span class="badge badge-warning">
                                                        <i class="feather icon-star"></i> <?= number_format($partner['rating'], 1) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if ($partner['status'] == 1): ?>
                                                        <span class="badge badge-success">Hoạt động</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-secondary">Ngừng HĐ</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="?act=sua-doi-tac&id=<?= $partner['partner_id'] ?>" 
                                                           class="btn btn-sm btn-primary" title="Sửa">
                                                            <i class="feather icon-edit"></i>
                                                        </a>
                                                        <a href="?act=xoa-doi-tac&id=<?= $partner['partner_id'] ?>" 
                                                           class="btn btn-sm btn-danger" 
                                                           onclick="return confirm('Bạn có chắc muốn xóa đối tác này?')" 
                                                           title="Xóa">
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
