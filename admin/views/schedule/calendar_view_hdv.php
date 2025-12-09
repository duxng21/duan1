<?php
/**
 * View: Lịch xem tháng dành cho HDV
 * Use Case 1: Bước 6, Luồng phụ A2
 * Hiển thị lịch trực quan với các ngày có tour được đánh dấu
 * Khi bấm vào ngày, hiển thị popup danh sách tour
 */
?>
<?php require_once './views/core/header.php'; ?>
<?php require_once './views/core/menu.php'; ?>

<div class="container-fluid">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="page-title">Lịch xem tháng</h1>
                <p class="text-muted">Xem tổng quan lịch làm việc theo tháng</p>
            </div>
            <div class="col-auto">
                <a href="?act=hdv-lich-cua-toi" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
            </div>
        </div>
    </div>

    <!-- Chọn tháng/năm -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" class="row g-3 align-items-end">
                        <input type="hidden" name="act" value="hdv-xem-lich-thang">

                        <div class="col-md-3">
                            <label for="month" class="form-label">Tháng</label>
                            <select name="month" id="month" class="form-select" onchange="this.form.submit()">
                                <?php
                                $current_month = intval($month ?? date('m'));
                                for ($m = 1; $m <= 12; $m++) {
                                    $selected = ($m == $current_month) ? 'selected' : '';
                                    echo "<option value='$m' $selected>" . sprintf('Tháng %d', $m) . "</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="year" class="form-label">Năm</label>
                            <select name="year" id="year" class="form-select" onchange="this.form.submit()">
                                <?php
                                $current_year = intval($year ?? date('Y'));
                                for ($y = $current_year - 1; $y <= $current_year + 2; $y++) {
                                    $selected = ($y == $current_year) ? 'selected' : '';
                                    echo "<option value='$y' $selected>$y</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="col-md-6 text-end">
                            <button type="button" class="btn btn-outline-secondary" onclick="window.location.href='?act=hdv-xem-lich-thang&month=' + (new Date()).getMonth() + 1 + '&year=' + (new Date()).getFullYear()">
                                <i class="fas fa-calendar-today"></i> Tháng hiện tại
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Lịch tháng -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0 text-center">
                        Tháng <?= str_pad($month, 2, '0', STR_PAD_LEFT) ?> / <?= $year ?>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered text-center" style="min-height: 500px;">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 14.28%">Thứ Hai</th>
                                    <th style="width: 14.28%">Thứ Ba</th>
                                    <th style="width: 14.28%">Thứ Tư</th>
                                    <th style="width: 14.28%">Thứ Năm</th>
                                    <th style="width: 14.28%">Thứ Sáu</th>
                                    <th style="width: 14.28%">Thứ Bảy</th>
                                    <th style="width: 14.28%">Chủ Nhật</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $first_day = mktime(0, 0, 0, $month, 1, $year);
                                $last_day = mktime(0, 0, 0, $month + 1, 0, $year);
                                $first_day_of_week = (date('N', $first_day) - 1) % 7; // 0 = Thứ Hai, 6 = Chủ Nhật
                                $total_days = date('d', $last_day);

                                $day = 1;
                                for ($week = 0; $week < 6; $week++) {
                                    echo '<tr style="height: 100px;">';
                                    for ($dow = 0; $dow < 7; $dow++) {
                                        if (($week == 0 && $dow < $first_day_of_week) || $day > $total_days) {
                                            echo '<td class="bg-light"></td>';
                                        } else {
                                            $current_date = sprintf('%04d-%02d-%02d', $year, $month, $day);
                                            $has_events = isset($calendar_events[$day]) && !empty($calendar_events[$day]);

                                            $today = date('Y-m-d');
                                            $is_today = ($current_date === $today) ? true : false;

                                            $cell_class = $is_today ? 'bg-primary bg-opacity-10 table-active' : 'bg-white';
                                            $cell_class .= $has_events ? ' border-3 border-success' : '';
                                        ?>
                                <td class="p-2 <?= $cell_class ?>" style="vertical-align: top; position: relative; cursor: <?= $has_events ? 'pointer' : 'default' ?>;"
                                    <?php if ($has_events): ?>
                                    data-bs-toggle="modal" data-bs-target="#dayModal" onclick="showDayEvents(<?= $day ?>)"
                                    <?php endif; ?>>
                                    <div class="fw-bold mb-2">
                                        <?= $day ?>
                                        <?php if ($is_today): ?>
                                        <span class="badge bg-danger rounded-circle" style="font-size: 0.6rem;">●</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="small">
                                        <?php if ($has_events): ?>
                                            <?php foreach (array_slice($calendar_events[$day], 0, 2) as $schedule): ?>
                                            <div class="badge bg-success mb-1" style="display: block; font-size: 0.65rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                <?= htmlspecialchars(substr($schedule['tour_name'], 0, 20)) ?>...
                                            </div>
                                            <?php endforeach; ?>
                                            <?php if (count($calendar_events[$day]) > 2): ?>
                                            <div class="text-muted" style="font-size: 0.7rem;">
                                                +<?= count($calendar_events[$day]) - 2 ?> thêm
                                            </div>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                        <?php
                                            $day++;
                                        }
                                    }
                                    echo '</tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-light">
                    <div class="row">
                        <div class="col-auto">
                            <span class="badge bg-success">■</span> Có tour được phân công
                        </div>
                        <div class="col-auto">
                            <span class="badge bg-danger">●</span> Hôm nay
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Danh sách tour của tháng -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-list"></i> Danh sách tour tháng <?= str_pad($month, 2, '0', STR_PAD_LEFT) ?>
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($calendar_data)): ?>
                    <div class="timeline">
                        <?php foreach ($calendar_data as $schedule): ?>
                        <div class="timeline-item mb-4">
                            <div class="timeline-marker bg-success">
                                <i class="fas fa-check text-white"></i>
                            </div>
                            <div class="timeline-content">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="mb-1">
                                            <strong><?= htmlspecialchars($schedule['tour_name']) ?></strong>
                                            <span class="badge bg-info">
                                                <?= htmlspecialchars($schedule['tour_code']) ?>
                                            </span>
                                        </h6>
                                        <small class="text-muted">
                                            <i class="fas fa-calendar-alt"></i>
                                            <?= date('d/m/Y', strtotime($schedule['departure_date'])) ?>
                                            <?php if ($schedule['return_date']): ?>
                                            - <?= date('d/m/Y', strtotime($schedule['return_date'])) ?>
                                            <?php endif; ?>
                                        </small>
                                    </div>
                                    <a href="?act=hdv-chi-tiet-tour&id=<?= $schedule['schedule_id'] ?>"
                                        class="btn btn-sm btn-primary">
                                        <i class="fas fa-arrow-right"></i> Xem
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Không có tour nào được phân công trong tháng này
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Chi tiết ngày -->
<div class="modal fade" id="dayModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tour ngày <span id="modalDay"></span>/<span id="modalMonth"><?= $month ?></span>/<span id="modalYear"><?= $year ?></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="dayEventsContainer">
                <!-- Sẽ được populate bằng JavaScript -->
            </div>
        </div>
    </div>
</div>

<script>
function showDayEvents(day) {
    document.getElementById('modalDay').textContent = day;
    
    // Dữ liệu các sự kiện theo ngày (được tạo từ PHP)
    const calendarData = <?php echo json_encode($calendar_events); ?>;
    
    const events = calendarData[day] || [];
    let html = '';
    
    if (events.length === 0) {
        html = '<div class="alert alert-info">Không có tour nào</div>';
    } else {
        events.forEach(function(schedule) {
            html += `
                <div class="card mb-2">
                    <div class="card-body">
                        <h6 class="card-title mb-2">
                            <strong>${schedule.tour_name}</strong>
                            <span class="badge bg-info">${schedule.tour_code}</span>
                        </h6>
                        <p class="card-text small mb-2">
                            <i class="fas fa-calendar-alt"></i> 
                            ${new Date(schedule.departure_date).toLocaleDateString('vi-VN')}
                        </p>
                        <a href="?act=hdv-chi-tiet-tour&id=${schedule.schedule_id}" class="btn btn-sm btn-primary">
                            <i class="fas fa-eye"></i> Xem chi tiết
                        </a>
                    </div>
                </div>
            `;
        });
    }
    
    document.getElementById('dayEventsContainer').innerHTML = html;
}
</script>

<style>
.timeline {
    position: relative;
    padding-left: 40px;
}

.timeline-item {
    position: relative;
}

.timeline-marker {
    position: absolute;
    left: -40px;
    top: 0;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid white;
}

.timeline-content {
    padding: 10px 15px;
    background: #f8f9fa;
    border-radius: 4px;
}
</style>

<?php require_once './views/core/footer.php'; ?>
