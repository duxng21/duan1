<?php require_once __DIR__ . '/../core/header.php'; ?>
<?php require_once __DIR__ . '/../core/menu.php'; ?>
<?php require_once __DIR__ . '/../core/alert.php'; ?>
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row"></div>
        <div class="content-body">
            <section class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Tạo phiên bản mới cho tour:
                        <?= htmlspecialchars($tour['tour_name'] ?? ('#' . $tour['tour_id'])) ?></h4>
                    <a class="btn btn-outline-secondary"
                        href="?act=quan-ly-phien-ban&tour_id=<?= (int) $tour['tour_id'] ?>">← Quay lại</a>
                </div>
                <div class="card-content">
                    <div class="card-body">
                        <form method="POST" action="?act=tao-phien-ban">
                            <input type="hidden" name="tour_id" value="<?= (int) $tour['tour_id'] ?>" />

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Tên phiên bản</label>
                                        <input type="text" name="version_name" class="form-control" required />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Loại</label>
                                        <select name="version_type" class="form-control">
                                            <option value="season">Mùa</option>
                                            <option value="promo">Khuyến mãi</option>
                                            <option value="special">Đặc biệt</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Trạng thái hiển thị</label><br />
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" name="status" id="status"
                                                class="custom-control-input" checked>
                                            <label class="custom-control-label" for="status">Hiện</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Từ ngày</label>
                                        <input type="date" name="start_date" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Đến ngày</label>
                                        <input type="date" name="end_date" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Kích hoạt</label><br />
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" name="is_active" id="is_active"
                                                class="custom-control-input">
                                            <label class="custom-control-label" for="is_active">Kích hoạt ngay</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Lên lịch kích hoạt (tùy chọn)</label>
                                        <input type="datetime-local" name="scheduled_at" class="form-control" />
                                        <input type="hidden" name="activation_mode" value="manual" />
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Mô tả khác biệt</label>
                                <textarea name="description" class="form-control" rows="3"
                                    placeholder="Ghi chú ngắn: thay đổi giá, dịch vụ, lịch trình ..."></textarea>
                            </div>

                            <hr />
                            <h5>Clone dữ liệu</h5>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Nguồn clone</label>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" id="clone_none" name="clone_mode" value="none"
                                                class="custom-control-input" checked>
                                            <label class="custom-control-label" for="clone_none">Không clone</label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" id="clone_tour" name="clone_mode" value="tour"
                                                class="custom-control-input">
                                            <label class="custom-control-label" for="clone_tour">Clone từ tour
                                                gốc</label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" id="clone_version" name="clone_mode" value="version"
                                                class="custom-control-input" <?= (isset($_GET['clone_mode']) && $_GET['clone_mode'] === 'version') ? 'checked' : '' ?>>
                                            <label class="custom-control-label" for="clone_version">Clone từ phiên bản
                                                khác</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Chọn phiên bản nguồn (nếu clone từ phiên bản)</label>
                                        <select name="source_version_id" class="form-control">
                                            <option value="">-- Chọn phiên bản --</option>
                                            <?php foreach ($versions as $v): ?>
                                                <option value="<?= (int) $v['version_id'] ?>"
                                                    <?= (isset($_GET['source_version_id']) && (int) $_GET['source_version_id'] == (int) $v['version_id']) ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars(($v['version_name'] ?? '') . ' (' . ($v['version_type'] ?? '') . ')') ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Thành phần cần clone</label>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="clone_itinerary"
                                                name="clone_itinerary" checked>
                                            <label class="custom-control-label" for="clone_itinerary">Lịch trình</label>
                                        </div>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="clone_pricing"
                                                name="clone_pricing" checked>
                                            <label class="custom-control-label" for="clone_pricing">Giá</label>
                                        </div>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="clone_images"
                                                name="clone_images" checked>
                                            <label class="custom-control-label" for="clone_images">Hình ảnh</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-2">
                                <button type="submit" class="btn btn-primary"><i class="feather icon-save"></i> Lưu
                                    phiên bản</button>
                                <a class="btn btn-outline-secondary"
                                    href="?act=quan-ly-phien-ban&tour_id=<?= (int) $tour['tour_id'] ?>">Hủy</a>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../core/footer.php'; ?>