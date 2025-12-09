<?php
// Variables expected: $scheduleInfo, $guests, $summary
require_once './views/core/header.php';
require_once './views/core/menu.php';
?>

<!-- BEGIN: Content-->
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">
                            <i class="feather icon-users"></i> Danh sách khách
                        </h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="?act=hdv-lich-cua-toi">Lịch của tôi</a></li>
                                <li class="breadcrumb-item"><a
                                        href="?act=hdv-chi-tiet-tour&id=<?php echo (int) $scheduleInfo['schedule_id']; ?>">Chi
                                        tiết tour</a></li>
                                <li class="breadcrumb-item active">Danh sách khách
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-body">
            <!-- Thông tin tour -->
            <section id="tour-info">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">
                                    <?php echo htmlspecialchars($scheduleInfo['tour_name']); ?>
                                    <small
                                        class="text-muted">(<?php echo htmlspecialchars($scheduleInfo['code']); ?>)</small>
                                </h4>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <p class="mb-0">
                                        <i class="feather icon-calendar"></i>
                                        <strong>Khởi hành:</strong>
                                        <?php echo htmlspecialchars($scheduleInfo['departure_date']); ?>
                                        - <strong>Kết thúc:</strong>
                                        <?php echo htmlspecialchars($scheduleInfo['return_date'] ?? $scheduleInfo['departure_date']); ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Bộ lọc và danh sách -->
            <section id="guest-list">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title"><i class="feather icon-filter"></i> Bộ lọc</h4>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <input type="hidden" name="act" value="hdv-danh-sach-khach">
                                    <input type="hidden" name="schedule_id"
                                        value="<?php echo (int) $scheduleInfo['schedule_id']; ?>">
                                    <div class="form-row">
                                        <div class="col">
                                            <input type="text" class="form-control" name="search"
                                                placeholder="Tìm theo tên, email, phone"
                                                value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                                        </div>
                                        <div class="col">
                                            <select class="form-control" name="check_in_status">
                                                <option value="">Trạng thái điểm danh</option>
                                                <option value="Checked-In" <?php echo (($_GET['check_in_status'] ?? '') === 'Checked-In') ? 'selected' : ''; ?>>Đã có mặt</option>
                                                <option value="No-Show" <?php echo (($_GET['check_in_status'] ?? '') === 'No-Show') ? 'selected' : ''; ?>>Vắng mặt</option>
                                                <option value="Late" <?php echo (($_GET['check_in_status'] ?? '') === 'Late') ? 'selected' : ''; ?>>Đến trễ</option>
                                                <option value="Pending" <?php echo (($_GET['check_in_status'] ?? '') === 'Pending') ? 'selected' : ''; ?>>Chưa điểm danh</option>
                                            </select>
                                        </div>
                                        <div class="col">
                                            <select class="form-control" name="has_room">
                                                <option value="">Phân phòng</option>
                                                <option value="1" <?php echo (($_GET['has_room'] ?? '') === '1') ? 'selected' : ''; ?>>Đã có
                                                    phòng</option>
                                                <option value="0" <?php echo (($_GET['has_room'] ?? '') === '0') ? 'selected' : ''; ?>>Chưa có
                                                    phòng</option>
                                            </select>
                                        </div>
                                        <div class="col">
                                            <button type="submit" class="btn btn-primary">Lọc</button>
                                            <a class="btn btn-secondary"
                                                href="?act=hdv-danh-sach-khach&schedule_id=<?php echo (int) $scheduleInfo['schedule_id']; ?>">Xóa
                                                lọc</a>
                                        </div>
                                    </div>
                                    </form>
                                </div>
                                <div class="col-md-4 text-right">
                                    <a class="btn btn-secondary"
                                        href="?act=hdv-chi-tiet-tour&id=<?php echo (int) $scheduleInfo['schedule_id']; ?>">Quay
                                        lại tour</a>
                                    <a class="btn btn-success"
                                        href="?act=xuat-danh-sach-khach&schedule_id=<?php echo (int) $scheduleInfo['schedule_id']; ?>&format=excel">Xuất
                                        Excel</a>
                                    <a class="btn btn-danger"
                                        href="?act=xuat-danh-sach-khach&schedule_id=<?php echo (int) $scheduleInfo['schedule_id']; ?>&format=pdf">Xuất
                                        PDF</a>
                                </div>
                            </div>

                            <hr>
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Họ tên</th>
                                                <th>Giới tính</th>
                                                <th>Điện thoại</th>
                                                <th>Email</th>
                                                <th>Nhóm</th>
                                                <th>Phòng</th>
                                                <th>Trạng thái</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($guests as $g): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($g['full_name'] ?? ''); ?></td>
                                                    <td><?php echo htmlspecialchars($g['gender'] ?? ''); ?></td>
                                                    <td><?php echo htmlspecialchars($g['phone'] ?? ''); ?></td>
                                                    <td><?php echo htmlspecialchars($g['email'] ?? ''); ?></td>
                                                    <td><?php echo htmlspecialchars($g['group_name'] ?? ''); ?></td>
                                                    <td><?php echo htmlspecialchars($g['room_number'] ?? ''); ?></td>
                                                    <td><?php echo htmlspecialchars($g['check_in_status'] ?? 'Pending'); ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <h5>Thống kê nhanh</h5>
                                    <ul>
                                        <li>Tổng số khách: <?php echo (int) $summary['total_guests']; ?></li>
                                        <li>Nam: <?php echo (int) $summary['male_count']; ?> — Nữ:
                                            <?php echo (int) $summary['female_count']; ?>
                                        </li>
                                        <li>Người lớn: <?php echo (int) $summary['adult_count']; ?> — Trẻ em:
                                            <?php echo (int) $summary['child_count']; ?>
                                        </li>
                                        <li>Đã check-in: <?php echo (int) $summary['checked_in']; ?> — Vắng:
                                            <?php echo (int) $summary['no_show']; ?>
                                        </li>
                                        <li>Đã phân phòng: <?php echo (int) $summary['room_assigned']; ?></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
<!-- END: Content-->

<?php require_once './views/core/footer.php'; ?>