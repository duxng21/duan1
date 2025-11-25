<?php require_once __DIR__ . '/../core/header.php'; ?>
<?php require_once __DIR__ . '/../core/menu.php'; ?>
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">Chi tiết báo giá #<?= $quote['quote_id'] ?>
                        </h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">
            <?php require_once __DIR__ . '/../core/alert.php'; ?>

            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Thông tin báo giá</h4>
                            <div>
                                <a href="?act=xuat-bao-gia&id=<?= $quote['quote_id'] ?>&format=excel"
                                    class="btn btn-success btn-sm">
                                    <i class="feather icon-file-text"></i> Xuất Excel
                                </a>
                                <a href="?act=xuat-bao-gia&id=<?= $quote['quote_id'] ?>&format=pdf" target="_blank"
                                    class="btn btn-info btn-sm">
                                    <i class="feather icon-printer"></i> In / PDF
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table">
                                <tr>
                                    <th width="30%">Tour</th>
                                    <td><strong><?= htmlspecialchars($quote['tour_name']) ?></strong>
                                        (<?= htmlspecialchars($quote['tour_code']) ?>)</td>
                                </tr>
                                <tr>
                                    <th>Ngày khởi hành</th>
                                    <td><?= $quote['departure_date'] ? date('d/m/Y', strtotime($quote['departure_date'])) : '-' ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Thời gian</th>
                                    <td><?= $quote['duration_days'] ?? '-' ?> ngày</td>
                                </tr>
                                <tr>
                                    <th>Khách hàng</th>
                                    <td><?= htmlspecialchars($quote['customer_name']) ?></td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td><?= htmlspecialchars($quote['customer_email']) ?></td>
                                </tr>
                                <tr>
                                    <th>Số điện thoại</th>
                                    <td><?= htmlspecialchars($quote['customer_phone']) ?></td>
                                </tr>
                                <tr>
                                    <th>Địa chỉ</th>
                                    <td><?= htmlspecialchars($quote['customer_address']) ?></td>
                                </tr>
                                <tr>
                                    <th>Số lượng</th>
                                    <td>
                                        Người lớn: <?= $quote['num_adults'] ?> |
                                        Trẻ em: <?= $quote['num_children'] ?> |
                                        Em bé: <?= $quote['num_infants'] ?>
                                    </td>
                                </tr>
                            </table>

                            <?php if (!empty($options)): ?>
                                <hr>
                                <h5>Dịch vụ bổ sung</h5>
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Dịch vụ</th>
                                            <th>Số lượng</th>
                                            <th class="text-right">Đơn giá</th>
                                            <th class="text-right">Thành tiền</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($options as $opt): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($opt['option_name']) ?></td>
                                                <td><?= $opt['quantity'] ?></td>
                                                <td class="text-right"><?= number_format($opt['option_price'], 0, ',', '.') ?>
                                                </td>
                                                <td class="text-right">
                                                    <?= number_format($opt['option_price'] * $opt['quantity'], 0, ',', '.') ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php endif; ?>

                            <hr>
                            <h5>Bảng giá</h5>
                            <table class="table">
                                <tr>
                                    <th>Giá căn bản</th>
                                    <td class="text-right"><?= number_format($quote['base_price'], 0, ',', '.') ?> đ
                                    </td>
                                </tr>
                                <?php if ($quote['discount_value'] > 0): ?>
                                    <tr>
                                        <th>Chiết khấu
                                            <?php if ($quote['discount_type'] === 'percent'): ?>
                                                (<?= $quote['discount_value'] ?>%)
                                            <?php endif; ?>
                                        </th>
                                        <td class="text-right text-danger">
                                            -<?php
                                            if ($quote['discount_type'] === 'percent') {
                                                echo number_format($quote['base_price'] * $quote['discount_value'] / 100, 0, ',', '.');
                                            } else {
                                                echo number_format($quote['discount_value'], 0, ',', '.');
                                            }
                                            ?> đ
                                        </td>
                                    </tr>
                                <?php endif; ?>
                                <?php if ($quote['additional_fees'] > 0): ?>
                                    <tr>
                                        <th>Phụ phí</th>
                                        <td class="text-right"><?= number_format($quote['additional_fees'], 0, ',', '.') ?>
                                            đ</td>
                                    </tr>
                                <?php endif; ?>
                                <?php if ($quote['tax_percent'] > 0): ?>
                                    <tr>
                                        <th>Thuế (<?= $quote['tax_percent'] ?>%)</th>
                                        <td class="text-right">
                                            <?= number_format(($quote['base_price'] - ($quote['discount_type'] === 'percent' ? $quote['base_price'] * $quote['discount_value'] / 100 : $quote['discount_value'])) * $quote['tax_percent'] / 100, 0, ',', '.') ?>
                                            đ</td>
                                    </tr>
                                <?php endif; ?>
                                <tr class="font-weight-bold">
                                    <th>Tổng cộng</th>
                                    <td class="text-right text-primary">
                                        <?= number_format($quote['total_amount'], 0, ',', '.') ?> đ</td>
                                </tr>
                            </table>

                            <?php if ($quote['internal_notes']): ?>
                                <hr>
                                <p><strong>Ghi chú nội bộ:</strong> <?= nl2br(htmlspecialchars($quote['internal_notes'])) ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Trạng thái & hành động</h4>
                        </div>
                        <div class="card-body">
                            <p>
                                <strong>Trạng thái:</strong>
                                <?php
                                $badge = match ($quote['status']) {
                                    'Đang chờ' => 'badge-warning',
                                    'Đã chấp nhận' => 'badge-success',
                                    'Đã từ chối' => 'badge-danger',
                                    'Hết hạn' => 'badge-secondary',
                                    default => 'badge-light'
                                };
                                ?>
                                <span class="badge <?= $badge ?>"><?= htmlspecialchars($quote['status']) ?></span>
                            </p>
                            <p><strong>Thời hạn:</strong> <?= $quote['validity_days'] ?> ngày</p>
                            <p><strong>Ngày tạo:</strong> <?= date('d/m/Y H:i', strtotime($quote['created_at'])) ?></p>
                            <p><strong>Người tạo:</strong> <?= htmlspecialchars($quote['creator_name']) ?></p>

                            <hr>

                            <h6>Cập nhật trạng thái:</h6>
                            <div class="btn-group-vertical btn-block">
                                <a href="?act=cap-nhat-trang-thai-bao-gia&id=<?= $quote['quote_id'] ?>&status=Đang chờ"
                                    class="btn btn-outline-warning btn-sm">
                                    Đang chờ
                                </a>
                                <a href="?act=cap-nhat-trang-thai-bao-gia&id=<?= $quote['quote_id'] ?>&status=Đã chấp nhận"
                                    class="btn btn-outline-success btn-sm">
                                    Đã chấp nhận
                                </a>
                                <a href="?act=cap-nhat-trang-thai-bao-gia&id=<?= $quote['quote_id'] ?>&status=Đã từ chối"
                                    class="btn btn-outline-danger btn-sm">
                                    Đã từ chối
                                </a>
                                <a href="?act=cap-nhat-trang-thai-bao-gia&id=<?= $quote['quote_id'] ?>&status=Hết hạn"
                                    class="btn btn-outline-secondary btn-sm">
                                    Hết hạn
                                </a>
                            </div>

                            <hr>

                            <a href="?act=danh-sach-bao-gia" class="btn btn-outline-primary btn-block">
                                <i class="feather icon-arrow-left"></i> Quay lại
                            </a>
                            <a onclick="return confirm('Xóa báo giá này?')"
                                href="?act=xoa-bao-gia&id=<?= $quote['quote_id'] ?>"
                                class="btn btn-outline-danger btn-block">
                                <i class="feather icon-trash"></i> Xóa
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../core/footer.php'; ?>