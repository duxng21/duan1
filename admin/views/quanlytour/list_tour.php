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
            <!-- Thông báo -->
            <?php require_once __DIR__ . '/../core/alert.php'; ?>

            <section id="basic-datatable">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Danh sách Tour</h4>
                                <a href="?act=add-list"><button type="button"
                                        class="btn btn-primary mr-1 mb-1 waves-effect waves-light">Thêm danh
                                        sách</button></a>
                            </div>
                            <div class="card-content">
                                <div class="card-body card-dashboard">
                                    <!-- Bộ lọc -->
                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <form method="GET" action="" class="form-inline">
                                                <input type="hidden" name="act" value="list-tour">
                                                <label class="mr-2">Lọc theo danh mục:</label>
                                                <select name="category_id" class="form-control mr-2"
                                                    onchange="this.form.submit()">
                                                    <option value="">Tất cả danh mục</option>
                                                    <?php if (!empty($categories)): ?>
                                                        <?php foreach ($categories as $cat): ?>
                                                            <option value="<?= $cat['category_id'] ?>"
                                                                <?= (isset($_GET['category_id']) && $_GET['category_id'] == $cat['category_id']) ? 'selected' : '' ?>>
                                                                <?= htmlspecialchars($cat['category_name']) ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </select>
                                                <?php if (isset($_GET['category_id']) && $_GET['category_id']): ?>
                                                    <a href="?act=list-tour" class="btn btn-secondary btn-sm">Xóa bộ lọc</a>
                                                <?php endif; ?>
                                            </form>
                                        </div>
                                        <div class="col-md-8 text-right">
                                            <span class="badge badge-info">Tổng số: <?= count($tours) ?> tour</span>
                                        </div>
                                    </div>
                                    <!-- Kết thúc bộ lọc -->

                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered complex-headers">
                                            <thead>
                                                <tr>

                                                    <th>ID</th>
                                                    <th>Tên tour</th>
                                                    <th>Mã</th>
                                                    <th>Danh mục</th>
                                                    <th>Số ngày</th>
                                                    <th>Điểm xuất phát</th>
                                                    <th>Trạng thái</th>

                                                    <th>Hành động</th>

                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($tours as $row): ?>
                                                    <tr>
                                                        <td><?= $row['tour_id'] ?></td>
                                                        <td><?= htmlspecialchars($row['tour_name'] ?? '') ?></td>
                                                        <td><?= htmlspecialchars($row['code'] ?? '') ?></td>
                                                        <td><?= htmlspecialchars($row['category_name'] ?? 'Chưa phân loại') ?>
                                                        </td>
                                                        <td><?= $row['duration_days'] ?? 0 ?> ngày</td>
                                                        <td><?= htmlspecialchars($row['start_location'] ?? '') ?></td>
                                                        <td>
                                                            <?php
                                                            $statusClass = match ($row['status'] ?? 'Draft') {
                                                                'Public' => 'badge-success',
                                                                'Hidden' => 'badge-warning',
                                                                default => 'badge-secondary'
                                                            };
                                                            ?>
                                                            <span
                                                                class="badge <?= $statusClass ?>"><?= $row['status'] ?? 'Draft' ?></span>
                                                        </td>
                                                        <td>
                                                            <a href="?act=chi-tiet-tour&id=<?= $row['tour_id'] ?>"
                                                                class="btn btn-info btn-sm" title="Chi tiết tour">
                                                                <i class="feather icon-eye"></i>
                                                            </a>
                                                            <a href="?act=edit-list&id=<?= $row['tour_id'] ?>"
                                                                class="btn btn-warning btn-sm" title="Sửa">
                                                                <i class="feather icon-edit"></i>
                                                            </a>
                                                            <a onclick="return confirm('Xóa tour này?')"
                                                                href="?act=xoa-tour&id=<?= $row['tour_id'] ?>"
                                                                class="btn btn-danger btn-sm" title="Xóa">
                                                                <i class="feather icon-trash"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>

                                            </tbody>
                                            <tfoot>
                                            </tfoot>
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