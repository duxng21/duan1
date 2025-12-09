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
                                    <form method="POST" action="?act=luu-nhan-su" enctype="multipart/form-data">
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
                                                        <option value="Manager">Quản lý</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="date_of_birth">Ngày sinh</label>
                                                    <input type="date" class="form-control" id="date_of_birth"
                                                        name="date_of_birth">
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="gender">Giới tính</label>
                                                    <select class="form-control" id="gender" name="gender">
                                                        <option value="Nam" selected>Nam</option>
                                                        <option value="Nữ">Nữ</option>
                                                        <option value="Khác">Khác</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="avatar">Ảnh đại diện</label>
                                                    <input type="file" class="form-control-file" id="avatar"
                                                        name="avatar" accept="image/*">
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

                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="address">Địa chỉ</label>
                                                    <input type="text" class="form-control" id="address" name="address">
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
                                                    <label for="license_number">Số bằng HDV/Giấy phép hành nghề</label>
                                                    <input type="text" class="form-control" id="license_number"
                                                        name="license_number">
                                                </div>
                                            </div>

                                            <!-- Chuyên môn -->
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="staff_category">Phân loại</label>
                                                    <select class="form-control" id="staff_category"
                                                        name="staff_category">
                                                        <option value="Domestic" selected>Nội địa</option>
                                                        <option value="International">Quốc tế</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="group_specialty">Chuyên đoàn</label>
                                                    <select class="form-control" id="group_specialty"
                                                        name="group_specialty">
                                                        <option value="Both" selected>Cả hai</option>
                                                        <option value="Domestic">Nội địa</option>
                                                        <option value="International">Quốc tế</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="specialization">Chuyên môn</label>
                                                    <input type="text" class="form-control" id="specialization"
                                                        name="specialization"
                                                        placeholder="VD: Lịch sử, Văn hóa, Thiên nhiên">
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

                                            <!-- Sức khỏe -->
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="health_status">Tình trạng sức khỏe</label>
                                                    <select class="form-control" id="health_status"
                                                        name="health_status">
                                                        <option value="Good" selected>Tốt</option>
                                                        <option value="Average">Trung bình</option>
                                                        <option value="Weak">Yếu</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="health_notes">Ghi chú sức khỏe</label>
                                                    <input type="text" class="form-control" id="health_notes"
                                                        name="health_notes">
                                                </div>
                                            </div>

                                            <!-- Liên hệ khẩn cấp -->
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="emergency_contact">Người liên hệ khẩn cấp</label>
                                                    <input type="text" class="form-control" id="emergency_contact"
                                                        name="emergency_contact">
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="emergency_phone">SĐT khẩn cấp</label>
                                                    <input type="tel" class="form-control" id="emergency_phone"
                                                        name="emergency_phone">
                                                </div>
                                            </div>

                                            <!-- Thông tin ngân hàng -->
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="bank_account">Số tài khoản ngân hàng</label>
                                                    <input type="text" class="form-control" id="bank_account"
                                                        name="bank_account">
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="bank_name">Tên ngân hàng</label>
                                                    <input type="text" class="form-control" id="bank_name"
                                                        name="bank_name">
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