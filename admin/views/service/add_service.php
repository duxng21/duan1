<?php require_once './views/core/header.php'; ?>
<?php require_once './views/core/menu.php'; ?>

<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-header row">
            <h2 class="content-header-title">Thêm dịch vụ mới</h2>
            <div class="breadcrumb-wrapper">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="?act=/">Trang chủ</a></li>
                    <li class="breadcrumb-item"><a href="?act=danh-sach-dich-vu">Dịch vụ</a></li>
                    <li class="breadcrumb-item active">Thêm mới</li>
                </ol>
            </div>
        </div>

        <div class="content-body">
            <?php require_once './views/core/alert.php'; ?>

            <section>
                <div class="card">
                    <div class="card-header"><h4>Thông tin dịch vụ</h4></div>
                    <div class="card-body">
                        <form method="POST" action="?act=luu-dich-vu">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Tên dịch vụ <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="service_name" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Đối tác cung cấp <span class="text-danger">*</span></label>
                                        <select class="form-control" name="partner_id" required>
                                            <option value="">-- Chọn đối tác --</option>
                                            <?php foreach ($partners as $partner): ?>
                                                <option value="<?= $partner['partner_id'] ?>">
                                                    <?= htmlspecialchars($partner['partner_name']) ?> (<?= $partner['partner_type'] ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Loại dịch vụ <span class="text-danger">*</span></label>
                                        <select class="form-control" name="service_type" required>
                                            <option value="Restaurant">Nhà hàng</option>
                                            <option value="Hotel">Khách sạn</option>
                                            <option value="Vehicle">Phương tiện</option>
                                            <option value="Flight">Vé máy bay</option>
                                            <option value="Entrance">Vé tham quan</option>
                                            <option value="Insurance">Bảo hiểm</option>
                                            <option value="Other">Khác</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Đơn giá (VNĐ)</label>
                                        <input type="number" class="form-control" name="unit_price" min="0" step="1000" value="0">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Đơn vị tính</label>
                                        <input type="text" class="form-control" name="unit" value="pax" placeholder="pax, room, vehicle, ticket...">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Mô tả</label>
                                <textarea class="form-control" name="description" rows="3"></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Số lượng tối đa (Capacity)</label>
                                        <input type="number" class="form-control" name="capacity" min="0">
                                        <small class="form-text text-muted">Để trống nếu không giới hạn</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Địa điểm</label>
                                        <input type="text" class="form-control" name="location">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>SĐT liên hệ</label>
                                        <input type="text" class="form-control" name="contact_phone">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Nhà cung cấp</label>
                                        <input type="text" class="form-control" name="provider_name">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Đánh giá (0-5)</label>
                                        <input type="number" class="form-control" name="rating" min="0" max="5" step="0.1" value="0">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Trạng thái</label>
                                        <select class="form-control" name="status">
                                            <option value="1">Hoạt động</option>
                                            <option value="0">Ngừng hoạt động</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Ghi chú</label>
                                <textarea class="form-control" name="notes" rows="2"></textarea>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary"><i class="feather icon-save"></i> Lưu</button>
                                <a href="?act=danh-sach-dich-vu" class="btn btn-secondary"><i class="feather icon-x"></i> Hủy</a>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<?php require_once './views/core/footer.php'; ?>
