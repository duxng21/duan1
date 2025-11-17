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
                    <div class="col-md-6 col-12 mx-auto">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">
                                    <?= isset($category) ? "Sửa Danh Mục" : "Thêm Danh Mục Mới" ?>
                                </h4>
                            </div>
                            <div class="card-content">
                                <div class="card-body">

                                    <!-- ⭐ Form đúng router -->
                                    <form class="form" method="POST" action="?act=them-danh-muc">

                                        <!-- Hidden ID để biết đang sửa hay thêm -->
                                        <input type="hidden" name="id" value="<?= $category['category_id'] ?? '' ?>">

                                        <div class="form-body">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-label-group position-relative has-icon-left">

                                                        <input type="text" id="first-name-floating-icon"
                                                            class="form-control" name="category_name"
                                                            placeholder="Tên danh mục"
                                                            value="<?= $category['category_name'] ?? '' ?>">

                                                        <div class="form-control-position">
                                                            <i class="feather icon-hash"></i>
                                                        </div>
                                                        <label for="first-name-floating-icon">Tên danh mục</label>
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <button type="submit"
                                                        class="btn btn-primary mr-1 mb-1 waves-effect waves-light">
                                                        <?= isset($category) ? "Cập nhật" : "Thêm" ?>
                                                    </button>

                                                    <a href="?act=menu-tour"
                                                        class="btn btn-outline-warning mr-1 mb-1 waves-effect waves-light">
                                                        Hủy
                                                    </a>
                                                </div>
                                            </div>
                                        </div>

                                    </form>
                                    <!-- ⭐ Kết thúc form -->

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