<?php require_once './views/core/header.php'; ?>
<?php require_once './views/core/menu.php'; ?>

<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title">Thêm đối tác mới</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="?act=/">Trang chủ</a></li>
                                <li class="breadcrumb-item"><a href="?act=danh-sach-doi-tac">Đối tác</a></li>
                                <li class="breadcrumb-item active">Thêm mới</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-body">
            <?php require_once './views/core/alert.php'; ?>

            <section>
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Thông tin đối tác</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form method="POST" action="?act=luu-doi-tac">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Tên đối tác <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="partner_name" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Loại đối tác <span class="text-danger">*</span></label>
                                            <select class="form-control" name="partner_type" required>
                                                <option value="Restaurant">Nhà hàng</option>
                                                <option value="Hotel">Khách sạn</option>
                                                <option value="Transportation">Vận tải</option>
                                                <option value="Airline">Hàng không</option>
                                                <option value="Other">Khác</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Người liên hệ</label>
                                            <input type="text" class="form-control" name="contact_person">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Điện thoại</label>
                                            <input type="text" class="form-control" name="phone">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Email</label>
                                            <input type="email" class="form-control" name="email">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Địa chỉ</label>
                                    <textarea class="form-control" name="address" rows="2"></textarea>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Mã số thuế</label>
                                            <input type="text" class="form-control" name="tax_code">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Số tài khoản</label>
                                            <input type="text" class="form-control" name="bank_account">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Ngân hàng</label>
                                            <input type="text" class="form-control" name="bank_name">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Đánh giá (0-5)</label>
                                            <input type="number" class="form-control" name="rating" min="0" max="5" step="0.1" value="0">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
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
                                    <textarea class="form-control" name="notes" rows="3"></textarea>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="feather icon-save"></i> Lưu
                                    </button>
                                    <a href="?act=danh-sach-doi-tac" class="btn btn-secondary">
                                        <i class="feather icon-x"></i> Hủy
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<?php require_once './views/core/footer.php'; ?>
