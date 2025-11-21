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
                        <h2 class="content-header-title float-left mb-0">Chi tiết Tour: <?= htmlspecialchars($tour['tour_name'] ?? '') ?></h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">
            <!-- Thông báo -->
            <?php require_once __DIR__ . '/../core/alert.php'; ?>

            <!-- Tab Navigation -->
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="itinerary-tab" data-toggle="tab" href="#itinerary" role="tab">
                        <i class="feather icon-map"></i> Lịch trình
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="gallery-tab" data-toggle="tab" href="#gallery" role="tab">
                        <i class="feather icon-image"></i> Thư viện ảnh
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="policy-tab" data-toggle="tab" href="#policy" role="tab">
                        <i class="feather icon-file-text"></i> Chính sách
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="tags-tab" data-toggle="tab" href="#tags" role="tab">
                        <i class="feather icon-tag"></i> Tags
                    </a>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content">
                <!-- Lịch trình Tab -->
                <div class="tab-pane active" id="itinerary" role="tabpanel">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Lịch trình theo ngày</h4>
                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addItineraryModal">
                                <i class="feather icon-plus"></i> Thêm ngày
                            </button>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($itineraries)): ?>
                                <?php foreach ($itineraries as $itinerary): ?>
                                    <div class="card border-left-primary mb-2">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h5 class="mb-1">
                                                        <span class="badge badge-primary">Ngày <?= $itinerary['day_number'] ?></span>
                                                        <?= htmlspecialchars($itinerary['title']) ?>
                                                    </h5>
                                                    <p class="mb-0"><?= nl2br(htmlspecialchars($itinerary['description'])) ?></p>
                                                    <?php if ($itinerary['accommodation']): ?>
                                                        <small class="text-muted"><i class="feather icon-home"></i> Khách sạn: <?= htmlspecialchars($itinerary['accommodation']) ?></small>
                                                    <?php endif; ?>
                                                </div>
                                                <div>
                                                    <button class="btn btn-sm btn-warning" onclick="editItinerary(<?= $itinerary['itinerary_id'] ?>)">
                                                        <i class="feather icon-edit"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger" onclick="deleteItinerary(<?= $itinerary['itinerary_id'] ?>)">
                                                        <i class="feather icon-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-muted">Chưa có lịch trình nào.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Thư viện ảnh Tab -->
                <div class="tab-pane" id="gallery" role="tabpanel">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Thư viện ảnh</h4>
                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addGalleryModal">
                                <i class="feather icon-upload"></i> Thêm ảnh
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <?php if (!empty($gallery)): ?>
                                    <?php foreach ($gallery as $image): ?>
                                        <div class="col-md-3 col-sm-4 col-6 mb-3">
                                            <div class="card">
                                                <img src="<?= BASE_URL . $image['file_path'] ?>" class="card-img-top" alt="Tour image">
                                                <div class="card-body p-2">
                                                    <div class="d-flex justify-content-between">
                                                        <small><?= $image['is_featured'] ? '<span class="badge badge-success">Ảnh đại diện</span>' : '' ?></small>
                                                        <button class="btn btn-sm btn-danger" onclick="deleteImage(<?= $image['media_id'] ?>)">
                                                            <i class="feather icon-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p class="text-muted col-12">Chưa có ảnh nào.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Chính sách Tab -->
                <div class="tab-pane" id="policy" role="tabpanel">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Chính sách hủy/đổi tour</h4>
                        </div>
                        <div class="card-body">
                            <form action="?act=luu-chinh-sach&tour_id=<?= $tour['tour_id'] ?>" method="POST">
                                <div class="form-group">
                                    <label for="cancellation_policy">Chính sách hủy tour</label>
                                    <textarea name="cancellation_policy" id="cancellation_policy" class="form-control" rows="4" placeholder="VD: Hủy trước 7 ngày: hoàn 70%..."><?= htmlspecialchars($policies['cancellation_policy'] ?? '') ?></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="change_policy">Chính sách đổi tour</label>
                                    <textarea name="change_policy" id="change_policy" class="form-control" rows="4" placeholder="VD: Đổi tour phải trước 10 ngày..."><?= htmlspecialchars($policies['change_policy'] ?? '') ?></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="payment_policy">Chính sách thanh toán</label>
                                    <textarea name="payment_policy" id="payment_policy" class="form-control" rows="3" placeholder="VD: Đặt cọc 30% khi đăng ký..."><?= htmlspecialchars($policies['payment_policy'] ?? '') ?></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="note_policy">Lưu ý khác</label>
                                    <textarea name="note_policy" id="note_policy" class="form-control" rows="3" placeholder="Các lưu ý quan trọng khác..."><?= htmlspecialchars($policies['note_policy'] ?? '') ?></textarea>
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    <i class="feather icon-save"></i> Lưu chính sách
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Tags Tab -->
                <div class="tab-pane" id="tags" role="tabpanel">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Tags / Loại tour</h4>
                        </div>
                        <div class="card-body">
                            <form action="?act=luu-tags&tour_id=<?= $tour['tour_id'] ?>" method="POST">
                                <div class="form-group">
                                    <label>Chọn tags</label>
                                    <div class="row">
                                        <?php if (!empty($allTags)): ?>
                                            <?php foreach ($allTags as $tag): ?>
                                                <div class="col-md-3 col-sm-4 col-6">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input" 
                                                            id="tag_<?= $tag['tag_id'] ?>" 
                                                            name="tags[]" 
                                                            value="<?= $tag['tag_id'] ?>"
                                                            <?= in_array($tag['tag_id'], $tourTags ?? []) ? 'checked' : '' ?>>
                                                        <label class="custom-control-label" for="tag_<?= $tag['tag_id'] ?>">
                                                            <?= htmlspecialchars($tag['tag_name']) ?>
                                                        </label>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <p class="col-12 text-muted">Chưa có tag nào. <a href="?act=quan-ly-tags">Quản lý tags</a></p>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    <i class="feather icon-save"></i> Lưu tags
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-3">
                <a href="?act=list-tour" class="btn btn-secondary">
                    <i class="feather icon-arrow-left"></i> Quay lại danh sách
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Modal Thêm Lịch trình -->
<div class="modal fade" id="addItineraryModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm lịch trình</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="?act=them-lich-trinh&tour_id=<?= $tour['tour_id'] ?>" method="POST">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="day_number">Ngày thứ <span class="text-danger">*</span></label>
                        <input type="number" name="day_number" id="day_number" class="form-control" min="1" required>
                    </div>
                    <div class="form-group">
                        <label for="title">Tiêu đề <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="title" class="form-control" placeholder="VD: Khám phá Hà Nội" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Mô tả chi tiết</label>
                        <textarea name="description" id="description" class="form-control" rows="4" placeholder="Mô tả chi tiết các hoạt động trong ngày"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="accommodation">Khách sạn lưu trú</label>
                        <input type="text" name="accommodation" id="accommodation" class="form-control" placeholder="VD: Khách sạn 4 sao">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">Lưu</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Thêm Ảnh -->
<div class="modal fade" id="addGalleryModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm ảnh</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="?act=them-anh-tour&tour_id=<?= $tour['tour_id'] ?>" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="images">Chọn ảnh <span class="text-danger">*</span></label>
                        <input type="file" name="images[]" id="images" class="form-control" accept="image/*" multiple required>
                        <small class="text-muted">Có thể chọn nhiều ảnh cùng lúc</small>
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="is_featured" name="is_featured" value="1">
                            <label class="custom-control-label" for="is_featured">Đặt làm ảnh đại diện</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">Tải lên</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function deleteItinerary(id) {
    if (confirm('Bạn có chắc muốn xóa lịch trình này?')) {
        window.location.href = '?act=xoa-lich-trinh&id=' + id + '&tour_id=<?= $tour['tour_id'] ?>';
    }
}

function deleteImage(id) {
    if (confirm('Bạn có chắc muốn xóa ảnh này?')) {
        window.location.href = '?act=xoa-anh-tour&id=' + id + '&tour_id=<?= $tour['tour_id'] ?>';
    }
}

function editItinerary(id) {
    window.location.href = '?act=sua-lich-trinh&id=' + id + '&tour_id=<?= $tour['tour_id'] ?>';
}
</script>

<!-- END: Content-->
<?php require_once __DIR__ . '/../core/footer.php'; ?>
