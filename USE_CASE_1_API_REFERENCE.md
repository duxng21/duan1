# Use Case 1 - Technical API Reference

## Controllers & Methods

### ScheduleController

#### MyTours()

```php
/**
 * Danh sách tour được phân công cho HDV
 * @return void (include view: my_tours_list.php)
 */
public function MyTours()
```

**Variables đưa vào view:**

```php
$filter_month        // int (1-12) - Tháng lọc
$filter_year         // int - Năm lọc
$filter_status       // string - Trạng thái (Open, In Progress, Completed, Cancelled)
$tours               // array - Danh sách tour
$no_tours_message    // string - Thông báo khi không có tour
```

**Querystring Parameters:**

- `month` - Tháng (1-12)
- `year` - Năm
- `status` - Trạng thái tour
- `from_date` - Ngày bắt đầu (Y-m-d)
- `to_date` - Ngày kết thúc (Y-m-d)
- `week` - Tuần (ISO week number)

**Session Requirements:**

- `$_SESSION['staff_id']` - Staff ID của HDV
- `$_SESSION['user_id']` - User ID

**Errors:**

- E2: Không có tour (set `$no_tours_message`)

---

#### MyTourDetail()

```php
/**
 * Chi tiết tour dành cho HDV
 * @return void (include view: tour_detail_hdv.php)
 */
public function MyTourDetail()
```

**Variables đưa vào view:**

```php
$schedule           // array - Thông tin lịch khởi hành
$tour               // array - Thông tin tour
$schedule_id        // int - Schedule ID
$itineraries        // array - Lịch trình theo ngày
$gallery            // array - Danh sách ảnh
$policies           // array - Chính sách tour
$assigned_staff     // array - Danh sách nhân viên
$total_days         // int - Tổng số ngày
$departure          // DateTime - Ngày khởi hành
```

**Querystring Parameters:**

- `id` - Schedule ID (bắt buộc)

**Errors:**

- E3: Lịch không tìm thấy → Redirect
- Access denied → Redirect

---

#### MyTasks()

```php
/**
 * Danh sách nhiệm vụ của HDV
 * @return void (include view: my_tasks.php)
 */
public function MyTasks()
```

**Variables đưa vào view:**

```php
$tasks              // array - Danh sách nhiệm vụ
$schedule           // array - Thông tin lịch
$schedule_id        // int - Schedule ID
$tour               // array - Thông tin tour
$special_notes      // array - Ghi chú đặc biệt
$itineraries        // array - Lịch trình
$journey_logs       // array - Nhật ký
```

**Task Format:**

```php
$tasks[] = [
    'id'            => string - Task ID
    'type'          => string - Loại (Hướng dẫn đoàn, Ghi chú đặc biệt)
    'title'         => string - Tiêu đề
    'time'          => string - Thời gian
    'location'      => string - Địa điểm
    'responsible'   => string - Người phụ trách
    'description'   => string - Mô tả chi tiết
    'status'        => string - Trạng thái (Pending)
]
```

**Querystring Parameters:**

- `schedule_id` - Schedule ID (bắt buộc)

**Errors:**

- E3: Schedule không tìm thấy → Redirect
- Access denied → Redirect

---

#### MyCalendarView()

```php
/**
 * Lịch xem tháng
 * @return void (include view: calendar_view_hdv.php)
 */
public function MyCalendarView()
```

**Variables đưa vào view:**

```php
$month              // int - Tháng (1-12)
$year               // int - Năm
$calendar_data      // array - Dữ liệu lịch toàn tháng
$calendar_events    // array - Sự kiện theo ngày
$assigned_schedule_ids  // array - Schedule IDs được phân công
```

**Calendar Events Format:**

```php
$calendar_events[$day] = [
    [
        'schedule_id'       => int
        'tour_id'           => int
        'tour_name'         => string
        'tour_code'         => string
        'departure_date'    => string (Y-m-d)
        'return_date'       => string (Y-m-d)
        'status'            => string
    ]
]
```

**Querystring Parameters:**

- `month` - Tháng (default: tháng hiện tại)
- `year` - Năm (default: năm hiện tại)

---

#### ExportMySchedule()

```php
/**
 * Xuất lịch trình
 * @return void (file download)
 */
public function ExportMySchedule()
```

**Querystring Parameters:**

- `schedule_id` - Schedule ID (bắt buộc)
- `format` - Định dạng (pdf / excel)

**Headers returned:**

```php
// PDF
Content-Type: application/pdf
Content-Disposition: attachment; filename="lich-tour-<id>.pdf"

// Excel
Content-Type: application/vnd.ms-excel
Content-Disposition: attachment; filename="lich-tour-<id>.xls"
```

**Errors:**

- E4: Xuất file lỗi → Alert & Redirect
- Access denied → Alert & Redirect

---

#### exportScheduleToPDF() (private)

```php
private function exportScheduleToPDF($schedule, $tour, $itineraries)
```

**Parameters:**

- `$schedule` - array - Dữ liệu lịch
- `$tour` - array - Dữ liệu tour
- `$itineraries` - array - Lịch trình

**Output:**

- HTML output with PDF headers
- Content: Tour info + Itinerary

---

#### exportScheduleToExcel() (private)

```php
private function exportScheduleToExcel($schedule, $tour, $itineraries)
```

**Parameters:** (same as PDF)

**Output:**

- HTML table with Excel headers
- 2 tables: General info + Itinerary

---

## Models Used

### TourSchedule Model

#### getAllStaffAssignments()

```php
public function getAllStaffAssignments($filters = [])
// Returns: array of assignments with staff, schedule, tour data
// Filters: staff_id, staff_type, from_date, to_date
```

**Return Format:**

```php
[
    [
        'schedule_id'       => int
        'staff_id'          => int
        'staff_name'        => string
        'staff_type'        => string (Guide, Manager)
        'schedule_status'   => string (Open, In Progress, Completed, Cancelled)
        'departure_date'    => string (Y-m-d H:i:s)
        'return_date'       => string (Y-m-d H:i:s)
        'tour_id'           => int
        'tour_name'         => string
        'tour_code'         => string
        'role'              => string - Vai trò trong lịch
        'assigned_at'       => datetime
    ]
]
```

#### getScheduleById()

```php
public function getScheduleById($id)
// Returns: single schedule record with JOIN data
```

#### getScheduleStaff()

```php
public function getScheduleStaff($schedule_id)
// Returns: array of staff assigned to schedule
```

#### getCalendarView()

```php
public function getCalendarView($month, $year)
// Returns: array of schedules in given month/year
```

#### getJourneyLogs()

```php
public function getJourneyLogs($schedule_id)
// Returns: array of journey logs/notes
```

---

### Tour Model

#### getById()

```php
public function getById($id)
// Returns: single tour with related data
```

**Return Fields:**

- `tour_id`
- `tour_name`
- `code`
- `category_id`
- `category_name`
- `tour_image` (featured image)
- `tour_price`
- `itinerary_count`
- `image_count`
- `schedule_count`
- `tag_list`
- `has_policies`

---

### TourDetail Model

#### getItineraries()

```php
public function getItineraries($tour_id)
// Returns: array of itinerary items ordered by day
```

**Return Fields:**

- `itinerary_id`
- `tour_id`
- `day_number` (1, 2, 3, ...)
- `title` - Tiêu đề hoạt động
- `description` - Mô tả chi tiết
- `accommodation` - Nơi ở

#### getGallery()

```php
public function getGallery($tour_id)
// Returns: array of tour media/images
```

**Return Fields:**

- `media_id`
- `tour_id`
- `file_path` - Đường dẫn ảnh
- `is_featured` - Ảnh chính (0/1)

#### getPolicies()

```php
public function getPolicies($tour_id)
// Returns: single record with policies
```

**Return Fields:**

- `tour_id`
- `cancellation_policy` - Chính sách hủy
- `change_policy` - Chính sách thay đổi
- `payment_policy` - Chính sách thanh toán
- `note_policy` - Ghi chú quan trọng

---

### SpecialNote Model

#### getNotesBySchedule()

```php
public function getNotesBySchedule($schedule_id, $filters = [])
// Returns: array of special notes for schedule
```

**Return Fields:**

- `note_id`
- `guest_id`
- `booking_id`
- `full_name` - Tên khách
- `phone`
- `email`
- `room_number`
- `title` - Tiêu đề ghi chú
- `content` - Nội dung
- `note_date` - Ngày ghi chú
- `priority_level` - Mức độ ưu tiên
- `status` - Trạng thái
- `created_by`
- `creator_name`

---

## Permission Functions

### requireGuideRole()

```php
function requireGuideRole($permissionCode = null)
// Throws: Redirect to / if not GUIDE
// Parameters:
//   - $permissionCode (optional): Specific permission to check
```

**Permissions:**

- `tour.view` - Xem tour
- `schedule.view_own` - Xem lịch của mình
- `schedule.checkin` - Check-in
- `schedule.log.update` - Cập nhật nhật ký

### isOwnSchedule()

```php
function isOwnSchedule($schedule_id)
// Returns: boolean - Whether staff is assigned to schedule
```

### requireOwnScheduleOrAdmin()

```php
function requireOwnScheduleOrAdmin($schedule_id, $permissionCode = null)
// Allows: ADMIN (always) or GUIDE with own schedule
// Throws: Redirect if not allowed
```

---

## Routes

| Act                  | Method | Controller                             | View                  |
| -------------------- | ------ | -------------------------------------- | --------------------- |
| hdv-lich-cua-toi     | GET    | ScheduleController::MyTours()          | my_tours_list.php     |
| hdv-chi-tiet-tour    | GET    | ScheduleController::MyTourDetail()     | tour_detail_hdv.php   |
| hdv-nhiem-vu-cua-toi | GET    | ScheduleController::MyTasks()          | my_tasks.php          |
| hdv-xem-lich-thang   | GET    | ScheduleController::MyCalendarView()   | calendar_view_hdv.php |
| hdv-xuat-lich        | GET    | ScheduleController::ExportMySchedule() | (file download)       |

---

## Session Variables

**After Login (Role = GUIDE):**

```php
$_SESSION['user_id']        // int - User ID
$_SESSION['username']       // string - Username
$_SESSION['full_name']      // string - Full name
$_SESSION['email']          // string - Email
$_SESSION['role_code']      // string - 'GUIDE'
$_SESSION['staff_id']       // int - Staff ID (linked)
$_SESSION['permissions']    // array - Permission list
```

---

## Database Queries

### Get HDV Tours (by Schedule ID)

```sql
SELECT ss.*, s.*, ts.*, t.*
FROM schedule_staff ss
JOIN staff s ON ss.staff_id = s.staff_id
JOIN tour_schedules ts ON ss.schedule_id = ts.schedule_id
JOIN tours t ON ts.tour_id = t.tour_id
WHERE ss.staff_id = ?
  AND ts.departure_date >= ? AND ts.departure_date <= ?
ORDER BY ts.departure_date ASC
```

### Get Itineraries

```sql
SELECT * FROM tour_itineraries
WHERE tour_id = ?
ORDER BY day_number ASC
```

### Get Calendar Events

```sql
SELECT ts.*, t.tour_name, t.code
FROM tour_schedules ts
JOIN tours t ON ts.tour_id = t.tour_id
WHERE MONTH(ts.departure_date) = ?
  AND YEAR(ts.departure_date) = ?
  AND ts.status != 'Cancelled'
ORDER BY ts.departure_date ASC
```

---

## Error Handling

### HTTP Status Codes

- 200 - OK
- 302 - Redirect
- 400 - Bad Request
- 401 - Unauthorized
- 403 - Forbidden
- 404 - Not Found

### Session Messages

```php
$_SESSION['success']  // Green alert
$_SESSION['error']    // Red alert
$_SESSION['warning']  // Yellow alert
$_SESSION['info']     // Blue alert
```

### Examples:

```php
$_SESSION['error'] = 'Không tìm thấy lịch khởi hành!';
$_SESSION['success'] = 'Tải xuống thành công!';
```

---

## View Variable Reference

### my_tours_list.php

```php
$tours          // array - Tour list (key: tour_id_schedule_id)
$filter_month   // int - Current month filter
$filter_year    // int - Current year filter
$filter_status  // string - Current status filter
$no_tours_message // string - Message if no tours
```

### tour_detail_hdv.php

```php
$schedule       // array - Schedule details
$tour           // array - Tour info
$schedule_id    // int - Schedule ID
$itineraries    // array - Day-by-day itinerary
$gallery        // array - Tour images
$policies       // array - Tour policies
$assigned_staff // array - Staff members
$total_days     // int - Number of days
$departure      // DateTime - Departure datetime
```

### my_tasks.php

```php
$tasks          // array - Task list
$schedule       // array - Schedule info
$schedule_id    // int - Schedule ID
$tour           // array - Tour info
$special_notes  // array - Special notes from manager
$itineraries    // array - Tour itinerary
```

### calendar_view_hdv.php

```php
$month              // int - Month (1-12)
$year               // int - Year
$calendar_data      // array - All schedules
$calendar_events    // array - Events by day [$day => [schedules]]
$assigned_schedule_ids // array - Assigned schedule IDs
```

---

## JavaScript Functions

### calendar_view_hdv.php

```javascript
function showDayEvents(day)
// Parameters:
//   - day: int (1-31) - Day of month
// Action:
//   - Populate modal with events for day
//   - Show modal
```

### tour_detail_hdv.php

```javascript
function showImage(src)
// Parameters:
//   - src: string - Image URL
// Action:
//   - Set image in modal
//   - Show image modal
```

---

## API Response Format

### Success Response

```json
{
  "success": true,
  "data": { ... },
  "message": "Operation successful"
}
```

### Error Response

```json
{
  "success": false,
  "error": "Error message",
  "code": 400
}
```

---

**Last Updated:** 26/11/2025
**Version:** 1.0
**Status:** Complete
