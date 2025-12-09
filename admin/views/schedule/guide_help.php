<?php
/**
 * View: Hướng dẫn sử dụng và Hỗ trợ cho HDV
 * Cung cấp FAQ, hướng dẫn sử dụng, và thông tin liên hệ hỗ trợ
 */
?>
<?php require_once './views/core/header.php'; ?>
<?php require_once './views/core/menu.php'; ?>

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
                            <i class="feather icon-help-circle"></i> Trợ giúp & Hướng dẫn
                        </h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="?act=home-guide">Dashboard</a></li>
                                <li class="breadcrumb-item active">Trợ giúp
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-header-right text-md-right col-md-3 col-12 d-md-block d-none">
                <div class="form-group breadcrum-right">
                    <a href="?act=home-guide" class="btn btn-secondary btn-sm">
                        <i class="feather icon-arrow-left"></i> Quay lại
                    </a>
                </div>
            </div>
        </div>

        <div class="content-body">
            <div class="row">
                <!-- Liên hệ hỗ trợ -->
                <div class="col-md-4 mb-4">
                    <div class="card border-left-primary shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title text-primary">
                                <i class="feather icon-phone"></i> Liên hệ hỗ trợ
                            </h5>
                            <hr>
                            <div class="mb-3">
                                <strong>Hotline 24/7</strong>
                                <p class="mb-0">
                                    <i class="feather icon-phone text-success"></i>
                                    <a href="tel:1900xxxx">1900 xxxx</a>
                                </p>
                            </div>
                            <div class="mb-3">
                                <strong>Email hỗ trợ</strong>
                                <p class="mb-0">
                                    <i class="feather icon-mail text-info"></i>
                                    <a href="mailto:support@tourcompany.com">support@tourcompany.com</a>
                                </p>
                            </div>
                            <div class="mb-3">
                                <strong>Zalo/Telegram</strong>
                                <p class="mb-0">
                                    <i class="feather icon-message-square text-primary"></i>
                                    0909 xxx xxx
                                </p>
                            </div>
                            <div>
                                <strong>Giờ làm việc</strong>
                                <p class="mb-0">
                                    <i class="feather icon-clock text-warning"></i>
                                    Thứ 2 - Chủ nhật: 24/7
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Liên kết nhanh -->
                <div class="col-md-4 mb-4">
                    <div class="card border-left-success shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title text-success">
                                <i class="feather icon-link"></i> Liên kết nhanh
                            </h5>
                            <hr>
                            <div class="list-group list-group-flush">
                                <a href="?act=hdv-lich-cua-toi" class="list-group-item list-group-item-action">
                                    <i class="feather icon-calendar text-primary"></i> Lịch tour của tôi
                                </a>
                                <a href="?act=hdv-danh-sach-khach" class="list-group-item list-group-item-action">
                                    <i class="feather icon-users text-info"></i> Danh sách khách
                                </a>
                                <a href="?act=hdv-diem-danh" class="list-group-item list-group-item-action">
                                    <i class="feather icon-check-square text-success"></i> Điểm danh khách
                                </a>
                                <a href="?act=hdv-ho-so" class="list-group-item list-group-item-action">
                                    <i class="feather icon-user text-warning"></i> Hồ sơ của tôi
                                </a>
                                <a href="?act=hdv-thong-bao" class="list-group-item list-group-item-action">
                                    <i class="feather icon-bell text-danger"></i> Thông báo
                                </a>
                                <a href="?act=doi-mat-khau" class="list-group-item list-group-item-action">
                                    <i class="feather icon-lock text-secondary"></i> Đổi mật khẩu
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Thông tin khẩn cấp -->
                <div class="col-md-4 mb-4">
                    <div class="card border-left-danger shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title text-danger">
                                <i class="feather icon-alert-triangle"></i> Tình huống khẩn cấp
                            </h5>
                            <hr>
                            <div class="alert alert-danger">
                                <strong><i class="feather icon-phone"></i> SOS Hotline:</strong>
                                <h4 class="mb-0"><a href="tel:1900xxxx" class="text-danger">1900 xxxx</a></h4>
                            </div>
                            <p><strong>Khi cần hỗ trợ khẩn cấp:</strong></p>
                            <ul class="small">
                                <li>Tai nạn, sự cố y tế</li>
                                <li>Khách mất tích</li>
                                <li>Thay đổi lịch trình đột xuất</li>
                                <li>Vấn đề an ninh</li>
                                <li>Thiên tai, thời tiết xấu</li>
                            </ul>
                            <div class="mt-3">
                                <strong>Bệnh viện gần nhất:</strong>
                                <p class="mb-1">
                                    <i class="feather icon-map-pin text-danger"></i>
                                    <a href="tel:115">Cấp cứu: 115</a>
                                </p>
                                <p class="mb-0">
                                    <i class="feather icon-shield text-primary"></i>
                                    <a href="tel:113">Công an: 113</a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FAQ Accordion -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="feather icon-help-circle"></i>
                                Câu hỏi thường gặp (FAQ)
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="accordion" id="faqAccordion">

                                <!-- FAQ 1 -->
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="faq1">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapse1" aria-expanded="true">
                                            <i class="feather icon-help-circle text-primary me-2"></i>
                                            Làm thế nào để xem lịch tour được phân công?
                                        </button>
                                    </h2>
                                    <div id="collapse1" class="accordion-collapse collapse show"
                                        data-bs-parent="#faqAccordion">
                                        <div class="accordion-body">
                                            <p>Để xem lịch tour của bạn:</p>
                                            <ol>
                                                <li>Vào menu <strong>"Lịch tour của tôi"</strong> → <strong>"Danh sách
                                                        tour"</strong></li>
                                                <li>Sử dụng bộ lọc theo tháng, năm, trạng thái để tìm tour cụ thể</li>
                                                <li>Nhấp vào <strong>"Chi tiết"</strong> để xem thông tin đầy đủ về tour
                                                </li>
                                                <li>Hoặc chọn <strong>"Xem lịch tháng"</strong> để có cái nhìn tổng quan
                                                </li>
                                            </ol>
                                        </div>
                                    </div>
                                </div>

                                <!-- FAQ 2 -->
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="faq2">
                                        <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#collapse2">
                                            <i class="feather icon-help-circle text-primary me-2"></i>
                                            Làm thế nào để điểm danh khách?
                                        </button>
                                    </h2>
                                    <div id="collapse2" class="accordion-collapse collapse"
                                        data-bs-parent="#faqAccordion">
                                        <div class="accordion-body">
                                            <p>Quy trình điểm danh khách:</p>
                                            <ol>
                                                <li>Vào menu <strong>"Quản lý khách"</strong> → <strong>"Điểm danh
                                                        khách"</strong></li>
                                                <li>Chọn tour và ngày cần điểm danh</li>
                                                <li>Đánh dấu checkbox cho mỗi khách có mặt</li>
                                                <li>Ghi chú nếu khách vắng mặt hoặc đến muộn</li>
                                                <li>Nhấn <strong>"Lưu điểm danh"</strong> để hoàn tất</li>
                                            </ol>
                                            <p class="text-warning mb-0">
                                                <i class="feather icon-alert-triangle"></i>
                                                <strong>Lưu ý:</strong> Điểm danh càng sớm càng tốt, tốt nhất trước khi
                                                khởi
                                                hành.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- FAQ 3 -->
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="faq3">
                                        <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#collapse3">
                                            <i class="feather icon-help-circle text-primary me-2"></i>
                                            Tôi có thể xem thông tin khách trước khi tour khởi hành không?
                                        </button>
                                    </h2>
                                    <div id="collapse3" class="accordion-collapse collapse"
                                        data-bs-parent="#faqAccordion">
                                        <div class="accordion-body">
                                            <p>Có, bạn có thể xem danh sách khách bất cứ lúc nào:</p>
                                            <ul>
                                                <li>Vào <strong>"Quản lý khách"</strong> → <strong>"Danh sách
                                                        khách"</strong>
                                                </li>
                                                <li>Chọn tour cần xem</li>
                                                <li>Hệ thống hiển thị: họ tên, số điện thoại, yêu cầu đặc biệt, dị ứng,
                                                    ghi chú
                                                </li>
                                                <li>Bạn có thể xuất file PDF hoặc Excel để in ra</li>
                                            </ul>
                                            <p class="text-info mb-0">
                                                <i class="feather icon-info"></i>
                                                <strong>Mẹo:</strong> Nên xem danh sách khách trước 1-2 ngày để chuẩn bị
                                                tốt
                                                nhất.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- FAQ 4 -->
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="faq4">
                                        <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#collapse4">
                                            <i class="feather icon-help-circle text-primary me-2"></i>
                                            Tôi cần làm gì khi có sự cố trong tour?
                                        </button>
                                    </h2>
                                    <div id="collapse4" class="accordion-collapse collapse"
                                        data-bs-parent="#faqAccordion">
                                        <div class="accordion-body">
                                            <p><strong>Khi gặp sự cố, hãy thực hiện theo thứ tự:</strong></p>
                                            <ol>
                                                <li><strong class="text-danger">Ưu tiên 1:</strong> Đảm bảo an toàn cho
                                                    khách
                                                </li>
                                                <li><strong class="text-warning">Ưu tiên 2:</strong> Gọi hotline hỗ trợ
                                                    ngay lập
                                                    tức: <strong>1900 xxxx</strong></li>
                                                <li>Báo cáo chi tiết tình huống: địa điểm, mức độ nghiêm trọng, số người
                                                    ảnh
                                                    hưởng</li>
                                                <li>Chụp ảnh/video nếu có thể (để làm bằng chứng)</li>
                                                <li>Ghi nhật ký tour về sự cố trong hệ thống</li>
                                                <li>Thực hiện theo hướng dẫn từ bộ phận hỗ trợ</li>
                                            </ol>
                                            <div class="alert alert-danger mb-0">
                                                <i class="feather icon-alert-triangle"></i>
                                                <strong>Khẩn cấp:</strong> Với tai nạn y tế nghiêm trọng, gọi 115 (Cấp
                                                cứu)
                                                trước, sau đó báo công ty.
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- FAQ 5 -->
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="faq5">
                                        <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#collapse5">
                                            <i class="feather icon-help-circle text-primary me-2"></i>
                                            Làm sao để cập nhật hồ sơ cá nhân?
                                        </button>
                                    </h2>
                                    <div id="collapse5" class="accordion-collapse collapse"
                                        data-bs-parent="#faqAccordion">
                                        <div class="accordion-body">
                                            <p>Để cập nhật hồ sơ:</p>
                                            <ol>
                                                <li>Vào <strong>"Hồ sơ của tôi"</strong> từ menu</li>
                                                <li>Xem thông tin hiện tại: thông tin cá nhân, chứng chỉ, lịch sử tour
                                                </li>
                                                <li>Liên hệ với bộ phận nhân sự qua email hoặc hotline để yêu cầu cập
                                                    nhật thông
                                                    tin</li>
                                            </ol>
                                            <p class="text-info mb-0">
                                                <i class="feather icon-info"></i>
                                                <strong>Lưu ý:</strong> Chỉ admin/nhân sự mới có quyền chỉnh sửa thông
                                                tin nhân
                                                sự. HDV không thể tự chỉnh sửa.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- FAQ 6 -->
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="faq6">
                                        <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#collapse6">
                                            <i class="feather icon-help-circle text-primary me-2"></i>
                                            Tôi quên mật khẩu, phải làm sao?
                                        </button>
                                    </h2>
                                    <div id="collapse6" class="accordion-collapse collapse"
                                        data-bs-parent="#faqAccordion">
                                        <div class="accordion-body">
                                            <p>Nếu quên mật khẩu:</p>
                                            <ul>
                                                <li>Liên hệ hotline: <strong>1900 xxxx</strong></li>
                                                <li>Hoặc gửi email: <strong>support@tourcompany.com</strong></li>
                                                <li>Cung cấp: Họ tên, số điện thoại, CMND để xác minh</li>
                                                <li>Bộ phận IT sẽ reset mật khẩu và gửi cho bạn qua email/SMS</li>
                                            </ul>
                                            <p class="text-success mb-0">
                                                <i class="feather icon-check-circle"></i>
                                                <strong>Mẹo bảo mật:</strong> Sau khi nhận mật khẩu mới, hãy đổi ngay
                                                bằng mật
                                                khẩu riêng của bạn.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hướng dẫn sử dụng -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">
                                <i class="feather icon-book-open"></i>
                                Hướng dẫn sử dụng chi tiết
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0">
                                            <i class="feather icon-check-circle text-success"
                                                style="font-size: 2rem;"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6>1. Đăng nhập hệ thống</h6>
                                            <p class="text-muted">Sử dụng tài khoản được cấp từ công ty. Mật khẩu mặc
                                                định:
                                                <code>Guide@2025</code>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0">
                                            <i class="feather icon-calendar text-primary" style="font-size: 2rem;"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6>2. Xem lịch tour</h6>
                                            <p class="text-muted">Kiểm tra lịch hàng ngày, chuẩn bị trước tour 1-2 ngày
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0">
                                            <i class="feather icon-users text-info" style="font-size: 2rem;"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6>3. Xem thông tin khách</h6>
                                            <p class="text-muted">Tìm hiểu về khách: yêu cầu đặc biệt, dị ứng, ghi chú
                                                quan
                                                trọng</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0">
                                            <i class="feather icon-check-square text-success"
                                                style="font-size: 2rem;"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6>4. Điểm danh khách</h6>
                                            <p class="text-muted">Điểm danh trước khi khởi hành, ghi chú khách vắng/muộn
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0">
                                            <i class="feather icon-edit text-warning" style="font-size: 2rem;"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6>5. Ghi nhật ký tour</h6>
                                            <p class="text-muted">Ghi lại hoạt động, sự kiện, sự cố trong tour (nếu có)
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0">
                                            <i class="feather icon-bell text-danger" style="font-size: 2rem;"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6>6. Kiểm tra thông báo</h6>
                                            <p class="text-muted">Theo dõi thông báo từ công ty về thay đổi lịch, yêu
                                                cầu mới
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Download tài liệu -->
            <div class="row mt-4 mb-4">
                <div class="col-12">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <h5>
                                <i class="feather icon-download text-primary"></i>
                                Tài liệu hướng dẫn
                            </h5>
                            <p class="text-muted">Tải xuống tài liệu hướng dẫn chi tiết để sử dụng offline</p>
                            <div class="btn-group" role="group">
                                <button class="btn btn-primary" disabled>
                                    <i class="feather icon-file-text"></i> Hướng dẫn HDV (PDF)
                                </button>
                                <button class="btn btn-success" disabled>
                                    <i class="feather icon-video"></i> Video hướng dẫn
                                </button>
                                <button class="btn btn-info" disabled>
                                    <i class="feather icon-book"></i> Quy trình chuẩn
                                </button>
                            </div>
                            <p class="text-muted small mt-2 mb-0">
                                <i class="feather icon-info"></i> Liên hệ bộ phận IT để được cấp tài liệu
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END: Content-->

<?php require_once './views/core/footer.php'; ?>