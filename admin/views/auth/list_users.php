<?php require_once __DIR__ . '/../core/header.php'; ?>
<?php require_once __DIR__ . '/../core/menu.php'; ?>
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <h2 class="content-header-title">Danh sách User</h2>
            </div>
        </div>
        <div class="content-body">
            <?php require_once __DIR__ . '/../core/alert.php'; ?>
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Tài khoản hệ thống</h4>
                    <a href="?act=tao-user" class="btn btn-primary btn-sm"><i class="feather icon-user-plus"></i> Tạo
                        user</a>
                </div>
                <div class="card-body">
                    <?php if (!empty($users)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover table-sm">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>ID</th>
                                        <th>Username</th>
                                        <th>Họ tên</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Staff ID</th>
                                        <th>Trạng thái</th>
                                        <th>Last Login</th>
                                        <th>Ngày tạo</th>
                                        <th>Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i = 1;
                                    foreach ($users as $u): ?>
                                        <tr>
                                            <td class="text-muted" style="width:40px;"><?= $i++ ?></td>
                                            <td><?= $u['user_id'] ?></td>
                                            <td><strong><?= htmlspecialchars($u['username']) ?></strong></td>
                                            <td><?= htmlspecialchars($u['full_name']) ?></td>
                                            <td><?= htmlspecialchars($u['email']) ?></td>
                                            <td><span
                                                    class="badge badge-<?= $u['role_code'] === 'ADMIN' ? 'danger' : 'info' ?>"><?= $u['role_code'] ?></span>
                                            </td>
                                            <td><?= $u['staff_id'] ?: '-' ?></td>
                                            <td>
                                                <?php
                                                $statusClass = match ($u['status']) {
                                                    'Active' => 'success',
                                                    'Locked' => 'danger',
                                                    'Inactive' => 'secondary',
                                                    default => 'light'
                                                };
                                                ?>
                                                <span class="badge badge-<?= $statusClass ?>"><?= $u['status'] ?></span>
                                            </td>
                                            <td><?= $u['last_login'] ? date('d/m/Y H:i', strtotime($u['last_login'])) : '-' ?>
                                            </td>
                                            <td><?= date('d/m/Y', strtotime($u['created_at'])) ?></td>
                                            <td style="width:120px;">
                                                <?php if ($u['user_id'] != ($_SESSION['user_id'] ?? 0)): ?>
                                                    <?php if ($u['status'] === 'Active'): ?>
                                                        <a href="?act=doi-trang-thai-user&action=lock&user_id=<?= $u['user_id'] ?>"
                                                            class="btn btn-sm btn-outline-danger"
                                                            onclick="return confirm('Khóa user này?');" title="Khóa user"><i
                                                                class="feather icon-lock"></i></a>
                                                    <?php else: ?>
                                                        <a href="?act=doi-trang-thai-user&action=unlock&user_id=<?= $u['user_id'] ?>"
                                                            class="btn btn-sm btn-outline-success"
                                                            onclick="return confirm('Mở khóa user này?');" title="Mở khóa user"><i
                                                                class="feather icon-unlock"></i></a>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="text-muted small">(Bạn)</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted mb-0">Chưa có user nào.</p>
                    <?php endif; ?>
                </div>
            </div>
            <a href="index.php" class="btn btn-secondary btn-sm"><i class="feather icon-arrow-left"></i> Quay lại</a>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../core/footer.php'; ?>