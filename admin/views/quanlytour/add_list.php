<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/menu.php'; ?>
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
                                <h4 class="card-title">Thêm danh sách Tour</h4>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <form class="form form-vertical">
                                        <div class="form-body">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label for="first-name-vertical">Tên Tour</label>
                                                        <input type="text" id="name" class="form-control" name="name"
                                                            placeholder="Tên Tour">
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label for="email-id-vertical">Mã</label>
                                                        <input type="text" id="code" class="form-control" name="code"
                                                            placeholder="Mã">
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label for="contact-info-vertical">Danh mục</label>
                                                        <select class="form-control" id="danhmuc">
                                                            <option value="">--Chọn--</option>
                                                            <option value="Trong nước">Trong nước</option>
                                                            <option value="Quốc tế">Quốc tế</option>
                                                            <option value="Đà Lạt">Đà Lạt</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label for="email-id-vertical">Số ngày</label>
                                                        <input type="number" id="day" class="form-control" name="day"
                                                            placeholder="Số ngày">
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label for="email-id-vertical">Điểm xuất phát</label>
                                                        <input type="number" id="start" class="form-control"
                                                            name="start" placeholder="Diểm xuất phát">
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label for="contact-info-vertical">Trạng thái</label>
                                                        <select class="form-control" id="trangthai">
                                                            <option value="">--Chọn--</option>
                                                            <option value="Option 1">[Option 1]</option>
                                                            <option value="Option 2">[Option 2]</option>
                                                            <option value="Option 3">[Option 3]</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <button type="submit"
                                                        class="btn btn-primary mr-1 mb-1 waves-effect waves-light">Thêm</button>
                                                    <button type="reset"
                                                        class="btn btn-outline-warning mr-1 mb-1 waves-effect waves-light">Đặt
                                                        lại</button>
                                                </div>
                                            </div>
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
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>