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
                            <?= isset($feedback) ? 'Sửa' : 'Tạo' ?> Feedback Tour
                        </h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="?act=hdv-lich-cua-toi">Lịch của tôi</a></li>
                                <li class="breadcrumb-item"><a href="?act=view-tour-feedback&schedule_id=<?= $schedule['schedule_id'] ?>">Feedback</a></li>
                                <li class="breadcrumb-item active"><?= isset($feedback) ? 'Sửa' : 'Tạo mới' ?></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-body">
            <!-- Tour Info -->
            <div class="alert alert-info">
                <h5><i class="feather icon-info"></i> <?= htmlspecialchars($schedule['tour_name']) ?></h5>
                <p class="mb-0">
                    <strong>Mã tour:</strong> <?= $schedule['tour_code'] ?? 'N/A' ?> | 
                    <strong>Ngày khởi hành:</strong> <?= date('d/m/Y', strtotime($schedule['departure_date'])) ?>
                </p>
            </div>

            <form method="POST" action="?act=<?= isset($feedback) ? 'update-feedback' : 'create-feedback' ?>" enctype="multipart/form-data">
                <input type="hidden" name="schedule_id" value="<?= $schedule['schedule_id'] ?>">
                <?php if (isset($feedback)): ?>
                    <input type="hidden" name="feedback_id" value="<?= $feedback['feedback_id'] ?>">
                <?php endif; ?>

                <!-- Rating Section -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title"><i class="feather icon-star"></i> Đánh giá</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Overall Rating -->
                            <div class="col-md-4 col-12 mb-2">
                                <label>Đánh giá tổng quan <span class="text-danger">*</span></label>
                                <div class="rating-input">
                                    <?php for ($i = 5; $i >= 1; $i--): ?>
                                        <input type="radio" id="overall_<?= $i ?>" name="overall_rating" value="<?= $i ?>" 
                                               <?= (isset($feedback) && $feedback['overall_rating'] == $i) || (!isset($feedback) && $i == 5) ? 'checked' : '' ?>>
                                        <label for="overall_<?= $i ?>"><i class="feather icon-star"></i></label>
                                    <?php endfor; ?>
                                </div>
                            </div>

                            <!-- Service Rating -->
                            <div class="col-md-4 col-12 mb-2">
                                <label>Dịch vụ</label>
                                <div class="rating-input">
                                    <?php for ($i = 5; $i >= 1; $i--): ?>
                                        <input type="radio" id="service_<?= $i ?>" name="service_rating" value="<?= $i ?>"
                                               <?= (isset($feedback) && $feedback['service_rating'] == $i) ? 'checked' : '' ?>>
                                        <label for="service_<?= $i ?>"><i class="feather icon-star"></i></label>
                                    <?php endfor; ?>
                                </div>
                            </div>

                            <!-- Guide Rating -->
                            <div class="col-md-4 col-12 mb-2">
                                <label>Hướng dẫn viên</label>
                                <div class="rating-input">
                                    <?php for ($i = 5; $i >= 1; $i--): ?>
                                        <input type="radio" id="guide_<?= $i ?>" name="guide_rating" value="<?= $i ?>"
                                               <?= (isset($feedback) && $feedback['guide_rating'] == $i) ? 'checked' : '' ?>>
                                        <label for="guide_<?= $i ?>"><i class="feather icon-star"></i></label>
                                    <?php endfor; ?>
                                </div>
                            </div>

                            <!-- Food Rating -->
                            <div class="col-md-4 col-12 mb-2">
                                <label>Ẩm thực</label>
                                <div class="rating-input">
                                    <?php for ($i = 5; $i >= 1; $i--): ?>
                                        <input type="radio" id="food_<?= $i ?>" name="food_rating" value="<?= $i ?>"
                                               <?= (isset($feedback) && $feedback['food_rating'] == $i) ? 'checked' : '' ?>>
                                        <label for="food_<?= $i ?>"><i class="feather icon-star"></i></label>
                                    <?php endfor; ?>
                                </div>
                            </div>

                            <!-- Accommodation Rating -->
                            <div class="col-md-4 col-12 mb-2">
                                <label>Chỗ ở</label>
                                <div class="rating-input">
                                    <?php for ($i = 5; $i >= 1; $i--): ?>
                                        <input type="radio" id="accommodation_<?= $i ?>" name="accommodation_rating" value="<?= $i ?>"
                                               <?= (isset($feedback) && $feedback['accommodation_rating'] == $i) ? 'checked' : '' ?>>
                                        <label for="accommodation_<?= $i ?>"><i class="feather icon-star"></i></label>
                                    <?php endfor; ?>
                                </div>
                            </div>

                            <!-- Transportation Rating -->
                            <div class="col-md-4 col-12 mb-2">
                                <label>Phương tiện</label>
                                <div class="rating-input">
                                    <?php for ($i = 5; $i >= 1; $i--): ?>
                                        <input type="radio" id="transportation_<?= $i ?>" name="transportation_rating" value="<?= $i ?>"
                                               <?= (isset($feedback) && $feedback['transportation_rating'] == $i) ? 'checked' : '' ?>>
                                        <label for="transportation_<?= $i ?>"><i class="feather icon-star"></i></label>
                                    <?php endfor; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Feedback Content -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title"><i class="feather icon-message-square"></i> Nội dung phản hồi</h4>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Nhận xét chung</label>
                            <textarea class="form-control" name="feedback_text" rows="4" 
                                      placeholder="Mô tả trải nghiệm tổng quan của tour..."><?= isset($feedback) ? htmlspecialchars($feedback['feedback_text']) : '' ?></textarea>
                        </div>

                        <div class="form-group">
                            <label><i class="feather icon-thumbs-up text-success"></i> Điểm tích cực</label>
                            <textarea class="form-control" name="positive_points" rows="3" 
                                      placeholder="Những điểm tốt, khách hàng hài lòng..."><?= isset($feedback) ? htmlspecialchars($feedback['positive_points']) : '' ?></textarea>
                        </div>

                        <div class="form-group">
                            <label><i class="feather icon-alert-triangle text-warning"></i> Điểm cần cải thiện</label>
                            <textarea class="form-control" name="improvement_points" rows="3" 
                                      placeholder="Những điểm cần khắc phục, cải thiện..."><?= isset($feedback) ? htmlspecialchars($feedback['improvement_points']) : '' ?></textarea>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="recommend_to_others" 
                                       name="recommend_to_others" value="1" 
                                       <?= (!isset($feedback) || $feedback['recommend_to_others']) ? 'checked' : '' ?>>
                                <label class="custom-control-label" for="recommend_to_others">
                                    Khách hàng sẵn sàng giới thiệu tour cho người khác
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Images -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title"><i class="feather icon-image"></i> Hình ảnh</h4>
                    </div>
                    <div class="card-body">
                        <?php if (isset($feedback) && !empty($images)): ?>
                            <div class="row mb-3">
                                <?php foreach ($images as $image): ?>
                                    <div class="col-md-3 col-6 mb-2">
                                        <div class="position-relative">
                                            <img src="../<?= $image['file_path'] ?>" class="img-fluid rounded" alt="Feedback image">
                                            <a href="?act=delete-feedback-image&image_id=<?= $image['image_id'] ?>&feedback_id=<?= $feedback['feedback_id'] ?>" 
                                               onclick="return confirm('Xóa ảnh này?');"
                                               class="btn btn-sm btn-danger position-absolute" style="top: 5px; right: 15px;">
                                                <i class="feather icon-x"></i>
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <div class="form-group">
                            <label>Thêm hình ảnh mới</label>
                            <input type="file" class="form-control" name="images[]" accept="image/*" multiple>
                            <small class="form-text text-muted">Chọn nhiều ảnh cùng lúc (JPG, PNG, GIF, WEBP)</small>
                        </div>
                    </div>
                </div>

                <!-- Settings -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title"><i class="feather icon-settings"></i> Cài đặt</h4>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Trạng thái</label>
                            <select class="form-control" name="status">
                                <option value="Draft" <?= (isset($feedback) && $feedback['status'] === 'Draft') ? 'selected' : '' ?>>Nháp</option>
                                <option value="Published" <?= (!isset($feedback) || $feedback['status'] === 'Published') ? 'selected' : '' ?>>Xuất bản</option>
                            </select>
                        </div>

                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="is_public" name="is_public" value="1"
                                   <?= (!isset($feedback) || $feedback['is_public']) ? 'checked' : '' ?>>
                            <label class="custom-control-label" for="is_public">
                                Hiển thị công khai (cho phép hiển thị trên website)
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="card">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary">
                            <i class="feather icon-save"></i> <?= isset($feedback) ? 'Cập nhật' : 'Tạo' ?> Feedback
                        </button>
                        <a href="?act=view-tour-feedback&schedule_id=<?= $schedule['schedule_id'] ?>" class="btn btn-secondary">
                            <i class="feather icon-x"></i> Hủy
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- END: Content-->

<style>
.rating-input {
    display: flex;
    flex-direction: row-reverse;
    justify-content: flex-end;
}
.rating-input input {
    display: none;
}
.rating-input label {
    cursor: pointer;
    font-size: 24px;
    color: #ddd;
    margin: 0 3px;
}
.rating-input label:hover,
.rating-input label:hover ~ label,
.rating-input input:checked ~ label {
    color: #ffc107;
}
</style>

<?php require_once __DIR__ . '/../core/footer.php'; ?>
