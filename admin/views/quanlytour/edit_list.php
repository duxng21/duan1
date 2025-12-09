<?php require_once __DIR__ . '/../core/header.php'; ?>
<?php require_once __DIR__ . '/../core/menu.php'; ?>
<!-- BEGIN: Content-->
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
        </div>
        <div class="content-body">
            <!-- Thông báo -->
            <?php require_once __DIR__ . '/../core/alert.php'; ?>

            <section id="basic-vertical-layouts">
                <div class="row match-height">
                    <div class="col-md-6 col-12 mx-auto">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Chỉnh Sửa Tour</h4>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <form action="?act=cap-nhat-tour&id=<?= $tour['tour_id'] ?>" method="POST"
                                        enctype="multipart/form-data">

                                        <div class="form-group">
                                            <label for="category_id">Danh mục <span class="text-danger">*</span></label>
                                            <select name="category_id" id="category_id" class="form-control" required>
                                                <option value="">-- Chọn danh mục --</option>
                                                <?php if (!empty($categories)): ?>
                                                    <?php foreach ($categories as $cat): ?>
                                                        <option value="<?= $cat['category_id'] ?>"
                                                            <?= ($tour['category_id'] == $cat['category_id']) ? 'selected' : '' ?>>
                                                            <?= htmlspecialchars($cat['category_name']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="tour_name">Tên tour <span class="text-danger">*</span></label>
                                            <input type="text" name="tour_name" id="tour_name" class="form-control"
                                                placeholder="Nhập tên tour"
                                                value="<?= htmlspecialchars($tour['tour_name'] ?? '') ?>" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="code">Mã tour</label>
                                            <input type="text" name="code" id="code" class="form-control"
                                                placeholder="VD: TOUR001"
                                                value="<?= htmlspecialchars($tour['code'] ?? '') ?>">
                                        </div>

                                        <div class="form-group">
                                            <label for="tour_image">Hình ảnh</label>
                                            <?php if (!empty($tour['tour_image'])): ?>
                                                <div class="mb-2">
                                                    <img src="<?= BASE_URL . $tour['tour_image'] ?>" width="150"
                                                        class="img-thumbnail">
                                                    <p class="text-muted small">Ảnh hiện tại</p>
                                                </div>
                                            <?php endif; ?>
                                            <input type="file" name="tour_image" id="tour_image" class="form-control"
                                                accept="image/*">
                                            <small class="form-text text-muted">Chọn file ảnh mới (để trống nếu không
                                                đổi)</small>
                                        </div>

                                        <div class="form-group">
                                            <label for="tour_price">Giá tour <span class="text-danger">*</span></label>
                                            <input type="number" name="tour_price" id="tour_price" class="form-control"
                                                placeholder="VD: 5000000" value="<?= $tour['tour_price'] ?? 0 ?>"
                                                min="0" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="short_intro">Giới thiệu ngắn</label>
                                            <textarea name="short_intro" id="short_intro" class="form-control" rows="2"
                                                placeholder="Giới thiệu ngắn gọn về tour (1-2 câu)"><?= htmlspecialchars($tour['short_intro'] ?? '') ?></textarea>
                                        </div>

                                        <div class="form-group">
                                            <label for="inclusions">Bao gồm</label>
                                            <textarea name="inclusions" id="inclusions" class="form-control" rows="3"
                                                placeholder="Những gì bao gồm trong tour (mỗi mục 1 dòng)"><?= htmlspecialchars($tour['inclusions'] ?? '') ?></textarea>
                                            <small class="text-muted">VD: Vé máy bay, Khách sạn 4 sao, Ăn
                                                sáng...</small>
                                        </div>

                                        <div class="form-group">
                                            <label for="exclusions">Không bao gồm</label>
                                            <textarea name="exclusions" id="exclusions" class="form-control" rows="3"
                                                placeholder="Những gì không bao gồm trong tour"><?= htmlspecialchars($tour['exclusions'] ?? '') ?></textarea>
                                            <small class="text-muted">VD: Chi phí cá nhân, Bảo hiểm du lịch...</small>
                                        </div>

                                        <div class="form-group">
                                            <label for="base_duration">Thời lượng chuẩn (ngày)</label>
                                            <input type="number" name="base_duration" id="base_duration"
                                                class="form-control" placeholder="VD: 3"
                                                value="<?= $tour['base_duration'] ?? '' ?>" min="1">
                                        </div>

                                        <div class="form-group">
                                            <label for="start_location">Điểm xuất phát</label>
                                            <input type="text" name="start_location" id="start_location"
                                                class="form-control" placeholder="VD: Hà Nội"
                                                value="<?= htmlspecialchars($tour['start_location'] ?? '') ?>">
                                        </div>

                                        <div class="form-group">
                                            <label for="difficulty">Độ khó</label>
                                            <select name="difficulty" id="difficulty" class="form-control">
                                                <option value="">-- Chọn độ khó --</option>
                                                <option value="Easy" <?= ($tour['difficulty'] ?? '') == 'Easy' ? 'selected' : '' ?>>Dễ (Easy)</option>
                                                <option value="Moderate" <?= ($tour['difficulty'] ?? '') == 'Moderate' ? 'selected' : '' ?>>Trung bình (Moderate)</option>
                                                <option value="Challenging" <?= ($tour['difficulty'] ?? '') == 'Challenging' ? 'selected' : '' ?>>Khó (Challenging)</option>
                                            </select>
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
<?php require_once __DIR__ . '/../core/footer.php'; ?>