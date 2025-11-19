<?php require_once __DIR__ . '/../core/header.php'; ?>
<?php require_once __DIR__ . '/../core/menu.php'; ?>
<?php require_once __DIR__ . '/../core/alert.php'; ?>
<!-- BEGIN: Content-->
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
        </div>
        <div class="content-body">
            <section id="data-list-view" class="data-list-view-header">
                <div class="d-flex justify-content-between align-items-center mb-2">

    <!-- FILTER BÊN TRÁI -->
    <form method="GET" action="" class="form-inline m-0 p-0">
        <input type="hidden" name="act" value="list-tour">

        <div class="form-group mr-1 mb-1">
            <label class="mr-50">Lọc theo danh mục:</label>
            <select name="category_id" class="custom-select" onchange="this.form.submit()">
                <option value="">Tất cả danh mục</option>
                <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['category_id'] ?>"
                    <?= (isset($_GET['category_id']) && $_GET['category_id'] == $cat['category_id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['category_name']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>

        <?php if (!empty($_GET['category_id'])): ?>
            <a href="?act=list-tour" class="btn btn-outline-danger btn-sm ml-1">Xóa lọc</a>
        <?php endif; ?>
    </form>

    <!-- NÚT THÊM MỚI BÊN PHẢI -->
    <a href="?act=them-booking" class="btn btn-primary">
        <i class="feather icon-plus"></i> Thêm mới
    </a>

</div>

                    <!-- DataTable starts -->
                    <div class="table-responsive">
                        <!-- Thay đổi: đồng bộ header và body với bảng bên dưới, sửa lỗi ký tự '<' dư và escape dữ liệu -->
                        <table class="table data-list-view">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>TÊN KHÁCH</th>
                                    <th>SỐ ĐIỆN THOẠI</th>
                                    <th>SỐ NGƯỜI LỚN</th>
                                    <th>SỐ TRẺ EM</th>
                                    <th>LOẠI BOOKING</th>
                                    <th>LOẠI TOUR</th>
                                    <th>TỐNG TIỀN</th>
                                    <th>HÀNH ĐỘNG</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td></td>
                                    <td class="product-name"></td>
                                    <td class="product-category"></td>
                                    <td></td>
                                    <td></td>
                                    <td class="product-price"></td>
                                    <td></td>
                                    <td></td>
                                    <td class="product-action">
                                        <a href="?act=edit-list&id=<?= htmlspecialchars($row['tour_id'] ?? '') ?>"><span class="action-edit"><i class="feather icon-edit"></i></span></a>
                                        <a onclick="return confirm('Xóa tour này?')" href="?act=xoa-tour&id=<?= htmlspecialchars($row['tour_id'] ?? '') ?>"><span class="action-delete"><i class="feather icon-trash"></i></span></a>
                                        <a href=""><span class="action-detail"><i class="feather icon-eye"></i></span></a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- DataTable ends -->
                </section>
        </div>
    </div>
</div>
<!-- END: Content-->
<?php require_once __DIR__ . '/../core/footer.php'; ?>