<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh Sách Yêu Cầu Đặc Biệt - <?= htmlspecialchars($schedule['tour_name'] ?? '') ?></title>
    <style>
        @media print {
            .no-print { display: none !important; }
            @page { margin: 1cm; }
        }
        
        body {
            font-family: 'Times New Roman', serif;
            font-size: 12px;
            line-height: 1.4;
            color: #000;
            margin: 0;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        
        .company-name {
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .report-title {
            font-size: 14px;
            font-weight: bold;
            margin: 10px 0;
        }
        
        .tour-info {
            margin: 15px 0;
            border: 1px solid #000;
            padding: 10px;
        }
        
        .tour-info table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .tour-info td {
            padding: 5px;
            border: none;
            vertical-align: top;
        }
        
        .label {
            font-weight: bold;
            width: 25%;
        }
        
        .stats-section {
            margin: 20px 0;
            text-align: center;
        }
        
        .stats-grid {
            display: table;
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        
        .stats-row {
            display: table-row;
        }
        
        .stats-cell {
            display: table-cell;
            width: 25%;
            padding: 8px;
            text-align: center;
            border: 1px solid #000;
            font-weight: bold;
        }
        
        .guest-list {
            margin-top: 20px;
        }
        
        .guest-item {
            margin-bottom: 15px;
            border: 1px solid #000;
            padding: 10px;
            page-break-inside: avoid;
        }
        
        .guest-item.high-priority {
            background-color: #ffe6e6;
        }
        
        .guest-item.medium-priority {
            background-color: #fff4e6;
        }
        
        .guest-name {
            font-weight: bold;
            font-size: 13px;
        }
        
        .priority-badge {
            float: right;
            font-size: 10px;
            padding: 2px 6px;
            border: 1px solid #000;
            text-transform: uppercase;
        }
        
        .guest-details {
            margin: 5px 0;
            font-size: 11px;
        }
        
        .requirements {
            margin-top: 8px;
            padding: 8px;
            border: 1px dashed #666;
            background-color: #f9f9f9;
        }
        
        .requirement-item {
            margin-bottom: 5px;
            font-size: 11px;
        }
        
        .footer {
            margin-top: 30px;
            border-top: 1px solid #000;
            padding-top: 10px;
            text-align: center;
            font-size: 10px;
        }
        
        .signature-area {
            margin-top: 30px;
            display: table;
            width: 100%;
        }
        
        .signature-column {
            display: table-cell;
            width: 50%;
            text-align: center;
        }
        
        .signature-title {
            font-weight: bold;
            margin-bottom: 40px;
        }
        
        .signature-line {
            border-top: 1px solid #000;
            width: 150px;
            margin: 0 auto;
            padding-top: 5px;
            font-size: 10px;
        }
        
        .checklist-section {
            margin-top: 20px;
            page-break-before: always;
        }
        
        .checklist-item {
            margin: 8px 0;
            padding: 5px;
            border-bottom: 1px dotted #ccc;
        }
        
        .checkbox {
            display: inline-block;
            width: 12px;
            height: 12px;
            border: 1px solid #000;
            margin-right: 8px;
            vertical-align: middle;
        }
        
        .no-requirements {
            text-align: center;
            padding: 40px;
            font-style: italic;
            background-color: #f5f5f5;
            border: 2px dashed #ccc;
        }
        
        h3 {
            font-size: 14px;
            margin: 15px 0 10px 0;
            text-transform: uppercase;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">CÔNG TY DU LỊCH ABC TRAVEL</div>
        <div style="font-size: 10px; margin: 5px 0;">
            Địa chỉ: 123 Đường ABC, Quận 1, TP.HCM | ☎ 028.1234.5678 | ✉ info@abctravel.com
        </div>
        <div class="report-title">DANH SÁCH KHÁCH CÓ YÊU CẦU ĐẶC BIỆT</div>
        <div style="font-size: 11px; font-style: italic;">
            In ngày: <?= date('d/m/Y H:i') ?>
        </div>
    </div>

    <div class="tour-info">
        <table>
            <tr>
                <td class="label">Tên tour:</td>
                <td><?= htmlspecialchars($schedule['tour_name'] ?? '') ?></td>
                <td class="label">Mã tour:</td>
                <td><?= htmlspecialchars($schedule['code'] ?? '') ?></td>
            </tr>
            <tr>
                <td class="label">Ngày khởi hành:</td>
                <td><?= isset($schedule['departure_date']) ? date('d/m/Y', strtotime($schedule['departure_date'])) : '' ?></td>
                <td class="label">Số ngày:</td>
                <td><?= $schedule['duration_days'] ?? '' ?> ngày</td>
            </tr>
            <tr>
                <td class="label">Hướng dẫn viên:</td>
                <td><?= htmlspecialchars($schedule['guide_names'] ?? 'Chưa phân công') ?></td>
                <td class="label">Tổng khách:</td>
                <td><?= $schedule['total_guests'] ?? 0 ?> người</td>
            </tr>
        </table>
    </div>

    <div class="stats-section">
        <h3>THỐNG KÊ NHANH</h3>
        <div class="stats-grid">
            <div class="stats-row">
                <div class="stats-cell">
                    <?= $statistics['total_notes'] ?? 0 ?><br>
                    <small>Tổng yêu cầu</small>
                </div>
                <div class="stats-cell">
                    <?= $statistics['high_priority'] ?? 0 ?><br>
                    <small>Ưu tiên cao</small>
                </div>
                <div class="stats-cell">
                    <?= $statistics['pending'] ?? 0 ?><br>
                    <small>Chờ xử lý</small>
                </div>
                <div class="stats-cell">
                    <?= count($guests) ?><br>
                    <small>Khách có yêu cầu</small>
                </div>
            </div>
        </div>
    </div>

    <div class="guest-list">
        <h3>DANH SÁCH CHI TIẾT</h3>
        
        <?php if (empty($guests)): ?>
            <div class="no-requirements">
                <h4>✅ KHÔNG CÓ YÊU CẦU ĐẶC BIỆT</h4>
                <p>Lịch khởi hành này không có khách nào yêu cầu dịch vụ đặc biệt.</p>
                <p>Có thể tiến hành tour theo kế hoạch tiêu chuẩn.</p>
            </div>
        <?php else: ?>
            <?php 
            // Sort by priority
            usort($guests, function($a, $b) {
                return $b['max_priority'] <=> $a['max_priority'];
            });
            
            $counter = 1;
            foreach ($guests as $guest): 
                $priority_class = '';
                $priority_text = '';
                
                switch ($guest['max_priority']) {
                    case 3: 
                        $priority_class = 'high-priority'; 
                        $priority_text = 'CAO';
                        break;
                    case 2: 
                        $priority_class = 'medium-priority'; 
                        $priority_text = 'TRUNG BÌNH';
                        break;
                    default: 
                        $priority_class = 'low-priority'; 
                        $priority_text = 'THẤP';
                        break;
                }
            ?>
                <div class="guest-item <?= $priority_class ?>">
                    <div class="guest-name">
                        <?= $counter ?>. <?= htmlspecialchars($guest['full_name']) ?>
                        <span class="priority-badge">ƯU TIÊN <?= $priority_text ?></span>
                    </div>
                    
                    <div class="guest-details">
                        <strong>☎ Điện thoại:</strong> <?= htmlspecialchars($guest['phone']) ?>
                        <?php if ($guest['room_number']): ?>
                            | <strong>🏠 Phòng:</strong> <?= htmlspecialchars($guest['room_number']) ?>
                        <?php endif; ?>
                        | <strong>📋 Số yêu cầu:</strong> <?= $guest['note_count'] ?>
                    </div>
                    
                    <div class="requirements">
                        <strong>YÊU CẦU ĐẶC BIỆT:</strong>
                        <?php 
                        $requirements = explode(' | ', $guest['all_requirements']);
                        foreach ($requirements as $req): 
                        ?>
                            <div class="requirement-item">• <?= htmlspecialchars($req) ?></div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php 
            $counter++;
            endforeach; 
            ?>
        <?php endif; ?>
    </div>

    <?php if (!empty($guests)): ?>
    <div class="checklist-section">
        <h3>CHECKLIST CHUẨN BỊ</h3>
        
        <div style="border: 1px solid #000; padding: 10px; margin: 10px 0;">
            <h4>🍽️ BỘ PHẬN ẨM THỰC:</h4>
            <div class="checklist-item">
                <span class="checkbox"></span> Chuẩn bị món ăn chay cho khách yêu cầu
            </div>
            <div class="checklist-item">
                <span class="checkbox"></span> Kiểm tra nguyên liệu tránh dị ứng thực phẩm
            </div>
            <div class="checklist-item">
                <span class="checkbox"></span> Chuẩn bị đồ ăn đặc biệt cho khách tiểu đường
            </div>
            <div class="checklist-item">
                <span class="checkbox"></span> Thông báo nhà hàng về các yêu cầu đặc biệt
            </div>
        </div>

        <div style="border: 1px solid #000; padding: 10px; margin: 10px 0;">
            <h4>🚌 BỘ PHẬN VẬN CHUYỂN:</h4>
            <div class="checklist-item">
                <span class="checkbox"></span> Chuẩn bị xe có thang máng cho xe lăn (nếu cần)
            </div>
            <div class="checklist-item">
                <span class="checkbox"></span> Sắp xếp chỗ ngồi phù hợp cho khách khuyết tật
            </div>
            <div class="checklist-item">
                <span class="checkbox"></span> Chuẩn bị túi thuốc cấp cứu và thuốc cần thiết
            </div>
        </div>

        <div style="border: 1px solid #000; padding: 10px; margin: 10px 0;">
            <h4>🏨 BỘ PHẬN LƯU TRÚ:</h4>
            <div class="checklist-item">
                <span class="checkbox"></span> Thông báo khách sạn về yêu cầu đặc biệt
            </div>
            <div class="checklist-item">
                <span class="checkbox"></span> Xin phòng tầng thấp cho khách sợ cao
            </div>
            <div class="checklist-item">
                <span class="checkbox"></span> Phòng gần thang máy cho khách khó di chuyển
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="signature-area">
        <div class="signature-column">
            <div class="signature-title">NGƯỜI LẬP DANH SÁCH</div>
            <div class="signature-line">
                <?= htmlspecialchars($_SESSION['full_name'] ?? 'Admin') ?>
            </div>
        </div>
        <div class="signature-column">
            <div class="signature-title">HDV XÁC NHẬN</div>
            <div class="signature-line">
                &nbsp;
            </div>
        </div>
    </div>

    <div class="footer">
        <strong>GHI CHÚ QUAN TRỌNG:</strong><br>
        • HDV cần đọc kỹ và xác nhận hiểu rõ tất cả yêu cầu đặc biệt trước khi khởi hành<br>
        • Trong tour, cần cập nhật tình trạng xử lý vào hệ thống để theo dõi<br>
        • Thu thập phản hồi từ khách về chất lượng phục vụ sau khi kết thúc tour<br>
        <em style="margin-top: 10px; display: block;">
            Tài liệu được tạo tự động từ hệ thống lúc <?= date('d/m/Y H:i:s') ?>
        </em>
    </div>

    <div class="no-print" style="position: fixed; top: 10px; right: 10px;">
        <button onclick="window.print()" style="padding: 10px 15px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer;">
            🖨️ In
        </button>
        <button onclick="window.close()" style="padding: 10px 15px; background: #6c757d; color: white; border: none; border-radius: 5px; cursor: pointer; margin-left: 5px;">
            ✖️ Đóng
        </button>
    </div>

    <script>
        // Auto print when page loads
        window.addEventListener('load', function() {
            setTimeout(function() {
                window.print();
            }, 500);
        });
    </script>
</body>
</html>