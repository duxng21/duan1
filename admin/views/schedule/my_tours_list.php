<?php
/**
 * View: Danh sách tour được phân công cho HDV (Lịch làm việc)
 * Use Case 1: Bước 2, 3
 * Hiển thị danh sách tour, cho phép lọc theo: tháng, tuần, trạng thái
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
                        <h2 class="content-header-title float-left mb-0">
                            <i class="feather icon-calendar"></i> Lịch làm việc của tôi
                        </h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="?act=home-guide">Dashboard</a></li>
                                <li class="breadcrumb-item active">Lịch làm việc
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-header-right text-md-right col-md-3 col-12 d-md-block d-none">
                <div class="form-group breadcrum-right">
                    <a href="?act=hdv-xem-lich-thang" class="btn btn-primary btn-sm">
                        <i class="feather icon-calendar"></i> Xem lịch tháng
                    </a>
                </div>
            </div>
        </div>

        <div class="content-body">
            <!-- Bộ lọc (Use Case 1 - Bước 3a) -->
            <section id="filters">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title"><i class="feather icon-filter"></i> Bộ lọc</h4>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <form method="GET" class="row g-3 align-items-end">
                                        <input type="hidden" name="act" value="hdv-lich-cua-toi">

                                        <!-- Lọc theo tháng -->
                                        <div class="col-md-3">
                                            <label for="month" class="form-label">Tháng</label>
                                            <select name="month" id="month" class="form-select">
                                                <option value="">Tất cả</option>
                                                <?php
                                                $current_month = intval($_GET['month'] ?? date('m'));
                                                $current_year = intval($_GET['year'] ?? date('Y'));
                                                for ($m = 1; $m <= 12; $m++) {
                                                    $selected = ($m == $current_month) ? 'selected' : '';
                                                    echo "<option value='$m' $selected>" . sprintf('Tháng %d', $m) . "</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <!-- Năm -->
                                        <div class="col-md-2">
                                            <label for="year" class="form-label">Năm</label>
                                            <select name="year" id="year" class="form-select">
                                                <?php
                                                $year_now = intval(date('Y'));
                                                for ($y = $year_now - 1; $y <= $year_now + 2; $y++) {
                                                    $selected = ($y == $current_year) ? 'selected' : '';
                                                    echo "<option value='$y' $selected>$y</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <!-- Lọc theo trạng thái -->
                                        <div class="col-md-3">
                                            <label for="status" class="form-label">Trạng thái tour</label>
                                            <select name="status" id="status" class="form-select">
                                                <option value="">Tất cả</option>
                                                <option value="Open" <?= ($_GET['status'] ?? '') === 'Open' ? 'selected' : '' ?>>
                                                    <i class="fas fa-circle text-success"></i> Sắp diễn ra
                                                </option>
                                                <option value="In Progress" <?= ($_GET['status'] ?? '') === 'In Progress' ? 'selected' : '' ?>>
                                                    <i class="fas fa-circle text-warning"></i> Đang diễn ra
                                                </option>
                                                <option value="Completed" <?= ($_GET['status'] ?? '') === 'Completed' ? 'selected' : '' ?>>
                                                    <i class="fas fa-circle text-success"></i> Đã kết thúc
                                                </option>
                                                <option value="Cancelled" <?= ($_GET['status'] ?? '') === 'Cancelled' ? 'selected' : '' ?>>
                                                    <i class="fas fa-circle text-danger"></i> Đã hủy
                                                </option>
                                            </select>
                                        </div>

                                        <div class="col-md-4">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="feather icon-search"></i> Lọc
                                            </button>
                                            <a href="?act=hdv-lich-cua-toi" class="btn btn-outline-secondary">
                                                <i class="feather icon-refresh-cw"></i> Xóa bộ lọc
                                            </a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Danh sách tour (Use Case 1 - Bước 2b) -->
            <div class="row">
                <div class="col-12">
                    <?php if (!empty($_SESSION['success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> <?= htmlspecialchars($_SESSION['success']) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php unset($_SESSION['success']);
                    endif; ?>

                    <?php if (!empty($_SESSION['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($_SESSION['error']) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php unset($_SESSION['error']);
                    endif; ?>

                    <!-- E2: Không có tour nào -->
                    <?php if (!empty($no_tours_message)): ?>
                        <div class="alert alert-info" role="alert">
                            <i class="fas fa-info-circle"></i> <?= $no_tours_message ?>
                        </div>
                    <?php else: ?>

                        <!-- Bảng danh sách tour -->
                        <div class="card">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 12%">Mã tour</th>
                                            <th style="width: 25%">Tên tour</th>
                                            <th style="width: 18%">Khởi hành - Kết thúc</th>
                                            <th style="width: 15%">Trạng thái</th>
                                            <th style="width: 15%">Hành động</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($tours)): ?>
                                            <?php foreach ($tours as $key => $item): ?>
                                                <?php
                                                $tour = $item['tour'];
                                                $schedule = $item['schedule'];
                                                $assignment = $item['assignment'];

                                                // Xác định trạng thái
                                                $status = $schedule['status'] ?? 'Open';
                                                $today = date('Y-m-d');
                                                $depart = $schedule['departure_date'] ?? null;
                                                $return = $schedule['return_date'] ?? $depart;

                                                // Mặc định
                                                $status_badge = 'primary';
                                                $status_text = 'Sắp đi';

                                                if ($status === 'Cancelled') {
                                                    $status_badge = 'danger';
                                                    $status_text = 'Đã hủy';
                                                } elseif ($status === 'Completed') {
                                                    $status_badge = 'success';
                                                    $status_text = 'Hoàn thành';
                                                } else {
                                                    // Trạng thái động cho HDV: sắp đi / đang đi / kết thúc
                                                    if ($depart && $today < $depart) {
                                                        $status_badge = 'primary';
                                                        $status_text = 'Sắp đi';
                                                    } elseif ($depart && $return && $today >= $depart && $today <= $return) {
                                                        $status_badge = 'warning';
                                                        $status_text = 'Đang đi';
                                                    } else {
                                                        $status_badge = 'secondary';
                                                        $status_text = 'Kết thúc';
                                                    }
                                                }
                                                ?>
                                                <tr>
                                                    <td>
                                                        <strong><?= htmlspecialchars($tour['code'] ?? 'N/A') ?></strong>
                                                    </td>
                                                    <td>
                                                        <?= htmlspecialchars($tour['tour_name'] ?? 'Chưa xác định') ?>
                                                    </td>
                                                    <td>
                                                        <small>
                                                            <i class="fas fa-calendar-alt text-muted"></i>
                                                            <?= date('d/m/Y', strtotime($schedule['departure_date'])) ?>
                                                            <?php if (!empty($schedule['return_date'])): ?>
                                                                - <?= date('d/m/Y', strtotime($schedule['return_date'])) ?>
                                                            <?php endif; ?>
                                                        </small>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-<?= $status_badge ?>">
                                                            <?= $status_text ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group btn-group-sm" role="group">
                                                            <a href="?act=hdv-chi-tiet-tour&id=<?= $schedule['schedule_id'] ?>"
                                                                class="btn btn-outline-primary" title="Xem chi tiết">
                                                                <i class="fas fa-eye"></i> Chi tiết
                                                            </a>
                                                            <a href="?act=hdv-nhiem-vu-cua-toi&schedule_id=<?= $schedule['schedule_id'] ?>"
                                                                class="btn btn-outline-info" title="Xem nhiệm vụ">
                                                                <i class="fas fa-tasks"></i> Nhiệm vụ
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="6" class="text-center text-muted py-4">
                                                    <i class="fas fa-inbox" style="font-size: 2rem;"></i>
                                                    <p>Không có tour nào</p>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Thông tin hữu ích -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6 class="card-title">
                                <i class="fas fa-lightbulb text-warning"></i> Mẹo
                            </h6>
                            <ul class="small mb-0">
                                <li>Nhấp vào "Chi tiết" để xem toàn bộ thông tin tour và lịch trình chi tiết
                                </li>
                                <li>Nhấp vào "Nhiệm vụ" để xem danh sách công việc cụ thể của bạn</li>
                                <li>Sử dụng "Xem lịch tháng" để có cái nhìn tổng quan về toàn tháng</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6 class="card-title">
                                <i class="fas fa-info-circle text-info"></i> Thông tin
                            </h6>
                            <ul class="small mb-0">
                                <li>Trạng thái "Sắp diễn ra" = Tour chưa khởi hành</li>
                                <li>Trạng thái "Đang diễn ra" = Tour đang trong quá trình thực hiện</li>
                                <li>Trạng thái "Đã kết thúc" = Tour đã hoàn thành</li>
                            </ul>
                        </div>
                    </div>
                </div>
                </section>
            </div>
        </div>
    </div>
    <!-- END: Content-->

    <?php require_once './views/core/footer.php'; ?>