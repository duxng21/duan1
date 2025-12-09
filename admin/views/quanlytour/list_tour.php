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
                                    <option value="<?= $cat['category_id'] ?>" <?= (isset($_GET['category_id']) && $_GET['category_id'] == $cat['category_id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat['category_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <?php if (!empty($_GET['category_id'])): ?>
                            <a href="?act=list-tour" class="btn btn-outline-danger btn-sm ml-1">Xóa lọc</a>
                        <?php endif; ?>
                    </form>

                    <!-- NÚT THÊM MỚI & BULK SEED BÊN PHẢI -->
                    <div class="btn-group">
                        <a href="?act=add-list" class="btn btn-primary">
                            <i class="feather icon-plus"></i> Thêm mới
                        </a>
                        <?php if (function_exists('isAdmin') && isAdmin()): ?>
                            <a href="?act=seed-all-tours"
                                onclick="return confirm('Seed lịch trình & chính sách mẫu cho tất cả tour chưa có?');"
                                class="btn btn-outline-secondary">
                                <i class="feather icon-database"></i> Seed tất cả
                            </a>
                        <?php endif; ?>
                    </div>

                </div>

                <!-- DataTable starts -->
                <div class="table-responsive">
                    <!-- Thay đổi: đồng bộ header và body với bảng bên dưới, sửa lỗi ký tự '<' dư và escape dữ liệu -->
                    <style>
                        /* Tăng kích thước chữ bảng tour một chút */
                        .tour-table td,
                        .tour-table th {
                            font-size: 0.95rem;
                        }

                        .tour-table .product-name {
                            font-weight: 600;
                        }

                        .tour-table tbody tr {
                            cursor: pointer;
                        }

                        .tour-table .collapse td {
                            font-size: 0.85rem;
                        }
                    </style>
                    <table class="table data-list-view tour-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>TÊN TOUR</th>
                                <th>MÃ</th>
                                <th>DANH MỤC</th>
                                <th>SỐ NGÀY</th>
                                <th>ĐIỂM XUẤT PHÁT</th>
                                <th>TRẠNG THÁI</th>
                                <th>HÀNH ĐỘNG</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tours as $row): ?>
                                <?php $tourId = htmlspecialchars($row['tour_id'] ?? ''); ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['tour_id'] ?? '') ?></td>
                                    <td class="product-name"><?= htmlspecialchars($row['tour_name'] ?? '') ?></td>
                                    <td class="product-category"><?= htmlspecialchars($row['code'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($row['category_name'] ?? 'Chưa phân loại') ?></td>
                                    <td><?= htmlspecialchars($row['duration_days'] ?? 0) ?> ngày</td>
                                    <td class="product-price"><?= htmlspecialchars($row['start_location'] ?? '') ?></td>
                                    <td><?php
                                    switch ($row['status'] ?? 'Draft') {
                                        case 'Public':
                                            $statusClass = 'badge-success';
                                            break;
                                        case 'Hidden':
                                            $statusClass = 'badge-warning';
                                            break;
                                        default:
                                            $statusClass = 'badge-secondary';
                                            break;
                                    }
                                    ?>
                                        <span
                                            class="badge <?= $statusClass ?>"><?= htmlspecialchars($row['status'] ?? 'Draft') ?></span>
                                    </td>
                                    <td class="product-action">
                                        <a href="?act=edit-list&id=<?= htmlspecialchars($row['tour_id'] ?? '') ?>"
                                            title="Sửa tour"><span class="action-edit"><i
                                                    class="feather icon-edit"></i></span></a>
                                        <a href="?act=clone-tour-form&id=<?= htmlspecialchars($row['tour_id'] ?? '') ?>"
                                            title="Clone tour"><span class="action-clone"><i
                                                    class="feather icon-copy text-info"></i></span></a>
                                        <a href="?act=quan-ly-phien-ban&tour_id=<?= htmlspecialchars($row['tour_id'] ?? '') ?>"
                                            title="Quản lý phiên bản"><span class="action-versions"><i
                                                    class="feather icon-layers text-primary"></i></span></a>
                                        <a onclick="return confirm('Xóa tour này?')"
                                            href="?act=xoa-tour&id=<?= htmlspecialchars($row['tour_id'] ?? '') ?>"
                                            title="Xóa tour"><span class="action-delete"><i
                                                    class="feather icon-trash"></i></span></a>
                                        <?php if (function_exists('isAdmin') && isAdmin() && ((int) ($row['itinerary_count'] ?? 0) === 0 || (int) ($row['has_policies'] ?? 0) === 0)): ?>
                                            <a href="?act=seed-tour-data&id=<?= $tourId ?>" title="Seed dữ liệu mẫu"
                                                onclick="return confirm('Seed lịch trình & chính sách mẫu cho tour này?');">
                                                <span class="action-seed"><i class="feather icon-database"></i></span>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>

                            <?php endforeach; ?>
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