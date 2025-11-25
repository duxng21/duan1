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
                        <h2 class="content-header-title float-left mb-0">Báo cáo doanh thu – chi phí – lợi nhuận tour
                        </h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">
            <?php require_once __DIR__ . '/../core/alert.php'; ?>

            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Bộ lọc</h4>
                </div>
                <div class="card-body">
                    <form method="GET">
                        <input type="hidden" name="act" value="bao-cao-tour" />
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label>Từ ngày</label>
                                <input type="date" name="from_date"
                                    value="<?= htmlspecialchars($filters['from_date']) ?>" class="form-control" />
                            </div>
                            <div class="form-group col-md-3">
                                <label>Đến ngày</label>
                                <input type="date" name="to_date" value="<?= htmlspecialchars($filters['to_date']) ?>"
                                    class="form-control" />
                            </div>
                            <div class="form-group col-md-3">
                                <label>Danh mục tour</label>
                                <select name="category_id" class="form-control">
                                    <option value="">Tất cả</option>
                                    <?php foreach ($categories as $c): ?>
                                        <option value="<?= $c['category_id'] ?>"
                                            <?= ($filters['category_id'] == $c['category_id'] ? 'selected' : '') ?>>
                                            <?= htmlspecialchars($c['category_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group col-md-3 d-flex align-items-end">
                                <button class="btn btn-primary mr-1" type="submit"><i class="feather icon-filter"></i>
                                    Lọc</button>
                                <a class="btn btn-success mr-1"
                                    href="?act=xuat-bao-cao-tour&format=csv&from_date=<?= urlencode($filters['from_date']) ?>&to_date=<?= urlencode($filters['to_date']) ?>&category_id=<?= urlencode($filters['category_id']) ?>">
                                    <i class="feather icon-file-text"></i> Xuất Excel (CSV)
                                </a>
                                <a class="btn btn-info" target="_blank"
                                    href="?act=xuat-bao-cao-tour&format=print&from_date=<?= urlencode($filters['from_date']) ?>&to_date=<?= urlencode($filters['to_date']) ?>&category_id=<?= urlencode($filters['category_id']) ?>">
                                    <i class="feather icon-printer"></i> In / PDF
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Thống kê tổng hợp</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Tour</th>
                                    <th>Danh mục</th>
                                    <th class="text-right">Doanh thu</th>
                                    <th class="text-right">Chi phí dịch vụ</th>
                                    <th class="text-right">Chi phí nhân sự</th>
                                    <th class="text-right">Lợi nhuận</th>
                                    <th class="text-right">Tỷ lệ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sumRev = $sumServ = $sumStaff = $sumProfit = 0;
                                foreach ($rows as $r):
                                    $sumRev += (float) $r['revenue'];
                                    $sumServ += (float) $r['service_cost'];
                                    $sumStaff += (float) $r['staff_cost'];
                                    $sumProfit += (float) $r['profit'];
                                    ?>
                                    <tr>
                                        <td><strong><?= htmlspecialchars($r['tour_name']) ?></strong></td>
                                        <td><?= htmlspecialchars($r['category_name']) ?></td>
                                        <td class="text-right"><?= number_format($r['revenue'], 0, ',', '.') ?></td>
                                        <td class="text-right"><?= number_format($r['service_cost'], 0, ',', '.') ?></td>
                                        <td class="text-right"><?= number_format($r['staff_cost'], 0, ',', '.') ?></td>
                                        <td class="text-right"><?= number_format($r['profit'], 0, ',', '.') ?></td>
                                        <td class="text-right">
                                            <?php $m = (float) $r['margin_percent']; ?>
                                            <span class="badge <?= $m >= 0 ? 'badge-success' : 'badge-danger' ?>">
                                                <?= ($m >= 0 ? '+' : '') . number_format($m, 2, ',', '.') ?>%
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Tổng</th>
                                    <th></th>
                                    <th class="text-right"><?= number_format($sumRev, 0, ',', '.') ?></th>
                                    <th class="text-right"><?= number_format($sumServ, 0, ',', '.') ?></th>
                                    <th class="text-right"><?= number_format($sumStaff, 0, ',', '.') ?></th>
                                    <th class="text-right"><?= number_format($sumProfit, 0, ',', '.') ?></th>
                                    <th class="text-right">
                                        <?php $overall = $sumRev > 0 ? round($sumProfit / $sumRev * 100, 2) : 0; ?>
                                        <span class="badge <?= $overall >= 0 ? 'badge-success' : 'badge-danger' ?>">
                                            <?= ($overall >= 0 ? '+' : '') . number_format($overall, 2, ',', '.') ?>%
                                        </span>
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../core/footer.php'; ?>