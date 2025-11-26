# Use Case 1: Xem lịch trình tour và lịch làm việc của mình - IMPLEMENTATION GUIDE

## Tổng Quan

Hướng dẫn viên (HDV) có thể xem danh sách tour đã được phân công, xem chi tiết thông tin tour, danh sách nhiệm vụ của mình, lịch tháng trực quan, và tải xuống lịch trình.

## Thành Phần Được Triển Khai

### 1. Controller: ScheduleController (Mở rộng)

**File:** `admin/controllers/ScheduleController.php`

#### Các Method Mới:

- **MyTours()** - Danh sách tour của HDV (Bước 2, 3)

  - Hiển thị danh sách tour được phân công
  - Hỗ trợ lọc theo: tháng, tuần, trạng thái tour
  - Bảng hiển thị: Mã tour, Tên tour, Khởi hành-Kết thúc, Điểm đến, Trạng thái

- **MyTourDetail()** - Chi tiết tour (Bước 4, 5)

  - Hiển thị thông tin chung: mã, tên, loại, số ngày
  - Tab: Lịch trình, Ảnh, Nhiệm vụ, Chính sách, Đội ngũ
  - Lịch trình theo ngày với hoạt động, nơi ở, thời gian
  - Hình ảnh minh họa

- **MyTasks()** - Danh sách nhiệm vụ (Bước 5)

  - Công việc: hướng dẫn đoàn, ghi chú từ quản lý
  - Hiển thị: loại công việc, thời gian, địa điểm, người phụ trách, mô tả
  - Phân loại theo loại công việc

- **MyCalendarView()** - Xem lịch tháng (Bước 6, Luồng A2)

  - Hiển thị lịch tháng trực quan
  - Đánh dấu ngày có tour
  - Modal popup khi click vào ngày
  - Danh sách tour trong tháng

- **ExportMySchedule()** - Xuất lịch trình (Bước 7, Luồng A3)
  - Hỗ trợ xuất PDF và Excel
  - Bao gồm: thông tin tour, lịch trình, nhiệm vụ

#### Helper Methods:

- **exportScheduleToPDF()** - Xuất HTML/PDF
- **exportScheduleToExcel()** - Xuất XLS

### 2. Views (Giao Diện)

#### my_tours_list.php

**Đường dẫn:** `admin/views/schedule/my_tours_list.php`

- Bộ lọc: Tháng, Năm, Trạng thái tour
- Bảng danh sách tour với các hành động
- Thông báo khi không có tour (E2)
- Xử lý session messages

#### tour_detail_hdv.php

**Đường dẫn:** `admin/views/schedule/tour_detail_hdv.php`

- Tabs navigation (Lịch trình, Ảnh, Nhiệm vụ, Chính sách, Đội ngũ)
- Timeline lịch trình theo ngày
- Gallery ảnh với modal xem lớn
- Bảng chính sách (Hủy, Thay đổi, Thanh toán, Ghi chú)
- Danh sách đội ngũ
- Nút xuất PDF/Excel

#### my_tasks.php

**Đường dẫn:** `admin/views/schedule/my_tasks.php`

- Tabs: Tất cả nhiệm vụ, Hướng dẫn đoàn, Ghi chú đặc biệt
- Task cards với: tiêu đề, loại, thời gian, địa điểm, người phụ trách, mô tả
- Thống kê nhiệm vụ (tổng, hướng dẫn, ghi chú)
- Hướng dẫn và gợi ý

#### calendar_view_hdv.php

**Đường dẫn:** `admin/views/schedule/calendar_view_hdv.php`

- Lịch HTML 7 cột (Thứ Hai - Chủ Nhật)
- Ngày có tour được đánh dấu với badge xanh
- Hôm nay được đánh dấu với badge đỏ
- Kích cỡ ô: 100px (có thể cuộn)
- Modal popup hiển thị chi tiết ngày khi click
- Danh sách tour tháng với timeline
- Nút chọn tháng/năm mẫu

### 3. Routes (Định Tuyến)

**File:** `admin/index.php`

```php
'hdv-lich-cua-toi' => (new ScheduleController())->MyTours(),
'hdv-chi-tiet-tour' => (new ScheduleController())->MyTourDetail(),
'hdv-nhiem-vu-cua-toi' => (new ScheduleController())->MyTasks(),
'hdv-xem-lich-thang' => (new ScheduleController())->MyCalendarView(),
'hdv-xuat-lich' => (new ScheduleController())->ExportMySchedule(),
```

### 4. Quyền Truy Cập (Permissions)

**File:** `commons/permission_simple.php`

Thêm function mới:

```php
function requireGuideRole($permissionCode = null)
```

Quyền HDV:

- `tour.view` - Xem tour
- `schedule.view_own` - Xem lịch của mình
- `schedule.checkin` - Check-in
- `schedule.log.update` - Cập nhật nhật ký

### 5. Models Sử Dụng

- **TourSchedule::getAllStaffAssignments()** - Lấy phân công nhân sự
- **TourSchedule::getScheduleById()** - Chi tiết lịch
- **TourSchedule::getScheduleStaff()** - Đội ngũ lịch
- **TourSchedule::getCalendarView()** - Dữ liệu lịch tháng
- **TourSchedule::getJourneyLogs()** - Nhật ký tour
- **Tour::getById()** - Chi tiết tour
- **TourDetail::getItineraries()** - Lịch trình
- **TourDetail::getGallery()** - Hình ảnh
- **TourDetail::getPolicies()** - Chính sách
- **SpecialNote::getNotesBySchedule()** - Ghi chú đặc biệt

## Luồng Sử Dụng

### Luồng Chính

1. **Bước 1** - HDV đăng nhập

   - System: Kiểm tra tài khoản/mật khẩu
   - Hiển thị trang chính của HDV

2. **Bước 2-3** - HDV xem danh sách tour

   - Truy cập: `?act=hdv-lich-cua-toi`
   - Hỗ trợ lọc theo tháng, tuần, trạng thái
   - Bảng: Mã, Tên, Khởi hành-Kết thúc, Điểm đến, Trạng thái

3. **Bước 4-5** - HDV xem chi tiết tour

   - Click "Chi tiết" trên bảng
   - Tab: Lịch trình, Ảnh, Nhiệm vụ, Chính sách, Đội ngũ
   - Lịch trình theo ngày với mô tả chi tiết

4. **Bước 5** - HDV xem nhiệm vụ của mình

   - Click "Nhiệm vụ" hoặc tab tương ứng
   - Danh sách công việc: hướng dẫn đoàn, ghi chú đặc biệt
   - Thông tin: loại, thời gian, địa điểm, người phụ trách

5. **Bước 6** - HDV xem lịch tháng

   - Click "Xem lịch tháng"
   - Lịch trực quan với ngày được đánh dấu
   - Modal popup chi tiết khi click ngày

6. **Bước 7** - HDV tải xuống lịch trình
   - Click "Xuất PDF" hoặc "Xuất Excel"
   - File: Thông tin tour, lịch trình, nhiệm vụ
   - Tải file về máy

### Luồng Phụ

- **A1**: Lọc theo thời gian (tháng/tuần/trạng thái)
- **A2**: Xem lịch dưới dạng calendar
- **A3**: Tải xuống lịch trình offline

### Luồng Ngoại Lệ

- **E1**: Đăng nhập thất bại → Thông báo "Sai tài khoản hoặc mật khẩu"
- **E2**: Không có tour → Thông báo "Hiện tại bạn chưa được phân công tour nào"
- **E3**: Lỗi tải dữ liệu → Thông báo "Không thể tải dữ liệu"
- **E4**: Lỗi xuất file → Thông báo "Tải xuống thất bại"

## Yêu Cầu Database

Tables cần tồn tại:

- `tours` - Danh sách tour
- `tour_schedules` - Lịch khởi hành
- `schedule_staff` - Phân công nhân sự
- `tour_itineraries` - Lịch trình theo ngày
- `tour_media` - Hình ảnh tour
- `tour_policies` - Chính sách tour
- `guest_special_notes` - Ghi chú đặc biệt
- `schedule_journey_logs` - Nhật ký tour

## Styling & UI

- **Framework**: Bootstrap 5
- **Icons**: FontAwesome 6
- **Colors**: Primary (0d6efd), Success (198754), Warning (ffc107), Danger (dc3545), Info (0dcaf0)
- **Components**:
  - Cards
  - Tabs
  - Badges
  - Buttons
  - Alerts
  - Timeline
  - Modal
  - Tables

## Cách Sử Dụng

### Cho HDV

1. Đăng nhập với tài khoản GUIDE
2. Truy cập "Lịch của tôi" từ menu
3. Xem danh sách tour, chi tiết, nhiệm vụ
4. Xem lịch tháng trực quan
5. Xuất lịch trình

### Cho Admin

- Tất cả chức năng HDV + quản lý
- Có thể xem lịch của bất kỳ HDV nào

## Testing Checklist

- [ ] HDV không được truy cập nếu chưa đăng nhập
- [ ] Danh sách tour chỉ hiển thị tour được phân công
- [ ] Lọc tháng/tuần hoạt động chính xác
- [ ] Chi tiết tour hiển thị đầy đủ thông tin
- [ ] Lịch tháng hiển thị đúng số ngày
- [ ] Modal popup hoạt động khi click ngày
- [ ] Xuất PDF/Excel không lỗi
- [ ] Ghi chú đặc biệt hiển thị đúng
- [ ] Đội ngũ hiển thị đầy đủ

## Mở Rộng Tương Lai

- Thêm check-in/check-out
- Thêm cập nhật nhật ký tour
- Thêm upload/chụp ảnh trong quá trình tour
- Thêm đánh giá tour
- Thêm nhắn tin với quản lý
- Thêm bản đồ GPS
- Thêm offline mode
