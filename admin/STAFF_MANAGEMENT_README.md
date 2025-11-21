# HỆ THỐNG QUẢN LÝ NHÂN VIÊN HƯỚNG DẪN VIÊN - MỞ RỘNG

## TỔNG QUAN

Hệ thống quản lý nhân viên HDV đã được mở rộng với các tính năng sau:

### 1. **Hồ sơ chi tiết HDV**
- Thông tin cá nhân đầy đủ: họ tên, ngày sinh, giới tính, địa chỉ, ảnh đại diện
- Thông tin liên hệ: SĐT, email, liên hệ khẩn cấp
- Thông tin nghề nghiệp: CMND, số giấy phép HDV, số tài khoản ngân hàng
- Tình trạng sức khỏe và ghi chú sức khỏe

### 2. **Phân loại HDV theo nhóm**
- **Theo phạm vi**: Nội địa / Quốc tế / Cả hai
- **Theo chuyên môn**: Chuyên tuyến (Miền Bắc, Châu Âu...)
- **Theo đối tượng**: Khách lẻ / Khách đoàn / Cả hai

### 3. **Quản lý chứng chỉ chuyên môn**
- Lưu trữ đa dạng chứng chỉ: HDV, ngoại ngữ, an toàn, sơ cấp cứu...
- Theo dõi ngày cấp, ngày hết hạn
- Cảnh báo chứng chỉ sắp hết hạn (30 ngày)
- Đính kèm file scan

### 4. **Quản lý ngôn ngữ**
- Ghi nhận nhiều ngôn ngữ sử dụng
- Phân loại trình độ: Cơ bản / Trung cấp / Thành thạo / Bản ngữ
- Lưu chứng chỉ ngoại ngữ (TOEIC, IELTS, HSK...)

### 5. **Lịch sử dẫn tour**
- Ghi nhận đầy đủ các tour đã dẫn
- Đánh giá từ khách hàng (1-5 sao)
- Đánh giá từ quản lý (1-5 sao)
- Ghi nhận vấn đề phát sinh
- Theo dõi lương và thưởng

### 6. **Quản lý lịch nghỉ / bận**
- Đăng ký nghỉ phép, nghỉ ốm, nghỉ không lương
- Quy trình duyệt: Chờ duyệt → Đã duyệt / Từ chối
- Kiểm tra trùng lặp lịch nghỉ
- Tránh phân công trùng lặp

### 7. **Đánh giá định kỳ**
- Đánh giá 6 tiêu chí:
  - Kỹ năng chuyên môn
  - Kỹ năng giao tiếp
  - Tinh thần trách nhiệm
  - Giải quyết vấn đề
  - Phục vụ khách hàng
  - Làm việc nhóm
- Ghi nhận điểm mạnh, điểm yếu
- Lập kế hoạch cải thiện

### 8. **Theo dõi hiệu suất**
- Tổng số tour đã dẫn
- Điểm đánh giá trung bình
- Top performers theo tháng/quý/năm
- Báo cáo chi tiết doanh thu

### 9. **Tìm HDV phù hợp**
- Kiểm tra lịch trống
- Lọc theo: Loại HDV, phạm vi, ngôn ngữ
- Đề xuất HDV có rating cao
- Tránh phân công trùng lặp

---

## CÀI ĐẶT

### Bước 1: Chạy SQL Script
```bash
mysql -u root -p duan1 < admin/expand_staff_management.sql
```

### Bước 2: Cập nhật Model trong code
Trong `admin/index.php`, thêm:
```php
require_once './models/StaffExpanded.php';
```

### Bước 3: Cập nhật Controller
Thay thế:
```php
$this->modelStaff = new Staff();
```
Thành:
```php
$this->modelStaff = new StaffExpanded();
```

---

## SỬ DỤNG

### 1. Quản lý Chứng chỉ

#### Thêm chứng chỉ:
```php
$data = [
    'staff_id' => 1,
    'certificate_name' => 'Chứng chỉ HDV Quốc tế',
    'certificate_type' => 'Hướng dẫn viên',
    'certificate_number' => 'HDV-2024-001',
    'issued_by' => 'Tổng cục Du lịch',
    'issued_date' => '2024-01-15',
    'expiry_date' => '2029-01-15',
    'status' => 'Còn hạn'
];
$modelStaff->addCertificate($data);
```

#### Lấy danh sách chứng chỉ sắp hết hạn:
```php
$expiring = $modelStaff->getExpiringCertificates(30); // 30 ngày
```

### 2. Quản lý Lịch nghỉ

#### Đăng ký nghỉ:
```php
$data = [
    'staff_id' => 1,
    'timeoff_type' => 'Nghỉ phép',
    'from_date' => '2025-12-24',
    'to_date' => '2025-12-26',
    'reason' => 'Nghỉ lễ Giáng sinh'
];
$modelStaff->requestTimeOff($data);
```

#### Duyệt nghỉ:
```php
$modelStaff->approveTimeOff($timeoff_id, $approved_by_user_id, 'Đồng ý');
```

#### Kiểm tra trùng lịch:
```php
$conflicts = $modelStaff->checkTimeOffConflict($staff_id, $from_date, $to_date);
if (!empty($conflicts)) {
    echo "Nhân viên đã đăng ký nghỉ trong khoảng thời gian này!";
}
```

### 3. Lịch sử dẫn tour

#### Thêm lịch sử:
```php
$data = [
    'staff_id' => 1,
    'schedule_id' => 5,
    'tour_id' => 3,
    'role' => 'Hướng dẫn viên chính',
    'departure_date' => '2025-11-01',
    'return_date' => '2025-11-05',
    'number_of_guests' => 20,
    'customer_rating' => 4.5,
    'manager_rating' => 4.8,
    'completed_status' => 'Hoàn thành tốt',
    'salary_paid' => 5000000,
    'bonus' => 500000
];
$modelStaff->addTourHistory($data);
// Tự động cập nhật total_tours và performance_rating
```

#### Lấy báo cáo hiệu suất:
```php
$report = $modelStaff->getPerformanceReport($staff_id, '2025-01-01', '2025-12-31');
echo "Tổng tour: " . $report['total_tours'];
echo "Đánh giá TB: " . $report['avg_customer_rating'];
echo "Tổng thu nhập: " . $report['total_salary'];
```

### 4. Tìm HDV phù hợp

#### Tìm HDV rảnh trong ngày:
```php
$filters = [
    'staff_type' => 'Hướng dẫn viên',
    'staff_category' => 'Quốc tế',
    'language' => 'Tiếng Anh'
];
$available = $modelStaff->getAvailableStaff('2025-12-01', $filters);
```

#### Top performers:
```php
$top10 = $modelStaff->getTopPerformers(10, 'month');
foreach ($top10 as $staff) {
    echo $staff['full_name'] . " - Rating: " . $staff['avg_rating'];
}
```

### 5. Đánh giá định kỳ

```php
$data = [
    'staff_id' => 1,
    'evaluation_period' => 'Q4/2025',
    'evaluator_name' => 'Nguyễn Văn A',
    'professional_skill' => 4.5,
    'communication_skill' => 4.8,
    'responsibility' => 5.0,
    'problem_solving' => 4.3,
    'customer_service' => 4.7,
    'teamwork' => 4.6,
    'strengths' => 'Giao tiếp tốt, nhiệt tình',
    'weaknesses' => 'Cần cải thiện tiếng Anh chuyên ngành',
    'improvement_plan' => 'Tham gia khóa học TOEIC',
    'evaluation_date' => '2025-12-31'
];
$modelStaff->addEvaluation($data);
```

---

## VIEWS & REPORTS

### View: Hiệu suất nhân viên
```sql
SELECT * FROM v_staff_performance 
WHERE performance_rating >= 4.0 
ORDER BY avg_customer_rating DESC;
```

### View: Nhân viên có lịch trống
```sql
SELECT * FROM v_staff_availability 
WHERE upcoming_tours < 2
ORDER BY upcoming_tours ASC;
```

---

## TÍCH HỢP VÀO HỆ THỐNG HIỆN TẠI

### Cập nhật form thêm/sửa nhân viên:
Thêm các trường mới vào `add_staff.php` và `edit_staff.php`:
- Ngày sinh
- Giới tính
- Địa chỉ
- Upload ảnh đại diện
- Tình trạng sức khỏe
- Phân loại (Nội địa/Quốc tế)
- Chuyên tuyến
- Chuyên khách đoàn/lẻ

### Trang chi tiết nhân viên mới:
Hiển thị:
- Tab 1: Thông tin cơ bản
- Tab 2: Chứng chỉ & Ngôn ngữ
- Tab 3: Lịch sử dẫn tour
- Tab 4: Lịch nghỉ/bận
- Tab 5: Đánh giá định kỳ
- Tab 6: Thống kê hiệu suất

### Tích hợp vào phân công nhân sự:
Khi phân công, hệ thống tự động:
1. Kiểm tra HDV có rảnh không
2. Kiểm tra HDV có đăng ký nghỉ không
3. Đề xuất HDV phù hợp theo yêu cầu
4. Hiển thị rating và số tour đã dẫn

---

## LƯU Ý

1. **Upload file**: Cần tạo folder `uploads/staff/` với các subfolder:
   - `avatars/` - Ảnh đại diện
   - `certificates/` - Chứng chỉ
   - `timeoff/` - Đơn xin nghỉ

2. **Cron job**: Thiết lập tự động cảnh báo chứng chỉ sắp hết hạn

3. **Quyền truy cập**: 
   - Admin: Toàn quyền
   - Manager: Xem, duyệt nghỉ, đánh giá
   - Staff: Chỉ xem thông tin cá nhân

4. **Backup**: Định kỳ backup database do có nhiều bảng mới

---

## ROADMAP MỞ RỘNG

- [ ] Tích hợp gửi thông báo qua email/SMS
- [ ] App mobile cho nhân viên check-in tour
- [ ] Tính lương tự động dựa trên tour đã dẫn
- [ ] Dashboard analytics chi tiết
- [ ] Export báo cáo PDF/Excel
- [ ] Tích hợp chấm công bằng QR Code

---

## HỖ TRỢ

Nếu gặp vấn đề, kiểm tra:
1. Database đã import đầy đủ chưa
2. Model StaffExpanded đã được require chưa
3. Quyền ghi file trong folder uploads/
4. PHP version >= 8.0 (để dùng match expression)
