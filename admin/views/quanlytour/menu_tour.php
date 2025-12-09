<?php require_once __DIR__ . '/../core/header.php'; ?>
<?php require_once __DIR__ . '/../core/menu.php'; ?>
<!-- BEGIN: Content-->
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
        </div>
        <div class="content-body">
            <!-- Alert messages -->
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="feather icon-check-circle"></i>
                    <?= $_SESSION['success'];
                    unset($_SESSION['success']); ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="feather icon-alert-circle"></i>
                    <?= $_SESSION['error'];
                    unset($_SESSION['error']); ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <section id="basic-datatable">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Danh mục Tour</h4>

                                <!-- ⭐ LINK ĐÚNG THEO ROUTER -->
                                <div>
                                    <a href="?act=them-danh-muc"
                                        class="btn btn-primary mr-1 mb-1 waves-effect waves-light">
                                        Thêm danh mục
                                    </a>
                                    <a href="?act=seed-categories" class="btn btn-outline-secondary mr-1 mb-1">
                                        <i class="feather icon-database"></i> Seed danh mục mẫu
                                    </a>
                                </div>

                            </div>
                            <div class="card-content">
                                <div class="card-body card-dashboard">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered zero-configuration">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Tên Danh Mục</th>
                                                    <th>Số Tour</th>
                                                    <th>Trạng Thái</th>
                                                    <th>Hành động</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                <?php if (!empty($categories)): ?>
                                                    <?php foreach ($categories as $index => $category): ?>
                                                        <tr>
                                                            <td><?= $index + 1 ?></td>
                                                            <td>
                                                                <strong><?= htmlspecialchars($category['category_name']) ?></strong>
                                                                <?php if (!empty($category['description'])): ?>
                                                                    <br><small
                                                                        class="text-muted"><?= htmlspecialchars(substr($category['description'], 0, 50)) ?><?= strlen($category['description']) > 50 ? '...' : '' ?></small>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <span class="badge badge-primary badge-pill">
                                                                    <?= $category['tour_count'] ?? 0 ?> tour
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <?php if ($category['status'] == 1): ?>
                                                                    <span class="badge badge-success">Hoạt động</span>
                                                                <?php else: ?>
                                                                    <span class="badge badge-secondary">Ẩn</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <a href="?act=them-danh-muc&id=<?= $category['category_id'] ?>"
                                                                    class="btn btn-sm btn-warning" title="Sửa">
                                                                    <i class="feather icon-edit"></i>
                                                                </a>

                                                                <a href="?act=clone-category&id=<?= $category['category_id'] ?>"
                                                                    class="btn btn-sm btn-info" title="Nhân bản">
                                                                    <i class="feather icon-copy"></i>
                                                                </a>

                                                                <?php if (($category['tour_count'] ?? 0) == 0): ?>
                                                                    <a href="?act=menu-tour&delete_id=<?= $category['category_id'] ?>"
                                                                        onclick="return confirm('Bạn có chắc chắn muốn xóa danh mục \'<?= htmlspecialchars($category['category_name']) ?>\' không?');"
                                                                        class="btn btn-sm btn-danger" title="Xóa">
                                                                        <i class="feather icon-trash"></i>
                                                                    </a>
                                                                <?php else: ?>
                                                                    <button class="btn btn-sm btn-secondary" disabled
                                                                        title="Không thể xóa danh mục đang có tour">
                                                                        <i class="feather icon-trash"></i>
                                                                    </button>
                                                                <?php endif; ?>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="5" class="text-center text-muted">
                                                            <i class="feather icon-inbox"
                                                                style="font-size: 48px; opacity: 0.3;"></i>
                                                            <p>Chưa có danh mục nào</p>
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
            </section>
        </div>
    </div>
</div>
<!-- END: Content-->
<?php require_once __DIR__ . '/../core/footer.php'; ?>