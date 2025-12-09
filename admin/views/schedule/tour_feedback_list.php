<?php require_once __DIR__ . '/../core/header.php'; ?>
<?php require_once __DIR__ . '/../core/menu.php'; ?>

<!-- BEGIN: Content-->
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">Feedback & Đánh giá Tour</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="?act=hdv-lich-cua-toi">Lịch của tôi</a></li>
                                <li class="breadcrumb-item"><a
                                        href="?act=hdv-chi-tiet-tour&id=<?= $schedule['schedule_id'] ?>">Chi tiết
                                        tour</a></li>
                                <li class="breadcrumb-item active">Feedback</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-body">
            <!-- Tour Info Card -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="feather icon-info"></i> <?= htmlspecialchars($schedule['tour_name']) ?>
                    </h4>
                    <div class="heading-elements">
                        <span class="badge badge-primary">
                            <?= date('d/m/Y', strtotime($schedule['departure_date'])) ?>
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <p class="mb-0"><strong>Mã tour:</strong>
                                <?= htmlspecialchars($schedule['tour_code'] ?? 'N/A') ?></p>
                            <p class="mb-0"><strong>Trạng thái:</strong>
                                <span
                                    class="badge badge-<?= $schedule['status'] === 'Confirmed' ? 'success' : 'warning' ?>">
                                    <?= $schedule['status'] ?>
                                </span>
                            </p>
                        </div>
                        <div class="col-md-4 text-right">
                            <a href="?act=create-feedback-form&schedule_id=<?= $schedule['schedule_id'] ?>"
                                class="btn btn-primary">
                                <i class="feather icon-plus"></i> Tạo Feedback Mới
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Card -->
            <?php if ($statistics && $statistics['total_feedbacks'] > 0): ?>
                <div class="card">
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-2 col-6">
                                <h3 class="text-primary"><?= $statistics['total_feedbacks'] ?></h3>
                                <p class="text-muted mb-0">Tổng feedback</p>
                            </div>
                            <div class="col-md-2 col-6">
                                <h3 class="text-warning"><?= number_format($statistics['avg_overall_rating'], 1) ?> <i
                                        class="feather icon-star"></i></h3>
                                <p class="text-muted mb-0">Đánh giá TB</p>
                            </div>
                            <div class="col-md-2 col-6">
                                <h3 class="text-info"><?= number_format($statistics['avg_service_rating'], 1) ?></h3>
                                <p class="text-muted mb-0">Dịch vụ</p>
                            </div>
                            <div class="col-md-2 col-6">
                                <h3 class="text-success"><?= number_format($statistics['avg_guide_rating'], 1) ?></h3>
                                <p class="text-muted mb-0">HDV</p>
                            </div>
                            <div class="col-md-2 col-6">
                                <h3 class="text-danger"><?= number_format($statistics['avg_food_rating'], 1) ?></h3>
                                <p class="text-muted mb-0">Ẩm thực</p>
                            </div>
                            <div class="col-md-2 col-6">
                                <h3 class="text-success">
                                    <?= $statistics['recommend_count'] ?>/<?= $statistics['total_feedbacks'] ?></h3>
                                <p class="text-muted mb-0">Giới thiệu</p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Feedback List -->
            <?php if (!empty($feedbacks)): ?>
                <?php foreach ($feedbacks as $feedback): ?>
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center w-100">
                                <div>
                                    <h5 class="mb-0">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i
                                                class="feather icon-star <?= $i <= $feedback['overall_rating'] ? 'text-warning' : 'text-muted' ?>"></i>
                                        <?php endfor; ?>
                                        (<?= $feedback['overall_rating'] ?>/5)
                                    </h5>
                                    <small class="text-muted">
                                        Bởi <?= htmlspecialchars($feedback['author_name']) ?>
                                        - <?= date('d/m/Y H:i', strtotime($feedback['created_at'])) ?>
                                    </small>
                                </div>
                                <div>
                                    <?php if ($feedback['is_public']): ?>
                                        <span class="badge badge-success"><i class="feather icon-eye"></i> Công khai</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary"><i class="feather icon-eye-off"></i> Riêng tư</span>
                                    <?php endif; ?>

                                    <span
                                        class="badge badge-<?= $feedback['status'] === 'Published' ? 'primary' : 'warning' ?>">
                                        <?= $feedback['status'] ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Rating Details -->
                            <div class="row mb-2">
                                <?php if ($feedback['service_rating']): ?>
                                    <div class="col-md-2 col-6 mb-1">
                                        <small class="text-muted">Dịch vụ:</small>
                                        <strong class="ml-1"><?= $feedback['service_rating'] ?>/5</strong>
                                    </div>
                                <?php endif; ?>
                                <?php if ($feedback['guide_rating']): ?>
                                    <div class="col-md-2 col-6 mb-1">
                                        <small class="text-muted">HDV:</small>
                                        <strong class="ml-1"><?= $feedback['guide_rating'] ?>/5</strong>
                                    </div>
                                <?php endif; ?>
                                <?php if ($feedback['food_rating']): ?>
                                    <div class="col-md-2 col-6 mb-1">
                                        <small class="text-muted">Ẩm thực:</small>
                                        <strong class="ml-1"><?= $feedback['food_rating'] ?>/5</strong>
                                    </div>
                                <?php endif; ?>
                                <?php if ($feedback['accommodation_rating']): ?>
                                    <div class="col-md-2 col-6 mb-1">
                                        <small class="text-muted">Chỗ ở:</small>
                                        <strong class="ml-1"><?= $feedback['accommodation_rating'] ?>/5</strong>
                                    </div>
                                <?php endif; ?>
                                <?php if ($feedback['transportation_rating']): ?>
                                    <div class="col-md-2 col-6 mb-1">
                                        <small class="text-muted">Di chuyển:</small>
                                        <strong class="ml-1"><?= $feedback['transportation_rating'] ?>/5</strong>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Feedback Text -->
                            <?php if (!empty($feedback['feedback_text'])): ?>
                                <p class="card-text"><?= nl2br(htmlspecialchars($feedback['feedback_text'])) ?></p>
                            <?php endif; ?>

                            <?php if (!empty($feedback['positive_points'])): ?>
                                <div class="alert alert-success mb-1">
                                    <strong><i class="feather icon-thumbs-up"></i> Điểm tích cực:</strong><br>
                                    <?= nl2br(htmlspecialchars($feedback['positive_points'])) ?>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($feedback['improvement_points'])): ?>
                                <div class="alert alert-warning mb-1">
                                    <strong><i class="feather icon-alert-circle"></i> Cần cải thiện:</strong><br>
                                    <?= nl2br(htmlspecialchars($feedback['improvement_points'])) ?>
                                </div>
                            <?php endif; ?>

                            <?php if ($feedback['recommend_to_others']): ?>
                                <p class="text-success"><i class="feather icon-check-circle"></i> Sẵn sàng giới thiệu cho người khác
                                </p>
                            <?php endif; ?>

                            <!-- Admin Response -->
                            <?php if (!empty($feedback['admin_response'])): ?>
                                <div class="alert alert-info">
                                    <strong><i class="feather icon-message-circle"></i> Phản hồi từ quản lý:</strong><br>
                                    <?= nl2br(htmlspecialchars($feedback['admin_response'])) ?>
                                    <br><small class="text-muted">Phản hồi lúc:
                                        <?= date('d/m/Y H:i', strtotime($feedback['responded_at'])) ?></small>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="card-footer">
                            <a href="?act=edit-feedback&feedback_id=<?= $feedback['feedback_id'] ?>"
                                class="btn btn-sm btn-warning">
                                <i class="feather icon-edit"></i> Sửa
                            </a>
                            <a href="?act=toggle-feedback-visibility&feedback_id=<?= $feedback['feedback_id'] ?>"
                                class="btn btn-sm btn-info">
                                <i class="feather icon-<?= $feedback['is_public'] ? 'eye-off' : 'eye' ?>"></i>
                                <?= $feedback['is_public'] ? 'Ẩn' : 'Hiển thị' ?>
                            </a>
                            <a href="?act=delete-feedback&feedback_id=<?= $feedback['feedback_id'] ?>"
                                onclick="return confirm('Bạn có chắc chắn muốn xóa feedback này?');"
                                class="btn btn-sm btn-danger">
                                <i class="feather icon-trash"></i> Xóa
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="feather icon-message-circle" style="font-size: 64px; opacity: 0.3;"></i>
                        <h4 class="mt-3">Chưa có feedback nào</h4>
                        <p class="text-muted">Hãy tạo feedback đầu tiên cho tour này</p>
                        <a href="?act=create-feedback-form&schedule_id=<?= $schedule['schedule_id'] ?>"
                            class="btn btn-primary">
                            <i class="feather icon-plus"></i> Tạo Feedback Mới
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<!-- END: Content-->

<?php require_once __DIR__ . '/../core/footer.php'; ?>