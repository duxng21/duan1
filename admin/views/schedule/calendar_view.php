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
                        <h2 class="content-header-title float-left mb-0">Lịch khởi hành theo tháng</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">
            <!-- Thông báo -->
            <?php require_once __DIR__ . '/../core/alert.php'; ?>

            <section id="calendar-view">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center w-100">
                                    <div>
                                        <a href="?act=xem-lich-theo-thang&month=<?= $prevMonth ?>&year=<?= $prevYear ?>"
                                            class="btn btn-outline-primary btn-sm">
                                            <i class="feather icon-chevron-left"></i> Tháng trước
                                        </a>
                                    </div>
                                    <h4 class="card-title mb-0">
                                        Tháng <?= $month ?>/<?= $year ?>
                                    </h4>
                                    <div>
                                        <a href="?act=xem-lich-theo-thang&month=<?= $nextMonth ?>&year=<?= $nextYear ?>"
                                            class="btn btn-outline-primary btn-sm">
                                            Tháng sau <i class="feather icon-chevron-right"></i>
                                        </a>
                                        <a href="?act=danh-sach-lich-khoi-hanh" class="btn btn-info btn-sm ml-1">
                                            <i class="feather icon-list"></i> Xem dạng danh sách
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered text-center">
                                            <thead class="bg-primary text-white">
                                                <tr>
                                                    <th>Chủ nhật</th>
                                                    <th>Thứ 2</th>
                                                    <th>Thứ 3</th>
                                                    <th>Thứ 4</th>
                                                    <th>Thứ 5</th>
                                                    <th>Thứ 6</th>
                                                    <th>Thứ 7</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                // Tạo calendar
                                                $firstDay = mktime(0, 0, 0, $month, 1, $year);
                                                $daysInMonth = date('t', $firstDay);
                                                $dayOfWeek = date('w', $firstDay); // 0 (Sunday) đến 6 (Saturday)
                                                
                                                $currentDay = 1;
                                                $totalCells = ceil(($daysInMonth + $dayOfWeek) / 7) * 7;

                                                // Group schedules by date
                                                $schedulesByDate = [];
                                                foreach ($schedules as $schedule) {
                                                    $date = date('Y-m-d', strtotime($schedule['departure_date']));
                                                    if (!isset($schedulesByDate[$date])) {
                                                        $schedulesByDate[$date] = [];
                                                    }
                                                    $schedulesByDate[$date][] = $schedule;
                                                }

                                                for ($i = 0; $i < $totalCells; $i++):
                                                    if ($i % 7 == 0)
                                                        echo '<tr>';

                                                    if ($i < $dayOfWeek || $currentDay > $daysInMonth):
                                                        echo '<td class="bg-light" style="height: 120px;"></td>';
                                                    else:
                                                        $currentDate = sprintf('%04d-%02d-%02d', $year, $month, $currentDay);
                                                        $hasSchedules = isset($schedulesByDate[$currentDate]);
                                                        $cellClass = $hasSchedules ? 'bg-light-primary' : '';
                                                        ?>
                                                        <td class="<?= $cellClass ?> align-top p-1"
                                                            style="height: 120px; vertical-align: top;">
                                                            <div class="font-weight-bold mb-1"><?= $currentDay ?></div>
                                                            <?php if ($hasSchedules): ?>
                                                                <div class="small">
                                                                    <?php foreach ($schedulesByDate[$currentDate] as $schedule): ?>
                                                                        <a href="?act=chi-tiet-lich-khoi-hanh&id=<?= $schedule['schedule_id'] ?>"
                                                                            class="badge badge-info badge-sm d-block mb-1 text-left"
                                                                            title="<?= htmlspecialchars($schedule['tour_name']) ?>"
                                                                            style="font-size: 0.7rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                                            <?= htmlspecialchars(substr($schedule['tour_name'], 0, 15)) ?>
                                                                            <?= strlen($schedule['tour_name']) > 15 ? '...' : '' ?>
                                                                            <br>
                                                                            <small><?= $schedule['total_guests'] ?? 0 ?>/<?= $schedule['max_participants'] ?>
                                                                                người</small>
                                                                        </a>
                                                                    <?php endforeach; ?>
                                                                </div>
                                                            <?php endif; ?>
                                                        </td>
                                                        <?php
                                                        $currentDay++;
                                                    endif;

                                                    if ($i % 7 == 6)
                                                        echo '</tr>';
                                                endfor;
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Chú thích -->
                                    <div class="mt-3">
                                        <h5>Danh sách chi tiết:</h5>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Ngày</th>
                                                        <th>Tour</th>
                                                        <th>Số người</th>
                                                        <th>Trạng thái</th>
                                                        <th>Hành động</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if (!empty($schedules)): ?>
                                                        <?php foreach ($schedules as $schedule): ?>
                                                            <tr>
                                                                <td><?= date('d/m/Y', strtotime($schedule['departure_date'])) ?>
                                                                </td>
                                                                <td><?= htmlspecialchars($schedule['tour_name'] ?? '') ?></td>
                                                                <td>
                                                                    <span class="badge badge-info">
                                                                        <?= $schedule['total_guests'] ?? 0 ?> /
                                                                        <?= $schedule['max_participants'] ?? 0 ?>
                                                                    </span>
                                                                </td>
                                                                <td>
                                                                    <?php
                                                                    switch ($schedule['status']) {
                                                                        case 'Open':
                                                                            $statusClass = 'badge-success';
                                                                            $statusText = 'Mở';
                                                                            break;
                                                                        case 'Full':
                                                                            $statusClass = 'badge-warning';
                                                                            $statusText = 'Đầy';
                                                                            break;
                                                                        case 'Confirmed':
                                                                            $statusClass = 'badge-primary';
                                                                            $statusText = 'Đã xác nhận';
                                                                            break;
                                                                        case 'Completed':
                                                                            $statusClass = 'badge-secondary';
                                                                            $statusText = 'Hoàn thành';
                                                                            break;
                                                                        case 'Cancelled':
                                                                            $statusClass = 'badge-danger';
                                                                            $statusText = 'Đã hủy';
                                                                            break;
                                                                        default:
                                                                            $statusClass = 'badge-light';
                                                                            $statusText = $schedule['status'];
                                                                            break;
                                                                    }
                                                                    ?>
                                                                    <span
                                                                        class="badge <?= $statusClass ?>"><?= $statusText ?></span>
                                                                </td>
                                                                <td>
                                                                    <a href="?act=chi-tiet-lich-khoi-hanh&id=<?= $schedule['schedule_id'] ?>"
                                                                        class="btn btn-info btn-sm">
                                                                        <i class="feather icon-eye"></i>
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <tr>
                                                            <td colspan="5" class="text-center text-muted">Không có lịch
                                                                khởi
                                                                hành trong tháng này</td>
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
                </div>
            </section>
        </div>
    </div>
</div>
<!-- END: Content-->
<?php require_once __DIR__ . '/../core/footer.php'; ?>