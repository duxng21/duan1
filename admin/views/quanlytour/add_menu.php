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
            <section id="floating-label-layouts">
                <div class="row">
                    <div class="col-md-8 col-12 mx-auto">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">
                                    <?= isset($category) ? "Sửa Danh Mục" : "Thêm Danh Mục Mới" ?>
                                </h4>
                            </div>
                            <div class="card-content">
                                <div class="card-body">

                                    <?php if (isset($_SESSION['error'])): ?>
                                        <div class="alert alert-danger">
                                            <i class="feather icon-alert-circle"></i>
                                            <?= $_SESSION['error'];
                                            unset($_SESSION['error']); ?>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Form -->
                                    <form class="form" method="POST" action="?act=them-danh-muc"
                                        enctype="multipart/form-data">

                                        <input type="hidden" name="id" value="<?= $category['category_id'] ?? '' ?>">

                                        <div class="form-body">
                                            <div class="row">
                                                <!-- Tên danh mục -->
                                                <div class="col-md-6 col-12">
                                                    <div class="form-group">
                                                        <label for="category_name">Tên danh mục <span
                                                                class="text-danger">*</span></label>
                                                        <div class="position-relative has-icon-left">
                                                            <input type="text" id="category_name" class="form-control"
                                                                name="category_name" placeholder="VD: Tour miền Bắc"
                                                                value="<?= htmlspecialchars($category['category_name'] ?? '') ?>"
                                                                required>
                                                            <div class="form-control-position">
                                                                <i class="feather icon-tag"></i>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Loại danh mục -->
                                                <div class="col-md-6 col-12">
                                                    <div class="form-group">
                                                        <label for="category_type">Loại danh mục</label>
                                                        <div class="position-relative has-icon-left">
                                                            <input type="text" id="category_type" class="form-control"
                                                                name="category_type" placeholder="VD: Miền, Châu lục"
                                                                value="<?= htmlspecialchars($category['category_type'] ?? '') ?>">
                                                            <div class="form-control-position">
                                                                <i class="feather icon-folder"></i>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Mô tả -->
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label for="description">Mô tả</label>
                                                        <textarea id="description" class="form-control"
                                                            name="description" rows="3"
                                                            placeholder="Mô tả về danh mục này..."><?= htmlspecialchars($category['description'] ?? '') ?></textarea>
                                                    </div>
                                                </div>

                                                <!-- Hình ảnh -->
                                                <div class="col-md-6 col-12">
                                                    <div class="form-group">
                                                        <label for="image">Hình ảnh đại diện</label>
                                                        <input type="file" id="image" class="form-control" name="image"
                                                            accept="image/*">
                                                        <?php if (!empty($category['image'])): ?>
                                                            <small class="form-text text-muted">
                                                                Đang dùng: <code><?= basename($category['image']) ?></code>
                                                            </small>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>

                                                <!-- Trạng thái -->
                                                <div class="col-md-6 col-12">
                                                    <div class="form-group">
                                                        <label>Trạng thái</label>
                                                        <div class="custom-control custom-switch custom-control-inline">
                                                            <input type="checkbox" class="custom-control-input"
                                                                id="status" name="status" value="1"
                                                                <?= (!isset($category) || $category['status'] == 1) ? 'checked' : '' ?>>
                                                            <label class="custom-control-label" for="status">
                                                                <span class="switch-text-left">Hoạt động</span>
                                                                <span class="switch-text-right">Ẩn</span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Buttons -->
                                                <div class="col-12">
                                                    <button type="submit"
                                                        class="btn btn-primary mr-1 waves-effect waves-light">
                                                        <i class="feather icon-check"></i>
                                                        <?= isset($category) ? "Cập nhật" : "Thêm mới" ?>
                                                    </button>

                                                    <a href="?act=menu-tour"
                                                        class="btn btn-outline-secondary waves-effect">
                                                        <i class="feather icon-x"></i>
                                                        Hủy
                                                    </a>
                                                </div>
                                            </div>
                                        </div>

                                    </form>

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