<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Báo cáo tài chính tour</title>
    <style>
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        th {
            background: #f2f2f2;
        }

        .right {
            text-align: right;
        }

        .muted {
            color: #777;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
        }

        .header small {
            color: #777;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>Báo cáo doanh thu – chi phí – lợi nhuận tour</h2>
        <small>Thời điểm: <?= date('d/m/Y H:i') ?></small>
    </div>
    <div class="no-print" style="margin:10px 0;">
        <button onclick="window.print()">In</button>
    </div>
    <table>
        <thead>
            <tr>
                <th>Tour</th>
                <th>Danh mục</th>
                <th class="right">Doanh thu</th>
                <th class="right">Chi phí dịch vụ</th>
                <th class="right">Chi phí nhân sự</th>
                <th class="right">Lợi nhuận</th>
                <th class="right">Tỷ lệ</th>
            </tr>
        </thead>
        <tbody>
            <?php $sumRev = $sumServ = $sumStaff = $sumProfit = 0;
            foreach ($rowsData as $r):
                $sumRev += (float) $r['revenue'];
                $sumServ += (float) $r['service_cost'];
                $sumStaff += (float) $r['staff_cost'];
                $sumProfit += (float) $r['profit'];
                ?>
                <tr>
                    <td><strong><?= htmlspecialchars($r['tour_name']) ?></strong></td>
                    <td><?= htmlspecialchars($r['category_name']) ?></td>
                    <td class="right"><?= number_format($r['revenue'], 0, ',', '.') ?></td>
                    <td class="right"><?= number_format($r['service_cost'], 0, ',', '.') ?></td>
                    <td class="right"><?= number_format($r['staff_cost'], 0, ',', '.') ?></td>
                    <td class="right"><?= number_format($r['profit'], 0, ',', '.') ?></td>
                    <td class="right"><?= number_format($r['margin_percent'], 2, ',', '.') ?>%</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <th>Tổng</th>
                <th></th>
                <th class="right"><?= number_format($sumRev, 0, ',', '.') ?></th>
                <th class="right"><?= number_format($sumServ, 0, ',', '.') ?></th>
                <th class="right"><?= number_format($sumStaff, 0, ',', '.') ?></th>
                <th class="right"><?= number_format($sumProfit, 0, ',', '.') ?></th>
                <th class="right">
                    <?php $overall = $sumRev > 0 ? round($sumProfit / $sumRev * 100, 2) : 0;
                    echo number_format($overall, 2, ',', '.'); ?>%
                </th>
            </tr>
        </tfoot>
    </table>
</body>

</html>