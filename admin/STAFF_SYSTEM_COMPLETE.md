# HỆ THỐNG QUẢN LÝ NHÂN SỰ MỞ RỘNG - HOÀN TẤT

## ✅ ĐÃ HOÀN THÀNH

### 1. DATABASE (✓ Đã chạy thành công)
- ✓ Mở rộng bảng `staff` với 15 cột mới
- ✓ Tạo 7 bảng phụ: 
  - `staff_certificates` - Quản lý chứng chỉ
  - `staff_languages` - Ngôn ngữ sử dụng
  - `staff_tour_history` - Lịch sử dẫn tour
  - `staff_time_off` - Lịch nghỉ/bận
  - `staff_evaluations` - Đánh giá định kỳ
  - `staff_experiences` - Kinh nghiệm làm việc
  - `staff_notifications` - Thông báo

### 2. MODELS (✓ Hoàn tất)
**File: `admin/models/Staff.php`**
- ✓ Cập nhật `create()` và `update()` với 23 trường
- ✓ Thêm 30+ methods mới:

**Chứng chỉ:**
- `getCertificates($staff_id)` - Lấy danh sách chứng chỉ
- `addCertificate($data)` - Thêm chứng chỉ mới
- `updateCertificate($id, $data)` - Cập nhật chứng chỉ
- `deleteCertificate($id)` - Xóa chứng chỉ
- `getExpiringCertificates($days)` - Lấy chứng chỉ sắp hết hạn

**Ngôn ngữ:**
- `getLanguages($staff_id)` - Lấy danh sách ngôn ngữ
- `addLanguage($data)` - Thêm ngôn ngữ
- `deleteLanguage($id)` - Xóa ngôn ngữ

**Lịch nghỉ/bận:**
- `getTimeOff($staff_id, $status)` - Lấy danh sách lịch nghỉ
- `addTimeOff($data)` - Đăng ký nghỉ (có kiểm tra trùng)
- `approveTimeOff($id, $approved_by, $notes)` - Duyệt lịch nghỉ
- `rejectTimeOff($id, $notes)` - Từ chối lịch nghỉ
- `checkTimeOffConflict($staff_id, $from, $to)` - Kiểm tra trùng lịch tour

**Lịch sử tour:**
- `getTourHistory($staff_id, $limit)` - Lịch sử các tour đã dẫn
- `addTourHistory($data)` - Thêm lịch sử tour
- `updateTourHistory($id, $data)` - Cập nhật đánh giá

**Đánh giá hiệu suất:**
- `getEvaluations($staff_id)` - Lấy danh sách đánh giá
- `addEvaluation($data)` - Thêm đánh giá (tự động tính điểm TB, cập nhật `performance_rating`)
- `getPerformanceStats($staff_id)` - Thống kê hiệu suất tổng quan

**Kinh nghiệm:**
- `getExperiences($staff_id)` - Lấy kinh nghiệm làm việc
- `addExperience($data)` - Thêm kinh nghiệm
- `deleteExperience($id)` - Xóa kinh nghiệm

**Phân loại & tìm kiếm:**
- `getStaffByCategory($category)` - Lọc theo Nội địa/Quốc tế
- `getStaffBySpecialization($specialization)` - Tìm theo chuyên tuyến

### 3. CONTROLLERS (✓ Hoàn tất)

**File: `admin/controllers/StaffController.php`** (Đã cập nhật)
- ✓ `StoreStaff()` - Thêm mới với 23 trường + upload avatar
- ✓ `UpdateStaff()` - Cập nhật với 23 trường + upload avatar

**File: `admin/controllers/StaffExtendedController.php`** (Mới tạo)
15 methods quản lý chi tiết:

**Chứng chỉ:**
- `ManageCertificates()` - Trang quản lý chứng chỉ
- `AddCertificate()` - Thêm chứng chỉ + upload file
- `DeleteCertificate()` - Xóa chứng chỉ

**Ngôn ngữ:**
- `ManageLanguages()` - Trang quản lý ngôn ngữ
- `AddLanguage()` - Thêm ngôn ngữ
- `DeleteLanguage()` - Xóa ngôn ngữ

**Lịch nghỉ:**
- `ManageTimeOff()` - Trang quản lý lịch nghỉ
- `AddTimeOff()` - Đăng ký nghỉ + upload file
- `ApproveTimeOff()` - Duyệt lịch nghỉ
- `RejectTimeOff()` - Từ chối lịch nghỉ

**Lịch sử tour:**
- `TourHistory()` - Xem lịch sử + thống kê
- `UpdateTourHistory()` - Cập nhật đánh giá từng tour

**Đánh giá:**
- `ManageEvaluations()` - Quản lý đánh giá định kỳ
- `AddEvaluation()` - Thêm đánh giá (6 tiêu chí)

**Dashboard:**
- `PerformanceDashboard()` - Báo cáo hiệu suất tổng quan

### 4. ROUTES (✓ Đã thêm vào index.php)
```php
// Quản lý chứng chỉ
'quan-ly-chung-chi' => StaffExtendedController->ManageCertificates()
'them-chung-chi' => StaffExtendedController->AddCertificate()
'xoa-chung-chi' => StaffExtendedController->DeleteCertificate()

// Quản lý ngôn ngữ
'quan-ly-ngon-ngu' => StaffExtendedController->ManageLanguages()
'them-ngon-ngu' => StaffExtendedController->AddLanguage()
'xoa-ngon-ngu' => StaffExtendedController->DeleteLanguage()

// Quản lý lịch nghỉ
'quan-ly-lich-nghi' => StaffExtendedController->ManageTimeOff()
'them-lich-nghi' => StaffExtendedController->AddTimeOff()
'duyet-lich-nghi' => StaffExtendedController->ApproveTimeOff()
'tu-choi-lich-nghi' => StaffExtendedController->RejectTimeOff()

// Lịch sử tour & đánh giá
'lich-su-tour' => StaffExtendedController->TourHistory()
'cap-nhat-lich-su-tour' => StaffExtendedController->UpdateTourHistory()
'quan-ly-danh-gia' => StaffExtendedController->ManageEvaluations()
'them-danh-gia' => StaffExtendedController->AddEvaluation()

// Dashboard
'dashboard-hieu-suat' => StaffExtendedController->PerformanceDashboard()
```

## 📋 CHỨC NĂNG CHI TIẾT

### 1. Hồ sơ chi tiết HDV
**Bảng staff đã mở rộng bao gồm:**
- ✓ Thông tin cá nhân: họ tên, ngày sinh, giới tính, ảnh đại diện
- ✓ Liên hệ: điện thoại, email, địa chỉ, liên hệ khẩn cấp
- ✓ Giấy tờ: CMND/CCCD, giấy phép HDV, kinh nghiệm (năm)
- ✓ Sức khỏe: tình trạng sức khỏe (Tốt/Khá/TB/Yếu), ghi chú sức khỏe
- ✓ Tài chính: số tài khoản ngân hàng, tên ngân hàng
- ✓ Hiệu suất: performance_rating (0-5.00), total_tours

**Chứng chỉ chuyên môn (bảng riêng):**
- ✓ Loại: HDV, Ngoại ngữ, Chuyên môn khác, An toàn, Sơ cấp cứu
- ✓ Số chứng chỉ, đơn vị cấp, ngày cấp, ngày hết hạn
- ✓ Trạng thái: Còn hạn / Sắp hết hạn / Hết hạn
- ✓ Upload file đính kèm

**Ngôn ngữ (bảng riêng):**
- ✓ Tên ngôn ngữ (Tiếng Anh, Trung, Nhật, Hàn...)
- ✓ Trình độ: Cơ bản / Trung cấp / Thành thạo / Bản ngữ
- ✓ Chứng chỉ ngoại ngữ và điểm số (TOEIC, IELTS, HSK, JLPT...)

### 2. Phân loại HDV
**3 tiêu chí phân loại:**
- ✓ **staff_category**: Nội địa / Quốc tế / Cả hai
- ✓ **specialization**: Chuyên tuyến (VD: Miền Bắc, Châu Âu, Nhật Bản...)
- ✓ **group_specialty**: Khách lẻ / Khách đoàn / Cả hai

**Tìm kiếm:**
```php
$staffDomestic = $modelStaff->getStaffByCategory('Nội địa');
$staffEurope = $modelStaff->getStaffBySpecialization('Châu Âu');
```

### 3. Theo dõi lịch làm việc & Hiệu suất
**Lịch sử tour (staff_tour_history):**
- ✓ Tự động ghi nhận khi phân công từ `schedule_staff`
- ✓ Lưu: số khách, vai trò (HDV chính/phụ/điều phối)
- ✓ Đánh giá khách hàng (0-5), đánh giá quản lý (0-5)
- ✓ Phản hồi, vấn đề phát sinh, trạng thái hoàn thành
- ✓ Lương, thưởng

**Thống kê hiệu suất:**
```php
$stats = $modelStaff->getPerformanceStats($staff_id);
// Trả về:
// - total_tours: Tổng số tour đã dẫn
// - avg_customer_rating: Điểm TB khách hàng
// - avg_manager_rating: Điểm TB quản lý
// - total_earnings: Tổng thu nhập
// - excellent_count: Số tour "Hoàn thành tốt"
// - issue_count: Số tour "Có vấn đề"
```

**Đánh giá định kỳ (staff_evaluations):**
- ✓ 6 tiêu chí đánh giá (0-5 mỗi tiêu chí):
  - Kỹ năng chuyên môn
  - Kỹ năng giao tiếp
  - Tinh thần trách nhiệm
  - Giải quyết vấn đề
  - Phục vụ khách hàng
  - Làm việc nhóm
- ✓ Tự động tính điểm trung bình
- ✓ Điểm mạnh, điểm yếu, kế hoạch cải thiện

**Nhắc nhở lịch:**
- ✓ Tự động lấy lịch sắp tới từ `getSchedulesByStaff()`
- ✓ Lọc theo tháng, năm, khoảng thời gian

### 4. Quản lý lịch nghỉ/bận (MỞ RỘNG)
**Tính năng:**
- ✓ Đăng ký nghỉ: Nghỉ phép / Nghỉ ốm / Nghỉ không lương / Bận cá nhân / Công tác khác
- ✓ Upload đơn xin nghỉ, giấy bác sĩ (file đính kèm)
- ✓ Trạng thái: Chờ duyệt / Đã duyệt / Từ chối / Đã hủy
- ✓ Kiểm tra trùng lặp: 
  - Không cho đăng ký 2 lịch nghỉ cùng thời điểm
  - Cảnh báo khi phân công tour trùng lịch nghỉ đã duyệt

**Quy trình duyệt:**
1. Nhân viên đăng ký: `addTimeOff()` -> status = 'Chờ duyệt'
2. Quản lý duyệt: `approveTimeOff()` -> status = 'Đã duyệt', ghi người duyệt + thời gian
3. Hoặc từ chối: `rejectTimeOff()` -> status = 'Từ chối', ghi lý do

**Tránh phân công trùng:**
```php
$conflicts = $modelStaff->checkTimeOffConflict($staff_id, $departure_date, $return_date);
if (count($conflicts) > 0) {
    echo "Nhân viên đã đăng ký nghỉ từ " . $conflicts[0]['from_date'] . " đến " . $conflicts[0]['to_date'];
}
```

**Kiểm tra tình trạng sẵn sàng:**
```php
// Kiểm tra trùng tour
$tourConflicts = $modelStaff->checkAvailability($staff_id, $departure_date, $return_date);

// Kiểm tra lịch nghỉ
$timeoffConflicts = $modelStaff->checkTimeOffConflict($staff_id, $departure_date, $return_date);

if (count($tourConflicts) == 0 && count($timeoffConflicts) == 0) {
    echo "Nhân viên rảnh, có thể phân công";
} else {
    echo "Nhân viên bận hoặc đã nghỉ";
}
```

## 🎯 CÁCH SỬ DỤNG

### Thêm/Sửa nhân viên (đã có form cũ)
**Cần cập nhật view thêm các trường:**
```php
<input type="date" name="date_of_birth" placeholder="Ngày sinh">
<select name="gender">
    <option value="Nam">Nam</option>
    <option value="Nữ">Nữ</option>
</select>
<input type="file" name="avatar" accept="image/*">
<select name="staff_category">
    <option value="Nội địa">Nội địa</option>
    <option value="Quốc tế">Quốc tế</option>
    <option value="Cả hai">Cả hai</option>
</select>
<input type="text" name="specialization" placeholder="Chuyên tuyến (VD: Miền Bắc)">
<select name="group_specialty">
    <option value="Khách lẻ">Khách lẻ</option>
    <option value="Khách đoàn">Khách đoàn</option>
    <option value="Cả hai">Cả hai</option>
</select>
<select name="health_status">
    <option value="Tốt">Tốt</option>
    <option value="Khá">Khá</option>
    <option value="Trung bình">Trung bình</option>
    <option value="Yếu">Yếu</option>
</select>
<input type="text" name="emergency_contact" placeholder="Người liên hệ khẩn cấp">
<input type="text" name="emergency_phone" placeholder="SĐT khẩn cấp">
<input type="text" name="bank_account" placeholder="Số tài khoản">
<input type="text" name="bank_name" placeholder="Ngân hàng">
```

### Quản lý chứng chỉ
```
URL: index.php?act=quan-ly-chung-chi&staff_id=1
- Hiển thị danh sách chứng chỉ
- Cảnh báo chứng chỉ sắp hết hạn (đỏ nếu < 30 ngày)
- Form thêm chứng chỉ mới + upload file
```

### Quản lý lịch nghỉ
```
URL: index.php?act=quan-ly-lich-nghi&staff_id=1  (của 1 nhân viên)
URL: index.php?act=quan-ly-lich-nghi             (tất cả - dành cho quản lý)

- Lọc theo trạng thái: Chờ duyệt, Đã duyệt, Từ chối
- Nút "Duyệt" / "Từ chối" cho quản lý
- Form đăng ký nghỉ mới
```

### Xem lịch sử tour & đánh giá
```
URL: index.php?act=lich-su-tour&staff_id=1
- Danh sách tour đã dẫn
- Thống kê: Tổng tour, điểm TB, tổng thu nhập
- Form cập nhật đánh giá từng tour
```

### Dashboard hiệu suất
```
URL: index.php?act=dashboard-hieu-suat&staff_id=1
- Biểu đồ hiệu suất
- Danh sách chứng chỉ sắp hết hạn
- Lịch sử đánh giá định kỳ
- Thống kê tổng quan
```

## 📁 CẤU TRÚC THƯ MỤC

```
admin/
├── models/
│   └── Staff.php (✓ Đã mở rộng 30+ methods)
├── controllers/
│   ├── StaffController.php (✓ Đã cập nhật create/update)
│   └── StaffExtendedController.php (✓ Mới tạo - 15 methods)
├── views/
│   └── staff/
│       ├── add_staff.php (⚠️ Cần cập nhật form)
│       ├── edit_staff.php (⚠️ Cần cập nhật form)
│       ├── list_staff.php (⚠️ Cần thêm cột phân loại)
│       ├── staff_detail.php (✓ Đã có)
│       ├── manage_certificates.php (❌ Cần tạo)
│       ├── manage_languages.php (❌ Cần tạo)
│       ├── manage_time_off.php (❌ Cần tạo)
│       ├── tour_history.php (❌ Cần tạo)
│       ├── manage_evaluations.php (❌ Cần tạo)
│       └── performance_dashboard.php (❌ Cần tạo)
├── uploads/
│   ├── avatars/ (✓ Tự động tạo khi upload)
│   ├── certificates/ (✓ Tự động tạo khi upload)
│   └── timeoff/ (✓ Tự động tạo khi upload)
└── index.php (✓ Đã thêm routes)
```

## ⚠️ VIỆC CẦN LÀM TIẾP

### Views cần tạo (6 files):
1. ❌ `manage_certificates.php` - Quản lý chứng chỉ
2. ❌ `manage_languages.php` - Quản lý ngôn ngữ
3. ❌ `manage_time_off.php` - Quản lý lịch nghỉ
4. ❌ `tour_history.php` - Lịch sử tour
5. ❌ `manage_evaluations.php` - Đánh giá định kỳ
6. ❌ `performance_dashboard.php` - Dashboard tổng quan

### Views cần cập nhật (3 files):
1. ⚠️ `add_staff.php` - Thêm form nhập 15 trường mới
2. ⚠️ `edit_staff.php` - Thêm form chỉnh sửa 15 trường mới
3. ⚠️ `list_staff.php` - Thêm cột: Avatar, Phân loại, Chuyên môn, Hiệu suất

### Tính năng bổ sung (tùy chọn):
- [ ] Thông báo tự động khi chứng chỉ sắp hết hạn
- [ ] Export báo cáo hiệu suất ra Excel
- [ ] Biểu đồ thống kê (Chart.js)
- [ ] Upload nhiều file chứng chỉ cùng lúc
- [ ] Tích hợp email gửi thông báo

## 🔗 LIÊN KẾT NHANH TRONG MENU

**Thêm vào menu sidebar:**
```html
<li class="nav-item">
    <a href="?act=danh-sach-nhan-su"><i data-feather="users"></i> Nhân sự</a>
    <ul>
        <li><a href="?act=danh-sach-nhan-su">Danh sách</a></li>
        <li><a href="?act=them-nhan-su">Thêm mới</a></li>
        <li><a href="?act=quan-ly-lich-nghi">Lịch nghỉ</a></li>
        <li><a href="?act=dashboard-hieu-suat">Báo cáo hiệu suất</a></li>
    </ul>
</li>
```

**Trong trang chi tiết nhân sự (staff_detail.php), thêm tabs:**
```html
<ul class="nav nav-tabs">
    <li><a href="?act=chi-tiet-nhan-su&id=<?=$staff_id?>">Thông tin chung</a></li>
    <li><a href="?act=quan-ly-chung-chi&staff_id=<?=$staff_id?>">Chứng chỉ</a></li>
    <li><a href="?act=quan-ly-ngon-ngu&staff_id=<?=$staff_id?>">Ngôn ngữ</a></li>
    <li><a href="?act=quan-ly-lich-nghi&staff_id=<?=$staff_id?>">Lịch nghỉ</a></li>
    <li><a href="?act=lich-su-tour&staff_id=<?=$staff_id?>">Lịch sử tour</a></li>
    <li><a href="?act=quan-ly-danh-gia&staff_id=<?=$staff_id?>">Đánh giá</a></li>
</ul>
```

## 📊 VÍ DỤ SỬ DỤNG

### 1. Thêm nhân viên mới
```php
$data = [
    'full_name' => 'Nguyễn Văn A',
    'date_of_birth' => '1990-05-15',
    'gender' => 'Nam',
    'phone' => '0912345678',
    'email' => 'nguyenvana@example.com',
    'staff_type' => 'Hướng dẫn viên',
    'staff_category' => 'Quốc tế',
    'specialization' => 'Châu Âu',
    'group_specialty' => 'Khách đoàn',
    'health_status' => 'Tốt',
    'emergency_contact' => 'Nguyễn Thị B',
    'emergency_phone' => '0987654321',
    'bank_account' => '1234567890',
    'bank_name' => 'Vietcombank'
];
$modelStaff->create($data);
```

### 2. Thêm chứng chỉ
```php
$certData = [
    'staff_id' => 1,
    'certificate_name' => 'Chứng chỉ HDV Quốc tế',
    'certificate_type' => 'Hướng dẫn viên',
    'certificate_number' => 'HDV-2024-001',
    'issued_by' => 'Tổng cục Du lịch',
    'issued_date' => '2024-01-15',
    'expiry_date' => '2029-01-15',
    'status' => 'Còn hạn'
];
$modelStaff->addCertificate($certData);
```

### 3. Đăng ký nghỉ phép
```php
$timeoffData = [
    'staff_id' => 1,
    'timeoff_type' => 'Nghỉ phép',
    'from_date' => '2025-12-24',
    'to_date' => '2025-12-26',
    'reason' => 'Nghỉ lễ Giáng sinh',
    'status' => 'Chờ duyệt'
];
$result = $modelStaff->addTimeOff($timeoffData);
```

### 4. Thêm đánh giá định kỳ
```php
$evalData = [
    'staff_id' => 1,
    'evaluation_period' => 'Q4/2025',
    'evaluator_name' => 'Trần Văn C',
    'professional_skill' => 4.5,
    'communication_skill' => 4.8,
    'responsibility' => 4.7,
    'problem_solving' => 4.3,
    'customer_service' => 4.9,
    'teamwork' => 4.6,
    'strengths' => 'Giao tiếp tốt, nhiệt tình',
    'weaknesses' => 'Chưa thành thạo tiếng Pháp',
    'improvement_plan' => 'Học thêm tiếng Pháp',
    'evaluation_date' => '2025-12-31'
];
$modelStaff->addEvaluation($evalData);
// Tự động cập nhật performance_rating trong bảng staff
```

## 📞 HỖ TRỢ

- Database: ✓ Hoàn thành 100%
- Models: ✓ Hoàn thành 100%
- Controllers: ✓ Hoàn thành 100%
- Routes: ✓ Hoàn thành 100%
- Views: ⚠️ Còn 6 files cần tạo, 3 files cần cập nhật

**Tổng kết:** Backend đã hoàn thiện, chỉ cần tạo giao diện (Views) là có thể sử dụng ngay!
