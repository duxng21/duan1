<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/menu.php'; ?>
<!-- BEGIN: Content-->
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
        </div>
        <div class="content-body">
            <!-- Thông báo -->
            <?php require_once __DIR__ . '/../layouts/alert.php'; ?>

            <section id="basic-vertical-layouts">
                <div class="row match-height">
                    <div class="col-md-6 col-12 mx-auto">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Sửa danh sách Tour</h4>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <form class="form form-vertical">
                                        <div class="form-body">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label for="first-name-vertical">Tên Tour</label>
                                                        <input type="text" id="name" class="form-control" name="name" placeholder="Tên Tour">
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                            <input type="file" name="tour_image" id="tour_image" class="form-control"
                                                accept="image/*">
                                            <small class="form-text text-muted">Chọn file ảnh mới (để trống nếu không
                                                đổi)</small>
                                        </div>

                                        <!-- <div class="form-group">
                                            <label for="tour_price">Giá tour <span class="text-danger">*</span></label>
                                            <input type="number" name="tour_price" id="tour_price" class="form-control"
                                                placeholder="VD: 5000000" value="<?= $tour['tour_price'] ?? 0 ?>"
                                                min="0" required>
                                        </div> -->



                                        <div class="form-group">
                                            <label for="description_short">Mô tả ngắn</label>
                                            <textarea name="description_short" id="description_short"
                                                class="form-control" rows="2"
                                                placeholder="Mô tả ngắn gọn về tour"><?= htmlspecialchars($tour['description_short'] ?? '') ?></textarea>
                                        </div>

                                        <div class="form-group">
                                            <label for="description_full">Mô tả chi tiết</label>
                                            <textarea name="description_full" id="description_full" class="form-control"
                                                rows="4"
                                                placeholder="Mô tả đầy đủ về tour"><?= htmlspecialchars($tour['description_full'] ?? '') ?></textarea>
                                        </div>

                                        <div class="form-group">
                                            <label for="duration_days">Thời lượng (số ngày)</label>
                                            <input type="number" name="duration_days" id="duration_days"
                                                class="form-control" placeholder="VD: 3"
                                                value="<?= $tour['duration_days'] ?? '' ?>" min="1">
                                        </div>

                                        <div class="form-group">
                                            <label for="start_location">Điểm khởi hành</label>
                                            <input type="text" name="start_location" id="start_location"
                                                class="form-control" placeholder="VD: Hà Nội"
                                                value="<?= htmlspecialchars($tour['start_location'] ?? '') ?>">
                                        </div>

                                        <div class="form-group">
                                            <label for="status">Trạng thái</label>
                                            <select name="status" id="status" class="form-control">
                                                <option value="Draft" <?= ($tour['status'] ?? '') == 'Draft' ? 'selected' : '' ?>>Nháp (Draft)</option>
                                                <option value="Public" <?= ($tour['status'] ?? '') == 'Public' ? 'selected' : '' ?>>Công khai (Public)</option>
                                                <option value="Hidden" <?= ($tour['status'] ?? '') == 'Hidden' ? 'selected' : '' ?>>Ẩn (Hidden)</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary mr-1">
                                                <i class="feather icon-save"></i> Cập nhật tour
                                            </button>
                                            <a href="?act=list-tour" class="btn btn-secondary">
                                                <i class="feather icon-x"></i> Hủy
                                            </a>
                                        </div>
                                    </form>
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
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>