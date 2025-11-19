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
                        <h2 class="content-header-title float-left mb-0">Danh sách Booking</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">
            <section id="booking-list">
                <div class="row">
                    <!-- Thống kê -->
                    <div class="col-12 mb-3">
                        <div class="row">
                            <?php
                            $stats = [
                                'Chờ xác nhận' => ['total' => 0, 'revenue' => 0, 'icon' => 'clock', 'color' => 'warning'],
                                'Đã đặt cọc' => ['total' => 0, 'revenue' => 0, 'icon' => 'check-circle', 'color' => 'info'],
                                'Hoàn tất' => ['total' => 0, 'revenue' => 0, 'icon' => 'check-square', 'color' => 'success'],
                                'Hủy' => ['total' => 0, 'revenue' => 0, 'icon' => 'x-circle', 'color' => 'danger']
                            ];

                            foreach ($statistics as $stat) {
                                if (isset($stats[$stat['status']])) {
                                    $stats[$stat['status']]['total'] = $stat['total'];
                                    $stats[$stat['status']]['revenue'] = $stat['total_revenue'];
                                }
                            }

                            foreach ($stats as $status => $data):
                                ?>
                                <div class="col-xl-3 col-sm-6 col-12">
                                    <div class="card">
                                        <div class="card-content">
                                            <div class="card-body">
                                                <div class="media d-flex">
                                                    <div class="align-self-center">
                                                        <i
                                                            class="feather icon-<?= $data['icon'] ?> text-<?= $data['color'] ?> font-large-2 float-left"></i>
                                                    </div>
                                                    <div class="media-body text-right">
                                                        <h3 class="text-<?= $data['color'] ?>"><?= $data['total'] ?></h3>
                                                        <span><?= $status ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Danh sách booking</h4>
                                <div>
                                    <a href="?act=them-booking" class="btn btn-primary btn-sm">
                                        <i class="feather icon-plus"></i> Tạo booking
                                    </a>
                                </div>
                            </div>
                            <div class="card-content">
                                <div class="card-body card-dashboard">
                                    <!-- Bộ lọc & Tìm kiếm -->
                                    <form method="GET" action="" class="mb-3">
                                        <input type="hidden" name="act" value="danh-sach-booking">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <select name="tour_id" class="form-control"
                                                    onchange="this.form.submit()">
                                                    <option value="">Tất cả tour</option>
                                                    <?php foreach ($tours as $tour): ?>
                                                        <option value="<?= $tour['tour_id'] ?>" <?= ($_GET['tour_id'] ?? '') == $tour['tour_id'] ? 'selected' : '' ?>>
                                                            <?= htmlspecialchars($tour['tour_name']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <select name="status" class="form-control"
                                                    onchange="this.form.submit()">
                                                    <option value="">Tất cả trạng thái</option>
                                                    <option value="Chờ xác nhận" <?= ($_GET['status'] ?? '') == 'Chờ xác nhận' ? 'selected' : '' ?>>Chờ xác nhận</option>
                                                    <option value="Đã đặt cọc" <?= ($_GET['status'] ?? '') == 'Đã đặt cọc' ? 'selected' : '' ?>>Đã đặt cọc</option>
                                                    <option value="Hoàn tất" <?= ($_GET['status'] ?? '') == 'Hoàn tất' ? 'selected' : '' ?>>Hoàn tất</option>
                                                    <option value="Hủy" <?= ($_GET['status'] ?? '') == 'Hủy' ? 'selected' : '' ?>>Hủy</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="input-group">
                                                    <input type="text" class="form-control" name="search"
                                                        placeholder="Tìm theo tên, SĐT..."
                                                        value="<?= $_GET['search'] ?? '' ?>">
                                                    <div class="input-group-append">
                                                        <button class="btn btn-primary" type="submit">
                                                            <i class="feather icon-search"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>

                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Mã</th>
                                                    <th>Khách hàng</th>
                                                    <th>Tour</th>
                                                    <th>Ngày đặt</th>
                                                    <th>Số khách</th>
                                                    <th>Tổng tiền</th>
                                                    <th>Trạng thái</th>
                                                    <th>Hành động</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($bookings)): ?>
                                                    <?php foreach ($bookings as $booking): ?>
                                                        <tr>
                                                            <td><strong>#<?= $booking['booking_id'] ?></strong></td>
                                                            <td>
                                                                <?= htmlspecialchars($booking['customer_name'] ?? 'N/A') ?><br>
                                                                <small
                                                                    class="text-muted"><?= htmlspecialchars($booking['customer_phone'] ?? '') ?></small>
                                                            </td>
                                                            <td><?= htmlspecialchars($booking['tour_name']) ?></td>
                                                            <td><?= date('d/m/Y H:i', strtotime($booking['booking_date'])) ?>
                                                            </td>
                                                            <td>
                                                                <?= $booking['num_adults'] ?> NL
                                                                <?= $booking['num_children'] > 0 ? ', ' . $booking['num_children'] . ' TE' : '' ?>
                                                                <?= $booking['num_infants'] > 0 ? ', ' . $booking['num_infants'] . ' EB' : '' ?>
                                                            </td>
                                                            <td><?= number_format($booking['total_amount'], 0, ',', '.') ?> đ
                                                            </td>
                                                            <td>
                                                                <?php
                                                                $statusClass = match ($booking['status']) {
                                                                    'Hoàn tất' => 'badge-success',
                                                                    'Đã đặt cọc' => 'badge-info',
                                                                    'Chờ xác nhận' => 'badge-warning',
                                                                    'Hủy' => 'badge-danger',
                                                                    default => 'badge-secondary'
                                                                };
                                                                ?>
                                                                <span
                                                                    class="badge <?= $statusClass ?>"><?= $booking['status'] ?></span>
                                                            </td>
                                                            <td>
                                                                <a href="?act=chi-tiet-booking&id=<?= $booking['booking_id'] ?>"
                                                                    class="btn btn-info btn-sm" title="Chi tiết">
                                                                    <i class="feather icon-eye"></i>
                                                                </a>
                                                                <?php if ($booking['status'] != 'Hủy'): ?>
                                                                    <a href="?act=sua-booking&id=<?= $booking['booking_id'] ?>"
                                                                        class="btn btn-warning btn-sm" title="Sửa">
                                                                        <i class="feather icon-edit"></i>
                                                                    </a>
                                                                    <a onclick="return confirm('Hủy booking này?')"
                                                                        href="?act=huy-booking&id=<?= $booking['booking_id'] ?>"
                                                                        class="btn btn-danger btn-sm" title="Hủy">
                                                                        <i class="feather icon-x"></i>
                                                                    </a>
                                                                <?php endif; ?>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="8" class="text-center text-muted">Chưa có booking nào
                                                        </td>
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
            </section>
        </div>
    </div>
</div>
<!-- END: Content-->
<?php require_once __DIR__ . '/../core/footer.php'; ?>