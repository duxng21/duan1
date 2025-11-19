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
                        <h2 class="content-header-title float-left mb-0">Thêm nhân sự mới</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="?act=danh-sach-nhan-su">Danh sách nhân sự</a></li>
                                <li class="breadcrumb-item active">Thêm mới</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">
            <section id="add-staff">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Thông tin nhân sự</h4>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <form method="POST" action="?act=luu-nhan-su">
                                        <div class="row">
                                            <!-- Thông tin cơ bản -->
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="full_name">Họ tên <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="full_name"
                                                        name="full_name" required>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="staff_type">Loại nhân sự <span
                                                            class="text-danger">*</span></label>
                                                    <select class="form-control" id="staff_type" name="staff_type"
                                                        required>
                                                        <option value="">-- Chọn loại --</option>
                                                        <option value="Guide">Hướng dẫn viên</option>
                                                        <option value="Driver">Tài xế</option>
                                                        <option value="Support">Hỗ trợ</option>
                                                        <option value="Manager">Quản lý</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="phone">Điện thoại</label>
                                                    <input type="tel" class="form-control" id="phone" name="phone">
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="email">Email</label>
                                                    <input type="email" class="form-control" id="email" name="email">
                                                </div>
                                            </div>

                                            <!-- Giấy tờ -->
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="id_card">CMND/CCCD</label>
                                                    <input type="text" class="form-control" id="id_card" name="id_card">
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="license_number">Số bằng lái (Dành cho tài xế)</label>
                                                    <input type="text" class="form-control" id="license_number"
                                                        name="license_number">
                                                </div>
                                            </div>

                                            <!-- Kinh nghiệm & Ngôn ngữ -->
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="experience_years">Kinh nghiệm (năm)</label>
                                                    <input type="number" class="form-control" id="experience_years"
                                                        name="experience_years" min="0" value="0">
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="languages">Ngôn ngữ (phân cách bằng dấu phẩy)</label>
                                                    <input type="text" class="form-control" id="languages"
                                                        name="languages" placeholder="Tiếng Việt, English, 中文">
                                                </div>
                                            </div>

                                            <!-- Trạng thái -->
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input" id="status"
                                                            name="status" checked>
                                                        <label class="custom-control-label" for="status">Đang làm
                                                            việc</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Ghi chú -->
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="notes">Ghi chú</label>
                                                    <textarea class="form-control" id="notes" name="notes"
                                                        rows="4"></textarea>
                                                </div>
                                            </div>

                                            <!-- Buttons -->
                                            <div class="col-12">
                                                <button type="submit" class="btn btn-primary mr-1">
                                                    <i class="feather icon-save"></i> Lưu
                                                </button>
                                                <a href="?act=danh-sach-nhan-su" class="btn btn-outline-secondary">
                                                    <i class="feather icon-x"></i> Hủy
                                                </a>
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
<?php require_once __DIR__ . '/../core/footer.php'; ?>