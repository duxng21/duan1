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
                        <h2 class="content-header-title float-left mb-0">Chỉnh sửa Tour:
                            <?= htmlspecialchars($tour['tour_name'] ?? 'N/A') ?>
                        </h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="?act=/">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="?act=list-tour">Danh sách Tour</a></li>
                                <li class="breadcrumb-item active">Chỉnh sửa</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-header-right text-md-right col-md-3 col-12 d-md-block d-none">
                <div class="form-group breadcrum-right">
                    <button type="button" class="btn btn-outline-primary" onclick="previewTour()">
                        <i class="feather icon-eye"></i> Xem trước
                    </button>
                </div>
            </div>
        </div>
        <div class="content-body">
            <!-- Thông báo -->
            <?php require_once __DIR__ . '/../core/alert.php'; ?>

            <!-- Form với Multi-Tab -->
            <form id="tourForm" action="?act=cap-nhat-tour-day-du&id=<?= $tour['tour_id'] ?>" method="POST"
                enctype="multipart/form-data">

                <!-- Action Buttons - Sticky Top -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span
                                    class="badge badge-<?= $tour['status'] === 'Public' ? 'success' : ($tour['status'] === 'Draft' ? 'warning' : 'secondary') ?>">
                                    <?= $tour['status'] ?>
                                </span>
                                <small class="text-muted ml-2">Cập nhật lần cuối:
                                    <?= date('d/m/Y H:i', strtotime($tour['created_at'] ?? 'now')) ?></small>
                            </div>
                            <div>
                                <a href="?act=list-tour" class="btn btn-secondary">
                                    <i class="feather icon-x"></i> Hủy
                                </a>
                                <button type="submit" name="action" value="draft" class="btn btn-warning">
                                    <i class="feather icon-save"></i> Lưu nháp
                                </button>
                                <button type="submit" name="action" value="publish" class="btn btn-success">
                                    <i class="feather icon-check"></i> Cập nhật & Công khai
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab Navigation -->
                <ul class="nav nav-tabs nav-justified" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="overview-tab" data-toggle="tab" href="#overview" role="tab">
                            <i class="feather icon-info"></i> Tổng quan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="itinerary-tab" data-toggle="tab" href="#itinerary" role="tab">
                            <i class="feather icon-map"></i> Lịch trình
                            <span class="badge badge-pill badge-primary"><?= count($itineraries ?? []) ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="pricing-tab" data-toggle="tab" href="#pricing" role="tab">
                            <i class="feather icon-dollar-sign"></i> Giá & Gói
                            <span class="badge badge-pill badge-primary"><?= count($prices ?? []) ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="media-tab" data-toggle="tab" href="#media" role="tab">
                            <i class="feather icon-image"></i> Hình ảnh
                            <span class="badge badge-pill badge-primary"><?= count($gallery ?? []) ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="policy-tab" data-toggle="tab" href="#policy" role="tab">
                            <i class="feather icon-file-text"></i> Chính sách
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="providers-tab" data-toggle="tab" href="#providers" role="tab">
                            <i class="feather icon-users"></i> Nhà cung cấp
                            <span class="badge badge-pill badge-primary"><?= count($providers ?? []) ?></span>
                        </a>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content">

                    <!-- TAB 1: TỔNG QUAN -->
                    <div class="tab-pane active" id="overview" role="tabpanel">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Thông tin cơ bản</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label for="tour_name">Tên Tour <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="tour_name" name="tour_name"
                                                value="<?= htmlspecialchars($tour['tour_name'] ?? '') ?>" required>
                                            <small class="form-text text-muted">Tên tour sẽ hiển thị công khai</small>
                                        </div>

                                        <div class="form-group">
                                            <label for="code">Mã Tour <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="code" name="code"
                                                value="<?= htmlspecialchars($tour['code'] ?? '') ?>" required>
                                            <small class="form-text text-muted">VD: HNSG3N2D, DLAT5N4D</small>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="category_id">Danh mục <span
                                                            class="text-danger">*</span></label>
                                                    <select class="form-control" id="category_id" name="category_id"
                                                        required>
                                                        <option value="">-- Chọn danh mục --</option>
                                                        <?php foreach ($categories as $cat): ?>
                                                            <option value="<?= $cat['category_id'] ?>"
                                                                <?= ($tour['category_id'] ?? '') == $cat['category_id'] ? 'selected' : '' ?>>
                                                                <?= htmlspecialchars($cat['category_name']) ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="duration_days">Thời lượng (ngày/đêm) <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="duration_days"
                                                        name="duration_days"
                                                        value="<?= htmlspecialchars($tour['duration_days'] ?? '') ?>"
                                                        placeholder="VD: 3N2Đ" required>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="start_location">Điểm khởi hành <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="start_location"
                                                name="start_location"
                                                value="<?= htmlspecialchars($tour['start_location'] ?? '') ?>"
                                                placeholder="VD: Hà Nội, TP. Hồ Chí Minh" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="description_short">Mô tả ngắn</label>
                                            <textarea class="form-control" id="description_short"
                                                name="description_short" rows="3"
                                                placeholder="Mô tả ngắn gọn về tour (1-2 câu)"><?= htmlspecialchars($tour['description_short'] ?? '') ?></textarea>
                                            <small class="form-text text-muted">Hiển thị trong danh sách tour</small>
                                        </div>

                                        <div class="form-group">
                                            <label for="description_full">Mô tả chi tiết</label>
                                            <textarea class="form-control" id="description_full" name="description_full"
                                                rows="6"
                                                placeholder="Mô tả đầy đủ về tour"><?= htmlspecialchars($tour['description_full'] ?? '') ?></textarea>
                                            <small class="form-text text-muted">Hỗ trợ HTML/Rich text</small>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Ảnh đại diện hiện tại</label>
                                            <?php if (!empty($tour['tour_image'])): ?>
                                                <img src="<?= BASE_URL . $tour['tour_image'] ?>"
                                                    class="img-fluid rounded mb-2" alt="Tour image">
                                            <?php else: ?>
                                                <div class="alert alert-info">Chưa có ảnh đại diện</div>
                                            <?php endif; ?>
                                        </div>

                                        <div class="form-group">
                                            <label for="status">Trạng thái</label>
                                            <select class="form-control" id="status" name="status">
                                                <option value="Draft" <?= ($tour['status'] ?? '') === 'Draft' ? 'selected' : '' ?>>Draft (Nháp)</option>
                                                <option value="Public" <?= ($tour['status'] ?? '') === 'Public' ? 'selected' : '' ?>>Public (Công khai)</option>
                                                <option value="Hidden" <?= ($tour['status'] ?? '') === 'Hidden' ? 'selected' : '' ?>>Hidden (Ẩn)</option>
                                            </select>
                                        </div>

                                        <div class="alert alert-warning">
                                            <h6>Lưu ý:</h6>
                                            <ul class="mb-0">
                                                <li>Các trường có <span class="text-danger">*</span> là bắt buộc</li>
                                                <li>Tour ở trạng thái Draft không hiển thị công khai</li>
                                                <li>Cần đủ thông tin để chuyển sang Public</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 2: LỊCH TRÌNH -->
                    <div class="tab-pane" id="itinerary" role="tabpanel">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Lịch trình chi tiết theo ngày</h4>
                                <button type="button" class="btn btn-primary btn-sm" onclick="addItineraryRow()">
                                    <i class="feather icon-plus"></i> Thêm ngày mới
                                </button>
                            </div>
                            <div class="card-body">
                                <div id="itinerary-container">
                                    <?php if (!empty($itineraries)): ?>
                                        <?php foreach ($itineraries as $idx => $itinerary): ?>
                                            <div class="card border-left-primary mb-3 itinerary-item"
                                                data-day="<?= $itinerary['day_number'] ?>">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between mb-2">
                                                        <h5><span class="badge badge-primary">Ngày
                                                                <?= $itinerary['day_number'] ?></span></h5>
                                                        <div>
                                                            <button type="button" class="btn btn-sm btn-secondary"
                                                                onclick="moveItinerary(this, 'up')">
                                                                <i class="feather icon-arrow-up"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-secondary"
                                                                onclick="moveItinerary(this, 'down')">
                                                                <i class="feather icon-arrow-down"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-danger"
                                                                onclick="removeItinerary(this)">
                                                                <i class="feather icon-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>

                                                    <input type="hidden" name="itinerary[<?= $idx ?>][itinerary_id]"
                                                        value="<?= $itinerary['itinerary_id'] ?>">
                                                    <input type="hidden" name="itinerary[<?= $idx ?>][day_number]"
                                                        value="<?= $itinerary['day_number'] ?>" class="day-number-input">

                                                    <div class="form-group">
                                                        <label>Tiêu đề ngày</label>
                                                        <input type="text" class="form-control"
                                                            name="itinerary[<?= $idx ?>][title]"
                                                            value="<?= htmlspecialchars($itinerary['title']) ?>"
                                                            placeholder="VD: Khám phá Hà Nội cổ kính">
                                                    </div>

                                                    <div class="form-group">
                                                        <label>Mô tả hoạt động</label>
                                                        <textarea class="form-control"
                                                            name="itinerary[<?= $idx ?>][description]" rows="3"
                                                            placeholder="Mô tả chi tiết các hoạt động trong ngày"><?= htmlspecialchars($itinerary['description']) ?></textarea>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Chỗ ở</label>
                                                                <input type="text" class="form-control"
                                                                    name="itinerary[<?= $idx ?>][accommodation]"
                                                                    value="<?= htmlspecialchars($itinerary['accommodation'] ?? '') ?>"
                                                                    placeholder="VD: Khách sạn Melia 4*">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Bữa ăn</label>
                                                                <input type="text" class="form-control"
                                                                    name="itinerary[<?= $idx ?>][meals]" value=""
                                                                    placeholder="VD: Sáng, Trưa, Tối">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="alert alert-info">
                                            Chưa có lịch trình. Nhấn "Thêm ngày mới" để bắt đầu.
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 3: GIÁ & GÓI -->
                    <div class="tab-pane" id="pricing" role="tabpanel">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Cấu hình giá và gói tour</h4>
                                <button type="button" class="btn btn-primary btn-sm" onclick="addPricePackage()">
                                    <i class="feather icon-plus"></i> Thêm gói giá
                                </button>
                            </div>
                            <div class="card-body">
                                <div id="pricing-container">
                                    <?php if (!empty($prices)): ?>
                                        <?php foreach ($prices as $idx => $price): ?>
                                            <div class="card border mb-3 price-package-item">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between mb-2">
                                                        <h5>Gói <?= $idx + 1 ?></h5>
                                                        <button type="button" class="btn btn-sm btn-danger"
                                                            onclick="removePricePackage(this)">
                                                            <i class="feather icon-trash"></i> Xóa gói
                                                        </button>
                                                    </div>

                                                    <input type="hidden" name="prices[<?= $idx ?>][price_id]"
                                                        value="<?= $price['price_id'] ?>">

                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Tên gói</label>
                                                                <input type="text" class="form-control"
                                                                    name="prices[<?= $idx ?>][package_name]"
                                                                    value="<?= htmlspecialchars($price['package_name'] ?? '') ?>"
                                                                    placeholder="VD: Tiêu chuẩn, Cao cấp, VIP">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Chiết khấu (%)</label>
                                                                <input type="number" class="form-control"
                                                                    name="prices[<?= $idx ?>][discount_percent]"
                                                                    value="<?= $price['discount_percent'] ?? 0 ?>" min="0"
                                                                    max="100">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label>Giá người lớn (VNĐ)</label>
                                                                <input type="number" class="form-control"
                                                                    name="prices[<?= $idx ?>][price_adult]"
                                                                    value="<?= $price['price_adult'] ?? 0 ?>" min="0">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label>Giá trẻ em (VNĐ)</label>
                                                                <input type="number" class="form-control"
                                                                    name="prices[<?= $idx ?>][price_child]"
                                                                    value="<?= $price['price_child'] ?? 0 ?>" min="0">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label>Giá em bé (VNĐ)</label>
                                                                <input type="number" class="form-control"
                                                                    name="prices[<?= $idx ?>][price_infant]"
                                                                    value="<?= $price['price_infant'] ?? 0 ?>" min="0">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Áp dụng từ ngày</label>
                                                                <input type="date" class="form-control"
                                                                    name="prices[<?= $idx ?>][valid_from]"
                                                                    value="<?= $price['valid_from'] ?? '' ?>">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Đến ngày</label>
                                                                <input type="date" class="form-control"
                                                                    name="prices[<?= $idx ?>][valid_to]"
                                                                    value="<?= $price['valid_to'] ?? '' ?>">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="alert alert-info">
                                            Chưa có gói giá nào. Nhấn "Thêm gói giá" để bắt đầu.
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 4: HÌNH ẢNH & MEDIA -->
                    <div class="tab-pane" id="media" role="tabpanel">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Quản lý hình ảnh & media</h4>
                                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal"
                                    data-target="#uploadMediaModal">
                                    <i class="feather icon-upload"></i> Upload ảnh
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="row" id="gallery-container">
                                    <?php if (!empty($gallery)): ?>
                                        <?php foreach ($gallery as $image): ?>
                                            <div class="col-md-3 col-sm-4 col-6 mb-3 gallery-item">
                                                <div class="card">
                                                    <img src="<?= BASE_URL . $image['file_path'] ?>" class="card-img-top"
                                                        alt="Tour image">
                                                    <div class="card-body p-2">
                                                        <input type="text" class="form-control form-control-sm mb-1"
                                                            placeholder="Caption..."
                                                            value="<?= htmlspecialchars($image['caption'] ?? '') ?>">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" class="custom-control-input"
                                                                    id="featured-<?= $image['media_id'] ?>"
                                                                    <?= $image['is_featured'] ? 'checked' : '' ?>>
                                                                <label class="custom-control-label"
                                                                    for="featured-<?= $image['media_id'] ?>">
                                                                    <small>Ảnh nổi bật</small>
                                                                </label>
                                                            </div>
                                                            <button type="button" class="btn btn-sm btn-danger"
                                                                onclick="deleteMedia(<?= $image['media_id'] ?>)">
                                                                <i class="feather icon-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="col-12">
                                            <div class="alert alert-info">Chưa có ảnh nào. Upload ảnh để bắt đầu.</div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 5: CHÍNH SÁCH -->
                    <div class="tab-pane" id="policy" role="tabpanel">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Chính sách đặt cọc, hủy, đổi, hoàn tiền</h4>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="cancellation_policy">Chính sách hủy tour</label>
                                    <textarea class="form-control" id="cancellation_policy" name="cancellation_policy"
                                        rows="4"
                                        placeholder="VD: Hủy trước 15 ngày: hoàn 80%, Hủy trước 7 ngày: hoàn 50%..."><?= htmlspecialchars($policies['cancellation_policy'] ?? '') ?></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="change_policy">Chính sách đổi tour/ngày khởi hành</label>
                                    <textarea class="form-control" id="change_policy" name="change_policy" rows="4"
                                        placeholder="VD: Đổi tour phải trước 10 ngày, đổi ngày phải trước 7 ngày..."><?= htmlspecialchars($policies['change_policy'] ?? '') ?></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="payment_policy">Chính sách thanh toán</label>
                                    <textarea class="form-control" id="payment_policy" name="payment_policy" rows="3"
                                        placeholder="VD: Đặt cọc 30% khi đăng ký, thanh toán 70% còn lại trước 3 ngày..."><?= htmlspecialchars($policies['payment_policy'] ?? '') ?></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="note_policy">Lưu ý & điều kiện khác</label>
                                    <textarea class="form-control" id="note_policy" name="note_policy" rows="3"
                                        placeholder="Các lưu ý quan trọng khác..."><?= htmlspecialchars($policies['note_policy'] ?? '') ?></textarea>
                                </div>

                                <div class="alert alert-warning">
                                    <i class="feather icon-alert-triangle"></i>
                                    <strong>Cảnh báo:</strong> Hệ thống sẽ kiểm tra mâu thuẫn trong chính sách. Ví dụ:
                                    "Hoàn tiền 100%" nhưng ghi "Không hoàn tiền" sẽ được cảnh báo.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 6: NHÀ CUNG CẤP -->
                    <div class="tab-pane" id="providers" role="tabpanel">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Nhà cung cấp & Dịch vụ liên kết</h4>
                                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal"
                                    data-target="#addProviderModal">
                                    <i class="feather icon-plus"></i> Thêm nhà cung cấp
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Loại dịch vụ</th>
                                                <th>Nhà cung cấp</th>
                                                <th>Liên hệ</th>
                                                <th>Giá đối tác</th>
                                                <th>Ghi chú hợp đồng</th>
                                                <th>Hành động</th>
                                            </tr>
                                        </thead>
                                        <tbody id="providers-tbody">
                                            <?php if (!empty($providers)): ?>
                                                <?php foreach ($providers as $provider): ?>
                                                    <tr>
                                                        <td>
                                                            <span class="badge badge-info">
                                                                <?php
                                                                switch ($provider['service_type']) {
                                                                    case 'Hotel':
                                                                        echo 'Khách sạn';
                                                                        break;
                                                                    case 'Restaurant':
                                                                        echo 'Nhà hàng';
                                                                        break;
                                                                    case 'Transport':
                                                                        echo 'Vận chuyển';
                                                                        break;
                                                                    case 'Flight':
                                                                        echo 'Máy bay';
                                                                        break;
                                                                    default:
                                                                        echo $provider['service_type'];
                                                                        break;
                                                                }
                                                                ?>
                                                            </span>
                                                        </td>
                                                        <td><?= htmlspecialchars($provider['provider_name']) ?></td>
                                                        <td>
                                                            <?= htmlspecialchars($provider['contact_phone'] ?? 'N/A') ?><br>
                                                            <small><?= htmlspecialchars($provider['contact_email'] ?? '') ?></small>
                                                        </td>
                                                        <td><?= number_format($provider['default_price'] ?? 0) ?> VNĐ</td>
                                                        <td><small><?= htmlspecialchars($provider['notes'] ?? '') ?></small>
                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-sm btn-danger"
                                                                onclick="removeProvider(<?= $provider['service_id'] ?>)">
                                                                <i class="feather icon-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted">Chưa có nhà cung cấp nào
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Bottom Action Buttons -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-end">
                            <a href="?act=list-tour" class="btn btn-secondary mr-2">
                                <i class="feather icon-x"></i> Hủy
                            </a>
                            <button type="submit" name="action" value="draft" class="btn btn-warning mr-2">
                                <i class="feather icon-save"></i> Lưu nháp
                            </button>
                            <button type="submit" name="action" value="publish" class="btn btn-success">
                                <i class="feather icon-check"></i> Cập nhật & Công khai
                            </button>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>
<!-- END: Content-->

<!-- JavaScript -->
<script>
    // Add itinerary row dynamically
    let itineraryIndex = <?= count($itineraries ?? []) ?>;
    function addItineraryRow() {
        const container = document.getElementById('itinerary-container');
        const dayNumber = container.querySelectorAll('.itinerary-item').length + 1;

        const html = `
        <div class="card border-left-primary mb-3 itinerary-item" data-day="${dayNumber}">
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <h5><span class="badge badge-primary">Ngày ${dayNumber}</span></h5>
                    <div>
                        <button type="button" class="btn btn-sm btn-secondary" onclick="moveItinerary(this, 'up')">
                            <i class="feather icon-arrow-up"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-secondary" onclick="moveItinerary(this, 'down')">
                            <i class="feather icon-arrow-down"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-danger" onclick="removeItinerary(this)">
                            <i class="feather icon-trash"></i>
                        </button>
                    </div>
                </div>

                <input type="hidden" name="itinerary[${itineraryIndex}][day_number]" value="${dayNumber}" class="day-number-input">

                <div class="form-group">
                    <label>Tiêu đề ngày</label>
                    <input type="text" class="form-control" name="itinerary[${itineraryIndex}][title]" placeholder="VD: Khám phá Hà Nội cổ kính">
                </div>

                <div class="form-group">
                    <label>Mô tả hoạt động</label>
                    <textarea class="form-control" name="itinerary[${itineraryIndex}][description]" rows="3"></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Chỗ ở</label>
                            <input type="text" class="form-control" name="itinerary[${itineraryIndex}][accommodation]" placeholder="VD: Khách sạn Melia 4*">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Bữa ăn</label>
                            <input type="text" class="form-control" name="itinerary[${itineraryIndex}][meals]" placeholder="VD: Sáng, Trưa, Tối">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;

        container.insertAdjacentHTML('beforeend', html);
        itineraryIndex++;
    }

    function removeItinerary(btn) {
        if (confirm('Bạn có chắc muốn xóa lịch trình ngày này?')) {
            btn.closest('.itinerary-item').remove();
            reorderItineraryDays();
        }
    }

    function moveItinerary(btn, direction) {
        const item = btn.closest('.itinerary-item');
        if (direction === 'up') {
            const prev = item.previousElementSibling;
            if (prev && prev.classList.contains('itinerary-item')) {
                item.parentNode.insertBefore(item, prev);
            }
        } else {
            const next = item.nextElementSibling;
            if (next && next.classList.contains('itinerary-item')) {
                item.parentNode.insertBefore(next, item);
            }
        }
        reorderItineraryDays();
    }

    function reorderItineraryDays() {
        const items = document.querySelectorAll('.itinerary-item');
        items.forEach((item, index) => {
            const dayNumber = index + 1;
            item.setAttribute('data-day', dayNumber);
            item.querySelector('.badge').textContent = 'Ngày ' + dayNumber;
            item.querySelector('.day-number-input').value = dayNumber;
        });
    }

    // Add price package
    let priceIndex = <?= count($prices ?? []) ?>;
    function addPricePackage() {
        const container = document.getElementById('pricing-container');
        const html = `
        <div class="card border mb-3 price-package-item">
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <h5>Gói mới</h5>
                    <button type="button" class="btn btn-sm btn-danger" onclick="removePricePackage(this)">
                        <i class="feather icon-trash"></i> Xóa gói
                    </button>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Tên gói</label>
                            <input type="text" class="form-control" name="prices[${priceIndex}][package_name]" placeholder="VD: Tiêu chuẩn, Cao cấp, VIP">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Chiết khấu (%)</label>
                            <input type="number" class="form-control" name="prices[${priceIndex}][discount_percent]" value="0" min="0" max="100">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Giá người lớn (VNĐ)</label>
                            <input type="number" class="form-control" name="prices[${priceIndex}][price_adult]" value="0" min="0">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Giá trẻ em (VNĐ)</label>
                            <input type="number" class="form-control" name="prices[${priceIndex}][price_child]" value="0" min="0">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Giá em bé (VNĐ)</label>
                            <input type="number" class="form-control" name="prices[${priceIndex}][price_infant]" value="0" min="0">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Áp dụng từ ngày</label>
                            <input type="date" class="form-control" name="prices[${priceIndex}][valid_from]">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Đến ngày</label>
                            <input type="date" class="form-control" name="prices[${priceIndex}][valid_to]">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
        container.insertAdjacentHTML('beforeend', html);
        priceIndex++;
    }

    function removePricePackage(btn) {
        if (confirm('Bạn có chắc muốn xóa gói giá này?')) {
            btn.closest('.price-package-item').remove();
        }
    }

    function previewTour() {
        // TODO: Implement preview functionality
        alert('Chức năng xem trước đang được phát triển');
    }

    // Form validation before submit
    document.getElementById('tourForm').addEventListener('submit', function (e) {
        const tourName = document.getElementById('tour_name').value.trim();
        const code = document.getElementById('code').value.trim();
        const categoryId = document.getElementById('category_id').value;
        const durationDays = document.getElementById('duration_days').value.trim();
        const startLocation = document.getElementById('start_location').value.trim();

        if (!tourName || !code || !categoryId || !durationDays || !startLocation) {
            e.preventDefault();
            alert('Vui lòng điền đầy đủ các trường bắt buộc ở tab Tổng quan!');
            // Switch to overview tab
            document.getElementById('overview-tab').click();
            return false;
        }
    });
</script>

<?php require_once __DIR__ . '/../core/footer.php'; ?>