<?php
/**
 * View: Danh sách nhiệm vụ của HDV
 * Use Case 1: Bước 5
 * Hiển thị: công việc, thời gian, địa điểm, người phụ trách, ghi chú đặc biệt
 */
?>
<?php require_once './views/core/header.php'; ?>
<?php require_once './views/core/menu.php'; ?>

<!-- BEGIN: Content-->
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">Nhiệm vụ của tôi</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="?act=hdv-lich-cua-toi">Lịch của tôi</a></li>
                                <li class="breadcrumb-item"><a
                                        href="?act=hdv-chi-tiet-tour&id=<?= $schedule_id ?>"><?= htmlspecialchars($tour['tour_name'] ?? '') ?></a>
                                </li>
                                <li class="breadcrumb-item active">Nhiệm vụ của tôi</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-header-right text-md-right col-md-3 col-12 d-md-block d-none">
                <div class="form-group breadcrum-right">
                    <a href="?act=hdv-chi-tiet-tour&id=<?= $schedule_id ?>" class="btn btn-secondary">
                        <i class="feather icon-arrow-left"></i> Quay lại
                    </a>
                </div>
            </div>
        </div>
        <div class="content-body">

            <?php if (!empty($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> <?= htmlspecialchars($_SESSION['success']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['success']);
            endif; ?>

            <!-- Tab: Công việc theo loại -->
            <div class="row">
                <div class="col-12">
                    <ul class="nav nav-tabs" role="tablist" id="taskTabs">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="all-tasks-tab" data-bs-toggle="tab"
                                data-bs-target="#all-tasks" type="button" role="tab">
                                <i class="fas fa-list"></i> Tất cả nhiệm vụ
                                <span class="badge bg-primary ms-2"><?= count($tasks) ?></span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="guidance-tab" data-bs-toggle="tab" data-bs-target="#guidance"
                                type="button" role="tab">
                                <i class="fas fa-map"></i> Hướng dẫn đoàn
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="notes-tab" data-bs-toggle="tab" data-bs-target="#notes"
                                type="button" role="tab">
                                <i class="fas fa-sticky-note"></i> Ghi chú đặc biệt
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <!-- Tab: Tất cả nhiệm vụ -->
                        <div class="tab-pane fade show active" id="all-tasks" role="tabpanel">
                            <div class="card border-top-0 rounded-0">
                                <div class="card-body">
                                    <?php if (!empty($tasks)): ?>
                                        <?php foreach ($tasks as $task): ?>
                                            <div class="task-card card mb-3 border-left-thick"
                                                style="border-left: 4px solid #0d6efd;">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-start">
                                                        <div class="flex-grow-1">
                                                            <div class="d-flex align-items-center mb-2">
                                                                <h5 class="mb-0 flex-grow-1">
                                                                    <?= htmlspecialchars($task['title']) ?>
                                                                </h5>
                                                                <span class="badge bg-info">
                                                                    <?= htmlspecialchars($task['type']) ?>
                                                                </span>
                                                            </div>

                                                            <div class="row mt-3">
                                                                <div class="col-md-3">
                                                                    <p class="mb-1 text-muted small">
                                                                        <i class="fas fa-clock"></i> <strong>Thời gian:</strong>
                                                                    </p>
                                                                    <p class="mb-0">
                                                                        <?= htmlspecialchars($task['time']) ?>
                                                                    </p>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <p class="mb-1 text-muted small">
                                                                        <i class="fas fa-map-marker-alt"></i> <strong>Địa
                                                                            điểm:</strong>
                                                                    </p>
                                                                    <p class="mb-0">
                                                                        <?= htmlspecialchars($task['location']) ?: 'N/A' ?>
                                                                    </p>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <p class="mb-1 text-muted small">
                                                                        <i class="fas fa-user"></i> <strong>Người phụ
                                                                            trách:</strong>
                                                                    </p>
                                                                    <p class="mb-0">
                                                                        <?= htmlspecialchars($task['responsible']) ?>
                                                                    </p>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <p class="mb-1 text-muted small">
                                                                        <strong>Trạng thái:</strong>
                                                                    </p>
                                                                    <p class="mb-0">
                                                                        <span class="badge bg-warning">
                                                                            <?= htmlspecialchars($task['status']) ?>
                                                                        </span>
                                                                    </p>
                                                                </div>
                                                            </div>

                                                            <?php if (!empty($task['description'])): ?>
                                                                <hr class="my-2">
                                                                <div>
                                                                    <p class="mb-0 text-break">
                                                                        <?= nl2br(htmlspecialchars($task['description'])) ?>
                                                                    </p>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle"></i> Chưa có nhiệm vụ nào
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Tab: Hướng dẫn đoàn -->
                        <div class="tab-pane fade" id="guidance" role="tabpanel">
                            <div class="card border-top-0 rounded-0">
                                <div class="card-body">
                                    <?php
                                    $guidance_tasks = array_filter($tasks, fn($t) => $t['type'] === 'Hướng dẫn đoàn');
                                    ?>
                                    <?php if (!empty($guidance_tasks)): ?>
                                        <?php foreach ($guidance_tasks as $task): ?>
                                            <div class="card mb-3 border-start border-success"
                                                style="border-left-width: 4px !important;">
                                                <div class="card-body">
                                                    <h6 class="card-title mb-2">
                                                        <i class="fas fa-check-circle text-success"></i>
                                                        <?= htmlspecialchars($task['title']) ?>
                                                    </h6>
                                                    <p class="card-text text-muted small">
                                                        <?= nl2br(htmlspecialchars($task['description'])) ?>
                                                    </p>
                                                    <small class="text-muted">
                                                        <i class="fas fa-clock"></i> <?= htmlspecialchars($task['time']) ?>
                                                    </small>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="alert alert-warning">
                                            <i class="fas fa-inbox"></i> Không có công việc hướng dẫn đoàn
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Tab: Ghi chú đặc biệt -->
                        <div class="tab-pane fade" id="notes" role="tabpanel">
                            <div class="card border-top-0 rounded-0">
                                <div class="card-body">
                                    <?php
                                    $special_notes = array_filter($tasks, fn($t) => $t['type'] === 'Ghi chú đặc biệt');
                                    ?>
                                    <?php if (!empty($special_notes)): ?>
                                        <?php foreach ($special_notes as $task): ?>
                                            <div class="card mb-3 border-start border-warning"
                                                style="border-left-width: 4px !important; background-color: #fffbf0;">
                                                <div class="card-body">
                                                    <h6 class="card-title mb-2">
                                                        <i class="fas fa-exclamation-circle text-warning"></i>
                                                        <?= htmlspecialchars($task['title']) ?>
                                                    </h6>
                                                    <p class="card-text mb-2">
                                                        <?= nl2br(htmlspecialchars($task['description'])) ?>
                                                    </p>
                                                    <small class="text-muted">
                                                        <i class="fas fa-clock"></i> <?= htmlspecialchars($task['time']) ?> |
                                                        <i class="fas fa-user"></i>
                                                        <?= htmlspecialchars($task['responsible']) ?>
                                                    </small>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="alert alert-warning">
                                            <i class="fas fa-sticky-note"></i> Không có ghi chú đặc biệt nào
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bảng tóm tắt -->
            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="card bg-light border-0">
                        <div class="card-body text-center">
                            <h5 class="text-muted">Tổng cộng</h5>
                            <h3 class="text-primary mb-0"><?= count($tasks) ?></h3>
                            <p class="text-muted small mb-0">nhiệm vụ</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-light border-0">
                        <div class="card-body text-center">
                            <h5 class="text-muted">Hướng dẫn đoàn</h5>
                            <h3 class="text-success mb-0">
                                <?= count(array_filter($tasks, fn($t) => $t['type'] === 'Hướng dẫn đoàn')) ?></h3>
                            <p class="text-muted small mb-0">công việc</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-light border-0">
                        <div class="card-body text-center">
                            <h5 class="text-muted">Ghi chú đặc biệt</h5>
                            <h3 class="text-warning mb-0">
                                <?= count(array_filter($tasks, fn($t) => $t['type'] === 'Ghi chú đặc biệt')) ?></h3>
                            <p class="text-muted small mb-0">ghi chú</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hướng dẫn -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card bg-info bg-opacity-10 border-info">
                        <div class="card-body">
                            <h6 class="card-title">
                                <i class="fas fa-lightbulb text-info"></i> Gợi ý
                            </h6>
                            <ul class="mb-0 small">
                                <li>Kiểm tra kỹ danh sách nhiệm vụ trước khi tour khởi hành</li>
                                <li>Lưu ý các ghi chú đặc biệt từ quản lý</li>
                                <li>Liên hệ quản lý nếu có bất kỳ thắc mắc hoặc vấn đề</li>
                                <li>Cập nhật nhật ký trong quá trình thực hiện tour</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php require_once './views/core/footer.php'; ?>