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
                                'Driver' => ['total' => 0, 'active' => 0, 'icon' => 'navigation', 'color' => 'info', 'title' => 'Tài xế'],
                                'Support' => ['total' => 0, 'active' => 0, 'icon' => 'life-buoy', 'color' => 'warning', 'title' => 'Hỗ trợ'],
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
                                                            <?= $data['active'] ?>/<?= $data['total'] ?></h3>
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
                                    <a href="?act=them-nhan-su" class="btn btn-primary btn-sm">
                                        <i class="feather icon-plus"></i> Thêm nhân sự
                                    </a>
                                </div>
                            </div>
                            <div class="card-content">
                                <div class="card-body card-dashboard">
                                    <!-- Bộ lọc -->
                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <form method="GET" action="">
                                                <input type="hidden" name="act" value="danh-sach-nhan-su">
                                                <label class="mr-2">Lọc theo loại:</label>
                                                <select name="type" class="form-control" onchange="this.form.submit()">
                                                    <option value="">Tất cả</option>
                                                    <option value="Guide" <?= ($_GET['type'] ?? '') == 'Guide' ? 'selected' : '' ?>>Hướng dẫn viên</option>
                                                    <option value="Driver" <?= ($_GET['type'] ?? '') == 'Driver' ? 'selected' : '' ?>>Tài xế</option>
                                                    <option value="Support" <?= ($_GET['type'] ?? '') == 'Support' ? 'selected' : '' ?>>Hỗ trợ</option>
                                                    <option value="Manager" <?= ($_GET['type'] ?? '') == 'Manager' ? 'selected' : '' ?>>Quản lý</option>
                                                </select>
                                            </form>
                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Họ tên</th>
                                                    <th>Loại</th>
                                                    <th>Điện thoại</th>
                                                    <th>Email</th>
                                                    <th>Kinh nghiệm</th>
                                                    <th>Ngôn ngữ</th>
                                                    <th>Trạng thái</th>
                                                    <th>Hành động</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($staffList)): ?>
                                                    <?php foreach ($staffList as $staff): ?>
                                                        <tr>
                                                            <td><?= $staff['staff_id'] ?></td>
                                                            <td><strong><?= htmlspecialchars($staff['full_name']) ?></strong>
                                                            </td>
                                                            <td>
                                                                <?php
                                                                $typeClass = match ($staff['staff_type']) {
                                                                    'Guide' => 'badge-primary',
                                                                    'Driver' => 'badge-info',
                                                                    'Support' => 'badge-warning',
                                                                    'Manager' => 'badge-success',
                                                                    default => 'badge-light'
                                                                };
                                                                $typeName = match ($staff['staff_type']) {
                                                                    'Guide' => 'HDV',
                                                                    'Driver' => 'Tài xế',
                                                                    'Support' => 'Hỗ trợ',
                                                                    'Manager' => 'Quản lý',
                                                                    default => $staff['staff_type']
                                                                };
                                                                ?>
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