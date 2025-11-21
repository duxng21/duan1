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

                    <!-- NÚT THÊM MỚI BÊN PHẢI -->
                    <a href="?act=add-list" class="btn btn-primary">
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
                                <tr>
                                    <td><?= htmlspecialchars($row['tour_id'] ?? '') ?></td>
                                    <td class="product-name"><?= htmlspecialchars($row['tour_name'] ?? '') ?></td>
                                    <td class="product-category"><?= htmlspecialchars($row['code'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($row['category_name'] ?? 'Chưa phân loại') ?></td>
                                    <td><?= htmlspecialchars($row['duration_days'] ?? 0) ?> ngày</td>
                                    <td class="product-price"><?= htmlspecialchars($row['start_location'] ?? '') ?></td>
                                    <td><?php
                                    $statusClass = match ($row['status'] ?? 'Draft') {
                                        'Public' => 'badge-success',
                                        'Hidden' => 'badge-warning',
                                        default => 'badge-secondary'
                                    };
                                    ?>
                                        <span class="badge <?= $statusClass ?>"><?= htmlspecialchars($row['status'] ?? 'Draft') ?></span>
                                    </td>
                                    <td class="product-action">
                                        <a href="?act=chi-tiet-tour&id=<?= htmlspecialchars($row['tour_id'] ?? '') ?>"
                                            title="Chi tiết tour"><span class="action-detail"><i
                                                    class="feather icon-eye"></i></span></a>
                                        <a href="?act=edit-list&id=<?= htmlspecialchars($row['tour_id'] ?? '') ?>"
                                            title="Sửa tour"><span class="action-edit"><i
                                                    class="feather icon-edit"></i></span></a>
                                        <a onclick="return confirm('Xóa tour này?')"
                                            href="?act=xoa-tour&id=<?= htmlspecialchars($row['tour_id'] ?? '') ?>"
                                            title="Xóa tour"><span class="action-delete"><i
                                                    class="feather icon-trash"></i></span></a>
                                    </td>
                                </tr>
                                    <tr id="detail-row-<?= $tourId ?>" class="collapse bg-light">
                                        <td colspan="8" class="p-2">
                                            <div class="row no-gutters">
                                                <div class="col-md-3 col-sm-4 col-12 pr-2 mb-2">
                                                    <?php if (!empty($row['tour_image'])): ?>
                                                        <img src="<?= BASE_URL . htmlspecialchars($row['tour_image']) ?>" alt="Ảnh tour" class="img-fluid rounded shadow-sm w-100">
                                                    <?php else: ?>
                                                        <div class="text-muted small">Chưa có ảnh đại diện</div>
                                                    <?php endif; ?>
                                                    <div class="mt-1 small">
                                                        <strong>Ảnh:</strong> <?= (int)($row['image_count'] ?? 0) ?>
                                                    </div>
                                                </div>
                                                <div class="col-md-9 col-sm-8 col-12 pl-2">
                                                    <div class="row">
                                                        <div class="col-md-6 col-12 small">
                                                            <div class="mb-1"><strong>Giá chuẩn:</strong> <?= isset($row['tour_price']) ? number_format($row['tour_price']) . ' đ' : 'Chưa có'; ?></div>
                                                            <div class="mb-1"><strong>Điểm xuất phát:</strong> <?= htmlspecialchars($row['start_location'] ?? 'Đang cập nhật') ?></div>
                                                            <div class="mb-1"><strong>Lịch khởi hành:</strong> <?= (int)($row['schedule_count'] ?? 0) ?> lịch</div>
                                                            <div class="mb-1"><strong>Lịch trình ngày:</strong> <?= (int)($row['itinerary_count'] ?? 0) ?> mục</div>
                                                        </div>
                                                        <div class="col-md-6 col-12 small">
                                                            <div class="mb-1"><strong>Tags:</strong> <?= !empty($row['tag_list']) ? htmlspecialchars($row['tag_list']) : '<span class="text-muted">Chưa có</span>' ?></div>
                                                            <div class="mb-1"><strong>Chính sách:</strong> <?= (isset($row['has_policies']) && $row['has_policies'] > 0) ? '<span class="text-success">Đã thiết lập</span>' : '<span class="text-muted">Chưa có</span>' ?></div>
                                                            <div class="mb-1"><strong>Trạng thái:</strong> <span class="badge <?= $statusClass ?>"><?= htmlspecialchars($row['status'] ?? 'Draft') ?></span></div>
                                                        </div>
                                                    </div>
                                                    <div class="mt-2">
                                                        <a href="?act=chi-tiet-tour&id=<?= $tourId ?>" class="btn btn-sm btn-primary">Quản lý chi tiết đầy đủ</a>
                                                        <a href="?act=them-lich-khoi-hanh&tour_id=<?= $tourId ?>" class="btn btn-sm btn-outline-success">Thêm lịch khởi hành</a>
                                                        <a href="?act=edit-list&id=<?= $tourId ?>" class="btn btn-sm btn-outline-warning">Sửa thông tin</a>
                                                    </div>
                                                </div>
                                            </div>
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
