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
                        <h2 class="content-header-title float-left mb-0">
                            <i class="feather icon-bell"></i> Thông báo
                        </h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">Thông báo</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Tất cả thông báo</h4>
                            <div class="heading-elements">
                                <button class="btn btn-sm btn-primary" onclick="markAllAsRead()">
                                    <i class="feather icon-check"></i> Đánh dấu đã đọc tất cả
                                </button>
                            </div>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <?php if (!empty($notifications)): ?>
                                    <ul class="list-group">
                                        <?php foreach ($notifications as $notif): ?>
                                            <li class="list-group-item <?= $notif['is_read'] ? '' : 'bg-light-primary' ?>">
                                                <div class="media">
                                                    <div
                                                        class="avatar bg-<?= $notif['type'] == 'urgent' ? 'danger' : 'primary' ?> mr-2">
                                                        <?php
                                                        switch ($notif['type']) {
                                                            case 'tour':
                                                                $icon = 'map';
                                                                break;
                                                            case 'urgent':
                                                                $icon = 'alert-circle';
                                                                break;
                                                            case 'info':
                                                                $icon = 'info';
                                                                break;
                                                            case 'reminder':
                                                                $icon = 'clock';
                                                                break;
                                                            default:
                                                                $icon = 'bell';
                                                                break;
                                                        }
                                                        ?>
                                                        <i class="feather icon-<?= $icon ?>"></i>
                                                    </div>
                                                    <div class="media-body">
                                                        <div class="d-flex justify-content-between">
                                                            <h6 class="mb-0"><?= htmlspecialchars($notif['title']) ?></h6>
                                                            <small class="text-muted">
                                                                <?= timeAgo($notif['created_at']) ?>
                                                            </small>
                                                        </div>
                                                        <p class="mb-0"><?= htmlspecialchars($notif['message']) ?></p>
                                                        <?php if (!empty($notif['action_url'])): ?>
                                                            <a href="<?= htmlspecialchars($notif['action_url']) ?>"
                                                                class="btn btn-sm btn-outline-primary mt-1">
                                                                Xem chi tiết
                                                            </a>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <div class="text-center py-5">
                                        <i class="feather icon-bell font-large-2 text-muted mb-2"></i>
                                        <p class="text-muted">Không có thông báo mới</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function markAllAsRead() {
        if (confirm('Đánh dấu tất cả thông báo đã đọc?')) {
            // AJAX call to mark all as read
            fetch('?act=hdv-danh-dau-da-doc', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    }
                });
        }
    }
</script>
<!-- END: Content-->
<?php require_once __DIR__ . '/../core/footer.php'; ?>