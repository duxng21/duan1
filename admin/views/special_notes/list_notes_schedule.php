<?php require_once __DIR__ . '/../core/header.php'; ?>
<?php require_once __DIR__ . '/../core/menu.php'; ?>

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
                            <i class="feather icon-alert-circle"></i> Ghi chú đặc biệt
                        </h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="?act=/">Trang chủ</a></li>
                                <li class="breadcrumb-item"><a href="?act=danh-sach-lich-khoi-hanh">Lịch khởi hành</a></li>
                                <li class="breadcrumb-item active">Ghi chú đặc biệt</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-header-right text-md-right col-md-3 col-12">
                <a href="?act=bao-cao-yeu-cau-dac-biet&schedule_id=<?= $_GET['schedule_id'] ?? '' ?>" 
                   class="btn btn-primary">
                    <i class="feather icon-printer"></i> In báo cáo
                </a>
            </div>
        </div>

        <div class="content-body">
            <!-- Thông báo -->
            <?php require_once __DIR__ . '/../core/alert.php'; ?>

            <!-- Thống kê -->
            <div class="row">
                <div class="col-lg-3 col-md-6 col-12">
                    <div class="card">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <h3 class="text-bold-700 mb-0"><?= $statistics['total_notes'] ?? 0 ?></h3>
                                <p class="mb-0">Tổng ghi chú</p>
                            </div>
                            <div class="avatar bg-primary p-50">
                                <div class="avatar-content">
                                    <i class="feather icon-file-text text-white font-medium-3"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-12">
                    <div class="card">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <h3 class="text-bold-700 mb-0 text-danger"><?= $statistics['high_priority'] ?? 0 ?></h3>
                                <p class="mb-0">Ưu tiên cao</p>
                            </div>
                            <div class="avatar bg-danger p-50">
                                <div class="avatar-content">
                                    <i class="feather icon-alert-triangle text-white font-medium-3"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-12">
                    <div class="card">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <h3 class="text-bold-700 mb-0 text-warning"><?= $statistics['pending'] ?? 0 ?></h3>
                                <p class="mb-0">Chờ xử lý</p>
                            </div>
                            <div class="avatar bg-warning p-50">
                                <div class="avatar-content">
                                    <i class="feather icon-clock text-white font-medium-3"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-12">
                    <div class="card">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <h3 class="text-bold-700 mb-0 text-success"><?= $statistics['resolved'] ?? 0 ?></h3>
                                <p class="mb-0">Đã hoàn thành</p>
                            </div>
                            <div class="avatar bg-success p-50">
                                <div class="avatar-content">
                                    <i class="feather icon-check-circle text-white font-medium-3"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="card">
                <div class="card-body">
                    <form method="GET" class="form-inline">
                        <input type="hidden" name="act" value="<?= $_GET['act'] ?>">
                        <input type="hidden" name="schedule_id" value="<?= $_GET['schedule_id'] ?? '' ?>">
                        
                        <div class="form-group mr-2">
                            <label class="mr-2">Mức độ:</label>
                            <select name="priority" class="form-control">
                                <option value="">Tất cả</option>
                                <option value="High" <?= ($_GET['priority'] ?? '') === 'High' ? 'selected' : '' ?>>Cao</option>
                                <option value="Medium" <?= ($_GET['priority'] ?? '') === 'Medium' ? 'selected' : '' ?>>Trung bình</option>
                                <option value="Low" <?= ($_GET['priority'] ?? '') === 'Low' ? 'selected' : '' ?>>Thấp</option>
                            </select>
                        </div>
                        
                        <div class="form-group mr-2">
                            <label class="mr-2">Trạng thái:</label>
                            <select name="status" class="form-control">
                                <option value="">Tất cả</option>
                                <option value="Pending" <?= ($_GET['status'] ?? '') === 'Pending' ? 'selected' : '' ?>>Chờ xử lý</option>
                                <option value="Acknowledged" <?= ($_GET['status'] ?? '') === 'Acknowledged' ? 'selected' : '' ?>>Đã nhận</option>
                                <option value="In Progress" <?= ($_GET['status'] ?? '') === 'In Progress' ? 'selected' : '' ?>>Đang xử lý</option>
                                <option value="Resolved" <?= ($_GET['status'] ?? '') === 'Resolved' ? 'selected' : '' ?>>Đã hoàn thành</option>
                            </select>
                        </div>
                        
                        <div class="form-group mr-2">
                            <label class="mr-2">Loại:</label>
                            <select name="note_type" class="form-control">
                                <option value="">Tất cả</option>
                                <option value="Dietary" <?= ($_GET['note_type'] ?? '') === 'Dietary' ? 'selected' : '' ?>>🍽️ Ăn uống</option>
                                <option value="Medical" <?= ($_GET['note_type'] ?? '') === 'Medical' ? 'selected' : '' ?>>💊 Y tế</option>
                                <option value="Allergy" <?= ($_GET['note_type'] ?? '') === 'Allergy' ? 'selected' : '' ?>>⚠️ Dị ứng</option>
                                <option value="Mobility" <?= ($_GET['note_type'] ?? '') === 'Mobility' ? 'selected' : '' ?>>♿ Di chuyển</option>
                                <option value="Other" <?= ($_GET['note_type'] ?? '') === 'Other' ? 'selected' : '' ?>>📝 Khác</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="feather icon-filter"></i> Lọc
                        </button>
                        <a href="?act=<?= $_GET['act'] ?>&schedule_id=<?= $_GET['schedule_id'] ?? '' ?>" class="btn btn-secondary ml-2">
                            <i class="feather icon-x"></i> Xóa bộ lọc
                        </a>
                    </form>
                </div>
            </div>

            <!-- Danh sách ghi chú -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Danh sách ghi chú đặc biệt</h4>
                </div>
                <div class="card-body">
                    <?php if (!empty($notes)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Khách hàng</th>
                                        <th>Loại</th>
                                        <th>Nội dung</th>
                                        <th>Mức độ</th>
                                        <th>Trạng thái</th>
                                        <th>Người tạo</th>
                                        <th>Ngày tạo</th>
                                        <th>Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($notes as $note): ?>
                                        <tr>
                                            <td>
                                                <strong><?= htmlspecialchars($note['full_name']) ?></strong><br>
                                                <small><?= htmlspecialchars($note['phone'] ?? '') ?></small>
                                                <?php if (!empty($note['room_number'])): ?>
                                                    <br><span class="badge badge-secondary">Phòng <?= htmlspecialchars($note['room_number']) ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php
                                                $typeIcons = [
                                                    'Dietary' => '🍽️ Ăn uống',
                                                    'Medical' => '💊 Y tế',
                                                    'Allergy' => '⚠️ Dị ứng',
                                                    'Mobility' => '♿ Di chuyển',
                                                    'Other' => '📝 Khác'
                                                ];
                                                echo $typeIcons[$note['note_type']] ?? $note['note_type'];
                                                ?>
                                            </td>
                                            <td>
                                                <div style="max-width: 300px;">
                                                    <?= nl2br(htmlspecialchars($note['note_content'])) ?>
                                                </div>
                                                <?php if (!empty($note['handler_notes'])): ?>
                                                    <small class="text-muted">
                                                        <i class="feather icon-message-circle"></i>
                                                        <?= nl2br(htmlspecialchars($note['handler_notes'])) ?>
                                                    </small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php
                                                $priorityBadge = match ($note['priority_level']) {
                                                    'High' => '<span class="badge badge-danger">Cao</span>',
                                                    'Medium' => '<span class="badge badge-warning">Trung bình</span>',
                                                    'Low' => '<span class="badge badge-info">Thấp</span>',
                                                    default => $note['priority_level']
                                                };
                                                echo $priorityBadge;
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                $statusBadge = match ($note['status']) {
                                                    'Pending' => '<span class="badge badge-secondary">Chờ xử lý</span>',
                                                    'Acknowledged' => '<span class="badge badge-primary">Đã nhận</span>',
                                                    'In Progress' => '<span class="badge badge-info">Đang xử lý</span>',
                                                    'Resolved' => '<span class="badge badge-success">Hoàn thành</span>',
                                                    default => $note['status']
                                                };
                                                echo $statusBadge;
                                                ?>
                                                <br>
                                                <button class="btn btn-sm btn-outline-primary mt-1" 
                                                        data-toggle="modal" 
                                                        data-target="#statusModal<?= $note['note_id'] ?>">
                                                    <i class="feather icon-edit-2"></i> Cập nhật
                                                </button>
                                            </td>
                                            <td>
                                                <small><?= htmlspecialchars($note['creator_name'] ?? 'N/A') ?></small>
                                            </td>
                                            <td>
                                                <small><?= date('d/m/Y H:i', strtotime($note['created_at'])) ?></small>
                                            </td>
                                            <td>
                                                <a href="?act=sua-ghi-chu&id=<?= $note['note_id'] ?>" 
                                                   class="btn btn-sm btn-warning" title="Sửa">
                                                    <i class="feather icon-edit"></i>
                                                </a>
                                                <?php if (isAdmin()): ?>
                                                    <a href="?act=xoa-ghi-chu&id=<?= $note['note_id'] ?>&return_url=<?= urlencode($_SERVER['REQUEST_URI']) ?>" 
                                                       class="btn btn-sm btn-danger" 
                                                       onclick="return confirm('Xóa ghi chú này?')" 
                                                       title="Xóa">
                                                        <i class="feather icon-trash"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>

                                        <!-- Modal cập nhật trạng thái -->
                                        <div class="modal fade" id="statusModal<?= $note['note_id'] ?>" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form method="POST" action="?act=cap-nhat-trang-thai-ghi-chu">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Cập nhật trạng thái xử lý</h5>
                                                            <button type="button" class="close" data-dismiss="modal">
                                                                <span>&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <input type="hidden" name="note_id" value="<?= $note['note_id'] ?>">
                                                            <input type="hidden" name="return_url" value="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
                                                            
                                                            <div class="form-group">
                                                                <label>Trạng thái <span class="text-danger">*</span></label>
                                                                <select name="status" class="form-control" required>
                                                                    <option value="Pending" <?= $note['status'] === 'Pending' ? 'selected' : '' ?>>Chờ xử lý</option>
                                                                    <option value="Acknowledged" <?= $note['status'] === 'Acknowledged' ? 'selected' : '' ?>>Đã nhận</option>
                                                                    <option value="In Progress" <?= $note['status'] === 'In Progress' ? 'selected' : '' ?>>Đang xử lý</option>
                                                                    <option value="Resolved" <?= $note['status'] === 'Resolved' ? 'selected' : '' ?>>Đã hoàn thành</option>
                                                                </select>
                                                            </div>
                                                            
                                                            <div class="form-group">
                                                                <label>Ghi chú xử lý</label>
                                                                <textarea name="handler_notes" class="form-control" rows="3" 
                                                                          placeholder="Nhập ghi chú về quá trình xử lý..."><?= htmlspecialchars($note['handler_notes'] ?? '') ?></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                                                            <button type="submit" class="btn btn-primary">Cập nhật</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center text-muted py-5">
                            <i class="feather icon-inbox font-large-2"></i>
                            <p class="mt-2">Chưa có ghi chú đặc biệt nào</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END: Content-->

<?php require_once __DIR__ . '/../core/footer.php'; ?>
