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

            <!-- Thông báo -->
            <?php require_once './views/core/alert.php'; ?>

            <!-- Thống kê tổng quan -->
            <div class="row">
                <div class="col-xl-4 col-md-4 col-12">
                    <div class="card">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="media d-flex">
                                    <div class="align-self-center">
                                        <i class="feather icon-list primary font-large-2 float-left"></i>
                                    </div>
                                    <div class="media-body text-right">
                                        <h3><?= count($tasks) ?></h3>
                                        <span>Tổng nhiệm vụ</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-md-4 col-12">
                    <div class="card">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="media d-flex">
                                    <div class="align-self-center">
                                        <i class="feather icon-map success font-large-2 float-left"></i>
                                    </div>
                                    <div class="media-body text-right">
                                        <h3><?= count(array_filter($tasks, fn($t) => $t['type'] === 'Hướng dẫn đoàn')) ?>
                                        </h3>
                                        <span>Hướng dẫn đoàn</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-md-4 col-12">
                    <div class="card">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="media d-flex">
                                    <div class="align-self-center">
                                        <i class="feather icon-alert-circle warning font-large-2 float-left"></i>
                                    </div>
                                    <div class="media-body text-right">
                                        <h3><?= count(array_filter($tasks, fn($t) => $t['type'] === 'Ghi chú đặc biệt')) ?>
                                        </h3>
                                        <span>Ghi chú đặc biệt</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab: Công việc theo loại -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Danh sách công việc</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <ul class="nav nav-tabs" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="all-tasks-tab" data-toggle="tab"
                                            href="#all-tasks">
                                            <i class="feather icon-list"></i> Tất cả
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="guidance-tab" data-toggle="tab" href="#guidance">
                                            <i class="feather icon-map"></i> Hướng dẫn đoàn
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="notes-tab" data-toggle="tab" href="#notes">
                                            <i class="feather icon-file-text"></i> Ghi chú đặc biệt
                                        </a>
                                    </li>
                                </ul>

                                <form method="post" action="?act=luu-nhiem-vu">
                                    <input type="hidden" name="schedule_id" value="<?= (int) $schedule_id ?>">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <div class="text-muted">
                                            Hoàn thành:
                                            <strong><?= (int) ($completed_count ?? 0) ?></strong>/<?= count($tasks) ?>
                                        </div>
                                        <div>
                                            <button type="submit" class="btn btn-primary btn-sm"><i
                                                    class="feather icon-save"></i> Lưu tiến độ</button>
                                        </div>
                                    </div>

                                    <div class="tab-content pt-1">
                                        <!-- Tab: Tất cả nhiệm vụ -->
                                        <div class="tab-pane active" id="all-tasks">
                                            <?php if (!empty($tasks)): ?>
                                                <?php foreach ($tasks as $task): ?>
                                                    <div class="card border-left-primary border-left-3 mb-1">
                                                        <div class="card-body">
                                                            <div class="d-flex justify-content-between align-items-start">
                                                                <div class="flex-grow-1">
                                                                    <h5 class="mb-1">
                                                                        <input type="checkbox" class="mr-50" name="done_keys[]"
                                                                            value="<?= htmlspecialchars($task['id']) ?>"
                                                                            <?= !empty($task['done']) ? 'checked' : '' ?>>
                                                                        <input type="hidden"
                                                                            name="task_titles[<?= htmlspecialchars($task['id']) ?>]"
                                                                            value="<?= htmlspecialchars($task['title']) ?>">
                                                                        <?= htmlspecialchars($task['title']) ?>
                                                                        <span
                                                                            class="badge badge-light-info ml-1"><?= htmlspecialchars($task['type']) ?></span>
                                                                    </h5>

                                                                    <div class="row mt-1">
                                                                        <div class="col-md-3 col-6 mb-1">
                                                                            <small class="text-muted">
                                                                                <i class="feather icon-clock"></i> Thời gian:
                                                                            </small>
                                                                            <p class="mb-0">
                                                                                <?= htmlspecialchars($task['time']) ?>
                                                                            </p>
                                                                        </div>
                                                                        <div class="col-md-3 col-6 mb-1">
                                                                            <small class="text-muted">
                                                                                <i class="feather icon-map-pin"></i> Địa điểm:
                                                                            </small>
                                                                            <p class="mb-0">
                                                                                <?= htmlspecialchars($task['location']) ?: 'N/A' ?>
                                                                            </p>
                                                                        </div>
                                                                        <div class="col-md-3 col-6 mb-1">
                                                                            <small class="text-muted">
                                                                                <i class="feather icon-user"></i> Phụ trách:
                                                                            </small>
                                                                            <p class="mb-0">
                                                                                <?= htmlspecialchars($task['responsible']) ?>
                                                                            </p>
                                                                        </div>
                                                                        <div class="col-md-3 col-6 mb-1">
                                                                            <small class="text-muted">Trạng thái:</small>
                                                                            <p class="mb-0">
                                                                                <span
                                                                                    class="badge badge-light-warning"><?= htmlspecialchars($task['status']) ?></span>
                                                                            </p>
                                                                        </div>
                                                                    </div>

                                                                    <?php if (!empty($task['description'])): ?>
                                                                        <div class="mt-1">
                                                                            <p class="mb-0 text-muted">
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
                                                    <div class="alert-body">
                                                        <i class="feather icon-info"></i> Chưa có nhiệm vụ nào
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Tab: Hướng dẫn đoàn -->
                                        <div class="tab-pane" id="guidance">
                                            <?php
                                            $guidance_tasks = array_filter($tasks, fn($t) => $t['type'] === 'Hướng dẫn đoàn');
                                            ?>
                                            <?php if (!empty($guidance_tasks)): ?>
                                                <?php foreach ($guidance_tasks as $task): ?>
                                                    <div class="card border-left-success border-left-3 mb-1">
                                                        <div class="card-body">
                                                            <h6 class="mb-1">
                                                                <label class="mb-0">
                                                                    <input type="checkbox" class="mr-50" name="done_keys[]"
                                                                        value="<?= htmlspecialchars($task['id']) ?>"
                                                                        <?= !empty($task['done']) ? 'checked' : '' ?>>
                                                                    <input type="hidden"
                                                                        name="task_titles[<?= htmlspecialchars($task['id']) ?>]"
                                                                        value="<?= htmlspecialchars($task['title']) ?>">
                                                                </label>
                                                                <i class="feather icon-check-circle text-success"></i>
                                                                <?= htmlspecialchars($task['title']) ?>
                                                            </h6>
                                                            <p class="text-muted mb-0">
                                                                <?= nl2br(htmlspecialchars($task['description'])) ?>
                                                            </p>
                                                            <small class="text-muted">
                                                                <i class="feather icon-clock"></i>
                                                                <?= htmlspecialchars($task['time']) ?>
                                                            </small>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <div class="alert alert-warning">
                                                    <div class="alert-body">
                                                        <i class="feather icon-inbox"></i> Không có công việc hướng dẫn đoàn
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Tab: Ghi chú đặc biệt -->
                                        <div class="tab-pane" id="notes">
                                            <?php
                                            $special_notes = array_filter($tasks, fn($t) => $t['type'] === 'Ghi chú đặc biệt');
                                            ?>
                                            <?php if (!empty($special_notes)): ?>
                                                <?php foreach ($special_notes as $task): ?>
                                                    <div class="card border-left-warning border-left-3 mb-1"
                                                        style="background-color: #fffbf0;">
                                                        <div class="card-body">
                                                            <h6 class="mb-1">
                                                                <label class="mb-0">
                                                                    <input type="checkbox" class="mr-50" name="done_keys[]"
                                                                        value="<?= htmlspecialchars($task['id']) ?>"
                                                                        <?= !empty($task['done']) ? 'checked' : '' ?>>
                                                                    <input type="hidden"
                                                                        name="task_titles[<?= htmlspecialchars($task['id']) ?>]"
                                                                        value="<?= htmlspecialchars($task['title']) ?>">
                                                                </label>
                                                                <i class="feather icon-alert-circle text-warning"></i>
                                                                <?= htmlspecialchars($task['title']) ?>
                                                            </h6>
                                                            <p class="mb-1"><?= nl2br(htmlspecialchars($task['description'])) ?>
                                                            </p>
                                                            <small class="text-muted">
                                                                <i class="feather icon-clock"></i>
                                                                <?= htmlspecialchars($task['time']) ?> |
                                                                <i class="feather icon-user"></i>
                                                                <?= htmlspecialchars($task['responsible']) ?>
                                                            </small>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <div class="alert alert-warning">
                                                    <div class="alert-body">
                                                        <i class="feather icon-file-text"></i> Không có ghi chú đặc biệt nào
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hướng dẫn -->
            <div class="row">
                <div class="col-12">
                    <div class="card bg-light-info">
                        <div class="card-body">
                            <h6 class="card-title">
                                <i class="feather icon-info text-info"></i> Gợi ý
                            </h6>
                            <ul class="mb-0">
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
    </div>
</div>
<!-- END: Content-->

<?php require_once './views/core/footer.php'; ?>