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
                                'Giữ chỗ' => ['total' => 0, 'revenue' => 0, 'icon' => 'clock', 'color' => 'warning'],
                                'Đã đặt cọc' => ['total' => 0, 'revenue' => 0, 'icon' => 'check-circle', 'color' => 'info'],
                                'Đã thanh toán' => ['total' => 0, 'revenue' => 0, 'icon' => 'credit-card', 'color' => 'primary'],
                                'Đã hoàn thành' => ['total' => 0, 'revenue' => 0, 'icon' => 'check-square', 'color' => 'success'],
                                'Đã hủy' => ['total' => 0, 'revenue' => 0, 'icon' => 'x-circle', 'color' => 'danger']
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
                                                    <option value="Giữ chỗ" <?= ($_GET['status'] ?? '') == 'Giữ chỗ' ? 'selected' : '' ?>>Giữ chỗ</option>
                                                    <option value="Đã đặt cọc" <?= ($_GET['status'] ?? '') == 'Đã đặt cọc' ? 'selected' : '' ?>>Đã đặt cọc</option>
                                                    <option value="Đã thanh toán" <?= ($_GET['status'] ?? '') == 'Đã thanh toán' ? 'selected' : '' ?>>Đã thanh toán</option>
                                                    <option value="Đã hoàn thành" <?= ($_GET['status'] ?? '') == 'Đã hoàn thành' ? 'selected' : '' ?>>Đã hoàn thành</option>
                                                    <option value="Đã hủy" <?= ($_GET['status'] ?? '') == 'Đã hủy' ? 'selected' : '' ?>>Đã hủy</option>
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
                                        <div class="row mt-2">
                                            <div class="col-md-12">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input"
                                                        id="include_cancelled" name="include_cancelled" value="1"
                                                        <?= ($_GET['include_cancelled'] ?? '0') == '1' ? 'checked' : '' ?>
                                                        onchange="this.form.submit()">
                                                    <label class="custom-control-label" for="include_cancelled">
                                                        <i class="feather icon-eye"></i> Xem booking bị hủy
                                                    </label>
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
                                                                <?php if ($booking['booking_type'] == 'Đoàn'): ?>
                                                                    <i class="feather icon-briefcase text-primary"></i>
                                                                    <strong><?= htmlspecialchars($booking['organization_name'] ?? 'N/A') ?></strong><br>
                                                                    <small class="text-muted">
                                                                        <?= htmlspecialchars($booking['contact_name'] ?? '') ?>
                                                                        <?= !empty($booking['contact_phone']) ? ' - ' . htmlspecialchars($booking['contact_phone']) : '' ?>
                                                                    </small>
                                                                <?php else: ?>
                                                                    <i class="feather icon-user"></i>
                                                                    <?= htmlspecialchars($booking['customer_name'] ?? 'N/A') ?><br>
                                                                    <small
                                                                        class="text-muted"><?= htmlspecialchars($booking['customer_phone'] ?? '') ?></small>
                                                                <?php endif; ?>
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
                                                                switch ($booking['status']) {
                                                                    case 'Đã hoàn thành':
                                                                        $statusClass = 'badge-success';
                                                                        break;
                                                                    case 'Đã thanh toán':
                                                                        $statusClass = 'badge-primary';
                                                                        break;
                                                                    case 'Đã đặt cọc':
                                                                        $statusClass = 'badge-info';
                                                                        break;
                                                                    case 'Giữ chỗ':
                                                                        $statusClass = 'badge-warning';
                                                                        break;
                                                                    case 'Đã hủy':
                                                                        $statusClass = 'badge-danger';
                                                                        break;
                                                                    default:
                                                                        $statusClass = 'badge-secondary';
                                                                        break;
                                                                }
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