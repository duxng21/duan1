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
                            <i class="feather icon-copy"></i> Clone Tour
                        </h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="?act=dashboard">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="?act=list-tour">Danh sách Tour</a></li>
                                <li class="breadcrumb-item active">Clone Tour</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-body">
            <!-- Tour gốc info -->
            <section id="original-tour-info">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">
                            <i class="feather icon-file-text text-primary"></i> Tour gốc
                        </h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <?php if (!empty($tour['tour_image'])): ?>
                                        <img src="<?= htmlspecialchars($tour['tour_image']) ?>" 
                                             alt="<?= htmlspecialchars($tour['tour_name']) ?>" 
                                             class="img-fluid rounded">
                                    <?php else: ?>
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 200px;">
                                            <i class="feather icon-image" style="font-size: 48px; color: #ddd;"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-9">
                                    <h3><?= htmlspecialchars($tour['tour_name']) ?></h3>
                                    <p><strong>Mã tour:</strong> <?= htmlspecialchars($tour['code']) ?></p>
                                    <p><strong>Danh mục:</strong> <?= htmlspecialchars($tour['category_name'] ?? 'N/A') ?></p>
                                    <p><strong>Thời lượng:</strong> <?= $tour['duration_days'] ?? 0 ?> ngày <?= $tour['duration_nights'] ?? 0 ?> đêm</p>
                                    
                                    <div class="mt-2">
                                        <span class="badge badge-info">
                                            <i class="feather icon-list"></i> <?= $tour['itinerary_count'] ?? 0 ?> lịch trình
                                        </span>
                                        <span class="badge badge-success">
                                            <i class="feather icon-image"></i> <?= $tour['image_count'] ?? 0 ?> hình ảnh
                                        </span>
                                        <span class="badge badge-warning">
                                            <i class="feather icon-calendar"></i> <?= $tour['schedule_count'] ?? 0 ?> lịch khởi hành
                                        </span>
                                        <?php if (!empty($tour['has_policies'])): ?>
                                            <span class="badge badge-primary">
                                                <i class="feather icon-shield"></i> Có chính sách
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Clone form -->
            <section id="clone-form">
                <form action="?act=clone-tour" method="POST">
                    <input type="hidden" name="tour_id" value="<?= $tour['tour_id'] ?>">

                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">
                                <i class="feather icon-settings text-success"></i> Tùy chọn Clone
                            </h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="new_tour_name">
                                                Tên tour mới <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="new_tour_name" 
                                                   name="new_tour_name" 
                                                   value="<?= htmlspecialchars($tour['tour_name']) ?> (Bản sao)" 
                                                   required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="new_tour_code">
                                                Mã tour mới <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="new_tour_code" 
                                                   name="new_tour_code" 
                                                   value="<?= htmlspecialchars($tour['code']) ?>_COPY_<?= time() ?>" 
                                                   required>
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <h5 class="mb-2">Chọn các phần cần clone:</h5>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="custom-control custom-checkbox mb-2">
                                            <input type="checkbox" 
                                                   class="custom-control-input" 
                                                   id="clone_itinerary" 
                                                   name="clone_itinerary" 
                                                   <?= ($itineraries && count($itineraries) > 0) ? 'checked' : '' ?>>
                                            <label class="custom-control-label" for="clone_itinerary">
                                                <i class="feather icon-list text-info"></i> 
                                                <strong>Lịch trình</strong> (<?= count($itineraries ?? []) ?> ngày)
                                            </label>
                                        </div>

                                        <div class="custom-control custom-checkbox mb-2">
                                            <input type="checkbox" 
                                                   class="custom-control-input" 
                                                   id="clone_pricing" 
                                                   name="clone_pricing" 
                                                   <?= ($pricing && count($pricing) > 0) ? 'checked' : '' ?>>
                                            <label class="custom-control-label" for="clone_pricing">
                                                <i class="feather icon-dollar-sign text-success"></i> 
                                                <strong>Giá & Gói tour</strong> (<?= count($pricing ?? []) ?> gói)
                                            </label>
                                        </div>

                                        <div class="custom-control custom-checkbox mb-2">
                                            <input type="checkbox" 
                                                   class="custom-control-input" 
                                                   id="clone_images" 
                                                   name="clone_images" 
                                                   <?= ($images && count($images) > 0) ? 'checked' : '' ?>>
                                            <label class="custom-control-label" for="clone_images">
                                                <i class="feather icon-image text-warning"></i> 
                                                <strong>Hình ảnh</strong> (<?= count($images ?? []) ?> ảnh)
                                            </label>
                                            <small class="form-text text-muted">Chỉ copy link ảnh, không copy file</small>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="custom-control custom-checkbox mb-2">
                                            <input type="checkbox" 
                                                   class="custom-control-input" 
                                                   id="clone_suppliers" 
                                                   name="clone_suppliers" 
                                                   <?= ($suppliers && count($suppliers) > 0) ? 'checked' : '' ?>>
                                            <label class="custom-control-label" for="clone_suppliers">
                                                <i class="feather icon-briefcase text-primary"></i> 
                                                <strong>Nhà cung cấp</strong> (<?= count($suppliers ?? []) ?> NCC)
                                            </label>
                                        </div>

                                        <div class="custom-control custom-checkbox mb-2">
                                            <input type="checkbox" 
                                                   class="custom-control-input" 
                                                   id="clone_policies" 
                                                   name="clone_policies" 
                                                   <?= ($policies && count($policies) > 0) ? 'checked' : '' ?>>
                                            <label class="custom-control-label" for="clone_policies">
                                                <i class="feather icon-shield text-danger"></i> 
                                                <strong>Chính sách</strong> (<?= count($policies ?? []) ?> chính sách)
                                            </label>
                                        </div>

                                        <div class="custom-control custom-checkbox mb-2">
                                            <input type="checkbox" 
                                                   class="custom-control-input" 
                                                   id="clone_tags" 
                                                   name="clone_tags" 
                                                   checked>
                                            <label class="custom-control-label" for="clone_tags">
                                                <i class="feather icon-tag text-info"></i> 
                                                <strong>Tags</strong>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-info mt-3" role="alert">
                                    <h4 class="alert-heading">
                                        <i class="feather icon-info"></i> Lưu ý
                                    </h4>
                                    <ul class="mb-0">
                                        <li>Tour mới sẽ được tạo với trạng thái <strong>Draft</strong></li>
                                        <li>Lịch khởi hành <strong>KHÔNG</strong> được clone (cần tạo mới)</li>
                                        <li>Hình ảnh chỉ copy đường dẫn, không tạo file mới</li>
                                        <li>Bạn có thể chỉnh sửa tour sau khi clone hoàn tất</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <a href="?act=list-tour" class="btn btn-secondary">
                                        <i class="feather icon-x"></i> Hủy
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="feather icon-copy"></i> Clone Tour
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </section>
        </div>
    </div>
</div>
<!-- END: Content-->

<?php require_once __DIR__ . '/../core/footer.php'; ?>
