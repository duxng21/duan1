<?php
$title = 'Báo Cáo Hiệu Quả Phục Vụ Đặc Biệt';
require_once './views/core/header.php';
require_once './views/core/menu.php';
?>

<style>
.efficiency-score {
    font-size: 2rem;
    font-weight: bold;
}
.score-excellent { color: #28a745; }
.score-good { color: #17a2b8; }
.score-fair { color: #ffc107; }
.score-poor { color: #dc3545; }

.service-category {
    border-left: 4px solid #007bff;
    padding: 15px;
    margin-bottom: 15px;
    background-color: #f8f9fa;
}

.feedback-item {
    border: 1px solid #ddd;
    border-radius: 5px;
    padding: 15px;
    margin-bottom: 15px;
}

.rating-stars {
    color: #ffc107;
    font-size: 1.2rem;
}

.timeline-item {
    position: relative;
    padding-left: 30px;
    margin-bottom: 20px;
}

.timeline-item::before {
    content: '';
    position: absolute;
    left: 10px;
    top: 5px;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background-color: #007bff;
}

.timeline-item.resolved::before {
    background-color: #28a745;
}

.timeline-item.delayed::before {
    background-color: #ffc107;
}

.timeline-item.failed::before {
    background-color: #dc3545;
}
</style>

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
                            <i data-feather="award"></i>
                            Báo Cáo Hiệu Quả Phục Vụ
                        </h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="?act=/">Trang chủ</a></li>
                                <li class="breadcrumb-item"><a href="?act=danh-sach-lich-khoi-hanh">Lịch khởi hành</a></li>
                                <li class="breadcrumb-item active">Báo cáo hiệu quả phục vụ</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-header-right text-md-right col-md-3 col-12 d-md-block d-none">
                <div class="form-group breadcrum-right">
                    <button class="btn btn-primary btn-sm" onclick="exportEfficiencyReport()">
                        <i data-feather="download"></i> Xuất báo cáo
                    </button>
                </div>
            </div>
        </div>

        <div class="content-body">
            <?php require_once './views/core/alert.php'; ?>

            <!-- Tour Information -->
            <?php if (isset($schedule) && $schedule): ?>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">
                                <i data-feather="info"></i>
                                Thông tin tour
                            </h4>
                            <div class="card-header-right">
                                <span class="badge badge-<?= strtotime($schedule['departure_date']) < time() ? 'success' : 'info' ?> badge-lg">
                                    <?= strtotime($schedule['departure_date']) < time() ? 'Đã hoàn thành' : 'Đang diễn ra' ?>
                                </span>
                            </div>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Tour:</strong> <?= htmlspecialchars($schedule['tour_name']) ?></p>
                                        <p><strong>Ngày khởi hành:</strong> <?= date('d/m/Y', strtotime($schedule['departure_date'])) ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Hướng dẫn viên:</strong> <?= htmlspecialchars($schedule['guide_names'] ?? 'Chưa có') ?></p>
                                        <p><strong>Tổng khách:</strong> <?= $schedule['total_guests'] ?? 0 ?> người</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Overall Efficiency Score -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">
                                <i data-feather="target"></i>
                                Điểm Hiệu Quả Tổng Thể
                            </h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body text-center">
                                <?php 
                                $fulfillment_rate = $efficiency['fulfillment_rate'] ?? 0;
                                $score_class = 'score-poor';
                                $score_text = 'Cần cải thiện';
                                $score_icon = 'frown';
                                
                                if ($fulfillment_rate >= 90) {
                                    $score_class = 'score-excellent';
                                    $score_text = 'Xuất sắc';
                                    $score_icon = 'smile';
                                } elseif ($fulfillment_rate >= 75) {
                                    $score_class = 'score-good';
                                    $score_text = 'Tốt';
                                    $score_icon = 'smile';
                                } elseif ($fulfillment_rate >= 60) {
                                    $score_class = 'score-fair';
                                    $score_text = 'Trung bình';
                                    $score_icon = 'meh';
                                }
                                ?>
                                
                                <div class="efficiency-score <?= $score_class ?>">
                                    <?= number_format($fulfillment_rate, 1) ?>%
                                </div>
                                <p class="lead mb-0">
                                    <i data-feather="<?= $score_icon ?>"></i>
                                    <?= $score_text ?>
                                </p>
                                <small class="text-muted">
                                    Tỷ lệ hoàn thành yêu cầu đặc biệt
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Statistics -->
            <div class="row">
                <div class="col-lg-3 col-md-6 col-12">
                    <div class="card">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="media d-flex">
                                    <div class="media-body text-left">
                                        <h3 class="primary"><?= $efficiency['total_special_requests'] ?? 0 ?></h3>
                                        <span>Tổng yêu cầu</span>
                                    </div>
                                    <div class="align-self-center">
                                        <i data-feather="clipboard" class="primary font-large-2 float-right"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-12">
                    <div class="card">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="media d-flex">
                                    <div class="media-body text-left">
                                        <h3 class="success"><?= $efficiency['fulfilled_requests'] ?? 0 ?></h3>
                                        <span>Đã hoàn thành</span>
                                    </div>
                                    <div class="align-self-center">
                                        <i data-feather="check-circle" class="success font-large-2 float-right"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-12">
                    <div class="card">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="media d-flex">
                                    <div class="media-body text-left">
                                        <h3 class="warning"><?= $efficiency['critical_resolved'] ?? 0 ?></h3>
                                        <span>Khẩn cấp đã giải quyết</span>
                                    </div>
                                    <div class="align-self-center">
                                        <i data-feather="alert-triangle" class="warning font-large-2 float-right"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-12">
                    <div class="card">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="media d-flex">
                                    <div class="media-body text-left">
                                        <h3 class="info"><?= number_format($efficiency['avg_response_time'] ?? 0, 1) ?>h</h3>
                                        <span>Thời gian phản hồi TB</span>
                                    </div>
                                    <div class="align-self-center">
                                        <i data-feather="clock" class="info font-large-2 float-right"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Service Categories Breakdown -->
            <div class="row">
                <div class="col-lg-6 col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">
                                <i data-feather="pie-chart"></i>
                                Phân loại dịch vụ được phục vụ
                            </h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <?php 
                                $categories = explode(',', $efficiency['service_categories'] ?? '');
                                $category_icons = [
                                    'Dietary' => '🍽️ Ăn uống',
                                    'Medical' => '💊 Y tế', 
                                    'Allergy' => '⚠️ Dị ứng',
                                    'Mobility' => '♿ Di chuyển',
                                    'Other' => '📝 Khác'
                                ];
                                ?>
                                
                                <?php foreach ($categories as $category): ?>
                                    <?php if (trim($category)): ?>
                                    <div class="service-category">
                                        <h6><?= $category_icons[trim($category)] ?? trim($category) ?></h6>
                                        <small class="text-muted">Đã được phục vụ trong tour này</small>
                                    </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                                
                                <?php if (empty(array_filter($categories))): ?>
                                    <div class="text-center text-muted">
                                        <i data-feather="info" class="font-large-1"></i>
                                        <p>Không có dịch vụ đặc biệt nào</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">
                                <i data-feather="users"></i>
                                Thông tin khách được phục vụ
                            </h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-6">
                                        <div class="avatar bg-light-primary p-50 mb-1">
                                            <div class="avatar-content">
                                                <i data-feather="user-check" class="font-medium-5"></i>
                                            </div>
                                        </div>
                                        <h5><?= $efficiency['guests_served'] ?? 0 ?></h5>
                                        <small>Khách có yêu cầu</small>
                                    </div>
                                    <div class="col-6">
                                        <div class="avatar bg-light-success p-50 mb-1">
                                            <div class="avatar-content">
                                                <i data-feather="heart" class="font-medium-5"></i>
                                            </div>
                                        </div>
                                        <h5><?= $schedule['total_guests'] ?? 0 ?></h5>
                                        <small>Tổng khách tour</small>
                                    </div>
                                </div>
                                
                                <div class="mt-2">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>Tỷ lệ khách có yêu cầu đặc biệt</span>
                                        <span><?= $schedule['total_guests'] > 0 ? number_format(($efficiency['guests_served'] ?? 0) * 100 / $schedule['total_guests'], 1) : 0 ?>%</span>
                                    </div>
                                    <div class="progress progress-sm">
                                        <div class="progress-bar bg-primary" style="width: <?= $schedule['total_guests'] > 0 ? round(($efficiency['guests_served'] ?? 0) * 100 / $schedule['total_guests']) : 0 ?>%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Notes Timeline -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">
                                <i data-feather="activity"></i>
                                Timeline Xử Lý Yêu Cầu
                            </h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <?php if (empty($notes)): ?>
                                    <div class="text-center text-muted py-4">
                                        <i data-feather="check-circle" class="font-large-2"></i>
                                        <p class="mt-2">Không có yêu cầu đặc biệt nào cho tour này</p>
                                    </div>
                                <?php else: ?>
                                    <?php foreach ($notes as $note): ?>
                                        <?php 
                                        $timeline_class = '';
                                        $status_icon = 'clock';
                                        $status_color = 'secondary';
                                        
                                        switch ($note['status']) {
                                            case 'Resolved':
                                                $timeline_class = 'resolved';
                                                $status_icon = 'check-circle';
                                                $status_color = 'success';
                                                break;
                                            case 'In Progress':
                                                $timeline_class = 'in-progress';
                                                $status_icon = 'settings';
                                                $status_color = 'warning';
                                                break;
                                            case 'Acknowledged':
                                                $timeline_class = 'acknowledged';
                                                $status_icon = 'eye';
                                                $status_color = 'info';
                                                break;
                                            default:
                                                $timeline_class = 'pending';
                                                $status_icon = 'clock';
                                                $status_color = 'secondary';
                                        }
                                        ?>
                                        <div class="timeline-item <?= $timeline_class ?>">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1">
                                                        <?php
                                                        $type_icons = [
                                                            'Dietary' => '🍽️',
                                                            'Medical' => '💊',
                                                            'Allergy' => '⚠️',
                                                            'Mobility' => '♿',
                                                            'Other' => '📝'
                                                        ];
                                                        echo $type_icons[$note['note_type']] ?? '📝';
                                                        ?>
                                                        <?= htmlspecialchars($note['full_name']) ?>
                                                        
                                                        <span class="badge badge-<?= $note['priority_level'] == 'High' ? 'danger' : ($note['priority_level'] == 'Medium' ? 'warning' : 'info') ?> badge-sm ml-1">
                                                            <?= $note['priority_level'] ?>
                                                        </span>
                                                    </h6>
                                                    <p class="mb-1"><?= htmlspecialchars($note['note_content']) ?></p>
                                                    
                                                    <?php if ($note['handler_notes']): ?>
                                                    <div class="alert alert-light-<?= $status_color ?> p-2 mt-2">
                                                        <strong>Xử lý:</strong> <?= htmlspecialchars($note['handler_notes']) ?>
                                                    </div>
                                                    <?php endif; ?>
                                                    
                                                    <small class="text-muted">
                                                        Tạo: <?= date('d/m/Y H:i', strtotime($note['created_at'])) ?>
                                                        <?php if ($note['resolved_at']): ?>
                                                            | Hoàn thành: <?= date('d/m/Y H:i', strtotime($note['resolved_at'])) ?>
                                                        <?php endif; ?>
                                                    </small>
                                                </div>
                                                <div class="text-right">
                                                    <i data-feather="<?= $status_icon ?>" class="<?= $status_color ?>"></i>
                                                    <div class="badge badge-<?= $status_color ?> mt-1">
                                                        <?= $note['status'] ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recommendations -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">
                                <i data-feather="lightbulb"></i>
                                Khuyến nghị cải tiến
                            </h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <?php 
                                $recommendations = [];
                                
                                if ($fulfillment_rate < 75) {
                                    $recommendations[] = [
                                        'type' => 'warning',
                                        'icon' => 'alert-triangle',
                                        'title' => 'Cải thiện tỷ lệ hoàn thành',
                                        'content' => 'Tỷ lệ hoàn thành yêu cầu còn thấp. Cần tăng cường đào tạo nhân viên và cải thiện quy trình xử lý.'
                                    ];
                                }
                                
                                if (($efficiency['avg_response_time'] ?? 0) > 8) {
                                    $recommendations[] = [
                                        'type' => 'info',
                                        'icon' => 'clock',
                                        'title' => 'Rút ngắn thời gian phản hồi',
                                        'content' => 'Thời gian phản hồi trung bình còn chậm. Cần tối ưu hóa quy trình thông báo và xử lý.'
                                    ];
                                }
                                
                                if (($efficiency['critical_resolved'] ?? 0) < ($efficiency['total_special_requests'] ?? 1)) {
                                    $recommendations[] = [
                                        'type' => 'danger',
                                        'icon' => 'alert-circle',
                                        'title' => 'Ưu tiên yêu cầu khẩn cấp',
                                        'content' => 'Cần có quy trình riêng cho các yêu cầu ưu tiên cao để đảm bảo xử lý kịp thời.'
                                    ];
                                }
                                
                                if ($fulfillment_rate >= 90) {
                                    $recommendations[] = [
                                        'type' => 'success',
                                        'icon' => 'award',
                                        'title' => 'Hiệu suất xuất sắc',
                                        'content' => 'Team đã thể hiện hiệu suất tuyệt vời. Tiếp tục duy trì và chia sẻ kinh nghiệm thành công.'
                                    ];
                                }
                                
                                if (empty($recommendations)) {
                                    $recommendations[] = [
                                        'type' => 'primary',
                                        'icon' => 'info',
                                        'title' => 'Duy trì chất lượng',
                                        'content' => 'Hiệu suất phục vụ ở mức tốt. Tiếp tục theo dõi và cải thiện liên tục.'
                                    ];
                                }
                                ?>
                                
                                <?php foreach ($recommendations as $rec): ?>
                                <div class="alert alert-<?= $rec['type'] ?> d-flex align-items-center">
                                    <i data-feather="<?= $rec['icon'] ?>" class="mr-2"></i>
                                    <div>
                                        <h6 class="alert-heading mb-1"><?= $rec['title'] ?></h6>
                                        <p class="mb-0"><?= $rec['content'] ?></p>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center">
                            <a href="?act=danh-sach-lich-khoi-hanh" class="btn btn-outline-secondary">
                                <i data-feather="arrow-left"></i> Quay lại
                            </a>
                            <button type="button" class="btn btn-outline-primary" onclick="window.print()">
                                <i data-feather="printer"></i> In báo cáo
                            </button>
                            <button type="button" class="btn btn-primary" onclick="exportEfficiencyReport()">
                                <i data-feather="download"></i> Xuất Excel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END: Content-->

<script>
function exportEfficiencyReport() {
    // Implementation for exporting efficiency report
    const scheduleId = <?= json_encode($schedule_id) ?>;
    window.location.href = `?act=xuat-excel-hieu-qua&schedule_id=${scheduleId}`;
}

// Add some interactive elements
document.addEventListener('DOMContentLoaded', function() {
    // Animate efficiency score
    const scoreElement = document.querySelector('.efficiency-score');
    if (scoreElement) {
        const finalScore = parseFloat(scoreElement.textContent);
        let currentScore = 0;
        const increment = finalScore / 50;
        
        const interval = setInterval(() => {
            currentScore += increment;
            if (currentScore >= finalScore) {
                currentScore = finalScore;
                clearInterval(interval);
            }
            scoreElement.textContent = currentScore.toFixed(1) + '%';
        }, 50);
    }
});
</script>

<?php require_once './views/core/footer.php'; ?>