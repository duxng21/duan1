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
            <section id="basic-vertical-layouts">
                <div class="row match-height">
                    <div class="col-md-6 col-12 mx-auto">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Thêm Booking Mới</h4>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <form action="?act=luu-tour" method="POST" enctype="multipart/form-data">
                                        <div class="form-group">
                                            <label for="tour_name">Tên khách hàng liên hệ <span class="text-danger">*</span></label>
                                            <input type="text" name="tour_name" id="tour_name" class="form-control"
                                                placeholder="Nhập tên tour" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="number">Số điện thoại <span class="text-danger">*</span></label>
                                            <input type="number" name="number" id="number" class="form-control"
                                                placeholder="Nhập số điện thoại">
                                        </div>

                                        <div class="form-group">
                                            <label for="email">Email</label>
                                            <input type="email" name="email" id="email" class="form-control"
                                                placeholder="Nhập email" required>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Số lượng khách (người lớn) <span class="text-danger">*</span></label>
                                                    <input type="number" class="form-control" placeholder="VD: 3" required>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Số lượng khách (trẻ em) <span class="text-danger">*</span></label>
                                                    <input type="number" class="form-control" placeholder="VD: 3" required>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="category_id">Loại booking <span class="text-danger">*</span></label>
                                            <select name="category_id" id="category_id" class="form-control" required>
                                                <option value="">-- Chọn --</option>
                                                <option value="">Theo đoàn</option>
                                                <option value="">Theo lẻ</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="category_id">Loại tour <span class="text-danger">*</span></label>
                                            <select name="category_id" id="category_id" class="form-control" required>
                                                <option value="">-- Chọn --</option>
                                                <option value="">Trong nước</option>
                                                <option value="">Quốc tế</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="description">Ghi chú</label>
                                            <textarea name="description" id="description"
                                                class="form-control" rows="2"
                                                placeholder="Ghi chú thêm điều gì đó?"></textarea>
                                        </div>

                                        <div class="form-group">
                                            <label for="status">Trạng thái</label>
                                            <select name="status" id="status" class="form-control">
                                                <option value="Draft">Nháp (Draft)</option>
                                                <option value="Public" selected>Công khai (Public)</option>
                                                <option value="Hidden">Ẩn (Hidden)</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary mr-1">
                                                <i class="feather icon-save"></i> Thêm Booking
                                            </button>
                                            <a href="/admin" class="btn btn-secondary">
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