<?php require_once __DIR__ . '/../core/header.php'; ?>
<?php require_once __DIR__ . '/../core/menu.php'; ?>
<?php require_once __DIR__ . '/../core/alert.php'; ?>
<!-- BEGIN: Content-->
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">Danh sách nhân sự</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">
            <section id="staff-list">
                <div class="row">
                    <!-- Thống kê -->
                    <div class="col-12 mb-3">
                        <div class="row">
                            <?php
                            $stats = [
                                'Guide' => ['total' => 0, 'active' => 0, 'icon' => 'user', 'color' => 'primary', 'title' => 'Hướng dẫn viên'],
                                'Manager' => ['total' => 0, 'active' => 0, 'icon' => 'briefcase', 'color' => 'success', 'title' => 'Quản lý']
                            ];

                            foreach ($statistics as $stat) {
                                $stats[$stat['staff_type']]['total'] = $stat['total'];
                                $stats[$stat['staff_type']]['active'] = $stat['active_count'];
                            }

                            foreach ($stats as $type => $data):
                                ?>
                                <div class="col-xl-3 col-sm-6 col-12">
                                    <div class="card">
                                        <div class="card-content">
                                            <div class="card-body">
                                                <div class="media d-flex">
                                                    <div class="align-self-center">
                                                        <i
                                                            class="feather icon-<?= $data['icon'] ?> text-<?= $data['color'] ?> font-large-2 float-left"></i>
                                                    </div>
                                                    <div class="media-body text-right">
                                                        <h3 class="text-<?= $data['color'] ?>">
                                                            <?= $data['active'] ?>/<?= $data['total'] ?>
                                                        </h3>
                                                        <span><?= $data['title'] ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Danh sách nhân sự</h4>
                                <div>
                                    <a href="?act=thong-ke-nhan-su" class="btn btn-info btn-sm mr-1">
                                        <i class="feather icon-bar-chart-2"></i> Thống kê
                                    </a>
                                    <a href="?act=xuat-excel-nhan-su<?= !empty($_GET['type']) ? '&type=' . $_GET['type'] : '' ?><?= !empty($_GET['status']) ? '&status=' . $_GET['status'] : '' ?>"
                                        class="btn btn-success btn-sm mr-1">
                                        <i class="feather icon-download"></i> Xuất Excel
                                    </a>
                                    <a href="?act=them-nhan-su" class="btn btn-primary btn-sm">
                                        <i class="feather icon-plus"></i> Thêm nhân sự
                                    </a>
                                </div>
                            </div>
                            <div class="card-content">
                                <div class="card-body card-dashboard">
                                    <!-- === Use Case 1: Bộ lọc và tìm kiếm (A1, A2) === -->
                                    <form method="GET" action="" class="mb-3">
                                        <input type="hidden" name="act" value="danh-sach-nhan-su">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label>Loại nhân sự:</label>
                                                <select name="type" class="form-control">
                                                    <option value="">Tất cả</option>
                                                    <option value="Guide" <?= ($_GET['type'] ?? '') == 'Guide' ? 'selected' : '' ?>>Hướng dẫn viên</option>
                                                    <option value="Manager" <?= ($_GET['type'] ?? '') == 'Manager' ? 'selected' : '' ?>>Quản lý</option>
                                                    <option value="Driver" <?= ($_GET['type'] ?? '') == 'Driver' ? 'selected' : '' ?>>Tài xế</option>
                                                    <option value="Support" <?= ($_GET['type'] ?? '') == 'Support' ? 'selected' : '' ?>>Hỗ trợ</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label>Trạng thái làm việc:</label>
                                                <select name="status" class="form-control">
                                                    <option value="">Tất cả</option>
                                                    <option value="1" <?= ($_GET['status'] ?? '') === '1' ? 'selected' : '' ?>>Đang hoạt động</option>
                                                    <option value="0" <?= ($_GET['status'] ?? '') === '0' ? 'selected' : '' ?>>Nghỉ phép/Nghỉ việc</option>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label>Phân loại:</label>
                                                <select name="category" class="form-control">
                                                    <option value="">Tất cả</option>
                                                    <option value="Domestic" <?= ($_GET['category'] ?? '') == 'Domestic' ? 'selected' : '' ?>>Nội địa</option>
                                                    <option value="International" <?= ($_GET['category'] ?? '') == 'International' ? 'selected' : '' ?>>Quốc tế</option>
                                                    <option value="Both" <?= ($_GET['category'] ?? '') == 'Both' ? 'selected' : '' ?>>Cả hai</option>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label>Ngôn ngữ:</label>
                                                <input type="text" name="language" class="form-control"
                                                    placeholder="VD: English, 中文"
                                                    value="<?= htmlspecialchars($_GET['language'] ?? '') ?>">
                                            </div>
                                            <div class="col-md-2">
                                                <label>&nbsp;</label>
                                                <button type="submit" class="btn btn-primary btn-block">
                                                    <i class="feather icon-filter"></i> Lọc
                                                </button>
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-md-10">
                                                <input type="text" name="search" class="form-control"
                                                    placeholder="Tìm kiếm theo tên, SĐT, email, chuyên môn..."
                                                    value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                                            </div>
                                            <div class="col-md-2">
                                                <button type="submit" class="btn btn-success btn-block">
                                                    <i class="feather icon-search"></i> Tìm kiếm
                                                </button>
                                            </div>
                                        </div>
                                    </form>

                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Họ tên</th>
                                                    <th>Loại</th>
                                                    <th>Phân loại</th>
                                                    <th>Chuyên môn</th>
                                                    <th>Ngôn ngữ</th>
                                                    <th>Số tour</th>
                                                    <th>Đánh giá</th>
                                                    <th>Trạng thái</th>
                                                    <th>Hành động</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($staffList)): ?>
                                                    <?php foreach ($staffList as $staff): ?>
                                                        <tr>
                                                            <td><?= $staff['staff_id'] ?></td>
                                                            <td>
                                                                <strong><?= htmlspecialchars($staff['full_name']) ?></strong><br>
                                                                <small class="text-muted">
                                                                    <i class="feather icon-phone"></i>
                                                                    <?= htmlspecialchars($staff['phone'] ?? 'N/A') ?>
                                                                </small>
                                                            </td>
                                                            <td>
                                                                <?php
                                                                $typeClass = match ($staff['staff_type']) {
                                                                    'Guide' => 'badge-primary',
                                                                    'Manager' => 'badge-success',
                                                                    'Driver' => 'badge-info',
                                                                    'Support' => 'badge-warning',
                                                                    default => 'badge-light'
                                                                };
                                                                $typeName = match ($staff['staff_type']) {
                                                                    'Guide' => 'HDV',
                                                                    'Manager' => 'Quản lý',
                                                                    'Driver' => 'Tài xế',
                                                                    'Support' => 'Hỗ trợ',
                                                                    default => $staff['staff_type']
                                                                };
                                                                ?>
                                                                <span class="badge <?= $typeClass ?>"><?= $typeName ?></span>
                                                            </td>
                                                            <td>
                                                                <?php if ($staff['staff_category']): ?>
                                                                    <span class="badge badge-light">
                                                                        <?= $staff['staff_category'] == 'Domestic' ? 'Nội địa' :
                                                                            ($staff['staff_category'] == 'International' ? 'Quốc tế' : 'Cả hai') ?>
                                                                    </span>
                                                                <?php else: ?>
                                                                    <span class="text-muted">N/A</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <?= htmlspecialchars($staff['specialization'] ?? 'N/A') ?>
                                                            </td>
                                                            <td>
                                                                <?= htmlspecialchars($staff['languages'] ?? 'N/A') ?>
                                                            </td>
                                                            <td>
                                                                <strong
                                                                    class="text-primary"><?= $staff['total_tours'] ?? 0 ?></strong>
                                                            </td>
                                                            <td>
                                                                <?php
                                                                $rating = $staff['performance_rating'] ?? 0;
                                                                $ratingColor = $rating >= 4 ? 'success' : ($rating >= 3 ? 'warning' : 'danger');
                                                                ?>
                                                                <span class="badge badge-<?= $ratingColor ?>">
                                                                    <i class="feather icon-star"></i>
                                                                    <?= number_format($rating, 1) ?>
                                                                </span>
                                                            </td>
                                                            <span class="badge <?= $typeClass ?>"><?= $typeName ?></span>
                                                            </td>
                                                            <td><?= htmlspecialchars($staff['phone'] ?? '') ?></td>
                                                            <td><?= htmlspecialchars($staff['email'] ?? '') ?></td>
                                                            <td><?= $staff['experience_years'] ?? 0 ?> năm</td>
                                                            <td><?= htmlspecialchars($staff['languages'] ?? '') ?></td>
                                                            <td>
                                                                <span
                                                                    class="badge <?= $staff['status'] ? 'badge-success' : 'badge-secondary' ?>">
                                                                    <?= $staff['status'] ? 'Đang làm việc' : 'Nghỉ việc' ?>
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <a href="?act=chi-tiet-nhan-su&id=<?= $staff['staff_id'] ?>"
                                                                    class="btn btn-info btn-sm" title="Chi tiết">
                                                                    <i class="feather icon-eye"></i>
                                                                </a>
                                                                <a href="?act=sua-nhan-su&id=<?= $staff['staff_id'] ?>"
                                                                    class="btn btn-warning btn-sm" title="Sửa">
                                                                    <i class="feather icon-edit"></i>
                                                                </a>
                                                                <a onclick="return confirm('Xóa nhân sự này?')"
                                                                    href="?act=xoa-nhan-su&id=<?= $staff['staff_id'] ?>"
                                                                    class="btn btn-danger btn-sm" title="Xóa">
                                                                    <i class="feather icon-trash"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="9" class="text-center text-muted">Chưa có nhân sự nào
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