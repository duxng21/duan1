<?php require_once __DIR__ . '/../core/header.php'; ?>
<?php require_once __DIR__ . '/../core/menu.php'; ?>
<?php require_once __DIR__ . '/../core/alert.php'; ?>
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row"></div>
        <div class="content-body">
            <section class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Phiên bản của tour:
                        <?= htmlspecialchars($tour['tour_name'] ?? ('#' . $tour['tour_id'])) ?></h4>
                    <div>
                        <a class="btn btn-primary"
                            href="?act=tao-phien-ban&tour_id=<?= htmlspecialchars($tour['tour_id']) ?>">
                            <i class="feather icon-plus"></i> Tạo phiên bản mới
                        </a>
                        <a class="btn btn-outline-secondary" href="?act=list-tour">
                            ← Quay lại danh sách tour
                        </a>
                    </div>
                </div>
                <div class="card-content">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Tên phiên bản</th>
                                        <th>Loại</th>
                                        <th>Thời gian áp dụng</th>
                                        <th>Trạng thái</th>
                                        <th>Kích hoạt</th>
                                        <th>Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($versions)):
                                        foreach ($versions as $v): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($v['version_name']) ?></td>
                                                <td><?= htmlspecialchars($v['version_type']) ?></td>
                                                <td>
                                                    <?php if (!empty($v['start_date']) || !empty($v['end_date'])): ?>
                                                        <?= htmlspecialchars($v['start_date'] ?? '') ?> →
                                                        <?= htmlspecialchars($v['end_date'] ?? '') ?>
                                                    <?php else: ?>
                                                        Không giới hạn
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php $badge = 'badge-secondary';
                                                    if (($v['status'] ?? '') === 'visible')
                                                        $badge = 'badge-success';
                                                    if (($v['status'] ?? '') === 'hidden')
                                                        $badge = 'badge-warning';
                                                    if (($v['status'] ?? '') === 'archived')
                                                        $badge = 'badge-dark';
                                                    ?>
                                                    <span
                                                        class="badge <?= $badge ?>"><?= htmlspecialchars($v['status'] ?? 'hidden') ?></span>
                                                </td>
                                                <td>
                                                    <?php if (!empty($v['is_active'])): ?>
                                                        <span class="badge badge-success">Active</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-light">Inactive</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if (empty($v['is_active'])): ?>
                                                        <a class="btn btn-sm btn-success"
                                                            href="?act=kich-hoat-phien-ban&tour_id=<?= (int) $tour['tour_id'] ?>&version_id=<?= (int) $v['version_id'] ?>">Kích
                                                            hoạt</a>
                                                    <?php else: ?>
                                                        <a class="btn btn-sm btn-warning"
                                                            href="?act=tam-dung-phien-ban&tour_id=<?= (int) $tour['tour_id'] ?>&version_id=<?= (int) $v['version_id'] ?>">Tạm
                                                            dừng</a>
                                                    <?php endif; ?>
                                                    <a class="btn btn-sm btn-outline-secondary"
                                                        href="?act=tao-phien-ban&tour_id=<?= (int) $tour['tour_id'] ?>&clone_mode=version&source_version_id=<?= (int) $v['version_id'] ?>">Nhân
                                                        bản</a>
                                                    <a class="btn btn-sm btn-outline-danger"
                                                        onclick="return confirm('Lưu trữ phiên bản này?');"
                                                        href="?act=luu-tru-phien-ban&tour_id=<?= (int) $tour['tour_id'] ?>&version_id=<?= (int) $v['version_id'] ?>">Lưu
                                                        trữ</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center">Chưa có phiên bản nào</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../core/footer.php'; ?>