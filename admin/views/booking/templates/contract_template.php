<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Hợp Đồng - <?= $data['document_number'] ?></title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11pt;
            line-height: 1.6;
            color: #000;
        }

        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #000;
        }

        .header-left {
            text-align: left;
            width: 48%;
        }

        .header-right {
            text-align: right;
            width: 48%;
        }

        .company-name {
            font-weight: bold;
            font-size: 12pt;
            text-transform: uppercase;
        }

        .doc-title {
            font-size: 18pt;
            font-weight: bold;
            text-align: center;
            margin: 25px 0;
            text-transform: uppercase;
        }

        .doc-subtitle {
            text-align: center;
            font-style: italic;
            margin-bottom: 25px;
        }

        .section-title {
            font-weight: bold;
            font-size: 12pt;
            margin: 20px 0 10px 0;
            text-transform: uppercase;
        }

        .party-info {
            margin: 15px 0;
            padding: 10px;
            background: #f9f9f9;
            border-left: 3px solid #333;
        }

        .info-line {
            margin: 5px 0;
        }

        .article {
            margin: 15px 0;
        }

        .article-title {
            font-weight: bold;
            margin-bottom: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 8px;
            text-align: left;
        }

        th {
            background: #e0e0e0;
            font-weight: bold;
        }

        .signature-section {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
            page-break-inside: avoid;
        }

        .signature-box {
            text-align: center;
            width: 45%;
        }

        .signature-title {
            font-weight: bold;
            margin-bottom: 70px;
        }

        ul {
            margin: 10px 0;
            padding-left: 25px;
        }

        li {
            margin: 5px 0;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <div class="header">
        <div class="header-left">
            <div class="company-name"><?= $data['company_info']['name'] ?></div>
            <div><?= $data['company_info']['address'] ?></div>
            <div>Tel: <?= $data['company_info']['phone'] ?></div>
            <div>MST: <?= $data['company_info']['tax_code'] ?></div>
        </div>
        <div class="header-right">
            <div style="font-weight: bold;">CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM</div>
            <div style="font-weight: bold;">Độc lập - Tự do - Hạnh phúc</div>
            <div style="margin-top: 20px;">Ngày <?= date('d') ?> tháng <?= date('m') ?> năm <?= date('Y') ?></div>
        </div>
    </div>

    <!-- Document Title -->
    <div class="doc-title">HỢP ĐỒNG DU LỊCH</div>
    <div class="doc-subtitle">Số: <?= $data['document_number'] ?></div>

    <!-- Intro -->
    <p style="text-align: justify;">
        Hôm nay, ngày <?= date('d') ?> tháng <?= date('m') ?> năm <?= date('Y') ?>, tại
        <?= $data['company_info']['address'] ?>,
        chúng tôi gồm có:
    </p>

    <!-- Party A -->
    <div class="section-title">BÊN A: CÔNG TY DU LỊCH (Bên cung cấp dịch vụ)</div>
    <div class="party-info">
        <div class="info-line"><strong>Công ty:</strong> <?= $data['company_info']['name'] ?></div>
        <div class="info-line"><strong>Địa chỉ:</strong> <?= $data['company_info']['address'] ?></div>
        <div class="info-line"><strong>Điện thoại:</strong> <?= $data['company_info']['phone'] ?></div>
        <div class="info-line"><strong>Email:</strong> <?= $data['company_info']['email'] ?></div>
        <div class="info-line"><strong>Mã số thuế:</strong> <?= $data['company_info']['tax_code'] ?></div>
        <div class="info-line"><strong>Tài khoản:</strong> <?= $data['company_info']['bank_account'] ?> -
            <?= $data['company_info']['bank_name'] ?></div>
    </div>

    <!-- Party B -->
    <div class="section-title">BÊN B: KHÁCH HÀNG (Bên sử dụng dịch vụ)</div>
    <div class="party-info">
        <?php if (!empty($data['booking']['organization_name'])): ?>
            <div class="info-line"><strong>Tên công ty/tổ chức:</strong>
                <?= htmlspecialchars($data['booking']['organization_name']) ?></div>
        <?php else: ?>
            <div class="info-line"><strong>Họ và tên:</strong> <?= htmlspecialchars($data['booking']['customer_name']) ?>
            </div>
        <?php endif; ?>
        <div class="info-line"><strong>Địa chỉ:</strong>
            <?= htmlspecialchars($data['booking']['customer_address'] ?? 'Chưa cập nhật') ?></div>
        <div class="info-line"><strong>Điện thoại:</strong> <?= htmlspecialchars($data['booking']['customer_phone']) ?>
        </div>
        <div class="info-line"><strong>Email:</strong> <?= htmlspecialchars($data['booking']['customer_email']) ?></div>
        <?php if (!empty($data['booking']['organization_name'])): ?>
            <div class="info-line"><strong>Người đại diện:</strong>
                <?= htmlspecialchars($data['booking']['contact_name']) ?></div>
        <?php endif; ?>
    </div>

    <p style="text-align: justify;">
        Hai bên thống nhất ký kết hợp đồng cung cấp dịch vụ du lịch với các điều khoản sau:
    </p>

    <!-- Article 1: Tour Info -->
    <div class="article">
        <div class="article-title">ĐIỀU 1: NỘI DUNG DỊCH VỤ</div>
        <p><strong>1.1. Thông tin chương trình tour:</strong></p>
        <table>
            <tr>
                <td style="width: 35%;"><strong>Tên chương trình:</strong></td>
                <td><?= htmlspecialchars($data['tour']['tour_name']) ?></td>
            </tr>
            <tr>
                <td><strong>Mã tour:</strong></td>
                <td><?= htmlspecialchars($data['tour']['code']) ?></td>
            </tr>
            <tr>
                <td><strong>Thời gian:</strong></td>
                <td><?= $data['tour']['duration_days'] ?> ngày <?= ($data['tour']['duration_days'] - 1) ?> đêm</td>
            </tr>
            <tr>
                <td><strong>Ngày khởi hành:</strong></td>
                <td><?= date('d/m/Y', strtotime($data['booking']['departure_date'] ?? $data['booking']['tour_date'])) ?>
                </td>
            </tr>
            <tr>
                <td><strong>Số lượng khách:</strong></td>
                <td><?= $data['booking']['adult_count'] ?> người lớn
                    <?php if ($data['booking']['child_count'] > 0): ?>
                        + <?= $data['booking']['child_count'] ?> trẻ em
                    <?php endif; ?>
                </td>
            </tr>
        </table>

        <?php if (!empty($data['itineraries'])): ?>
            <p><strong>1.2. Chương trình chi tiết:</strong></p>
            <?php foreach ($data['itineraries'] as $day): ?>
                <div style="margin: 10px 0;">
                    <strong>Ngày <?= $day['day_number'] ?>:</strong> <?= htmlspecialchars($day['title']) ?><br>
                    <div style="margin-left: 15px;"><?= nl2br(htmlspecialchars($day['description'])) ?></div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Article 2: Price -->
    <div class="article">
        <div class="article-title">ĐIỀU 2: GIÁ TRỊ HỢP ĐỒNG</div>
        <p><strong>2.1. Tổng giá trị hợp đồng:</strong> <?= number_format($data['total']) ?> VNĐ (Bằng chữ:
            <?= $data['booking']['total_text'] ?? 'Chưa cập nhật' ?>)</p>

        <p><strong>2.2. Chi tiết giá:</strong></p>
        <table>
            <tr>
                <th style="width: 50%;">Khoản mục</th>
                <th style="width: 25%;">Số lượng</th>
                <th style="width: 25%; text-align: right;">Thành tiền</th>
            </tr>
            <?php if ($data['booking']['adult_count'] > 0): ?>
                <tr>
                    <td>Giá tour người lớn (<?= number_format($data['booking']['adult_price']) ?> VNĐ/người)</td>
                    <td><?= $data['booking']['adult_count'] ?> người</td>
                    <td style="text-align: right;">
                        <?= number_format($data['booking']['adult_count'] * $data['booking']['adult_price']) ?> VNĐ</td>
                </tr>
            <?php endif; ?>
            <?php if ($data['booking']['child_count'] > 0): ?>
                <tr>
                    <td>Giá tour trẻ em (<?= number_format($data['booking']['child_price']) ?> VNĐ/người)</td>
                    <td><?= $data['booking']['child_count'] ?> người</td>
                    <td style="text-align: right;">
                        <?= number_format($data['booking']['child_count'] * $data['booking']['child_price']) ?> VNĐ</td>
                </tr>
            <?php endif; ?>
            <tr style="font-weight: bold;">
                <td colspan="2">Tổng cộng (bao gồm VAT 10%)</td>
                <td style="text-align: right;"><?= number_format($data['total']) ?> VNĐ</td>
            </tr>
        </table>

        <p><strong>2.3. Giá trên bao gồm:</strong></p>
        <ul>
            <li>Vận chuyển theo chương trình (xe du lịch có máy lạnh)</li>
            <li>Khách sạn theo tiêu chuẩn ghi trong chương trình (phòng 2-3 người)</li>
            <li>Các bữa ăn theo chương trình</li>
            <li>Vé tham quan các điểm trong chương trình</li>
            <li>Hướng dẫn viên nhiệt tình, có kinh nghiệm</li>
            <li>Bảo hiểm du lịch theo quy định</li>
        </ul>

        <p><strong>2.4. Giá chưa bao gồm:</strong></p>
        <ul>
            <li>Các chi phí cá nhân: giặt ủi, điện thoại, đồ uống có cồn...</li>
            <li>Phụ thu phòng đơn (nếu có): <?= number_format(500000) ?> VNĐ/đêm</li>
            <li>Chi phí ngoài chương trình</li>
        </ul>
    </div>

    <!-- Article 3: Payment Terms -->
    <div class="article">
        <div class="article-title">ĐIỀU 3: PHƯƠNG THỨC THANH TOÁN</div>
        <?= $data['terms_conditions']['payment_terms'] ?>
    </div>

    <!-- Article 4: Cancellation Policy -->
    <div class="article">
        <div class="article-title">ĐIỀU 4: ĐIỀU KIỆN HỦY TOUR</div>
        <p><strong>Bên B hủy tour:</strong></p>
        <?= $data['terms_conditions']['cancellation_policy'] ?>
    </div>

    <!-- Article 5: Responsibilities -->
    <div class="article">
        <div class="article-title">ĐIỀU 5: TRÁCH NHIỆM CÁC BÊN</div>
        <?= $data['terms_conditions']['responsibilities'] ?>
    </div>

    <!-- Article 6: Force Majeure -->
    <div class="article">
        <div class="article-title">ĐIỀU 6: ĐIỀU KHOẢN BẤT KHẢ KHÁNG</div>
        <p><?= $data['terms_conditions']['force_majeure'] ?></p>
    </div>

    <!-- Article 7: Dispute Resolution -->
    <div class="article">
        <div class="article-title">ĐIỀU 7: GIẢI QUYẾT TRANH CHẤP</div>
        <p>Mọi tranh chấp phát sinh từ hợp đồng này sẽ được hai bên cùng nhau thương lượng giải quyết trên tinh thần hợp
            tác, thiện chí. Trường hợp không thương lượng được, tranh chấp sẽ được đưa ra Tòa án nhân dân có thẩm quyền
            để giải quyết theo quy định của pháp luật Việt Nam.</p>
    </div>

    <!-- Article 8: Effectiveness -->
    <div class="article">
        <div class="article-title">ĐIỀU 8: HIỆU LỰC HỢP ĐỒNG</div>
        <p>Hợp đồng có hiệu lực kể từ ngày ký và kết thúc sau khi hai bên hoàn thành đầy đủ nghĩa vụ. Hợp đồng được lập
            thành 02 (hai) bản có giá trị pháp lý như nhau, mỗi bên giữ 01 bản.</p>
    </div>

    <!-- Signatures -->
    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-title">ĐẠI DIỆN BÊN B<br>KHÁCH HÀNG</div>
            <div>(Ký, ghi rõ họ tên)</div>
        </div>
        <div class="signature-box">
            <div class="signature-title">ĐẠI DIỆN BÊN A<br>CÔNG TY DU LỊCH</div>
            <div>(Ký, đóng dấu)</div>
        </div>
    </div>
</body>

</html>