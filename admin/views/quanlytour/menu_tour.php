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
            <section id="basic-datatable">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Danh mục Tour</h4>

                                <!-- ⭐ LINK ĐÚNG THEO ROUTER -->
                                <a href="?act=them-danh-muc">
                                    <button type="button" class="btn btn-primary mr-1 mb-1 waves-effect waves-light">
                                        Thêm danh mục
                                    </button>
                                </a>

                            </div>
                            <div class="card-content">
                                <div class="card-body card-dashboard">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered complex-headers">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Tên Tour</th>
                                                    <th>Hành động</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                <?php if (!empty($categories)) : ?>
                                                <?php foreach ($categories as $index => $category) : ?>
                                                <tr>
                                                    <td><?= $index + 1 ?></td>
                                                    <td><?= $category['category_name']; ?></td>
                                                    <td>
                                                        <!-- ⭐ SỬA ĐÚNG ROUTER -->
                                                        <a href="?act=them-danh-muc&id=<?= $category['category_id'] ?>">
                                                            <button type="button"
                                                                class="btn btn-sm bg-gradient-warning mr-1 mb-1 waves-effect waves-light">
                                                                Sửa
                                                            </button>
                                                        </a>

                                                        <!-- ⭐ XOÁ ĐÚNG ROUTER -->
                                                        <a href="?act=menu-tour&delete_id=<?= $category['category_id'] ?>"
                                                            onclick="return confirm('Bạn có chắc chắn muốn xoá không?');">
                                                            <button type="button"
                                                                class="btn btn-sm bg-gradient-danger mr-1 mb-1 waves-effect waves-light">
                                                                Xóa
                                                            </button>
                                                        </a>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                                <?php else : ?>
                                                <tr>
                                                    <td colspan="3" class="text-center text-muted">
                                                        Không có danh mục nào
                                                    </td>
                                                </tr>
                                                <?php endif; ?>
                                            </tbody>

                                            <tfoot></tfoot>
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