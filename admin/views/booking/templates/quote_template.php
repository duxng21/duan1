<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Báo Giá - <?= $data['document_number'] ?></title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #2196F3;
        }

        .company-name {
            font-size: 18pt;
            font-weight: bold;
            color: #2196F3;
            text-transform: uppercase;
        }

        .company-info {
            font-size: 9pt;
            color: #666;
            margin-top: 5px;
        }

        .doc-title {
            font-size: 20pt;
            font-weight: bold;
            color: #2196F3;
            text-align: center;
            margin: 30px 0;
            text-transform: uppercase;
        }

        .doc-number {
            text-align: center;
            font-size: 11pt;
            color: #666;
            margin-bottom: 20px;
        }

        .customer-info {
            margin: 20px 0;
            background: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
        }

        .info-row {
            margin-bottom: 8px;
        }

        .info-label {
            font-weight: bold;
            display: inline-block;
            width: 150px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        th {
            background: #2196F3;
            color: white;
            padding: 10px;
            text-align: left;
            font-weight: bold;
        }

        td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .total-section {
            margin-top: 20px;
            float: right;
            width: 300px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #ddd;
        }

        .total-row.grand-total {
            font-size: 14pt;
            font-weight: bold;
            color: #2196F3;
            border-bottom: 3px double #2196F3;
            margin-top: 10px;
        }

        .notes {
            clear: both;
            margin-top: 30px;
            padding: 15px;
            background: #fff9e6;
            border-left: 4px solid #ffc107;
        }

        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 9pt;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }

        .signature-section {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }

        .signature-box {
            text-align: center;
            width: 45%;
        }

        .signature-title {
            font-weight: bold;
            margin-bottom: 60px;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <div class="header">
        <div class="company-name"><?= $data['company_info']['name'] ?></div>
        <div class="company-info">
            <?= $data['company_info']['address'] ?><br>
            Tel: <?= $data['company_info']['phone'] ?> | Hotline: <?= $data['company_info']['hotline'] ?><br>
            Email: <?= $data['company_info']['email'] ?> | Website: <?= $data['company_info']['website'] ?>
        </div>
    </div>

    <!-- Document Title -->
    <div class="doc-title">BÁO GIÁ TOUR DU LỊCH</div>
    <div class="doc-number">Số: <?= $data['document_number'] ?> - Ngày: <?= $data['document_date'] ?></div>

    <!-- Customer Info -->
    <div class="customer-info">
        <div class="info-row">
            <span class="info-label">Kính gửi:</span>
            <strong><?= htmlspecialchars($data['booking']['customer_name'] ?? $data['booking']['organization_name']) ?></strong>
        </div>
        <?php if (!empty($data['booking']['organization_name'])): ?>
            <div class="info-row">
                <span class="info-label">Công ty/Tổ chức:</span>
                <?= htmlspecialchars($data['booking']['organization_name']) ?>
            </div>
        <?php endif; ?>
        <div class="info-row">
            <span class="info-label">Người liên hệ:</span>
            <?= htmlspecialchars($data['booking']['contact_name'] ?? $data['booking']['customer_name']) ?>
        </div>
        <div class="info-row">
            <span class="info-label">Điện thoại:</span>
            <?= htmlspecialchars($data['booking']['customer_phone']) ?>
        </div>
        <div class="info-row">
            <span class="info-label">Email:</span>
            <?= htmlspecialchars($data['booking']['customer_email']) ?>
        </div>
    </div>

    <!-- Tour Info -->
    <p>Trân trọng gửi Quý khách báo giá tour du lịch như sau:</p>

    <div style="margin: 20px 0; padding: 15px; background: #e3f2fd; border-radius: 5px;">
        <div class="info-row">
            <span class="info-label">Tên tour:</span>
            <strong><?= htmlspecialchars($data['tour']['tour_name']) ?></strong>
        </div>
        <div class="info-row">
            <span class="info-label">Mã tour:</span>
            <?= htmlspecialchars($data['tour']['code']) ?>
        </div>
        <div class="info-row">
            <span class="info-label">Thời gian:</span>
            <?= $data['tour']['duration_days'] ?> ngày <?= ($data['tour']['duration_days'] - 1) ?> đêm
        </div>
        <div class="info-row">
            <span class="info-label">Ngày khởi hành:</span>
            <?= date('d/m/Y', strtotime($data['booking']['departure_date'] ?? $data['booking']['tour_date'])) ?>
        </div>
        <div class="info-row">
            <span class="info-label">Số lượng khách:</span>
            <?= $data['booking']['adult_count'] ?> người lớn
            <?php if ($data['booking']['child_count'] > 0): ?>
                + <?= $data['booking']['child_count'] ?> trẻ em
            <?php endif; ?>
        </div>
    </div>

    <!-- Price Table -->
    <table>
        <thead>
            <tr>
                <th style="width: 50px;" class="text-center">STT</th>
                <th>Nội dung</th>
                <th style="width: 80px;" class="text-center">Đơn vị</th>
                <th style="width: 80px;" class="text-right">Số lượng</th>
                <th style="width: 120px;" class="text-right">Đơn giá (VNĐ)</th>
                <th style="width: 120px;" class="text-right">Thành tiền (VNĐ)</th>
            </tr>
        </thead>
        <tbody>
            <!-- Adult Price -->
            <?php if ($data['booking']['adult_count'] > 0): ?>
                <tr>
                    <td class="text-center">1</td>
                    <td>Giá tour cho người lớn</td>
                    <td class="text-center">Người</td>
                    <td class="text-right"><?= $data['booking']['adult_count'] ?></td>
                    <td class="text-right"><?= number_format($data['booking']['adult_price']) ?></td>
                    <td class="text-right">
                        <?= number_format($data['booking']['adult_count'] * $data['booking']['adult_price']) ?></td>
                </tr>
            <?php endif; ?>

            <!-- Child Price -->
            <?php if ($data['booking']['child_count'] > 0): ?>
                <tr>
                    <td class="text-center">2</td>
                    <td>Giá tour cho trẻ em</td>
                    <td class="text-center">Người</td>
                    <td class="text-right"><?= $data['booking']['child_count'] ?></td>
                    <td class="text-right"><?= number_format($data['booking']['child_price']) ?></td>
                    <td class="text-right">
                        <?= number_format($data['booking']['child_count'] * $data['booking']['child_price']) ?></td>
                </tr>
            <?php endif; ?>

            <!-- Additional Services -->
            <?php if (!empty($data['services'])):
                $stt = ($data['booking']['child_count'] > 0) ? 3 : 2;
                foreach ($data['services'] as $service): ?>
                    <tr>
                        <td class="text-center"><?= $stt++ ?></td>
                        <td><?= htmlspecialchars($service['service_name']) ?></td>
                        <td class="text-center">Phần</td>
                        <td class="text-right"><?= $service['quantity'] ?></td>
                        <td class="text-right"><?= number_format($service['unit_price']) ?></td>
                        <td class="text-right"><?= number_format($service['quantity'] * $service['unit_price']) ?></td>
                    </tr>
                <?php endforeach; endif; ?>
        </tbody>
    </table>

    <!-- Total Section -->
    <div class="total-section">
        <div class="total-row">
            <span>Tạm tính:</span>
            <strong><?= number_format($data['subtotal']) ?> VNĐ</strong>
        </div>
        <div class="total-row">
            <span>VAT (<?= $data['vat_rate'] ?>%):</span>
            <strong><?= number_format($data['vat_amount']) ?> VNĐ</strong>
        </div>
        <div class="total-row grand-total">
            <span>TỔNG CỘNG:</span>
            <span><?= number_format($data['total']) ?> VNĐ</span>
        </div>
    </div>

    <!-- Notes -->
    <div class="notes" style="clear: both;">
        <strong>Ghi chú:</strong><br>
        - Giá trên đã bao gồm: Vận chuyển, khách sạn, ăn uống, vé tham quan, hướng dẫn viên, bảo hiểm du lịch<br>
        - Giá chưa bao gồm: Chi phí cá nhân, đồ uống có cồn, phụ thu phòng đơn (nếu có)<br>
        - Báo giá có hiệu lực trong 7 ngày kể từ ngày phát hành<br>
        - Vui lòng đặt cọc 30% để giữ chỗ, thanh toán còn lại trước 7 ngày khởi hành
    </div>

    <!-- Signature -->
    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-title">KHÁCH HÀNG</div>
            <div>(Ký, ghi rõ họ tên)</div>
        </div>
        <div class="signature-box">
            <div class="signature-title">ĐẠI DIỆN CÔNG TY</div>
            <div>(Ký, đóng dấu)</div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        Cảm ơn Quý khách đã tin tưởng và lựa chọn dịch vụ của <?= $data['company_info']['name'] ?>!<br>
        Mọi thắc mắc xin vui lòng liên hệ: <?= $data['company_info']['hotline'] ?>
    </div>
</body>

</html>