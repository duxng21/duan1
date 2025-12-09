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
                            <i class="feather icon-book-open"></i> Nhật ký Tour
                        </h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="?act=home-guide">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="?act=hdv-lich-cua-toi">Lịch làm việc</a></li>
                                <li class="breadcrumb-item active">Nhật ký Tour</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-body">
            <!-- Tour info -->
            <section id="tour-info">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h4 class="mb-1"><?= htmlspecialchars($schedule['tour_name'] ?? 'N/A') ?></h4>
                                <p class="mb-0">
                                    <i class="feather icon-calendar"></i>
                                    <?= date('d/m/Y', strtotime($schedule['departure_date'])) ?>
                                    -
                                    <?= date('d/m/Y', strtotime($schedule['return_date'])) ?>
                                </p>
                                <p class="mb-0">
                                    <span
                                        class="badge badge-<?= $schedule['status'] === 'Confirmed' ? 'success' : 'warning' ?>">
                                        <?= htmlspecialchars($schedule['status']) ?>
                                    </span>
                                </p>
                            </div>
                            <div class="col-md-4 text-right">
                                <a href="?act=create-journal-entry-form&schedule_id=<?= $schedule['schedule_id'] ?>"
                                    class="btn btn-primary">
                                    <i class="feather icon-plus"></i> Thêm nhật ký
                                </a>
                                <?php if (!empty($journals)): ?>
                                    <a href="?act=export-tour-journal&schedule_id=<?= $schedule['schedule_id'] ?>"
                                        class="btn btn-outline-secondary">
                                        <i class="feather icon-download"></i> Xuất PDF
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Journal list -->
            <section id="journal-list">
                <?php if (empty($journals)): ?>
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="feather icon-book-open" style="font-size: 64px; color: #ddd;"></i>
                            <h4 class="mt-3">Chưa có nhật ký nào</h4>
                            <p class="text-muted">Bắt đầu ghi lại hành trình của tour này</p>
                            <a href="?act=create-journal-entry-form&schedule_id=<?= $schedule['schedule_id'] ?>"
                                class="btn btn-primary">
                                <i class="feather icon-plus"></i> Tạo nhật ký đầu tiên
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($journals as $journal): ?>
                        <div class="card">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center w-100">
                                    <div>
                                        <h4 class="card-title mb-0">
                                            <?= htmlspecialchars($journal['title']) ?>
                                        </h4>
                                        <small class="text-muted">
                                            <i class="feather icon-calendar"></i>
                                            <?= date('d/m/Y', strtotime($journal['journal_date'])) ?>
                                            |
                                            <i class="feather icon-user"></i>
                                            <?= htmlspecialchars($journal['author_name'] ?? 'N/A') ?>
                                            <?php if ($journal['image_count'] > 0): ?>
                                                |
                                                <i class="feather icon-image"></i>
                                                <?= $journal['image_count'] ?> ảnh
                                            <?php endif; ?>
                                        </small>
                                    </div>
                                    <div>
                                        <span
                                            class="badge badge-<?= $journal['status'] === 'Published' ? 'success' : 'secondary' ?>">
                                            <?= $journal['status'] ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($journal['content'])): ?>
                                    <div class="journal-content mb-3">
                                        <?= nl2br(htmlspecialchars(substr($journal['content'], 0, 300))) ?>
                                        <?php if (strlen($journal['content']) > 300): ?>
                                            <span class="text-muted">...</span>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($journal['location'])): ?>
                                    <p class="mb-2">
                                        <i class="feather icon-map-pin text-primary"></i>
                                        <strong>Địa điểm:</strong> <?= htmlspecialchars($journal['location']) ?>
                                    </p>
                                <?php endif; ?>

                                <?php if (!empty($journal['weather'])): ?>
                                    <p class="mb-2">
                                        <i class="feather icon-cloud text-info"></i>
                                        <strong>Thời tiết:</strong> <?= htmlspecialchars($journal['weather']) ?>
                                    </p>
                                <?php endif; ?>

                                <?php if (!empty($journal['incidents'])): ?>
                                    <div class="alert alert-warning mb-2" role="alert">
                                        <h5 class="alert-heading"><i class="feather icon-alert-triangle"></i> Sự cố</h5>
                                        <p class="mb-0"><?= nl2br(htmlspecialchars($journal['incidents'])) ?></p>
                                        <?php if (!empty($journal['incidents_resolved'])): ?>
                                            <hr>
                                            <p class="mb-0"><strong>Cách xử lý:</strong>
                                                <?= nl2br(htmlspecialchars($journal['incidents_resolved'])) ?></p>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>

                                <div class="mt-3">
                                    <a href="?act=edit-journal-entry&id=<?= $journal['journal_id'] ?>"
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="feather icon-edit"></i> Chỉnh sửa
                                    </a>
                                    <a href="?act=delete-journal-entry&id=<?= $journal['journal_id'] ?>"
                                        class="btn btn-sm btn-outline-danger" onclick="return confirm('Xóa nhật ký này?')">
                                        <i class="feather icon-trash"></i> Xóa
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </section>
        </div>
    </div>
</div>
<!-- END: Content-->

<?php require_once __DIR__ . '/../core/footer.php'; ?>