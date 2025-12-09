<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Hóa Đơn - <?= $data['document_number'] ?></title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            color: #000;
        }

        .invoice-header {
            border: 2px solid #000;
            padding: 10px;
            margin-bottom: 10px;
        }

        .invoice-top {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .company-info {
            width: 55%;
        }

        .invoice-type {
            width: 40%;
            text-align: right;
        }

        .company-name {
            font-weight: bold;
            font-size: 13pt;
            text-transform: uppercase;
        }

        .invoice-title {
            font-size: 16pt;
            font-weight: bold;
            text-align: center;
            margin: 15px 0;
        }

        .red-text {
            color: #d00;
        }

        .invoice-info {
            text-align: center;
            margin-bottom: 15px;
        }

        .customer-section {
            margin: 15px 0;
        }

        .info-row {
            display: flex;
            margin: 5px 0;
        }

        .info-label {
            width: 120px;
            font-weight: normal;
        }

        .info-value {
            flex: 1;
            border-bottom: 1px dotted #666;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            border: 1px solid #000;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
        }

        th {
            background: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .total-section {
            margin: 15px 0;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin: 8px 0;
        }

        .total-label {
            width: 70%;
            text-align: right;
            font-weight: bold;
        }

        .total-value {
            width: 28%;
            text-align: right;
            border-bottom: 1px solid #000;
        }

        .grand-total {
            font-size: 12pt;
            font-weight: bold;
        }

        .amount-text {
            margin: 15px 0;
            font-style: italic;
        }

        .signature-section {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
        }

        .signature-box {
            text-align: center;
            width: 30%;
        }

        .signature-title {
            font-weight: bold;
            margin-bottom: 60px;
        }

        .notes-section {
            margin-top: 20px;
            font-size: 9pt;
            border-top: 1px solid #999;
            padding-top: 10px;
        }
    </style>
</head>

<body>
    <!-- Invoice Header -->
    <div class="invoice-header">
        <div class="invoice-top">
            <div class="company-info">
                <div class="company-name"><?= $data['company_info']['name'] ?></div>
                <div><?= $data['company_info']['address'] ?></div>
                <div>Tel: <?= $data['company_info']['phone'] ?> | Fax: .....................</div>
                <div>MST: <?= $data['company_info']['tax_code'] ?></div>
            </div>
            <div class="invoice-type">
                <div style="font-weight: bold;">Mẫu số: 01GTKT3/001</div>
                <div style="font-weight: bold;">Ký hiệu: <?= $data['invoice_series'] ?></div>
                <div style="font-weight: bold; margin-top: 5px;">Số: <?= $data['document_number'] ?></div>
            </div>
        </div>
    </div>

    <!-- Invoice Title -->
    <div class="invoice-title">
        <div class="red-text">HÓA ĐƠN GIÁ TRỊ GIA TĂNG</div>
        <div style="font-size: 10pt; font-weight: normal; margin-top: 5px;">(VAT INVOICE)</div>
    </div>

    <!-- Invoice Date -->
    <div class="invoice-info">
        Ngày (Date) <?= date('d') ?> tháng (month) <?= date('m') ?> năm (year) <?= date('Y') ?>
    </div>

    <!-- Customer Info -->
    <div class="customer-section">
        <div class="info-row">
            <div class="info-label">Đơn vị mua hàng:</div>
            <div class="info-value">
                <?= htmlspecialchars($data['booking']['organization_name'] ?? $data['booking']['customer_name']) ?>
            </div>
        </div>
        <div class="info-row">
            <div class="info-label">Tên người mua hàng:</div>
            <div class="info-value"><?= htmlspecialchars($data['booking']['customer_name']) ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">Địa chỉ (Address):</div>
            <div class="info-value"><?= htmlspecialchars($data['booking']['customer_address'] ?? 'Chưa cập nhật') ?>
            </div>
        </div>
        <div class="info-row">
            <div class="info-label">Mã số thuế (Tax code):</div>
            <div class="info-value"><?= htmlspecialchars($data['customer_tax_info']['tax_code'] ?? '') ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">Số điện thoại (Tel):</div>
            <div class="info-value"><?= htmlspecialchars($data['booking']['customer_phone']) ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">Hình thức thanh toán:</div>
            <div class="info-value">Chuyển khoản / Tiền mặt</div>
        </div>
    </div>

    <!-- Items Table -->
    <table>
        <thead>
            <tr>
                <th style="width: 40px;">STT<br>(No.)</th>
                <th>Tên hàng hóa, dịch vụ<br>(Description)</th>
                <th style="width: 60px;">Đơn vị tính<br>(Unit)</th>
                <th style="width: 60px;">Số lượng<br>(Quantity)</th>
                <th style="width: 100px;">Đơn giá<br>(Unit price)</th>
                <th style="width: 110px;">Thành tiền<br>(Amount)</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data['items'] as $item): ?>
                <tr>
                    <td class="text-center"><?= $item['stt'] ?></td>
                    <td><?= htmlspecialchars($item['description']) ?></td>
                    <td class="text-center"><?= $item['unit'] ?></td>
                    <td class="text-center"><?= $item['quantity'] ?></td>
                    <td class="text-right"><?= number_format($item['price']) ?></td>
                    <td class="text-right"><?= number_format($item['amount']) ?></td>
                </tr>
            <?php endforeach; ?>

            <!-- Empty rows for standard invoice format -->
            <?php for ($i = count($data['items']); $i < 5; $i++): ?>
                <tr>
                    <td class="text-center"><?= $i + 1 ?></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
            <?php endfor; ?>
        </tbody>
    </table>

    <!-- Total Section -->
    <div class="total-section">
        <div class="total-row">
            <div class="total-label">Cộng tiền hàng (Total):</div>
            <div class="total-value"><?= number_format($data['subtotal']) ?></div>
        </div>
        <div class="total-row">
            <div class="total-label">Thuế suất GTGT (VAT rate): <?= $data['vat_rate'] ?>%</div>
            <div class="total-value"><?= number_format($data['vat_amount']) ?></div>
        </div>
        <div class="total-row grand-total">
            <div class="total-label">Tổng cộng tiền thanh toán (Grand total):</div>
            <div class="total-value"><?= number_format($data['total']) ?></div>
        </div>
    </div>

    <!-- Amount in Text -->
    <div class="amount-text">
        <strong>Số tiền viết bằng chữ (Total amount in words):</strong><br>
        <?= $data['total_text'] ?>
    </div>

    <!-- Signatures -->
    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-title">Người mua hàng<br>(Buyer)</div>
            <div style="font-size: 8pt;">(Ký, ghi rõ họ tên)</div>
        </div>
        <div class="signature-box">
            <div class="signature-title">Người bán hàng<br>(Seller)</div>
            <div style="font-size: 8pt;">(Ký, ghi rõ họ tên)</div>
        </div>
        <div class="signature-box">
            <div class="signature-title">Thủ trưởng đơn vị<br>(Director)</div>
            <div style="font-size: 8pt;">(Ký, đóng dấu)</div>
        </div>
    </div>

    <!-- Notes -->
    <div class="notes-section">
        <strong>Ghi chú:</strong><br>
        - Hóa đơn này là bằng chứng xác nhận giao dịch mua bán hàng hóa, dịch vụ<br>
        - Hóa đơn chỉ có giá trị khi có chữ ký và đóng dấu của đơn vị bán hàng<br>
        - Thông tin tài khoản: <?= $data['company_info']['bank_account'] ?> -
        <?= $data['company_info']['bank_name'] ?><br>
        - Cần bảo quản hóa đơn để đối chiếu khi cần thiết
    </div>

    <!-- Cut line -->
    <div style="margin-top: 20px; text-align: center; border-top: 1px dashed #999; padding-top: 10px; font-size: 9pt;">
        ✂ - - - - - - - - - - - - - - - - - - - - Đường cắt phần liên (Cut here) - - - - - - - - - - - - - - - - - - - -
        ✂
    </div>
</body>

</html>