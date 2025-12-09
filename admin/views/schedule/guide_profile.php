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
                            <i class="feather icon-user"></i> Hồ sơ cá nhân
                        </h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">Hồ sơ</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">
            <div class="row">
                <!-- Profile Card -->
                <div class="col-lg-4 col-md-5">
                    <div class="card">
                        <div class="card-body text-center py-4">
                            <?php if (!empty($guide_info['avatar'])): ?>
                                <img src="<?= htmlspecialchars($guide_info['avatar']) ?>" alt="Avatar"
                                    class="rounded-circle mb-3" width="120" height="120" style="object-fit: cover;">
                            <?php else: ?>
                                <div class="avatar bg-primary mx-auto mb-3" style="width: 120px; height: 120px;">
                                    <div class="avatar-content" style="font-size: 3rem;">
                                        <i class="feather icon-user"></i>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <h4 class="mb-1"><?= htmlspecialchars($guide_info['full_name'] ?? 'N/A') ?></h4>
                            <p class="text-muted mb-3">Hướng dẫn viên</p>

                            <div class="mb-3">
                                <span class="badge badge-primary badge-lg px-3 py-1">
                                    <i class="feather icon-star"></i>
                                    <?= number_format($guide_info['performance_rating'] ?? 0, 1) ?> / 5.0
                                </span>
                            </div>

                            <div class="row text-center mt-4">
                                <div class="col-6 border-right">
                                    <h4 class="text-primary mb-0"><?= $guide_info['total_tours'] ?? 0 ?></h4>
                                    <small class="text-muted">Tour đã dẫn</small>
                                </div>
                                <div class="col-6">
                                    <h4 class="text-success mb-0"><?= $guide_info['experience_years'] ?? 0 ?></h4>
                                    <small class="text-muted">Năm kinh nghiệm</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Info -->
                    <div class="card">
                        <div class="card-header pb-1">
                            <h5 class="card-title mb-0"><i class="feather icon-phone"></i> Thông tin liên hệ</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="text-muted small mb-1"><i class="feather icon-phone"></i> Điện
                                    thoại</label>
                                <p class="mb-0 font-weight-bold"><?= htmlspecialchars($guide_info['phone'] ?? 'N/A') ?>
                                </p>
                            </div>
                            <div class="mb-3">
                                <label class="text-muted small mb-1"><i class="feather icon-mail"></i> Email</label>
                                <p class="mb-0 font-weight-bold"><?= htmlspecialchars($guide_info['email'] ?? 'N/A') ?>
                                </p>
                            </div>
                            <div class="mb-0">
                                <label class="text-muted small mb-1"><i class="feather icon-map-pin"></i> Địa
                                    chỉ</label>
                                <p class="mb-0 font-weight-bold">
                                    <?= htmlspecialchars($guide_info['address'] ?? 'N/A') ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="col-lg-8 col-md-7">
                    <!-- Stats Cards -->
                    <div class="row mb-3">
                        <div class="col-sm-6 mb-3">
                            <div class="card mb-0">
                                <div class="card-body py-3">
                                    <div class="media d-flex align-items-center">
                                        <div class="avatar bg-light-primary mr-2">
                                            <div class="avatar-content">
                                                <i class="feather icon-calendar text-primary font-medium-4"></i>
                                            </div>
                                        </div>
                                        <div class="media-body text-right">
                                            <h3 class="text-primary mb-0"><?= $stats['upcoming'] ?? 0 ?></h3>
                                            <span class="text-muted">Tour sắp tới</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <div class="card mb-0">
                                <div class="card-body py-3">
                                    <div class="media d-flex align-items-center">
                                        <div class="avatar bg-light-success mr-2">
                                            <div class="avatar-content">
                                                <i class="feather icon-check-circle text-success font-medium-4"></i>
                                            </div>
                                        </div>
                                        <div class="media-body text-right">
                                            <h3 class="text-success mb-0"><?= $stats['completed_month'] ?? 0 ?></h3>
                                            <span class="text-muted">Hoàn thành tháng này</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabs -->
                    <div class="card">
                        <div class="card-body">
                            <ul class="nav nav-tabs nav-justified" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="info-tab" data-toggle="tab" href="#info" role="tab"
                                        aria-controls="info" aria-selected="true">
                                        <i class="feather icon-info"></i> Thông tin
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="skills-tab" data-toggle="tab" href="#skills" role="tab"
                                        aria-controls="skills" aria-selected="false">
                                        <i class="feather icon-award"></i> Kỹ năng
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="history-tab" data-toggle="tab" href="#history" role="tab"
                                        aria-controls="history" aria-selected="false">
                                        <i class="feather icon-clock"></i> Lịch sử
                                    </a>
                                </li>
                            </ul>

                            <div class="tab-content pt-3">
                                <!-- Info Tab -->
                                <div class="tab-pane active" id="info" role="tabpanel" aria-labelledby="info-tab">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="35%"><strong>CMND/CCCD:</strong></td>
                                            <td><?= htmlspecialchars($guide_info['id_card'] ?? 'N/A') ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Giấy phép HDV:</strong></td>
                                            <td><?= htmlspecialchars($guide_info['license_number'] ?? 'N/A') ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Ngôn ngữ:</strong></td>
                                            <td><?= htmlspecialchars($guide_info['languages'] ?? 'N/A') ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Chuyên môn:</strong></td>
                                            <td><?= htmlspecialchars($guide_info['specialization'] ?? 'N/A') ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Phân loại:</strong></td>
                                            <td>
                                                <?php
                                                $category = $guide_info['staff_category'] ?? '';
                                                switch ($category) {
                                                    case 'Domestic':
                                                        $categoryText = 'Nội địa';
                                                        break;
                                                    case 'International':
                                                        $categoryText = 'Quốc tế';
                                                        break;
                                                    default:
                                                        $categoryText = 'N/A';
                                                        break;
                                                }
                                                ?>
                                                <span class="badge badge-light"><?= $categoryText ?></span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>

                                <!-- Skills Tab -->
                                <div class="tab-pane" id="skills" role="tabpanel" aria-labelledby="skills-tab">
                                    <h5 class="mb-3">Kỹ năng & Chứng chỉ</h5>

                                    <?php if (!empty($certificates)): ?>
                                        <div class="list-group">
                                            <?php foreach ($certificates as $cert): ?>
                                                <div class="list-group-item">
                                                    <div class="d-flex justify-content-between">
                                                        <div>
                                                            <h6 class="mb-1">
                                                                <?= htmlspecialchars($cert['certificate_name']) ?>
                                                            </h6>
                                                            <p class="mb-0 text-muted small">
                                                                <i class="feather icon-calendar"></i>
                                                                <?= date('d/m/Y', strtotime($cert['issued_date'])) ?>
                                                                <?php if ($cert['expiry_date']): ?>
                                                                    - <?= date('d/m/Y', strtotime($cert['expiry_date'])) ?>
                                                                <?php endif; ?>
                                                            </p>
                                                        </div>
                                                        <div>
                                                            <?php
                                                            switch ($cert['status']) {
                                                                case 'Còn hạn':
                                                                    $statusClass = 'badge-success';
                                                                    break;
                                                                case 'Hết hạn':
                                                                    $statusClass = 'badge-danger';
                                                                    break;
                                                                case 'Sắp hết hạn':
                                                                    $statusClass = 'badge-warning';
                                                                    break;
                                                                default:
                                                                    $statusClass = 'badge-secondary';
                                                                    break;
                                                            }
                                                            ?>
                                                            <span class="badge <?= $statusClass ?>">
                                                                <?= htmlspecialchars($cert['status']) ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="text-center text-muted py-3">
                                            <i class="feather icon-award font-large-2"></i>
                                            <p>Chưa có chứng chỉ nào</p>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- History Tab -->
                                <div class="tab-pane" id="history" role="tabpanel" aria-labelledby="history-tab">
                                    <h5 class="mb-3">Lịch sử tour gần đây</h5>

                                    <?php if (!empty($tour_history)): ?>
                                        <div class="timeline timeline-one-side">
                                            <?php foreach ($tour_history as $history): ?>
                                                <div class="timeline-item">
                                                    <div class="timeline-point timeline-point-primary">
                                                        <i class="feather icon-map"></i>
                                                    </div>
                                                    <div class="timeline-event">
                                                        <div class="d-flex justify-content-between">
                                                            <h6><?= htmlspecialchars($history['tour_name']) ?></h6>
                                                            <small class="text-muted">
                                                                <?= date('d/m/Y', strtotime($history['departure_date'])) ?>
                                                            </small>
                                                        </div>
                                                        <p class="mb-0">
                                                            <i class="feather icon-users"></i>
                                                            <?= $history['number_of_guests'] ?> khách
                                                            <span class="mx-2">|</span>
                                                            <i class="feather icon-star"></i>
                                                            <?= number_format($history['customer_rating'] ?? 0, 1) ?> sao
                                                        </p>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="text-center text-muted py-3">
                                            <i class="feather icon-clock font-large-2"></i>
                                            <p>Chưa có lịch sử tour</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<!-- END: Content-->
<?php require_once __DIR__ . '/../core/footer.php'; ?>