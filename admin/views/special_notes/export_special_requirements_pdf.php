<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Báo Cáo Yêu Cầu Đặc Biệt - <?= htmlspecialchars($schedule['tour_name'] ?? '') ?></title>
    <style>
        @page {
            margin: 20mm;
            size: A4;
        }
        
        body {
            font-family: 'Times New Roman', serif;
            font-size: 12px;
            line-height: 1.4;
            color: #000;
            margin: 0;
            padding: 0;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
        }
        
        .company-name {
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        
        .company-info {
            font-size: 10px;
            margin-bottom: 10px;
        }
        
        .report-title {
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 15px;
            margin-bottom: 5px;
        }
        
        .report-subtitle {
            font-size: 12px;
            font-style: italic;
        }
        
        .tour-info {
            margin: 20px 0;
            background-color: #f8f9fa;
            padding: 15px;
            border: 1px solid #ddd;
        }
        
        .tour-info table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .tour-info td {
            padding: 5px;
            border: none;
        }
        
        .tour-info .label {
            font-weight: bold;
            width: 30%;
        }
        
        .statistics {
            margin: 20px 0;
        }
        
        .stats-grid {
            display: table;
            width: 100%;
            border-collapse: collapse;
        }
        
        .stats-row {
            display: table-row;
        }
        
        .stats-cell {
            display: table-cell;
            width: 25%;
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
            background-color: #f8f9fa;
        }
        
        .stats-number {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
        }
        
        .stats-label {
            font-size: 10px;
            text-transform: uppercase;
            margin-top: 5px;
        }
        
        .guest-list {
            margin-top: 30px;
        }
        
        .guest-item {
            margin-bottom: 20px;
            border: 1px solid #ddd;
            padding: 15px;
            page-break-inside: avoid;
        }
        
        .guest-item.high-priority {
            border-left: 5px solid #dc3545;
            background-color: #fff5f5;
        }
        
        .guest-item.medium-priority {
            border-left: 5px solid #ffc107;
            background-color: #fff8f0;
        }
        
        .guest-item.low-priority {
            border-left: 5px solid #007bff;
            background-color: #f0f8ff;
        }
        
        .guest-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            font-weight: bold;
        }
        
        .guest-name {
            font-size: 14px;
            color: #000;
        }
        
        .priority-badge {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            text-transform: uppercase;
            color: white;
        }
        
        .priority-high { background-color: #dc3545; }
        .priority-medium { background-color: #ffc107; color: #000; }
        .priority-low { background-color: #007bff; }
        
        .guest-details {
            font-size: 11px;
            color: #666;
            margin-bottom: 10px;
        }
        
        .requirements {
            background-color: #fff;
            padding: 10px;
            border-radius: 3px;
            border: 1px solid #eee;
        }
        
        .requirement-item {
            margin-bottom: 8px;
            padding: 5px;
            border-left: 3px solid #007bff;
            background-color: #f8f9fa;
        }
        
        .requirement-type {
            font-weight: bold;
            margin-bottom: 3px;
        }
        
        .footer {
            margin-top: 40px;
            border-top: 1px solid #ddd;
            padding-top: 15px;
            font-size: 10px;
        }
        
        .signature-section {
            margin-top: 40px;
            display: table;
            width: 100%;
        }
        
        .signature-column {
            display: table-cell;
            width: 50%;
            text-align: center;
            vertical-align: top;
        }
        
        .signature-title {
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .signature-subtitle {
            font-size: 10px;
            color: #666;
            margin-bottom: 40px;
        }
        
        .signature-line {
            border-top: 1px solid #000;
            width: 200px;
            margin: 0 auto;
            padding-top: 5px;
            font-size: 10px;
        }
        
        .no-requirements {
            text-align: center;
            color: #666;
            font-style: italic;
            padding: 40px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        
        @media print {
            .no-print { display: none !important; }
        }
        
        .print-date {
            position: absolute;
            top: 10px;
            right: 0;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="print-date">
        In ngày: <?= date('d/m/Y H:i') ?>
    </div>

    <div class="header">
        <div class="company-name">CÔNG TY DU LỊCH ABC TRAVEL</div>
        <div class="company-info">
            Địa chỉ: 123 Đường ABC, Quận 1, TP.HCM | Điện thoại: 028.1234.5678 | Email: info@abctravel.com
        </div>
        <div class="report-title">BÁO CÁO YÊU CẦU ĐẶC BIỆT</div>
        <div class="report-subtitle">Chuẩn bị dịch vụ đặc biệt cho khách hàng</div>
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
                <td class="label">Tổng số khách:</td>
                <td><?= $schedule['total_guests'] ?? 0 ?> người</td>
            </tr>
        </table>
    </div>

    <div class="statistics">
        <h3>THỐNG KÊ TỔNG QUAN</h3>
        <div class="stats-grid">
            <div class="stats-row">
                <div class="stats-cell">
                    <div class="stats-number"><?= $statistics['total_notes'] ?? 0 ?></div>
                    <div class="stats-label">Tổng yêu cầu</div>
                </div>
                <div class="stats-cell">
                    <div class="stats-number"><?= $statistics['high_priority'] ?? 0 ?></div>
                    <div class="stats-label">Ưu tiên cao</div>
                </div>
                <div class="stats-cell">
                    <div class="stats-number"><?= $statistics['pending'] ?? 0 ?></div>
                    <div class="stats-label">Chờ xử lý</div>
                </div>
                <div class="stats-cell">
                    <div class="stats-number"><?= count($guests) ?></div>
                    <div class="stats-label">Khách có yêu cầu</div>
                </div>
            </div>
        </div>
    </div>

    <div class="guest-list">
        <h3>DANH SÁCH KHÁCH CÓ YÊU CẦU ĐẶC BIỆT</h3>
        
        <?php if (empty($guests)): ?>
            <div class="no-requirements">
                <h4>✅ KHÔNG CÓ YÊU CẦU ĐẶC BIỆT</h4>
                <p>Lịch khởi hành này không có khách nào có yêu cầu đặc biệt.</p>
                <p>Có thể tiến hành tour theo kế hoạch chuẩn.</p>
            </div>
        <?php else: ?>
            <?php 
            // Sort guests by priority (High -> Medium -> Low)
            usort($guests, function($a, $b) {
                return $b['max_priority'] <=> $a['max_priority'];
            });
            
            $counter = 1;
            foreach ($guests as $guest): 
                $priority_class = '';
                $priority_text = '';
                $priority_badge_class = '';
                
                switch ($guest['max_priority']) {
                    case 3: 
                        $priority_class = 'high-priority'; 
                        $priority_text = 'CAO';
                        $priority_badge_class = 'priority-high';
                        break;
                    case 2: 
                        $priority_class = 'medium-priority'; 
                        $priority_text = 'TRUNG BÌNH';
                        $priority_badge_class = 'priority-medium';
                        break;
                    default: 
                        $priority_class = 'low-priority'; 
                        $priority_text = 'THẤP';
                        $priority_badge_class = 'priority-low';
                        break;
                }
            ?>
                <div class="guest-item <?= $priority_class ?>">
                    <div class="guest-header">
                        <span class="guest-name">
                            <?= $counter ?>. <?= htmlspecialchars($guest['full_name']) ?>
                        </span>
                        <span class="priority-badge <?= $priority_badge_class ?>">
                            ƯU TIÊN <?= $priority_text ?>
                        </span>
                    </div>
                    
                    <div class="guest-details">
                        <strong>Điện thoại:</strong> <?= htmlspecialchars($guest['phone']) ?> |
                        <?php if ($guest['room_number']): ?>
                            <strong>Phòng:</strong> <?= htmlspecialchars($guest['room_number']) ?> |
                        <?php endif; ?>
                        <strong>Số lượng yêu cầu:</strong> <?= $guest['note_count'] ?>
                    </div>
                    
                    <div class="requirements">
                        <div class="requirement-type">YÊU CẦU ĐẶC BIỆT:</div>
                        <?php 
                        $requirements = explode(' | ', $guest['all_requirements']);
                        foreach ($requirements as $req): 
                        ?>
                            <div class="requirement-item">
                                <?= htmlspecialchars($req) ?>
                            </div>
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
    <div style="page-break-before: always;">
        <h3>CHECKLIST CHUẨN BỊ DỊCH VỤ</h3>
        
        <div style="border: 1px solid #000; padding: 15px; margin: 20px 0;">
            <h4>🍽️ BỘ PHẬN ẨM THỰC:</h4>
            <div style="margin: 10px 0;">
                <input type="checkbox" style="margin-right: 10px;"> Chuẩn bị thức ăn chay cho khách có yêu cầu
            </div>
            <div style="margin: 10px 0;">
                <input type="checkbox" style="margin-right: 10px;"> Kiểm tra nguyên liệu để tránh dị ứng
            </div>
            <div style="margin: 10px 0;">
                <input type="checkbox" style="margin-right: 10px;"> Chuẩn bị thức ăn đặc biệt cho khách tiểu đường
            </div>
            <div style="margin: 10px 0;">
                <input type="checkbox" style="margin-right: 10px;"> Thông báo nhà hàng về yêu cầu đặc biệt
            </div>
        </div>

        <div style="border: 1px solid #000; padding: 15px; margin: 20px 0;">
            <h4>🚌 BỘ PHẬN VẬN CHUYỂN:</h4>
            <div style="margin: 10px 0;">
                <input type="checkbox" style="margin-right: 10px;"> Chuẩn bị xe có hỗ trợ xe lăn (nếu cần)
            </div>
            <div style="margin: 10px 0;">
                <input type="checkbox" style="margin-right: 10px;"> Sắp xếp chỗ ngồi phù hợp cho khách khó di chuyển
            </div>
            <div style="margin: 10px 0;">
                <input type="checkbox" style="margin-right: 10px;"> Chuẩn bị túi đựng thuốc emergency
            </div>
        </div>

        <div style="border: 1px solid #000; padding: 15px; margin: 20px 0;">
            <h4>🏨 BỘ PHẬN LƯU TRÚ:</h4>
            <div style="margin: 10px 0;">
                <input type="checkbox" style="margin-right: 10px;"> Thông báo khách sạn về yêu cầu đặc biệt
            </div>
            <div style="margin: 10px 0;">
                <input type="checkbox" style="margin-right: 10px;"> Sắp xếp phòng tầng thấp cho khách sợ cao
            </div>
            <div style="margin: 10px 0;">
                <input type="checkbox" style="margin-right: 10px;"> Phòng gần thang máy cho khách khó di chuyển
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="signature-section">
        <div class="signature-column">
            <div class="signature-title">NGƯỜI LẬP</div>
            <div class="signature-subtitle">(Ký và ghi rõ họ tên)</div>
            <div style="height: 50px;"></div>
            <div class="signature-line">
                <?= htmlspecialchars($_SESSION['full_name'] ?? 'Admin') ?>
            </div>
        </div>
        <div class="signature-column">
            <div class="signature-title">TRƯỞNG PHÒNG DUYỆT</div>
            <div class="signature-subtitle">(Ký và ghi rõ họ tên)</div>
            <div style="height: 50px;"></div>
            <div class="signature-line">
                &nbsp;
            </div>
        </div>
    </div>

    <div class="footer">
        <div style="text-align: center;">
            <strong>LưU Ý QUAN TRỌNG:</strong><br>
            • Báo cáo này cần được gửi đến tất cả bộ phận liên quan trước ngày khởi hành ít nhất 24 giờ<br>
            • HDV cần xác nhận đã nhận và hiểu rõ tất cả yêu cầu đặc biệt<br>
            • Trong quá trình tour, cần theo dõi và cập nhật tình trạng xử lý vào hệ thống<br>
            • Sau tour, thu thập phản hồi từ khách về chất lượng phục vụ các yêu cầu đặc biệt
        </div>
        <div style="text-align: right; margin-top: 20px;">
            <em>Báo cáo được tạo tự động từ hệ thống vào <?= date('d/m/Y H:i:s') ?></em>
        </div>
    </div>

    <script>
        // Auto print when page loads
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        };
    </script>
</body>
</html>