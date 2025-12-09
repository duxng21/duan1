<?php
$is_edit = isset($journal);
$page_title = $is_edit ? 'Chỉnh sửa nhật ký' : 'Tạo nhật ký mới';
?>
<?php require_once __DIR__ . '/../core/header.php'; ?>
<?php require_once __DIR__ . '/../core/menu.php'; ?>
<?php require_once __DIR__ . '/../core/alert.php'; ?>

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
                            <i class="feather icon-book-open"></i> <?= $page_title ?>
                        </h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="?act=home-guide">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="?act=hdv-lich-cua-toi">Lịch làm việc</a></li>
                                <li class="breadcrumb-item"><a
                                        href="?act=view-tour-journal&schedule_id=<?= $schedule['schedule_id'] ?>">Nhật
                                        ký</a></li>
                                <li class="breadcrumb-item active"><?= $page_title ?></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-body">
            <form action="?act=<?= $is_edit ? 'update-journal-entry' : 'create-journal-entry' ?>" method="POST"
                enctype="multipart/form-data">

                <?php if ($is_edit): ?>
                    <input type="hidden" name="journal_id" value="<?= $journal['journal_id'] ?>">
                <?php else: ?>
                    <input type="hidden" name="schedule_id" value="<?= $schedule['schedule_id'] ?>">
                <?php endif; ?>

                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Thông tin cơ bản</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="journal_date">Ngày <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="journal_date" name="journal_date"
                                        value="<?= $journal['journal_date'] ?? date('Y-m-d') ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">Trạng thái</label>
                                    <select class="form-control" id="status" name="status">
                                        <option value="Draft" <?= ($journal['status'] ?? '') === 'Draft' ? 'selected' : '' ?>>Nháp</option>
                                        <option value="Published" <?= ($journal['status'] ?? 'Published') === 'Published' ? 'selected' : '' ?>>Công khai</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="title">Tiêu đề <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title"
                                value="<?= htmlspecialchars($journal['title'] ?? '') ?>"
                                placeholder="VD: Ngày 1 - Khám phá Hà Nội cổ kính" required>
                        </div>

                        <div class="form-group">
                            <label for="content">Nội dung chi tiết</label>
                            <textarea class="form-control" id="content" name="content" rows="6"
                                placeholder="Ghi lại diễn biến chi tiết trong ngày..."><?= htmlspecialchars($journal['content'] ?? '') ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="location">Địa điểm</label>
                                    <input type="text" class="form-control" id="location" name="location"
                                        value="<?= htmlspecialchars($journal['location'] ?? '') ?>"
                                        placeholder="VD: Hồ Hoàn Kiếm, Hà Nội">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="weather">Thời tiết</label>
                                    <input type="text" class="form-control" id="weather" name="weather"
                                        value="<?= htmlspecialchars($journal['weather'] ?? '') ?>"
                                        placeholder="VD: Nắng, 28°C">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Hoạt động & Sự kiện</h4>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="activities">Hoạt động trong ngày</label>
                            <textarea class="form-control" id="activities" name="activities" rows="4"
                                placeholder="Liệt kê các hoạt động đã thực hiện..."><?= htmlspecialchars($journal['activities'] ?? '') ?></textarea>
                            <small class="form-text text-muted">VD: 8h00 - Tham quan Lăng Bác, 10h00 - Chùa Một
                                Cột...</small>
                        </div>

                        <div class="form-group">
                            <label for="incidents">Sự cố (nếu có)</label>
                            <textarea class="form-control" id="incidents" name="incidents" rows="3"
                                placeholder="Mô tả sự cố phát sinh..."><?= htmlspecialchars($journal['incidents'] ?? '') ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="incidents_resolved">Cách xử lý sự cố</label>
                            <textarea class="form-control" id="incidents_resolved" name="incidents_resolved" rows="3"
                                placeholder="Cách đã xử lý sự cố..."><?= htmlspecialchars($journal['incidents_resolved'] ?? '') ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="guest_feedback">Phản hồi của khách</label>
                            <textarea class="form-control" id="guest_feedback" name="guest_feedback" rows="3"
                                placeholder="Ghi nhận ý kiến, phản hồi của khách hàng..."><?= htmlspecialchars($journal['guest_feedback'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Hình ảnh</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($is_edit && !empty($images)): ?>
                            <div class="mb-3">
                                <h5>Ảnh hiện có</h5>
                                <div class="row">
                                    <?php foreach ($images as $img): ?>
                                        <div class="col-md-3 mb-2">
                                            <div class="card">
                                                <img src="<?= BASE_URL . htmlspecialchars($img['file_path']) ?>"
                                                    class="card-img-top" alt="Journal image">
                                                <div class="card-body p-2 text-center">
                                                    <a href="?act=delete-journal-image&id=<?= $img['image_id'] ?>&journal_id=<?= $journal['journal_id'] ?>"
                                                        class="btn btn-sm btn-danger" onclick="return confirm('Xóa ảnh này?')">
                                                        <i class="feather icon-trash"></i> Xóa
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="form-group">
                            <label for="images">Thêm hình ảnh mới</label>
                            <input type="file" class="form-control-file" id="images" name="images[]" accept="image/*"
                                multiple>
                            <small class="form-text text-muted">Có thể chọn nhiều ảnh cùng lúc</small>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <a href="?act=view-tour-journal&schedule_id=<?= $schedule['schedule_id'] ?>"
                                class="btn btn-secondary">
                                <i class="feather icon-x"></i> Hủy
                            </a>
                            <div>
                                <button type="submit" name="status" value="Draft" class="btn btn-outline-primary">
                                    <i class="feather icon-save"></i> Lưu nháp
                                </button>
                                <button type="submit" name="status" value="Published" class="btn btn-primary">
                                    <i class="feather icon-check"></i> <?= $is_edit ? 'Cập nhật' : 'Tạo nhật ký' ?>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- END: Content-->

<?php require_once __DIR__ . '/../core/footer.php'; ?>