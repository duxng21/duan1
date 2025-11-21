<?php require_once __DIR__ . '/../core/header.php'; ?>
<?php require_once __DIR__ . '/../core/menu.php'; ?>
<?php require_once __DIR__ . '/../core/alert.php'; ?>
<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">Chi tiết Booking #<?= $booking['booking_id'] ?>
                        </h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="?act=danh-sach-booking">Danh sách booking</a></li>
                                <li class="breadcrumb-item active">Chi tiết</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">
            <section id="booking-detail">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title"><i class="feather icon-file-text"></i> Thông tin booking</h4>
                                <div>
                                    <?php
                                    $statusClass = match ($booking['status']) {
                                        'Hoàn tất' => 'badge-success',
                                        'Đã đặt cọc' => 'badge-info',
                                        'Chờ xác nhận' => 'badge-warning',
                                        'Hủy' => 'badge-danger',
                                        default => 'badge-secondary'
                                    };
                                    ?>
                                    <span class="badge <?= $statusClass ?> badge-lg"><?= $booking['status'] ?></span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <dl class="row">
                                            <dt class="col-sm-5">Mã booking:</dt>
                                            <dd class="col-sm-7"><strong>#<?= $booking['booking_id'] ?></strong></dd>
                                            <dt class="col-sm-5">Loại booking:</dt>
                                            <dd class="col-sm-7">
                                                <span
                                                    class="badge badge-<?= $booking['booking_type'] == 'Đoàn' ? 'primary' : 'info' ?>">
                                                    <i
                                                        class="feather icon-<?= $booking['booking_type'] == 'Đoàn' ? 'users' : 'user' ?>"></i>
                                                    <?= $booking['booking_type'] ?>
                                                </span>
                                            </dd>
                                            <dt class="col-sm-5">Ngày đặt:</dt>
                                            <dd class="col-sm-7">
                                                <?= date('d/m/Y H:i', strtotime($booking['booking_date'])) ?></dd>
                                            <dt class="col-sm-5">Tour:</dt>
                                            <dd class="col-sm-7">
                                                <strong><?= htmlspecialchars($booking['tour_name']) ?></strong><br>
                                                <small class="text-muted"><?= $booking['tour_code'] ?></small>
                                            </dd>
                                            <?php if (!empty($booking['tour_date'])): ?>
                                                <dt class="col-sm-5">Ngày khởi hành:</dt>
                                                <dd class="col-sm-7">
                                                    <span class="text-primary">
                                                        <i class="feather icon-calendar"></i>
                                                        <?= date('d/m/Y', strtotime($booking['tour_date'])) ?>
                                                    </span>
                                                </dd>
                                            <?php endif; ?>
                                        </dl>
                                    </div>
                                    <div class="col-md-6">
                                        <dl class="row">
                                            <dt class="col-sm-5">Số người lớn:</dt>
                                            <dd class="col-sm-7"><?= $booking['num_adults'] ?> người</dd>
                                            <dt class="col-sm-5">Số trẻ em:</dt>
                                            <dd class="col-sm-7"><?= $booking['num_children'] ?> trẻ</dd>
                                            <dt class="col-sm-5">Số em bé:</dt>
                                            <dd class="col-sm-7"><?= $booking['num_infants'] ?> em</dd>
                                            <dt class="col-sm-5">Tổng số khách:</dt>
                                            <dd class="col-sm-7">
                                                <strong class="text-primary">
                                                    <?= $booking['num_adults'] + $booking['num_children'] + $booking['num_infants'] ?>
                                                    người
                                                </strong>
                                            </dd>
                                            <dt class="col-sm-5">Tổng tiền:</dt>
                                            <dd class="col-sm-7">
                                                <h4 class="text-success mb-0">
                                                    <?= number_format($booking['total_amount'], 0, ',', '.') ?> đ
                                                </h4>
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                                <?php if (!empty($booking['special_requests'])): ?>
                                    <hr>
                                    <div class="mt-2">
                                        <h5><i class="feather icon-message-square"></i> Yêu cầu đặc biệt:</h5>
                                        <div class="alert alert-info">
                                            <?= nl2br(htmlspecialchars($booking['special_requests'])) ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php if (!empty($booking_details)): ?>
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title"><i class="feather icon-package"></i> Dịch vụ bổ sung</h4>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Dịch vụ</th>
                                                    <th class="text-center">Số lượng</th>
                                                    <th class="text-right">Đơn giá</th>
                                                    <th class="text-right">Thành tiền</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $total_services = 0;
                                                foreach ($booking_details as $detail):
                                                    $subtotal = $detail['quantity'] * $detail['unit_price'];
                                                    $total_services += $subtotal;
                                                    ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars($detail['service_name']) ?></td>
                                                        <td class="text-center"><?= $detail['quantity'] ?></td>
                                                        <td class="text-right">
                                                            <?= number_format($detail['unit_price'], 0, ',', '.') ?> đ</td>
                                                        <td class="text-right"><?= number_format($subtotal, 0, ',', '.') ?> đ
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th colspan="3" class="text-right">Tổng dịch vụ:</th>
                                                    <th class="text-right text-primary">
                                                        <?= number_format($total_services, 0, ',', '.') ?> đ
                                                    </th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">
                                    <i
                                        class="feather icon-<?= $booking['booking_type'] == 'Đoàn' ? 'briefcase' : 'user' ?>"></i>
                                    <?= $booking['booking_type'] == 'Đoàn' ? 'Thông tin đoàn' : 'Thông tin khách hàng' ?>
                                </h4>
                            </div>
                            <div class="card-body">
                                <?php if ($booking['booking_type'] == 'Đoàn'): ?>
                                    <dl>
                                        <dt>Công ty/Tổ chức:</dt>
                                        <dd><strong><?= htmlspecialchars($booking['organization_name'] ?? 'N/A') ?></strong>
                                        </dd>
                                        <dt>Người liên hệ:</dt>
                                        <dd><?= htmlspecialchars($booking['contact_name'] ?? 'N/A') ?></dd>
                                        <dt>Điện thoại:</dt>
                                        <dd>
                                            <a href="tel:<?= $booking['contact_phone'] ?>">
                                                <i class="feather icon-phone"></i>
                                                <?= htmlspecialchars($booking['contact_phone'] ?? 'N/A') ?>
                                            </a>
                                        </dd>
                                        <?php if (!empty($booking['contact_email'])): ?>
                                            <dt>Email:</dt>
                                            <dd>
                                                <a href="mailto:<?= $booking['contact_email'] ?>">
                                                    <i class="feather icon-mail"></i>
                                                    <?= htmlspecialchars($booking['contact_email']) ?>
                                                </a>
                                            </dd>
                                        <?php endif; ?>
                                    </dl>
                                <?php else: ?>
                                    <dl>
                                        <dt>Họ tên:</dt>
                                        <dd><strong><?= htmlspecialchars($booking['customer_name'] ?? 'N/A') ?></strong>
                                        </dd>
                                        <dt>Điện thoại:</dt>
                                        <dd>
                                            <?php if (!empty($booking['customer_phone'])): ?>
                                                <a href="tel:<?= $booking['customer_phone'] ?>">
                                                    <i class="feather icon-phone"></i>
                                                    <?= htmlspecialchars($booking['customer_phone']) ?>
                                                </a>
                                            <?php else: ?>
                                                N/A
                                            <?php endif; ?>
                                        </dd>
                                        <?php if (!empty($booking['customer_email'])): ?>
                                            <dt>Email:</dt>
                                            <dd>
                                                <a href="mailto:<?= $booking['customer_email'] ?>">
                                                    <i class="feather icon-mail"></i>
                                                    <?= htmlspecialchars($booking['customer_email']) ?>
                                                </a>
                                            </dd>
                                        <?php endif; ?>
                                    </dl>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title"><i class="feather icon-settings"></i> Cập nhật trạng thái</h4>
                            </div>
                            <div class="card-body">
                                <?php if ($booking['status'] != 'Hủy' && $booking['status'] != 'Hoàn tất'): ?>
                                    <form method="POST" action="?act=cap-nhat-trang-thai-booking" class="mb-3">
                                        <input type="hidden" name="booking_id" value="<?= $booking['booking_id'] ?>">
                                        <div class="form-group">
                                            <label for="status">Chọn trạng thái mới:</label>
                                            <select name="status" id="status" class="form-control" required>
                                                <option value="">-- Chọn trạng thái --</option>
                                                <?php if ($booking['status'] == 'Chờ xác nhận'): ?>
                                                    <option value="Đã đặt cọc">Đã đặt cọc</option>
                                                    <option value="Hoàn tất">Hoàn tất</option>
                                                    <option value="Hủy">Hủy</option>
                                                <?php elseif ($booking['status'] == 'Đã đặt cọc'): ?>
                                                    <option value="Hoàn tất">Hoàn tất</option>
                                                    <option value="Hủy">Hủy</option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-block"
                                            onclick="return confirm('Xác nhận cập nhật trạng thái?')">
                                            <i class="feather icon-refresh-cw"></i> Cập nhật trạng thái
                                        </button>
                                    </form>
                                    <hr>
                                <?php endif; ?>

                                <div class="d-grid gap-2">
                                    <?php if ($booking['status'] != 'Hủy' && $booking['status'] != 'Hoàn tất'): ?>
                                        <a href="?act=sua-booking&id=<?= $booking['booking_id'] ?>"
                                            class="btn btn-warning btn-block mb-2">
                                            <i class="feather icon-edit"></i> Chỉnh sửa thông tin
                                        </a>
                                    <?php endif; ?>
                                    <a href="?act=danh-sach-booking" class="btn btn-outline-secondary btn-block">
                                        <i class="feather icon-arrow-left"></i> Quay lại danh sách
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../core/footer.php'; ?>