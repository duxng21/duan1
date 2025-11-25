<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Báo giá #<?= $quote['quote_id'] ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            padding: 20px;
            line-height: 1.6;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #333;
            padding-bottom: 15px;
        }

        .header h1 {
            color: #0066cc;
            font-size: 28px;
            margin-bottom: 5px;
        }

        .header p {
            color: #666;
            font-size: 14px;
        }

        .section {
            margin-bottom: 20px;
        }

        .section h3 {
            background: #f5f5f5;
            padding: 8px;
            border-left: 4px solid #0066cc;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        table th,
        table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        table th {
            background: #f9f9f9;
            font-weight: 600;
            width: 30%;
        }

        .pricing-table {
            margin-top: 20px;
        }

        .pricing-table td {
            text-align: right;
        }

        .total-row {
            font-size: 18px;
            font-weight: bold;
            background: #f0f8ff;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #ddd;
            font-size: 12px;
            color: #777;
            text-align: center;
        }

        .validity {
            background: #fff3cd;
            padding: 10px;
            border-left: 4px solid #ffc107;
            margin: 15px 0;
        }

        .options-table th,
        .options-table td {
            text-align: center;
        }

        .options-table th:first-child,
        .options-table td:first-child {
            text-align: left;
        }

        .no-print {
            display: block;
            margin-bottom: 15px;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="no-print">
        <button onclick="window.print()"
            style="padding: 10px 20px; background: #0066cc; color: white; border: none; cursor: pointer; font-size: 14px;">
            In báo giá
        </button>
    </div>

    <div class="header">
        <h1>BÁO GIÁ TOUR DU LỊCH</h1>
        <p>Mã báo giá: #<?= $quote['quote_id'] ?> | Ngày tạo: <?= date('d/m/Y', strtotime($quote['created_at'])) ?></p>
    </div>

    <div class="section">
        <h3>Thông tin khách hàng</h3>
        <table>
            <tr>
                <th>Tên khách hàng</th>
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
        </table>
    </div>

    <div class="section">
        <h3>Thông tin tour</h3>
        <table>
            <tr>
                <th>Tên tour</th>
                <td><?= htmlspecialchars($quote['tour_name']) ?></td>
            </tr>
            <tr>
                <th>Mã tour</th>
                <td><?= htmlspecialchars($quote['tour_code']) ?></td>
            </tr>
            <tr>
                <th>Thời gian</th>
                <td><?= $quote['duration_days'] ?? '-' ?> ngày</td>
            </tr>
            <tr>
                <th>Ngày khởi hành</th>
                <td><?= $quote['departure_date'] ? date('d/m/Y', strtotime($quote['departure_date'])) : 'Linh hoạt' ?>
                </td>
            </tr>
            <tr>
                <th>Số lượng khách</th>
                <td>
                    <strong>Người lớn:</strong> <?= $quote['num_adults'] ?> |
                    <strong>Trẻ em:</strong> <?= $quote['num_children'] ?> |
                    <strong>Em bé:</strong> <?= $quote['num_infants'] ?>
                </td>
            </tr>
        </table>
    </div>

    <?php if (!empty($options)): ?>
        <div class="section">
            <h3>Dịch vụ bổ sung</h3>
            <table class="options-table">
                <thead>
                    <tr>
                        <th>Dịch vụ</th>
                        <th>Số lượng</th>
                        <th>Đơn giá</th>
                        <th>Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($options as $opt): ?>
                        <tr>
                            <td><?= htmlspecialchars($opt['option_name']) ?></td>
                            <td><?= $opt['quantity'] ?></td>
                            <td><?= number_format($opt['option_price'], 0, ',', '.') ?> đ</td>
                            <td><?= number_format($opt['option_price'] * $opt['quantity'], 0, ',', '.') ?> đ</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <div class="section">
        <h3>Bảng giá chi tiết</h3>
        <table class="pricing-table">
            <tr>
                <th>Giá căn bản</th>
                <td><?= number_format($quote['base_price'], 0, ',', '.') ?> đ</td>
            </tr>
            <?php if ($quote['discount_value'] > 0): ?>
                <tr>
                    <th>Chiết khấu
                        <?php if ($quote['discount_type'] === 'percent'): ?>
                            (<?= $quote['discount_value'] ?>%)
                        <?php endif; ?>
                    </th>
                    <td style="color: #d9534f;">
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
                    <td><?= number_format($quote['additional_fees'], 0, ',', '.') ?> đ</td>
                </tr>
            <?php endif; ?>
            <?php if ($quote['tax_percent'] > 0): ?>
                <tr>
                    <th>Thuế (<?= $quote['tax_percent'] ?>%)</th>
                    <td><?= number_format(($quote['base_price'] - ($quote['discount_type'] === 'percent' ? $quote['base_price'] * $quote['discount_value'] / 100 : $quote['discount_value'])) * $quote['tax_percent'] / 100, 0, ',', '.') ?>
                        đ</td>
                </tr>
            <?php endif; ?>
            <tr class="total-row">
                <th>TỔNG CỘNG</th>
                <td style="color: #0066cc; font-size: 20px;"><?= number_format($quote['total_amount'], 0, ',', '.') ?> đ
                </td>
            </tr>
        </table>
    </div>

    <div class="validity">
        <strong>Thời hạn báo giá:</strong> <?= $quote['validity_days'] ?> ngày kể từ ngày
        <?= date('d/m/Y', strtotime($quote['created_at'])) ?>
    </div>

    <div class="section">
        <h3>Điều khoản & điều kiện</h3>
        <ul style="margin-left: 20px; font-size: 14px;">
            <li>Báo giá này chỉ có giá trị trong thời hạn nêu trên.</li>
            <li>Giá tour có thể thay đổi tùy theo thời điểm khởi hành và số lượng khách.</li>
            <li>Quý khách vui lòng đặt cọc ít nhất 30% giá trị tour để xác nhận.</li>
            <li>Điều kiện hủy tour và hoàn tiền sẽ được thông báo cụ thể khi đặt cọc.</li>
        </ul>
    </div>

    <div class="footer">
        <p><strong>Cảm ơn Quý khách đã quan tâm đến dịch vụ của chúng tôi!</strong></p>
        <p>Để được tư vấn chi tiết, vui lòng liên hệ hotline hoặc email của chúng tôi.</p>
    </div>
</body>

</html>