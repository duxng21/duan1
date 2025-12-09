<?php
// Core layout
require_once './views/core/header.php';
require_once './views/core/menu.php';

// Expect $scheduleInfo (optional) and $guests
$scheduleId = $_GET['schedule_id'] ?? $_GET['id'] ?? null;

// Build quick summary from guest list
$totalGuests = 0;
$checkedIn = 0;
$late = 0;
$noShow = 0;
$pending = 0;
if (!empty($guests) && is_array($guests)) {
    foreach ($guests as $g) {
        $totalGuests++;
        $st = $g['check_in_status'] ?? 'Pending';
        if ($st === 'Checked-In')
            $checkedIn++;
        elseif ($st === 'Late')
            $late++;
        elseif ($st === 'No-Show')
            $noShow++;
        else
            $pending++;
    }
}
?>

<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-12 mb-1">
                <div class="breadcrumbs-top d-inline-block">
                    <h4 class="content-header-title">Điểm danh khách</h4>
                    <div class="breadcrumb-wrapper d-none d-sm-block">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="?act=hdv-lich-cua-toi">Lịch của tôi</a></li>
                            <li class="breadcrumb-item"><a
                                    href="?act=hdv-chi-tiet-tour&id=<?php echo (int) $scheduleId; ?>">Chi tiết tour</a>
                            </li>
                            <li class="breadcrumb-item active">Điểm danh khách - Lịch
                                #<?php echo htmlspecialchars($scheduleId); ?></li>
                        </ol>
                    </div>
                </div>
            </div>

            <?php require_once './views/core/footer.php'; ?>
        </div>

        <div class="content-body">
            <?php require_once __DIR__ . '/../core/alert.php'; ?>

            <section id="checkin-summary" class="mb-1">
                <div class="row">
                    <div class="col-xl-3 col-md-6 col-12">
                        <div class="card">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-25">Tổng khách</h6>
                                    <h3 class="mb-0"><?php echo (int) $totalGuests; ?></h3>
                                </div>
                                <div class="avatar bg-primary text-white"><i class="feather icon-users"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 col-12">
                        <div class="card">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-25">Đã có mặt</h6>
                                    <h3 class="mb-0 text-success"><?php echo (int) $checkedIn; ?></h3>
                                </div>
                                <div class="avatar bg-success text-white"><i class="feather icon-check"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 col-12">
                        <div class="card">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-25">Đến trễ</h6>
                                    <h3 class="mb-0 text-warning"><?php echo (int) $late; ?></h3>
                                </div>
                                <div class="avatar bg-warning text-white"><i class="feather icon-clock"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 col-12">
                        <div class="card">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-25">Vắng mặt</h6>
                                    <h3 class="mb-0 text-danger"><?php echo (int) $noShow; ?></h3>
                                </div>
                                <div class="avatar bg-danger text-white"><i class="feather icon-user-x"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section id="checkin-table">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title mb-0">Danh sách khách</h4>
                            <small class="text-muted">Cập nhật trạng thái điểm danh cho từng khách</small>
                        </div>
                        <div class="heading-elements">
                            <a href="?act=xuat-bao-cao-diem-danh&schedule_id=<?php echo (int) $scheduleId; ?>&format=pdf"
                                class="btn btn-outline-secondary btn-sm mr-50">
                                <i class="feather icon-file-text"></i> PDF
                            </a>
                            <a href="?act=xuat-bao-cao-diem-danh&schedule_id=<?php echo (int) $scheduleId; ?>&format=excel"
                                class="btn btn-outline-success btn-sm">
                                <i class="feather icon-download"></i> Excel
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="post" action="?act=luu-diem-danh">
                            <input type="hidden" name="schedule_id" value="<?php echo (int) $scheduleId; ?>">

                            <div class="table-responsive">
                                <table class="table table-hover table-striped mb-0">
                                    <thead>
                                        <tr>
                                            <th style="width: 56px">#</th>
                                            <th>Họ tên</th>
                                            <th>SĐT</th>
                                            <th style="width: 260px">Trạng thái</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($guests)):
                                            $i = 1;
                                            foreach ($guests as $g): ?>
                                                <?php $status = $g['check_in_status'] ?? 'Pending'; ?>
                                                <tr>
                                                    <td><?php echo $i++; ?></td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar bg-light-primary mr-1"><i
                                                                    class="feather icon-user"></i></div>
                                                            <div>
                                                                <div class="font-weight-600">
                                                                    <?php echo htmlspecialchars($g['full_name']); ?>
                                                                </div>
                                                                <small class="text-muted">ID:
                                                                    <?php echo (int) ($g['member_id'] ?? $g['guest_id'] ?? 0); ?></small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <small><?php echo htmlspecialchars($g['phone'] ?? '-'); ?></small>
                                                    </td>
                                                    <td>
                                                        <select
                                                            name="updates[<?php echo (int) ($g['member_id'] ?? $g['guest_id'] ?? 0); ?>]"
                                                            class="custom-select">
                                                            <option value="Pending" <?php echo ($status === 'Pending' ? 'selected' : ''); ?>>Chưa điểm danh</option>
                                                            <option value="Checked-In" <?php echo ($status === 'Checked-In' ? 'selected' : ''); ?>>Đã có mặt</option>
                                                            <option value="Late" <?php echo ($status === 'Late' ? 'selected' : ''); ?>>Đến trễ</option>
                                                            <option value="No-Show" <?php echo ($status === 'No-Show' ? 'selected' : ''); ?>>Vắng mặt</option>
                                                        </select>
                                                    </td>
                                                </tr>
                                            <?php endforeach; else: ?>
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">Không có khách trong lịch
                                                    này.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-1 d-flex justify-content-between">
                                <a href="?act=hdv-chi-tiet-tour&id=<?php echo (int) $scheduleId; ?>"
                                    class="btn btn-light">
                                    <i class="feather icon-arrow-left"></i> Quay lại
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="feather icon-save"></i> Lưu và đồng bộ
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>