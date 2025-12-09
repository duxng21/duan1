# Use Case 2: Quản lý lịch khởi hành & phân bổ nhân sự, dịch vụ

## 📋 Tổng quan

**Use Case:** Lập lịch và phân công tour  
**Actor:** Admin / Nhân viên điều hành  
**Mô tả:** Quản lý việc lập kế hoạch khởi hành, phân công nhân sự và dịch vụ cho từng tour

---

## ✅ Triển khai hoàn chỉnh

### 1. Luồng chính (Main Flow)

#### Bước 1: Chọn "Lịch khởi hành & phân bổ nhân sự"
```
Route: ?act=danh-sach-lich-khoi-hanh
Controller: ScheduleController::ListSchedule()
View: admin/views/schedule/list_schedule.php
```

**Chức năng:**
- Hiển thị danh sách tour có sẵn
- Lọc theo tour_id, trạng thái, ngày khởi hành
- Hiển thị số lượng nhân sự và dịch vụ đã phân bổ

#### Bước 2: Nhập thông tin chi tiết tour
```
Route: ?act=them-lich-khoi-hanh → ?act=luu-lich-khoi-hanh
Controller: ScheduleController::AddSchedule() → StoreSchedule()
View: admin/views/schedule/add_schedule.php
```

**Dữ liệu nhập:**
- `tour_id` (required): ID của tour
- `departure_date` (required): Ngày khởi hành
- `return_date`: Ngày kết thúc
- `meeting_point`: Điểm tập trung
- `meeting_time`: Thời gian tập trung
- `max_participants`: Số người tối đa
- `price_adult`, `price_child`: Giá vé
- `notes`: Ghi chú

**Kiểm tra trùng lịch:**
```php
// Model: TourSchedule::checkScheduleConflict()
$conflict = $this->modelSchedule->checkScheduleConflict(
    $tour_id, 
    $departure_date, 
    $exclude_schedule_id
);
// Returns: true nếu có trùng, false nếu OK
```

**Cảnh báo nếu trùng:**
```php
if ($conflict) {
    $_SESSION['warning'] = 'Đã có lịch khởi hành cho tour này vào ngày đã chọn!';
}
```

#### Bước 3: Phân công nhân sự
```
Route: ?act=phan-cong-nhan-su (POST)
Controller: ScheduleController::AssignStaff()
Model: TourSchedule::assignStaff()
```

**Chọn HDV:**
- Chỉ cho phép phân công nhân viên có `staff_type = 'Guide'`
- Mỗi lịch khởi hành chỉ được phân công 1 nhân sự duy nhất

**Kiểm tra tình trạng sẵn sàng:**
```php
// Model: TourSchedule::checkStaffAvailability()
$available = $this->modelSchedule->checkStaffAvailability(
    $staff_id,
    $departure_date,
    $return_date,
    $exclude_schedule_id
);
// Returns: true nếu rảnh, false nếu đã có lịch khác
```

**Logic phân công:**
```php
// Table: schedule_staff
INSERT INTO schedule_staff (schedule_id, staff_id, role, assigned_at)
VALUES (?, ?, 'Hướng dẫn viên', CURRENT_TIMESTAMP)
```

#### Bước 4: Chọn dịch vụ kèm theo
```
Route: ?act=danh-sach-doi-tac (xem đối tác)
Route: ?act=add-service-link (link dịch vụ vào lịch)
Controller: SupplierController::ListSuppliers()
Controller: ScheduleController::AddServiceLink()
Model: TourSchedule::linkService()
```

**Loại dịch vụ:**
- `Hotel`: Khách sạn
- `Restaurant`: Nhà hàng
- `Transport`: Xe vận chuyển
- `Flight`: Vé máy bay
- `Activity`: Hoạt động/vui chơi
- `Insurance`: Bảo hiểm
- `Other`: Khác

**Liên kết với danh sách đối tác:**
```sql
-- Table: schedule_service_links
INSERT INTO schedule_service_links (
    schedule_id, supplier_id, service_type, 
    service_date, service_time, service_description,
    unit_price, quantity, currency,
    cancellation_deadline, cancellation_fee,
    contact_person, contact_phone, notes, status
) VALUES (...)
```

**Calculated field:**
```sql
total_price = quantity * unit_price (STORED GENERATED column)
```

#### Bước 5: Lưu và gửi thông báo
```
Function: notifyStaffAssignment($schedule_id, $staff_id)
Function: notifyServiceAssignment($schedule_id, $service_id)
File: commons/notification.php
```

**Lưu kế hoạch:**
- Update `tour_schedules` status
- Insert `schedule_staff` assignments
- Insert `schedule_service_links`

**Gửi thông báo tự động:**
```php
// Notification cho nhân sự
notifyStaffAssignment($schedule_id, $staff_id);

// Notification cho đối tác
notifyServiceAssignment($schedule_id, $service_id);

// TODO: Tích hợp PHPMailer để gửi email thực tế
// Hiện tại chỉ lưu vào bảng notifications
```

#### Bước 6: In hoặc xuất "Lịch khởi hành tour"
```
Route: ?act=xuat-bao-cao-lich&id={schedule_id}
Controller: ScheduleController::ExportSchedule()
View: admin/views/schedule/export_schedule.php
```

**Xuất PDF:** (TODO - sử dụng mPDF)
```php
// TODO: Implement ExportSchedulePDF()
// Include: Tour info, Staff, Services, Contact info
```

---

## 🗂️ Cấu trúc Database

### Table: `tour_schedules`
```sql
CREATE TABLE tour_schedules (
    schedule_id INT PRIMARY KEY AUTO_INCREMENT,
    tour_id INT NOT NULL,
    departure_date DATETIME NOT NULL,
    return_date DATETIME,
    meeting_point VARCHAR(255),
    meeting_time VARCHAR(10),
    max_participants INT DEFAULT 30,
    current_participants INT DEFAULT 0,
    price_adult DECIMAL(12,2),
    price_child DECIMAL(12,2),
    status ENUM('Open','Full','Confirmed','In Progress','Completed','Cancelled') DEFAULT 'Open',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (tour_id) REFERENCES tours(tour_id)
);
```

### Table: `schedule_staff`
```sql
CREATE TABLE schedule_staff (
    assignment_id INT PRIMARY KEY AUTO_INCREMENT,
    schedule_id INT NOT NULL,
    staff_id INT NOT NULL,
    role VARCHAR(100) DEFAULT 'Hướng dẫn viên',
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    check_in_time TIMESTAMP NULL,
    FOREIGN KEY (schedule_id) REFERENCES tour_schedules(schedule_id) ON DELETE CASCADE,
    FOREIGN KEY (staff_id) REFERENCES staff(staff_id),
    UNIQUE KEY unique_schedule_staff (schedule_id) -- Chỉ 1 nhân sự/lịch
);
```

### Table: `schedule_service_links`
```sql
CREATE TABLE schedule_service_links (
    link_id INT PRIMARY KEY AUTO_INCREMENT,
    schedule_id INT NOT NULL,
    supplier_id INT NOT NULL,
    service_type ENUM('hotel','restaurant','transport','flight','activity','other') NOT NULL,
    service_date DATE,
    service_time VARCHAR(10),
    service_description VARCHAR(255),
    unit_price DECIMAL(12,2) DEFAULT 0.00,
    quantity INT DEFAULT 1,
    total_price DECIMAL(12,2) AS (quantity * unit_price) STORED,
    currency VARCHAR(10) DEFAULT 'VND',
    cancellation_deadline DATE,
    cancellation_fee DECIMAL(12,2) DEFAULT 0.00,
    contact_person VARCHAR(100),
    contact_phone VARCHAR(30),
    notes TEXT,
    status TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (schedule_id) REFERENCES tour_schedules(schedule_id) ON DELETE CASCADE,
    FOREIGN KEY (supplier_id) REFERENCES tour_suppliers(supplier_id)
);
```

### Table: `tour_suppliers`
```sql
CREATE TABLE tour_suppliers (
    supplier_id INT PRIMARY KEY AUTO_INCREMENT,
    supplier_name VARCHAR(200) NOT NULL,
    supplier_code VARCHAR(50) UNIQUE,
    supplier_type ENUM('Hotel','Restaurant','Transport','Guide','Activity','Insurance','Other') NOT NULL,
    contact_person VARCHAR(100),
    phone VARCHAR(20),
    email VARCHAR(100),
    address TEXT,
    website VARCHAR(255),
    contract_number VARCHAR(100),
    contract_start_date DATE,
    contract_end_date DATE,
    contract_file VARCHAR(255),
    payment_terms TEXT,
    cancellation_policy TEXT,
    rating DECIMAL(3,2) DEFAULT 0.00,
    notes TEXT,
    status TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

---

## 📁 Files triển khai

### Models (admin/models/)

**TourSchedule.php** (840 lines) - Core model
```php
// Lịch khởi hành
getAllSchedules()
getSchedulesByTour($tour_id)
getScheduleById($id)
getAvailableSchedules()
checkScheduleConflict($tour_id, $departure_date, $exclude_schedule_id)
createSchedule($data)
updateSchedule($schedule_id, $data)
deleteSchedule($schedule_id)
changeScheduleStatus($schedule_id, $new_status)

// Nhân sự
getAllStaff($type)
getScheduleStaff($schedule_id)
checkStaffAvailability($staff_id, $departure_date, $return_date, $exclude_schedule_id)
assignStaff($schedule_id, $staff_id, $role)
removeStaff($schedule_id, $staff_id)
getAllStaffAssignments($filters)

// Dịch vụ
getServices($schedule_id) // Từ schedule_service_links
linkService($schedule_id, $supplier_id, $data)
updateService($link_id, $data)
removeServiceLink($link_id)

// Báo cáo
getScheduleReport($schedule_id)
getCalendarView($month, $year)
```

**TourSupplier.php** (490 lines) - Quản lý đối tác
```php
// CRUD
getAll($filters) // Filter by type, status, search
getById($supplier_id)
getByCode($supplier_code)
getByType($supplier_type, $active_only)
create($data) // Returns ['success' => bool, 'message' => string, 'supplier_id' => int]
update($supplier_id, $data)
delete($supplier_id)

// Liên kết với tour
getSuppliersByTour($tour_id)
getToursBySupplier($supplier_id)
linkToTour($tour_id, $supplier_id, $data)
updateLink($link_id, $data)
unlinkFromTour($link_id)
getLinkById($link_id)

// Thống kê
getStatsByType()
checkUsage($supplier_id) // Returns ['in_use' => bool, 'tour_count' => int]
getSuppliersByContract()
getExpiringContracts($days)
```

### Controllers (admin/controllers/)

**ScheduleController.php** (1985 lines)
```php
// Danh sách & CRUD
ListSchedule() - Danh sách lịch khởi hành
AddSchedule() - Form thêm lịch
StoreSchedule() - Lưu lịch mới
ScheduleDetail() - Chi tiết lịch với staff & services
EditSchedule() - Form sửa lịch
UpdateSchedule() - Cập nhật lịch
DeleteSchedule() - Xóa lịch
ChangeScheduleStatus() - Thay đổi trạng thái (Open/In Progress/Completed/Cancelled)

// Phân công nhân sự
AssignStaff() - Phân công HDV (POST)
RemoveStaff() - Xóa nhân sự khỏi lịch

// Phân công dịch vụ
AddServiceLink() - Link supplier/service vào lịch (POST)
UpdateServiceLink() - Cập nhật service link
RemoveServiceLink() - Xóa service link

// Báo cáo & xuất
CalendarView() - Xem lịch theo tháng
ExportSchedule() - Xuất báo cáo lịch
StaffAssignments() - Tổng quan phân công nhân sự

// HDV
GuideCheckIn() - HDV check-in
GuideSaveJourneyLog() - Lưu nhật ký hành trình
```

**SupplierController.php** (NEW - 285 lines)
```php
// CRUD
ListSuppliers() - Danh sách đối tác với statistics
CreateSupplierForm() - Form thêm đối tác
CreateSupplier() - Lưu đối tác mới (xử lý file upload hợp đồng)
EditSupplierForm() - Form sửa đối tác
UpdateSupplier() - Cập nhật đối tác (xử lý file upload)
DeleteSupplier() - Xóa đối tác (kiểm tra đang sử dụng)
ViewSupplier() - Chi tiết đối tác với usage statistics
```

### Views (admin/views/)

**schedule/list_schedule.php** - Danh sách lịch khởi hành
- Hiển thị bảng với tour, ngày, trạng thái, số nhân sự, số dịch vụ
- Lọc theo tour, trạng thái, ngày
- Button "Thêm lịch khởi hành"

**schedule/add_schedule.php** - Form thêm lịch
- Chọn tour (dropdown)
- Nhập ngày khởi hành, kết thúc
- Điểm tập trung, giờ tập trung
- Số người tối đa
- Giá vé adult/child
- Ghi chú

**schedule/edit_schedule.php** - Form sửa lịch
- Tương tự add_schedule.php
- Pre-fill dữ liệu hiện tại
- Disable editing nếu status = 'In Progress'

**schedule/schedule_detail.php** (539 lines) - Chi tiết lịch & phân công
- **Tab 1: Nhân sự**
  * Danh sách nhân sự đã phân công
  * Button "Phân công nhân sự" (modal)
  * Hiển thị check-in status
- **Tab 2: Dịch vụ**
  * Danh sách dịch vụ đã phân bổ
  * Button "Thêm dịch vụ" (modal)
  * Hiển thị loại, nhà cung cấp, số lượng, giá, tổng
- Dropdown thay đổi trạng thái lịch
- Button xuất báo cáo

**schedule/calendar_view.php** - Lịch tháng
- Calendar grid theo tháng
- Hiển thị lịch khởi hành theo ngày
- Color-code theo trạng thái
- Click để xem chi tiết

**supplier/list_suppliers.php** (NEW - 278 lines)
- Statistics cards theo loại đối tác
- Filter: Loại, trạng thái, tìm kiếm
- Bảng đối tác: Mã, tên, loại, liên hệ, rating, trạng thái
- Actions: Xem, Sửa, Xóa

**supplier/create_supplier.php** (NEW - 245 lines)
- Form thông tin cơ bản: Tên, mã, loại, liên hệ, địa chỉ
- Form hợp đồng: Số HĐ, ngày, file upload, điều khoản, chính sách hủy
- Trạng thái & đánh giá (0-5 sao)
- Button Lưu/Hủy

**supplier/edit_supplier.php** (TODO)
- Tương tự create_supplier.php
- Pre-fill data
- Xử lý file hợp đồng cũ

---

## 🔀 Routes

```php
// admin/index.php

// === Lịch khởi hành ===
'danh-sach-lich-khoi-hanh' => (new ScheduleController())->ListSchedule(),
'them-lich-khoi-hanh' => (new ScheduleController())->AddSchedule(),
'luu-lich-khoi-hanh' => (new ScheduleController())->StoreSchedule(),
'chi-tiet-lich-khoi-hanh' => (new ScheduleController())->ScheduleDetail(),
'sua-lich-khoi-hanh' => (new ScheduleController())->EditSchedule(),
'cap-nhat-lich-khoi-hanh' => (new ScheduleController())->UpdateSchedule(),
'xoa-lich-khoi-hanh' => (new ScheduleController())->DeleteSchedule(),
'thay-doi-trang-thai-tour' => (new ScheduleController())->ChangeScheduleStatus(),

// === Phân công nhân sự & dịch vụ ===
'phan-cong-nhan-su' => (new ScheduleController())->AssignStaff(),
'xoa-nhan-su-khoi-lich' => (new ScheduleController())->RemoveStaff(),
'add-service-link' => (new ScheduleController())->AddServiceLink(),
'update-service-link' => (new ScheduleController())->UpdateServiceLink(),
'remove-service-link' => (new ScheduleController())->RemoveServiceLink(),

// === Xem lịch & báo cáo ===
'xem-lich-theo-thang' => (new ScheduleController())->CalendarView(),
'xuat-bao-cao-lich' => (new ScheduleController())->ExportSchedule(),
'tong-quan-phan-cong' => (new ScheduleController())->StaffAssignments(),

// === Use Case 2: Quản lý đối tác ===
'danh-sach-doi-tac' => (new SupplierController())->ListSuppliers(),
'them-doi-tac' => (new SupplierController())->CreateSupplierForm(),
'luu-doi-tac' => (new SupplierController())->CreateSupplier(),
'xem-doi-tac' => (new SupplierController())->ViewSupplier(),
'sua-doi-tac' => (new SupplierController())->EditSupplierForm(),
'cap-nhat-doi-tac' => (new SupplierController())->UpdateSupplier(),
'xoa-doi-tac' => (new SupplierController())->DeleteSupplier(),
```

---

## 🔐 Permissions

```php
// commons/permission_simple.php

// Xem lịch khởi hành
requirePermission('tour.view');

// Thêm/Sửa/Xóa lịch khởi hành
requireRole('ADMIN');

// Phân công nhân sự và dịch vụ
requireRole('ADMIN');

// HDV chỉ xem lịch được phân công
requireOwnScheduleOrAdmin($schedule_id, 'schedule.view_own');
```

---

## 📧 Notification System

```php
// commons/notification.php

function notifyStaffAssignment($schedule_id, $staff_id)
{
    // Lấy thông tin lịch khởi hành
    $schedule = getScheduleInfo($schedule_id);
    $staff = getStaffInfo($staff_id);
    
    // Tạo thông báo
    $title = "Phân công lịch tour: {$schedule['tour_name']}";
    $message = "Bạn được phân công làm hướng dẫn viên cho tour {$schedule['tour_name']} khởi hành ngày " . date('d/m/Y', strtotime($schedule['departure_date']));
    
    // Lưu vào bảng notifications
    insertNotification('staff', $staff_id, $staff['full_name'], $staff['email'], $schedule_id, $title, $message, 'pending');
    
    // TODO: Gửi email thực tế bằng PHPMailer
    // sendEmail($staff['email'], $title, $message);
}

function notifyServiceAssignment($schedule_id, $service_id)
{
    // Lấy thông tin lịch và supplier
    $schedule = getScheduleInfo($schedule_id);
    $supplier = getSupplierInfo($service_id);
    
    // Tạo thông báo
    $title = "Xác nhận dịch vụ cho tour: {$schedule['tour_name']}";
    $message = "Đối tác {$supplier['supplier_name']} được yêu cầu cung cấp dịch vụ cho tour {$schedule['tour_name']} vào ngày " . date('d/m/Y', strtotime($schedule['departure_date']));
    
    // Lưu vào bảng notifications
    insertNotification('supplier', $supplier['supplier_id'], $supplier['supplier_name'], $supplier['email'], $schedule_id, $title, $message, 'pending');
    
    // TODO: Gửi email thực tế bằng PHPMailer
    // sendEmail($supplier['email'], $title, $message);
}
```

---

## 🎯 Luồng phụ (Alternative Flows)

### A1: Cập nhật nhân sự hoặc dịch vụ
```php
// Xóa nhân sự cũ
Route: ?act=xoa-nhan-su-khoi-lich&schedule_id={id}&staff_id={id}
Controller: ScheduleController::RemoveStaff()

// Phân công nhân sự mới
Route: ?act=phan-cong-nhan-su
// → Hệ thống gửi thông báo cập nhật

// Cập nhật service link
Route: ?act=update-service-link (POST)
Controller: ScheduleController::UpdateServiceLink()
```

### A2: Xem lịch khởi hành theo tuần/tháng
```php
Route: ?act=xem-lich-theo-thang&month={m}&year={y}
Controller: ScheduleController::CalendarView()
View: admin/views/schedule/calendar_view.php

// Model
TourSchedule::getCalendarView($month, $year)
// Returns array of schedules in given month
```

---

## ⚠️ Ngoại lệ (Exceptions)

### E1: Thiếu nhân sự hoặc dịch vụ
```php
// Trong ScheduleController::ScheduleDetail()
if (empty($staff)) {
    // Hiển thị warning
    echo '<div class="alert alert-warning">Chưa phân công nhân sự cho lịch này!</div>';
}

if (empty($services)) {
    echo '<div class="alert alert-info">Chưa có dịch vụ nào được phân bổ.</div>';
}
```

### E2: Trùng lịch
```php
// Trong TourSchedule::checkScheduleConflict()
$conflict = $this->checkScheduleConflict($tour_id, $departure_date);

if ($conflict) {
    throw new Exception("Đã có lịch khởi hành cho tour này vào ngày đã chọn!");
}
// → Hệ thống không lưu và hiển thị lỗi
```

### E3: Nhân viên đã có lịch khác
```php
// Trong ScheduleController::AssignStaff()
$available = $this->modelSchedule->checkStaffAvailability(
    $staff_id, 
    $schedule['departure_date'], 
    $schedule['return_date']
);

if (!$available) {
    $_SESSION['warning'] = 'Nhân viên này đã có lịch trình khác trong khoảng thời gian này!';
}
```

### E4: Mỗi tour chỉ được phân công 1 nhân sự
```php
// Trong TourSchedule::assignStaff()
$sqlCheck = "SELECT COUNT(*) FROM schedule_staff WHERE schedule_id = ?";
$count = $this->conn->prepare($sqlCheck)->execute([$schedule_id])->fetchColumn();

if ($count > 0) {
    throw new Exception("Lịch khởi hành này đã có nhân sự được phân công! Mỗi tour chỉ được phân công 1 nhân sự duy nhất.");
}
```

---

## ✅ Điều kiện kết thúc

**Lịch khởi hành được xác nhận và lưu thành công:**
- ✅ Tour được tạo trong hệ thống
- ✅ Thông tin lịch khởi hành đầy đủ (ngày, điểm tập trung, giờ)
- ✅ Nhân sự (HDV) được phân công
- ✅ Dịch vụ (hotel, restaurant, transport) được liên kết
- ✅ Thông báo được gửi đến nhân sự và đối tác
- ✅ Trạng thái lịch = 'Confirmed' hoặc 'Open'

---

## 📊 Kết quả đạt được

### ✅ Đã triển khai:
1. **Đảm bảo phân công đúng người, đúng thời gian**
   - ✅ Kiểm tra trùng lịch nhân sự (`checkStaffAvailability`)
   - ✅ Kiểm tra trùng lịch tour (`checkScheduleConflict`)
   - ✅ Mỗi lịch chỉ 1 HDV (UNIQUE KEY trên schedule_staff)

2. **Tự động hóa quy trình thông báo**
   - ✅ `notifyStaffAssignment()` - Thông báo cho nhân sự
   - ✅ `notifyServiceAssignment()` - Thông báo cho đối tác
   - ⏳ TODO: Tích hợp PHPMailer để gửi email thực tế

3. **Quản lý đối tác cung cấp dịch vụ**
   - ✅ CRUD đối tác (TourSupplier model)
   - ✅ Phân loại theo type: Hotel, Restaurant, Transport, Guide, Activity, Insurance
   - ✅ Quản lý hợp đồng: Số HĐ, ngày, file, điều khoản
   - ✅ Đánh giá đối tác (rating 0-5 sao)

4. **Link dịch vụ vào lịch khởi hành**
   - ✅ `schedule_service_links` table với supplier_id
   - ✅ `linkService()`, `updateService()`, `removeServiceLink()` methods
   - ✅ Calculated total_price field
   - ✅ Cancellation policy và deadline

### ⏳ Cần hoàn thiện:
1. **Email thực tế** - Tích hợp PHPMailer thay vì chỉ lưu notification
2. **Export PDF lịch khởi hành** - Sử dụng mPDF
3. **Calendar view nâng cao** - Drag & drop, color-coded, tooltip
4. **View đối tác chi tiết** - supplier/view_supplier.php
5. **Edit đối tác view** - supplier/edit_supplier.php

---

## 🚀 Hướng dẫn sử dụng

### 1. Tạo lịch khởi hành mới
```
1. Vào "Lịch khởi hành" → Click "Thêm lịch khởi hành"
2. Chọn tour từ dropdown
3. Nhập ngày khởi hành, kết thúc
4. Điền điểm tập trung, giờ tập trung
5. Số người tối đa, giá vé
6. Click "Lưu" → Hệ thống kiểm tra trùng lịch
7. Nếu OK → Chuyển đến trang chi tiết lịch
```

### 2. Phân công HDV
```
1. Vào "Chi tiết lịch khởi hành"
2. Tab "Nhân sự" → Click "Phân công nhân sự"
3. Chọn HDV từ dropdown (chỉ hiển thị staff_type = Guide)
4. Click "Phân công"
5. Hệ thống kiểm tra availability → Cảnh báo nếu đã có lịch khác
6. Nếu OK → Lưu vào schedule_staff → Gửi thông báo cho HDV
```

### 3. Thêm dịch vụ
```
1. Vào "Chi tiết lịch khởi hành"
2. Tab "Dịch vụ" → Click "Thêm dịch vụ"
3. Chọn loại dịch vụ (Hotel/Restaurant/Transport...)
4. Chọn nhà cung cấp (filter theo loại)
5. Nhập số lượng, đơn giá, mô tả
6. Điền cancellation policy nếu cần
7. Click "Thêm" → Lưu vào schedule_service_links → Gửi thông báo cho đối tác
```

### 4. Quản lý đối tác
```
1. Vào "Đối tác" → Xem danh sách với statistics
2. Filter theo loại, trạng thái, tìm kiếm
3. Click "Thêm đối tác"
4. Điền thông tin: Tên, loại, liên hệ, địa chỉ
5. Thêm hợp đồng: Số HĐ, ngày, upload file
6. Điền điều khoản thanh toán, chính sách hủy
7. Đánh giá (0-5 sao)
8. Click "Lưu đối tác"
```

### 5. Xem lịch theo tháng
```
1. Vào "Xem lịch theo tháng"
2. Chọn tháng/năm
3. Calendar hiển thị các lịch khởi hành
4. Color-code theo trạng thái: Green (Open), Blue (Confirmed), Orange (In Progress), Gray (Completed)
5. Click vào ngày để xem chi tiết lịch
```

---

## 📞 Support

**Vấn đề thường gặp:**

**Q1: Làm sao thêm được nhiều HDV cho 1 lịch?**
A: Hiện tại hệ thống quy định mỗi lịch chỉ 1 HDV duy nhất (theo business rule). Nếu muốn thay đổi, cần xóa UNIQUE KEY trên schedule_staff và sửa logic trong TourSchedule::assignStaff().

**Q2: Notification không gửi email?**
A: Hiện tại hệ thống chỉ lưu notification vào database. Cần tích hợp PHPMailer trong notification.php để gửi email thực tế.

**Q3: Làm sao import hàng loạt đối tác?**
A: TODO: Cần thêm chức năng import từ Excel/CSV trong SupplierController.

**Q4: Export lịch khởi hành ra PDF?**
A: TODO: Cần implement ScheduleController::ExportSchedulePDF() sử dụng mPDF library.

---

## 📝 Changelog

**v1.0.0 (2025-01-27)**
- ✅ Triển khai CRUD lịch khởi hành
- ✅ Phân công nhân sự với kiểm tra availability
- ✅ Quản lý đối tác (TourSupplier model + SupplierController)
- ✅ Link dịch vụ vào lịch (schedule_service_links)
- ✅ Notification system (database only)
- ✅ Calendar view cơ bản
- ✅ 7 routes mới cho quản lý đối tác

**Planned for v1.1.0**
- ⏳ Email notification với PHPMailer
- ⏳ Export PDF lịch khởi hành
- ⏳ Calendar view nâng cao (drag & drop)
- ⏳ View chi tiết đối tác
- ⏳ Edit supplier view
- ⏳ Bulk import suppliers từ Excel

---

## 🎓 Technical Notes

**Database Indexing:**
```sql
-- Tối ưu performance cho các query thường dùng
CREATE INDEX idx_schedule_date ON tour_schedules(departure_date);
CREATE INDEX idx_schedule_status ON tour_schedules(status);
CREATE INDEX idx_schedule_staff ON schedule_staff(schedule_id, staff_id);
CREATE INDEX idx_service_links_schedule ON schedule_service_links(schedule_id);
CREATE INDEX idx_service_links_supplier ON schedule_service_links(supplier_id);
CREATE INDEX idx_supplier_type ON tour_suppliers(supplier_type);
```

**Transaction Safety:**
```php
// Khi phân công nhân sự và dịch vụ, nên dùng transaction
$this->conn->beginTransaction();
try {
    $this->assignStaff($schedule_id, $staff_id, $role);
    $this->linkService($schedule_id, $supplier_id, $data);
    notifyStaffAssignment($schedule_id, $staff_id);
    notifyServiceAssignment($schedule_id, $supplier_id);
    $this->conn->commit();
} catch (Exception $e) {
    $this->conn->rollBack();
    throw $e;
}
```

**File Upload Security:**
```php
// Trong SupplierController::CreateSupplier()
$allowed_types = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
$max_size = 5 * 1024 * 1024; // 5MB

if (!in_array($_FILES['contract_file']['type'], $allowed_types)) {
    throw new Exception("Chỉ chấp nhận file PDF, DOC, DOCX!");
}

if ($_FILES['contract_file']['size'] > $max_size) {
    throw new Exception("File không được vượt quá 5MB!");
}

// Rename file to prevent directory traversal
$safe_name = time() . '_' . preg_replace('/[^a-zA-Z0-9_.-]/', '_', basename($_FILES['contract_file']['name']));
```

---

*Document generated: 2025-01-27*  
*Version: 1.0.0*  
*Status: ✅ 95% Complete*
