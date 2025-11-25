<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách đoàn</title>
    <style>
        @page {
            size: A4;
            margin: 15mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.4;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }

        .header h1 {
            font-size: 20pt;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 10pt;
            color: #666;
        }

        .info-section {
            margin: 20px 0;
            padding: 10px;
            background-color: #f8f9fa;
            border-left: 4px solid #3498db;
        }

        .info-section strong {
            display: inline-block;
            width: 150px;
        }

        .summary {
            display: flex;
            justify-content: space-around;
            margin: 20px 0;
            text-align: center;
        }

        .summary-item {
            padding: 10px 20px;
            background: #ecf0f1;
            border-radius: 5px;
        }

        .summary-item h3 {
            font-size: 24pt;
            color: #2980b9;
            margin-bottom: 5px;
        }

        .summary-item p {
            font-size: 9pt;
            color: #555;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        thead {
            background-color: #34495e;
            color: white;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            font-weight: bold;
            font-size: 10pt;
        }

        tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tbody tr:hover {
            background-color: #e8f4f8;
        }

        .badge {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 9pt;
            font-weight: bold;
        }

        .badge-success {
            background-color: #27ae60;
            color: white;
        }

        .badge-warning {
            background-color: #f39c12;
            color: white;
        }

        .badge-danger {
            background-color: #e74c3c;
            color: white;
        }

        .badge-info {
            background-color: #3498db;
            color: white;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 9pt;
            color: #888;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }

        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
        }

        .signature-box {
            text-align: center;
            width: 45%;
        }

        .signature-box strong {
            display: block;
            margin-bottom: 60px;
        }

        @media print {
            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
        }
    </style>
</head>

<body>
    <!-- Header -->
    <div class="header">
        <h1>DANH SÁCH ĐOÀN KHÁCH</h1>
        <p>In ngày: <?= date('d/m/Y H:i') ?></p>
    </div>

    <!-- Booking Information -->
    <?php if (isset($booking)): ?>
        <div class="info-section">
            <p><strong>Mã Booking:</strong> #<?= $booking['booking_id'] ?></p>
            <p><strong>Tour:</strong> <?= htmlspecialchars($booking['tour_name']) ?></p>
            <p><strong>Khách hàng:</strong>
                <?= htmlspecialchars($booking['customer_name'] ?? $booking['organization_name'] ?? 'N/A') ?></p>
            <p><strong>Ngày khởi hành:</strong>
                <?= isset($booking['tour_date']) ? date('d/m/Y', strtotime($booking['tour_date'])) : 'N/A' ?></p>
        </div>
    <?php endif; ?>

    <!-- Summary Statistics -->
    <div class="summary">
        <div class="summary-item">
            <h3><?= $summary['total_guests'] ?? 0 ?></h3>
            <p>Tổng khách</p>
        </div>
        <div class="summary-item">
            <h3><?= $summary['adult_count'] ?? 0 ?></h3>
            <p>Người lớn</p>
        </div>
        <div class="summary-item">
            <h3><?= $summary['child_count'] ?? 0 ?></h3>
            <p>Trẻ em</p>
        </div>
        <div class="summary-item">
            <h3><?= $summary['male_count'] ?? 0 ?></h3>
            <p>Nam</p>
        </div>
        <div class="summary-item">
            <h3><?= $summary['female_count'] ?? 0 ?></h3>
            <p>Nữ</p>
        </div>
    </div>

    <!-- Guest List Table -->
    <table>
        <thead>
            <tr>
                <th width="5%">STT</th>
                <th width="20%">Họ tên</th>
                <th width="12%">CMND/CCCD</th>
                <th width="8%">Giới tính</th>
                <th width="8%">Năm sinh</th>
                <th width="12%">SĐT</th>
                <th width="8%">Loại</th>
                <th width="10%">Phòng</th>
                <th width="12%">Ghi chú</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($guests)): ?>
                <?php $stt = 1; ?>
                <?php foreach ($guests as $guest): ?>
                    <tr>
                        <td><?= $stt++ ?></td>
                        <td><strong><?= htmlspecialchars($guest['full_name']) ?></strong></td>
                        <td><?= htmlspecialchars($guest['id_card'] ?? '') ?></td>
                        <td>
                            <?= $guest['gender'] == 'Male' ? 'Nam' : ($guest['gender'] == 'Female' ? 'Nữ' : 'Khác') ?>
                        </td>
                        <td><?= $guest['birth_date'] ? date('Y', strtotime($guest['birth_date'])) : '' ?></td>
                        <td><?= htmlspecialchars($guest['phone'] ?? '') ?></td>
                        <td>
                            <?php if ($guest['is_adult']): ?>
                                <span class="badge badge-info">NL</span>
                            <?php else: ?>
                                <span class="badge badge-warning">TE</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($guest['room_number']): ?>
                                <span class="badge badge-success"><?= htmlspecialchars($guest['room_number']) ?></span>
                            <?php else: ?>
                                <span class="badge badge-danger">-</span>
                            <?php endif; ?>
                        </td>
                        <td style="font-size: 9pt;">
                            <?= htmlspecialchars($guest['special_requirements'] ?? '') ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9" style="text-align: center; color: #888;">
                        Chưa có khách nào trong danh sách
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Signatures -->
    <div class="signatures">
        <div class="signature-box">
            <strong>HDV TRƯỞNG</strong>
            <p>(Ký, ghi rõ họ tên)</p>
        </div>
        <div class="signature-box">
            <strong>ĐIỀU HÀNH</strong>
            <p>(Ký, ghi rõ họ tên)</p>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Tài liệu này được tạo tự động bởi hệ thống quản lý tour</p>
        <p>© <?= date('Y') ?> - Bản quyền thuộc về Công ty</p>
    </div>

    <script>
        // Auto print when loaded
        window.onload = function () {
            window.print();
        };
    </script>
</body>

</html>