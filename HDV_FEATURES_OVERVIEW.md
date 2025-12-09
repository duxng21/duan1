# 🎯 Tổng Hợp Chức Năng HDV (Hướng Dẫn Viên) - Dự Án Duan1

---

## 📌 Mục Lục

1. [Controller & Routes](#1-controller--routes)
2. [Models & Database](#2-models--database)
3. [Views & Frontend](#3-views--frontend)
4. [Permissions & Security](#4-permissions--security)
5. [Database Tables](#5-database-tables)
6. [Main Use Cases](#6-main-use-cases)

---

## 1. Controller & Routes

### 📍 File: `admin/index.php` (Line 100-109)

**Routes cho chức năng HDV:**

```php
// HDV actions
'hdv-checkin' => (new ScheduleController())->GuideCheckIn(),
'hdv-luu-nhat-ky' => (new ScheduleController())->GuideSaveJourneyLog(),

// === Use Case 1: HDV Xem lịch trình tour và lịch làm việc ===
'hdv-lich-cua-toi' => (new ScheduleController())->MyTours(),
'hdv-chi-tiet-tour' => (new ScheduleController())->MyTourDetail(),
'hdv-nhiem-vu-cua-toi' => (new ScheduleController())->MyTasks(),
'hdv-xem-lich-thang' => (new ScheduleController())->MyCalendarView(),
'hdv-xuat-lich' => (new ScheduleController())->ExportMySchedule(),
```

### 📍 File: `admin/controllers/ScheduleController.php`

#### Method 1: `GuideCheckIn()` (Line 384+)

- **Chức năng**: HDV check-in cho một schedule
- **Quyền**: `requireOwnScheduleOrAdmin($schedule_id, 'schedule.checkin')`
- **Xử lý**:
  - Kiểm tra schedule_id
  - Lấy staff_id từ SESSION
  - Gọi `setStaffCheckIn()` từ model
  - Ghi log activity: 'guide_checkin'

```php
public function GuideCheckIn()
{
    requireLogin();
    $schedule_id = $_POST['schedule_id'] ?? null;
    if (!$schedule_id) {
        $_SESSION['error'] = 'Thiếu schedule_id!';
        header('Location: ?act=danh-sach-lich-khoi-hanh');
        exit();
    }
    requireOwnScheduleOrAdmin($schedule_id, 'schedule.checkin');
    if (isGuide()) {
        $staff_id = $_SESSION['staff_id'] ?? null;
        if ($staff_id) {
            $ok = $this->modelSchedule->setStaffCheckIn($schedule_id, $staff_id);
            if ($ok) {
                logUserActivity('guide_checkin', 'schedule', $schedule_id, 'HDV check-in');
                $_SESSION['success'] = 'Check-in thành công!';
            }
        }
    }
}
```

#### Method 2: `MyTours()` (Use Case 1 - Bước 2, 3)

- **Chức năng**: Hiển thị danh sách tour được phân công cho HDV
- **Filters**: Tháng, tuần, trạng thái tour
- **View**: `admin/views/schedule/list_tours_hdv.php`

#### Method 3: `MyTourDetail()` (Use Case 1 - Bước 4, 5)

- **Chức năng**: Xem chi tiết tour (thông tin, lịch trình, ảnh, nhiệm vụ)
- **View**: `admin/views/schedule/tour_detail_hdv.php`
- **Tabs**:
  - Lịch trình (từng ngày)
  - Hình ảnh (gallery)
  - Nhiệm vụ
  - Chính sách (Hủy, Thay đổi, Thanh toán)
  - Đội ngũ (nhân viên tham gia)

#### Method 4: `MyTasks()` (Use Case 1 - Bước 5)

- **Chức năng**: Xem danh sách nhiệm vụ của HDV trong tour
- **Tabs**:
  - Tất cả nhiệm vụ
  - Hướng dẫn đoàn
  - Ghi chú đặc biệt

#### Method 5: `MyCalendarView()` (Use Case 1 - Bước 6)

- **Chức năng**: Xem lịch tháng trực quan
- **View**: `admin/views/schedule/calendar_view_hdv.php`
- **Features**:
  - Lịch 7 cột (Thứ Hai - Chủ Nhật)
  - Ngày có tour: Badge xanh
  - Hôm nay: Badge đỏ
  - Click ngày → Popup chi tiết

#### Method 6: `ExportMySchedule()` (Use Case 1 - Bước 7)

- **Chức năng**: Xuất lịch trình sang PDF/Excel
- **Format**: PDF hoặc Excel
- **Helper Methods**:
  - `exportScheduleToPDF()`
  - `exportScheduleToExcel()`

---

## 2. Models & Database

### 📍 File: `admin/models/TourSchedule.php` hoặc `Schedule.php`

**Methods liên quan đến HDV:**

- `setStaffCheckIn($schedule_id, $staff_id)` - Cập nhật check-in cho HDV
- `getMySchedules($staff_id, $filters)` - Lấy danh sách tour của HDV
- `getScheduleDetail($schedule_id)` - Chi tiết tour
- `getMyTasks($schedule_id, $staff_id)` - Danh sách nhiệm vụ

---

## 3. Views & Frontend

### 📍 Danh sách Views HDV

#### 1. `admin/views/schedule/list_tours_hdv.php`

- **Tên thực**: Danh sách tour HDV
- **Route**: `?act=hdv-lich-cua-toi`
- **Hiển thị**:
  - Danh sách tour được phân công
  - Bảng: Mã tour, Tên, Khởi hành-Kết thúc, Điểm đến, Trạng thái
  - Filter: Tháng, tuần, trạng thái

#### 2. `admin/views/schedule/tour_detail_hdv.php` (Line 1+)

- **Route**: `?act=hdv-chi-tiet-tour&id=<schedule_id>`
- **Breadcrumb**: Lịch của tôi → Chi tiết tour
- **Tabs**:
  - Lịch trình (từng ngày)
  - Hình ảnh (gallery)
  - Nhiệm vụ
  - Chính sách (Hủy, Thay đổi, Thanh toán)
  - Đội ngũ
- **Buttons**:
  - Xuất PDF
  - Xuất Excel
  - Xem danh sách nhiệm vụ

```php
// Nút Nhiệm vụ trong tour_detail_hdv.php (Line 263)
<a href="?act=hdv-nhiem-vu-cua-toi&schedule_id=<?= $schedule_id ?>"
    class="btn btn-primary">
    <i class="fas fa-tasks"></i> Xem danh sách nhiệm vụ
</a>
```

#### 3. `admin/views/schedule/calendar_view_hdv.php` (Line 1+)

- **Route**: `?act=hdv-xem-lich-thang`
- **Mô tả**: Lịch xem tháng dành cho HDV (Use Case 1: Bước 6, Luồng phụ A2)
- **Features**:
  - Lịch HTML 7 cột
  - Đánh dấu ngày có tour
  - Modal popup khi click ngày
  - Danh sách tour tháng
  - Timeline style

---

## 4. Permissions & Security

### 📍 File: `commons/permission_simple.php`

#### Permission Check Functions:

```php
// === PHAN QUYEN DON GIAN: ADMIN & GUIDE ===

// Kiểm tra xem user có phải HDV không
function isGuide() {
    return isset($_SESSION['role_code']) && $_SESSION['role_code'] === 'GUIDE';
}

// Yêu cầu role GUIDE
function requireGuideRole($permissionCode = null) {
    if (!isGuide()) {
        $_SESSION['error'] = 'Chỉ hướng dẫn viên mới có thể truy cập!';
        // ...redirect to login
    }
}

// Kiểm tra quyền sở hữu lịch (HDV chỉ thao tác trên lịch được phân công)
// Line 80-92
function requireOwnScheduleOrAdmin($schedule_id, $permissionCode = null) {
    if (!isGuide())
        return;
    $staff_id = $_SESSION['staff_id'] ?? null;
    if (!$staff_id)
        return;
    try {
        $sql = "SELECT COUNT(*) FROM schedule_staff WHERE schedule_id = ? AND staff_id = ?";
        $stmt = $GLOBALS['conn']->prepare($sql);
        $stmt->execute([$schedule_id, $staff_id]);
        // Check if HDV owns this schedule
    }
}
```

#### Quyền HDV (Line 67-75):

```php
if (isGuide()) {
    // Quyền cho HDV: xem tour, xem lịch của mình, check-in và cập nhật nhật ký
    $guidePerms = [
        'tour.view',          // Xem tour
        'schedule.view_own',  // Xem lịch của mình
        'schedule.checkin',   // Check-in
        'schedule.log.update' // Cập nhật nhật ký
    ];
    return in_array($permissionCode, $guidePerms);
}
```

#### Các Restrictions:

- ✅ HDV xem tour được phân công
- ✅ HDV xem lịch riêng
- ✅ HDV xem nhiệm vụ
- ✅ HDV xem lịch tháng
- ✅ HDV xuất lịch
- ❌ HDV không xem lịch của HDV khác
- ❌ HDV không quản lý tour
- ❌ HDV không xóa/sửa dữ liệu

---

## 5. Database Tables

### 📍 File: `database_usecase2.sql`

#### Bảng chính liên quan:

**1. `staff` - Bảng nhân viên (HDV)**

```sql
CREATE TABLE staff (
    staff_id INT PRIMARY KEY AUTO_INCREMENT,
    staff_name VARCHAR(255) NOT NULL,
    staff_type ENUM('GUIDE', 'DRIVER', 'SUPPORT'), -- HDV là GUIDE
    ... (các trường khác)
);
```

**2. `schedule_staff` - Bảng phân công HDV cho schedule**

```sql
-- Bảng liên kết schedule và staff
ALTER TABLE schedule_staff ADD INDEX idx_schedule_staff_schedule (schedule_id);
ALTER TABLE schedule_staff ADD INDEX idx_schedule_staff_staff (staff_id);
```

**3. `staff_journey_log` - Nhật ký hành trình của HDV (Bảng mới)**

```sql
-- Bảng lưu nhật ký hành trình của HDV (bảng mới)
CREATE TABLE staff_journey_log (
    log_id INT PRIMARY KEY AUTO_INCREMENT,
    schedule_id INT NOT NULL,
    staff_id INT NOT NULL,
    ... (các trường khác),
    FOREIGN KEY (`staff_id`) REFERENCES `staff`(`staff_id`) ON DELETE CASCADE ON UPDATE CASCADE
);
```

**4. Views liên quan:**

- `v_staff_availability` - View thể hiện tình trạng sẵn sàng của HDV

```sql
CREATE OR REPLACE VIEW v_staff_availability AS
    SELECT
        s.staff_id,
        s.staff_name,
        s.staff_type,
        COUNT(DISTINCT ss.schedule_id) AS assigned_schedules,
        ...
    FROM staff s
    LEFT JOIN schedule_staff ss ON s.staff_id = ss.staff_id
    ...
```

#### Indexes:

```sql
ALTER TABLE schedule_staff ADD INDEX idx_schedule_staff_schedule (schedule_id);
ALTER TABLE schedule_staff ADD INDEX idx_schedule_staff_staff (staff_id);
```

---

## 6. Main Use Cases

### 📋 Use Case 1: Xem lịch trình tour và lịch làm việc của mình

**Luồng chính (8 bước):**

1. ✅ **HDV đăng nhập**

   - Role: GUIDE
   - File: `admin/controllers/AuthController.php`

2. ✅ **Chọn menu "Lịch của tôi"**

   - Route: `?act=hdv-lich-cua-toi`
   - Method: `MyTours()`

3. ✅ **Lọc danh sách tour**

   - Theo tháng, tuần, trạng thái
   - View: `list_tours_hdv.php`

4. ✅ **Xem chi tiết tour**

   - Route: `?act=hdv-chi-tiet-tour&id=<schedule_id>`
   - Method: `MyTourDetail()`
   - View: `tour_detail_hdv.php`

5. ✅ **Xem tab "Nhiệm vụ"**

   - Route: `?act=hdv-nhiem-vu-cua-toi&schedule_id=<id>`
   - Method: `MyTasks()`
   - Tabs: Tất cả, Hướng dẫn, Ghi chú

6. ✅ **Xem lịch dưới dạng calendar**

   - Route: `?act=hdv-xem-lich-thang`
   - Method: `MyCalendarView()`
   - View: `calendar_view_hdv.php`

7. ✅ **Tải xuống lịch trình**

   - Route: `?act=hdv-xuat-lich&schedule_id=<id>&format=pdf|excel`
   - Method: `ExportMySchedule()`
   - Formats: PDF, Excel

8. ✅ **Quay lại danh sách**
   - Navigation links

**Luồng phụ:**

- **A1**: Lọc theo thời gian (tháng/tuần)
- **A2**: Xem lịch tháng (Calendar View)
- **A3**: Xuất offline (PDF/Excel)

---

## 📊 Tóm Tắt Các Chức Năng HDV

| Chức Năng      | Route                  | Method                  | View                    | Status |
| -------------- | ---------------------- | ----------------------- | ----------------------- | ------ |
| Danh sách tour | `hdv-lich-cua-toi`     | `MyTours()`             | `list_tours_hdv.php`    | ✅     |
| Chi tiết tour  | `hdv-chi-tiet-tour`    | `MyTourDetail()`        | `tour_detail_hdv.php`   | ✅     |
| Nhiệm vụ       | `hdv-nhiem-vu-cua-toi` | `MyTasks()`             | `my_tasks.php`          | ✅     |
| Lịch tháng     | `hdv-xem-lich-thang`   | `MyCalendarView()`      | `calendar_view_hdv.php` | ✅     |
| Xuất lịch      | `hdv-xuat-lich`        | `ExportMySchedule()`    | -                       | ✅     |
| Check-in       | `hdv-checkin`          | `GuideCheckIn()`        | -                       | ✅     |
| Nhật ký        | `hdv-luu-nhat-ky`      | `GuideSaveJourneyLog()` | -                       | ✅     |

---

## 🔐 Security Features

✅ **Authentication**

- Login required: `requireLogin()`
- Role check: `requireGuideRole()`
- Session validation

✅ **Authorization**

- HDV chỉ xem tour được phân công: `isOwnSchedule()`
- Admin xem tất cả

✅ **Data Protection**

- XSS: Output escaped với `htmlspecialchars()`
- SQLi: PDO prepared statements
- CSRF: Session-based control flow

✅ **Data Privacy**

- HDV schedule list lọc theo staff_id
- Không lộ dữ liệu của HDV khác
- Proper JOIN conditions

---

## 📚 Tài Liệu Liên Quan

- `USE_CASE_1_QUICK_START.md` - Quick start guide
- `USE_CASE_1_IMPLEMENTATION.md` - Chi tiết triển khai
- `USE_CASE_1_COMPREHENSIVE_OVERVIEW.md` - Tổng quan toàn diện
- `USE_CASE_1_TESTING.md` - Testing scenarios
- `USE_CASE_1_API_REFERENCE.md` - API reference

---

_Tài liệu này được tạo để giúp developer dễ dàng định vị các phần code liên quan đến chức năng HDV trong dự án Duan1._
