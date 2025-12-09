# Use Case 1 Testing Guide

## 📋 Hướng Dẫn Kiểm Thử

## 1️⃣ Setup Dữ Liệu Kiểm Thử

### Tạo Tài Khoản HDV Test

```sql
-- 1. Tạo staff record (nếu chưa có)
INSERT INTO staff (full_name, phone, email, staff_type, status)
VALUES ('Nguyễn Văn Hướng Dẫn', '0999888777', 'guide@test.com', 'Guide', 1);

-- Lưu: staff_id (ví dụ: 5)
SELECT last_insert_id() AS staff_id;

-- 2. Tạo user account
INSERT INTO users (username, password, email, role_code, staff_id, status)
VALUES ('guide_test', MD5('123456'), 'guide@test.com', 'GUIDE', 5, 1);

-- Lưu: user_id
SELECT last_insert_id() AS user_id;
```

### Tạo Tour & Schedule Test

```sql
-- Tour 1
INSERT INTO tours (category_id, tour_name, code)
VALUES (1, 'Tour Hạ Long - Catba 3 Ngày', 'HL-001');

-- Schedule 1
INSERT INTO tour_schedules (
    tour_id, departure_date, return_date,
    meeting_point, meeting_time, max_participants, status
)
VALUES (
    (SELECT tour_id FROM tours WHERE code = 'HL-001'),
    DATE_ADD(CURDATE(), INTERVAL 10 DAY),
    DATE_ADD(CURDATE(), INTERVAL 12 DAY),
    'Khách sạn Galaxy Nha Trang',
    '08:00',
    20,
    'Open'
);

-- Phân công HDV
INSERT INTO schedule_staff (schedule_id, staff_id, role)
VALUES ((SELECT schedule_id FROM tour_schedules ORDER BY created_at DESC LIMIT 1), 5, 'Guide');

-- Tour Itinerary
INSERT INTO tour_itineraries (tour_id, day_number, title, description, accommodation)
VALUES
(1, 1, 'Nha Trang - Hạ Long', 'Khởi hành từ Nha Trang, di chuyển đến Hạ Long', 'Khách sạn Hạ Long Paradise'),
(1, 2, 'Khám phá Hạ Long', 'Tham quan đảo Hạ Long, các hang động, bãi biển', 'Khách sạn Hạ Long Paradise'),
(1, 3, 'Cát Bà - Trở về', 'Tham quan đảo Cát Bà, trở về Nha Trang', '');
```

## 2️⃣ Đăng Nhập

### Test Case: T1_LOGIN_001

**Tiêu đề**: Đăng nhập thành công với tài khoản HDV
**Bước**:

1. Truy cập: `/?act=login`
2. Username: `guide_test`
3. Password: `123456`
4. Click "Đăng nhập"

**Kết quả mong đợi** (E1):

- ✅ Đăng nhập thành công
- ✅ Chuyển hướng đến trang chủ admin
- ✅ Hiển thị menu HDV
- ✅ Session chứa role_code = 'GUIDE'

---

## 3️⃣ Danh Sách Tour (MyTours)

### Test Case: T2_MYTOURS_001

**Tiêu đề**: Xem danh sách tour được phân công
**Bước**:

1. Sau khi đăng nhập
2. Click menu "Lịch của tôi" hoặc truy cập: `?act=hdv-lich-cua-toi`

**Kết quả mong đợi** (Bước 2-3):

- ✅ Hiển thị danh sách tour
- ✅ Bảng có 6 cột: Mã tour, Tên, Khởi hành-Kết thúc, Điểm đến, Trạng thái, Hành động
- ✅ Hiển thị tour "HL-001" (Hạ Long 3 Ngày)
- ✅ Trạng thái badge: "Sắp diễn ra" (xanh dương)

### Test Case: T2_MYTOURS_002

**Tiêu đề**: Lọc theo tháng
**Bước**:

1. Ở danh sách tour
2. Chọn tháng hiện tại
3. Click "Lọc"

**Kết quả mong đợi** (Bước 3):

- ✅ Danh sách được cập nhật
- ✅ Chỉ hiển thị tour trong tháng được chọn
- ✅ URL có parameter: `month=...&year=...`

### Test Case: T2_MYTOURS_003

**Tiêu đề**: Không có tour nào được phân công
**Bước**:

1. Đăng nhập với HDV không được phân công
2. Truy cập: `?act=hdv-lich-cua-toi`

**Kết quả mong đợi** (E2):

- ✅ Hiển thị alert: "Hiện tại bạn chưa được phân công tour nào"
- ✅ Bảng rỗng hoặc không hiển thị

---

## 4️⃣ Chi Tiết Tour (MyTourDetail)

### Test Case: T3_DETAIL_001

**Tiêu đề**: Xem chi tiết tour
**Bước**:

1. Từ danh sách tour
2. Click "Chi tiết" ở hàng tour
3. Hoặc truy cập: `?act=hdv-chi-tiet-tour&id=<schedule_id>`

**Kết quả mong đợi** (Bước 4):

- ✅ Hiển thị thông tin chung tour
- ✅ Mã tour: HL-001
- ✅ Tên: Tour Hạ Long - Catba 3 Ngày
- ✅ Số ngày: 3 ngày
- ✅ Ngày khởi hành - Kết thúc
- ✅ Điểm tập trung: Khách sạn Galaxy Nha Trang

### Test Case: T3_DETAIL_002

**Tiêu đề**: Xem tab Lịch trình
**Bước**:

1. Ở trang chi tiết tour
2. Tab "Lịch trình" (mặc định là active)

**Kết quả mong đợi** (Bước 4b):

- ✅ Hiển thị 3 ngày
- ✅ Mỗi ngày có: Tiêu đề, Hoạt động, Nơi ở
- ✅ Timeline style với marker xanh
- ✅ Ngày 1: "Nha Trang - Hạ Long"
- ✅ Ngày 2: "Khám phá Hạ Long"
- ✅ Ngày 3: "Cát Bà - Trở về"

### Test Case: T3_DETAIL_003

**Tiêu đề**: Xem tab Ảnh
**Bước**:

1. Click tab "Hình ảnh"

**Kết quả mong đợi**:

- ✅ Hiển thị gallery (nếu có ảnh)
- ✅ Ảnh dạng grid 4 cột
- ✅ Click ảnh → Modal xem lớn
- ✅ Hoặc: "Chưa có hình ảnh nào"

### Test Case: T3_DETAIL_004

**Tiêu đề**: Xem tab Chính sách
**Bước**:

1. Click tab "Chính sách"

**Kết quả mong đợi**:

- ✅ Hiển thị 4 chính sách: Hủy, Thay đổi, Thanh toán, Ghi chú
- ✅ Mỗi chính sách trong box riêng
- ✅ Hoặc: "Chưa có chính sách nào"

### Test Case: T3_DETAIL_005

**Tiêu đề**: Xem tab Đội ngũ
**Bước**:

1. Click tab "Đội ngũ"

**Kết quả mong đợi**:

- ✅ Hiển thị danh sách người được phân công
- ✅ Thông tin: Tên, Vai trò, Điện thoại, Loại nhân viên

---

## 5️⃣ Nhiệm Vụ (MyTasks)

### Test Case: T4_TASKS_001

**Tiêu đề**: Xem danh sách nhiệm vụ
**Bước**:

1. Từ chi tiết tour, click tab "Nhiệm vụ" hoặc link "Xem danh sách nhiệm vụ"
2. Hoặc truy cập: `?act=hdv-nhiem-vu-cua-toi&schedule_id=<id>`

**Kết quả mong đợi** (Bước 5):

- ✅ Hiển thị danh sách công việc
- ✅ Tab "Tất cả nhiệm vụ" active
- ✅ Task cards với: Tiêu đề, Loại, Thời gian, Địa điểm, Người phụ trách, Mô tả

### Test Case: T4_TASKS_002

**Tiêu đề**: Phân loại nhiệm vụ
**Bước**:

1. Ở trang nhiệm vụ
2. Click tab "Hướng dẫn đoàn"

**Kết quả mong đợi**:

- ✅ Hiển thị các task loại "Hướng dẫn đoàn"
- ✅ Cards có border xanh (success)
- ✅ Icon tick xanh

### Test Case: T4_TASKS_003

**Tiêu đề**: Xem ghi chú đặc biệt
**Bước**:

1. Click tab "Ghi chú đặc biệt"

**Kết quả mong đợi**:

- ✅ Hiển thị các task loại "Ghi chú đặc biệt"
- ✅ Cards có border vàng (warning)
- ✅ Background nhạt vàng (#fffbf0)
- ✅ Icon cảnh báo vàng

### Test Case: T4_TASKS_004

**Tiêu đề**: Thống kê nhiệm vụ
**Bước**:

1. Scroll down
2. Xem 3 card thống kê

**Kết quả mong đợi**:

- ✅ Card 1: Tổng cộng = 3
- ✅ Card 2: Hướng dẫn đoàn = 2
- ✅ Card 3: Ghi chú đặc biệt = ?

---

## 6️⃣ Lịch Tháng (MyCalendarView)

### Test Case: T5_CALENDAR_001

**Tiêu đề**: Xem lịch tháng
**Bước**:

1. Từ danh sách tour, click "Xem lịch tháng"
2. Hoặc truy cập: `?act=hdv-xem-lich-thang`

**Kết quả mong đợi** (Bước 6):

- ✅ Hiển thị lịch tháng
- ✅ Header: "Tháng 11 / 2025"
- ✅ 7 cột: Thứ Hai, Thứ Ba, ..., Chủ Nhật
- ✅ Ngày có tour: Badge xanh "Sắp diễn ra"
- ✅ Hôm nay: Badge đỏ "●"

### Test Case: T5_CALENDAR_002

**Tiêu đề**: Click ngày có tour
**Bước**:

1. Ở lịch tháng
2. Click vào ngày có tour (ví dụ: ngày 15)

**Kết quả mong đợi** (Bước 6c):

- ✅ Modal popup xuất hiện
- ✅ Tiêu đề: "Tour ngày 15/11/2025"
- ✅ Danh sách tour ngày hôm đó
- ✅ Nút "Xem chi tiết" cho mỗi tour

### Test Case: T5_CALENDAR_003

**Tiêu đề**: Chuyển tháng
**Bước**:

1. Ở lịch tháng
2. Chọn tháng khác từ dropdown
3. Click "Lịch tháng hiện tại" hoặc chọn năm khác

**Kết quả mong đợi** (Luồng A2):

- ✅ Lịch được cập nhật
- ✅ URL thay đổi: `month=...&year=...`
- ✅ Danh sách tour dưới cập nhật

### Test Case: T5_CALENDAR_004

**Tiêu đề**: Danh sách tour tháng
**Bước**:

1. Scroll down ở trang lịch
2. Xem "Danh sách tour tháng"

**Kết quả mong đợi**:

- ✅ Timeline style
- ✅ Hiển thị tất cả tour tháng
- ✅ Nút "Xem" cho mỗi tour

---

## 7️⃣ Xuất Lịch (ExportMySchedule)

### Test Case: T6_EXPORT_001

**Tiêu đề**: Xuất PDF
**Bước**:

1. Từ chi tiết tour
2. Click nút "Xuất PDF"
3. Hoặc URL: `?act=hdv-xuat-lich&schedule_id=<id>&format=pdf`

**Kết quả mong đợi** (Bước 7):

- ✅ File PDF được tải
- ✅ Tên file: "lich-tour-<id>.pdf"
- ✅ Content: Thông tin tour, lịch trình
- ✅ Lưu file thành công

### Test Case: T6_EXPORT_002

**Tiêu đề**: Xuất Excel
**Bước**:

1. Từ chi tiết tour
2. Click nút "Xuất Excel"

**Kết quả mong đợi** (Bước 7):

- ✅ File Excel được tải
- ✅ Tên file: "lich-tour-<id>.xls"
- ✅ Bảng: Thông tin chung + Lịch trình chi tiết
- ✅ Lưu file thành công

### Test Case: T6_EXPORT_003

**Tiêu đề**: Lỗi xuất file
**Bước**:

1. Cấu hình sai hoặc database lỗi
2. Click "Xuất PDF"

**Kết quả mong đợi** (E4):

- ✅ Alert: "Tải xuống thất bại: ..."
- ✅ Không tải file
- ✅ Gợi ý: Thử lại hoặc đổi format

---

## 8️⃣ Xử Lý Lỗi

### Test Case: T7_ERROR_001

**Tiêu đề**: Đăng nhập thất bại (E1)
**Bước**:

1. Truy cập login
2. Username: `guide_test`
3. Password: `wrong_password`

**Kết quả mong đợi**:

- ✅ Alert: "Sai tài khoản hoặc mật khẩu"
- ✅ Quay lại trang login
- ✅ Link "Quên mật khẩu"

### Test Case: T7_ERROR_002

**Tiêu đề**: Không có tour nào (E2)
**Bước**:

1. Đăng nhập với HDV không có tour
2. Truy cập: `?act=hdv-lich-cua-toi`

**Kết quả mong đợi**:

- ✅ Alert: "Hiện tại bạn chưa được phân công tour nào"
- ✅ Bảng rỗng hoặc không hiển thị

### Test Case: T7_ERROR_003

**Tiêu đề**: Truy cập không được phép (E3)
**Bước**:

1. HDV A cố truy cập tour của HDV B
2. URL: `?act=hdv-chi-tiet-tour&id=<tour_B_id>`

**Kết quả mong đợi**:

- ✅ Alert: "Không có quyền truy cập lịch này"
- ✅ Redirect: Trang chủ hoặc danh sách tour

### Test Case: T7_ERROR_004

**Tiêu đề**: Lỗi tải dữ liệu (E3)
**Bước**:

1. Simulate database error
2. Truy cập: `?act=hdv-chi-tiet-tour&id=999`

**Kết quả mong đợi**:

- ✅ Alert: "Không tìm thấy lịch khởi hành!"
- ✅ Redirect: Danh sách tour

---

## 9️⃣ Kiểm Tra Bảo Mật

### Test Case: T8_SECURITY_001

**Tiêu đề**: HDV không được truy cập trang admin
**Bước**:

1. Đăng nhập với role GUIDE
2. Truy cập: `?act=danh-sach-nhan-su` (trang admin)

**Kết quả mong đợi**:

- ✅ Alert: "Không có quyền truy cập"
- ✅ Redirect: Trang chủ

### Test Case: T8_SECURITY_002

**Tiêu đề**: Kiểm tra session
**Bước**:

1. Đăng nhập
2. Kiểm tra: `$_SESSION['role_code']` = 'GUIDE'
3. `$_SESSION['staff_id']` = staff_id
4. `$_SESSION['user_id']` = user_id

**Kết quả mong đợi**:

- ✅ Session được set đúng

### Test Case: T8_SECURITY_003

**Tiêu đề**: XSS Prevention
**Bước**:

1. Tên tour: `<script>alert('XSS')</script>`
2. Xem danh sách tour

**Kết quả mong đợi**:

- ✅ Script không thực thi
- ✅ Hiển thị text bình thường (escaped)

---

## 🔟 Kiểm Tra Responsive

### Test Case: T9_RESPONSIVE_001

**Tiêu đề**: Desktop (1200px+)
**Bước**:

1. Mở browser ở độ phân giải 1920x1080
2. Truy cập tất cả trang

**Kết quả mong đợi**:

- ✅ Bảng đầy đủ hiển thị
- ✅ Không có scroll ngang
- ✅ Layout tối ưu

### Test Case: T9_RESPONSIVE_002

**Tiêu đề**: Tablet (768px-1199px)
**Bước**:

1. Mở browser ở độ phân giải 1024x768
2. Truy cập tất cả trang

**Kết quả mong đợi**:

- ✅ Bảng có cuộn ngang nếu cần
- ✅ Buttons stack xếp chồng
- ✅ Layout thích ứng

### Test Case: T9_RESPONSIVE_003

**Tiêu đề**: Mobile (<768px)
**Bước**:

1. Mở browser ở độ phân giải 375x667
2. Truy cập tất cả trang

**Kết quả mong đợi**:

- ✅ Mobile-friendly layout
- ✅ Buttons toàn chiều rộng
- ✅ Không có scroll ngang ngoài ý muốn

---

## 1️⃣1️⃣ Kiểm Tra Hiệu Suất

### Test Case: T10_PERFORMANCE_001

**Tiêu đề**: Load time < 2 giây
**Bước**:

1. Xóa cache
2. Truy cập tất cả trang
3. Đo load time

**Kết quả mong đợi**:

- ✅ Danh sách tour: < 1s
- ✅ Chi tiết tour: < 1.5s
- ✅ Lịch tháng: < 1.5s

### Test Case: T10_PERFORMANCE_002

**Tiêu đề**: Queries được tối ưu
**Bước**:

1. Bật MySQL query log
2. Truy cập các trang
3. Kiểm tra số queries

**Kết quả mong đợi**:

- ✅ Không có N+1 queries
- ✅ < 10 queries per page
- ✅ Có index trên các trường JOIN

---

## ✅ Test Report Template

```
═══════════════════════════════════════════════════════════
Test Case: T1_LOGIN_001
Status: ✅ PASS / ❌ FAIL / ⚠️ PARTIAL
Date: 26/11/2025
Tester: [Tên người test]
═══════════════════════════════════════════════════════════

Tiêu đề: Đăng nhập thành công với tài khoản HDV

Bước thực hiện:
[ ] Bước 1: Truy cập login
[ ] Bước 2: Nhập thông tin
[ ] Bước 3: Click đăng nhập

Kết quả mong đợi:
✅ Đăng nhập thành công
✅ Chuyển hướng trang chủ
✅ Hiển thị menu HDV

Kết quả thực tế:
[Mô tả kết quả thực tế]

Ghi chú:
[Ghi chú thêm nếu có]

═══════════════════════════════════════════════════════════
```

---

**Total Test Cases**: 27
**Categories**: 11

- Login: 1
- Danh sách tour: 3
- Chi tiết tour: 5
- Nhiệm vụ: 4
- Lịch tháng: 4
- Xuất lịch: 3
- Xử lý lỗi: 4
- Bảo mật: 3
- Responsive: 3
- Hiệu suất: 2
