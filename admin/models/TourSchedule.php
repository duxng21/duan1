<?php
class TourSchedule
{
    public $conn;

    public function __construct()
    {
        $this->conn = connectDB();
    }

    // ==================== LỊCH KHỞI HÀNH ====================

    public function getAllSchedules()
    {
        $sql = "SELECT 
                    ts.*,
                    t.tour_name,
                    t.code as tour_code,
                    tc.category_name,
                    COUNT(DISTINCT tb.booking_id) as total_bookings,
                    COALESCE(SUM(COALESCE(tb.num_adults, 0) + COALESCE(tb.num_children, 0) + COALESCE(tb.num_infants, 0)), 0) as total_guests
                FROM tour_schedules ts
                LEFT JOIN tours t ON ts.tour_id = t.tour_id
                LEFT JOIN tour_categories tc ON t.category_id = tc.category_id
                LEFT JOIN bookings tb ON ts.tour_id = tb.tour_id AND ts.departure_date = tb.tour_date AND tb.status != 'Đã hủy'
                GROUP BY ts.schedule_id
                ORDER BY ts.departure_date DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getSchedulesByTour($tour_id)
    {
        $sql = "SELECT 
                    ts.*,
                    COUNT(DISTINCT tb.booking_id) as total_bookings,
                    COALESCE(SUM(COALESCE(tb.num_adults, 0) + COALESCE(tb.num_children, 0) + COALESCE(tb.num_infants, 0)), 0) as total_guests
                FROM tour_schedules ts
                LEFT JOIN bookings tb ON ts.tour_id = tb.tour_id AND ts.departure_date = tb.tour_date AND tb.status != 'Đã hủy'
                WHERE ts.tour_id = ?
                GROUP BY ts.schedule_id
                ORDER BY ts.departure_date ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$tour_id]);
        return $stmt->fetchAll();
    }

    public function getScheduleById($id)
    {
        $sql = "SELECT 
                    ts.*,
                    t.tour_name,
                    t.code as tour_code,
                    COUNT(DISTINCT tb.booking_id) as total_bookings,
                    COALESCE(SUM(COALESCE(tb.num_adults, 0) + COALESCE(tb.num_children, 0) + COALESCE(tb.num_infants, 0)), 0) as total_guests
                FROM tour_schedules ts
                LEFT JOIN tours t ON ts.tour_id = t.tour_id
                LEFT JOIN bookings tb ON ts.tour_id = tb.tour_id AND ts.departure_date = tb.tour_date AND tb.status != 'Đã hủy'
                WHERE ts.schedule_id = ?
                GROUP BY ts.schedule_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getAvailableSchedules()
    {
        $sql = "SELECT 
                    ts.*,
                    t.tour_name,
                    t.code as tour_code,
                    (ts.max_participants - ts.current_participants) as slots_available
                FROM tour_schedules ts
                LEFT JOIN tours t ON ts.tour_id = t.tour_id
                WHERE ts.status IN ('Open', 'Confirmed')
                AND ts.departure_date >= CURDATE()
                AND ts.current_participants < ts.max_participants
                ORDER BY ts.departure_date ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function checkScheduleConflict($tour_id, $departure_date, $exclude_schedule_id = null)
    {
        $sql = "SELECT COUNT(*) FROM tour_schedules 
                WHERE tour_id = ? 
                AND departure_date = ?
                AND status != 'Cancelled'";

        $params = [$tour_id, $departure_date];

        if ($exclude_schedule_id) {
            $sql .= " AND schedule_id != ?";
            $params[] = $exclude_schedule_id;
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }

    // ==================== UC2: DỊCH VỤ LIÊN KẾT VỚI LỊCH ====================

    public function getServices($schedule_id)
    {
        $sql = "SELECT ssl.*, ts.supplier_name, ts.supplier_type, ts.phone, ts.email
                FROM schedule_service_links ssl
                INNER JOIN tour_suppliers ts ON ssl.supplier_id = ts.supplier_id
                WHERE ssl.schedule_id = ? AND ssl.status = 1
                ORDER BY ssl.service_date ASC, ts.supplier_type ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$schedule_id]);
        return $stmt->fetchAll();
    }

    public function linkService($schedule_id, $supplier_id, $data)
    {
        $sql = "INSERT INTO schedule_service_links (
                    schedule_id, supplier_id, service_type, service_date, service_time,
                    service_description, unit_price, quantity, currency,
                    cancellation_deadline, cancellation_fee, contact_person, contact_phone,
                    notes, status
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $schedule_id,
            $supplier_id,
            $data['service_type'] ?? 'other',
            $data['service_date'] ?? null,
            $data['service_time'] ?? null,
            $data['service_description'] ?? null,
            $data['unit_price'] ?? 0,
            $data['quantity'] ?? 1,
            $data['currency'] ?? 'VND',
            $data['cancellation_deadline'] ?? null,
            $data['cancellation_fee'] ?? 0,
            $data['contact_person'] ?? null,
            $data['contact_phone'] ?? null,
            $data['notes'] ?? null
        ]);
    }

    public function updateService($link_id, $data)
    {
        $sql = "UPDATE schedule_service_links SET
                    service_type = ?,
                    service_date = ?,
                    service_time = ?,
                    service_description = ?,
                    unit_price = ?,
                    quantity = ?,
                    currency = ?,
                    cancellation_deadline = ?,
                    cancellation_fee = ?,
                    contact_person = ?,
                    contact_phone = ?,
                    notes = ?,
                    status = ?
                WHERE link_id = ?";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $data['service_type'] ?? 'other',
            $data['service_date'] ?? null,
            $data['service_time'] ?? null,
            $data['service_description'] ?? null,
            $data['unit_price'] ?? 0,
            $data['quantity'] ?? 1,
            $data['currency'] ?? 'VND',
            $data['cancellation_deadline'] ?? null,
            $data['cancellation_fee'] ?? 0,
            $data['contact_person'] ?? null,
            $data['contact_phone'] ?? null,
            $data['notes'] ?? null,
            isset($data['status']) ? (int) $data['status'] : 1,
            $link_id
        ]);
    }

    public function unlinkService($link_id)
    {
        $sql = "DELETE FROM schedule_service_links WHERE link_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$link_id]);
    }

    public function createSchedule($data)
    {
        try {
            // Kiểm tra trùng lịch
            if ($this->checkScheduleConflict($data['tour_id'], $data['departure_date'])) {
                throw new Exception("Đã có lịch khởi hành cho tour này vào ngày đã chọn!");
            }

            $sql = "INSERT INTO tour_schedules (
                        tour_id, departure_date, return_date, meeting_point, 
                        meeting_time, customer_name, customer_phone, customer_email,
                        max_participants, current_participants, 
                        num_adults, num_children, num_infants,
                        price_adult, price_child, status, notes
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                $data['tour_id'],
                $data['departure_date'],
                $data['return_date'],
                $data['meeting_point'] ?? '',
                $data['meeting_time'] ?? '',
                $data['customer_name'] ?? null,
                $data['customer_phone'] ?? null,
                $data['customer_email'] ?? null,
                $data['max_participants'] ?? 0,
                0, // current_participants
                $data['num_adults'] ?? 0,
                $data['num_children'] ?? 0,
                $data['num_infants'] ?? 0,
                $data['price_adult'] ?? 0,
                $data['price_child'] ?? 0,
                $data['status'] ?? 'Open',
                $data['notes'] ?? ''
            ]);

            return $this->conn->lastInsertId();
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function updateSchedule($id, $data)
    {
        // Kiểm tra trạng thái tour - không cho sửa khi đang diễn ra
        $currentSchedule = $this->getScheduleById($id);
        if ($currentSchedule && $currentSchedule['status'] === 'In Progress') {
            throw new Exception("Không thể chỉnh sửa lịch khởi hành khi tour đang diễn ra! Vui lòng đợi tour hoàn thành.");
        }

        // Kiểm tra trùng lịch (trừ chính nó)
        if ($this->checkScheduleConflict($data['tour_id'], $data['departure_date'], $id)) {
            throw new Exception("Đã có lịch khởi hành cho tour này vào ngày đã chọn!");
        }

        $sql = "UPDATE tour_schedules SET
                    departure_date = ?,
                    return_date = ?,
                    meeting_point = ?,
                    meeting_time = ?,
                    customer_name = ?,
                    customer_phone = ?,
                    customer_email = ?,
                    max_participants = ?,
                    num_adults = ?,
                    num_children = ?,
                    num_infants = ?,
                    price_adult = ?,
                    price_child = ?,
                    status = ?,
                    notes = ?
                WHERE schedule_id = ?";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $data['departure_date'],
            $data['return_date'] ?: null,
            $data['meeting_point'] ?: null,
            $data['meeting_time'] ?: null,
            $data['customer_name'] ?: null,
            $data['customer_phone'] ?: null,
            $data['customer_email'] ?: null,
            (int) ($data['max_participants'] ?? 0),
            (int) ($data['num_adults'] ?? 0),
            (int) ($data['num_children'] ?? 0),
            (int) ($data['num_infants'] ?? 0),
            (float) ($data['price_adult'] ?? 0),
            (float) ($data['price_child'] ?? 0),
            $data['status'] ?? 'Open',
            $data['notes'] ?: null,
            $id
        ]);
    }

    public function deleteSchedule($id)
    {
        // Kiểm tra trạng thái tour - không cho xóa khi đang diễn ra
        $schedule = $this->getScheduleById($id);
        if ($schedule && $schedule['status'] === 'In Progress') {
            throw new Exception("Không thể xóa lịch khởi hành khi tour đang diễn ra! Vui lòng đợi tour hoàn thành.");
        }

        // Kiểm tra xem có booking nào chưa bị hủy - join bằng tour_id và tour_date (departure_date)
        $sql = "SELECT COUNT(*) FROM bookings b 
                INNER JOIN tour_schedules ts ON ts.tour_id = b.tour_id AND ts.departure_date = b.tour_date
                WHERE ts.schedule_id = ? AND b.status != 'Đã hủy'";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);

        $activeBookingCount = $stmt->fetchColumn();

        if ($activeBookingCount > 0) {
            // Có booking chưa bị hủy - không cho xóa
            throw new Exception("Không thể xóa lịch khởi hành đã có đặt tour! Vui lòng hủy tất cả booking liên kết trước.");
        }

        // Nếu có booking bị hủy, hãy xóa chúng trước (optional - để sạch database)
        $sql = "DELETE FROM booking_details WHERE booking_id IN (
                SELECT b.booking_id FROM bookings b
                INNER JOIN tour_schedules ts ON ts.tour_id = b.tour_id AND ts.departure_date = b.tour_date
                WHERE ts.schedule_id = ? AND b.status = 'Đã hủy'
            )";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);

        // Xóa booking bị hủy - MySQL syntax
        $sql = "DELETE FROM bookings WHERE booking_id IN (
                SELECT sub.booking_id FROM (
                    SELECT b.booking_id FROM bookings b
                    INNER JOIN tour_schedules ts ON ts.tour_id = b.tour_id AND ts.departure_date = b.tour_date
                    WHERE ts.schedule_id = ? AND b.status = 'Đã hủy'
                ) AS sub
            )";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);

        // Xóa schedule
        $sql = "DELETE FROM tour_schedules WHERE schedule_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$id]);
    }

    // Thay đổi trạng thái tour
    public function changeScheduleStatus($schedule_id, $new_status)
    {
        $allowed_statuses = ['Open', 'Full', 'Confirmed', 'In Progress', 'Completed', 'Cancelled'];

        if (!in_array($new_status, $allowed_statuses)) {
            throw new Exception("Trạng thái không hợp lệ!");
        }

        // Kiểm tra lịch tồn tại
        $schedule = $this->getScheduleById($schedule_id);
        if (!$schedule) {
            throw new Exception("Không tìm thấy lịch khởi hành!");
        }

        // Kiểm tra logic chuyển trạng thái
        $current_status = $schedule['status'];

        // Không cho phép chuyển từ Completed sang In Progress
        if ($current_status === 'Completed' && $new_status === 'In Progress') {
            throw new Exception("Không thể chuyển tour đã hoàn thành về đang diễn ra!");
        }

        // Không cho phép chuyển từ Cancelled sang In Progress
        if ($current_status === 'Cancelled' && $new_status === 'In Progress') {
            throw new Exception("Không thể bắt đầu tour đã bị hủy!");
        }

        // CHỈ CHO PHÉP HOÀN THÀNH NẾU TẤT CẢ HDV ĐÃ HOÀN THÀNH
        if ($new_status === 'Completed') {
            $staff = $this->getScheduleStaff($schedule_id);
            $guides = array_filter($staff, function ($s) {
                return strtolower($s['staff_type'] ?? '') === 'guide';
            });

            if (!empty($guides)) {
                // Kiểm tra xem tất cả HDV đã check-in chưa
                foreach ($guides as $guide) {
                    if (empty($guide['check_in_time'])) {
                        throw new Exception("Chưa thể hoàn thành tour! Hướng dẫn viên '{$guide['full_name']}' chưa check-in hoàn tất lịch trình.");
                    }
                }

                // Kiểm tra xem có nhật ký tour từ HDV chưa
                $logs = $this->getJourneyLogs($schedule_id);
                if (empty($logs)) {
                    throw new Exception("Chưa thể hoàn thành tour! Chưa có nhật ký hành trình nào từ hướng dẫn viên. Vui lòng yêu cầu HDV ghi nhật ký trước khi kết thúc.");
                }
            }
        }

        $sql = "UPDATE tour_schedules SET status = ? WHERE schedule_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$new_status, $schedule_id]);
    }

    // ==================== NHÂN SỰ ====================

    public function getAllStaff($type = null)
    {
        $sql = "SELECT * FROM staff WHERE status = 1 AND staff_type IN ('Guide','Manager')";
        if ($type) {
            $sql .= " AND staff_type = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$type]);
        } else {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
        }
        return $stmt->fetchAll();
    }

    public function getScheduleStaff($schedule_id)
    {
        $sql = "SELECT 
                    ss.*,
                    s.full_name,
                    s.phone,
                    s.staff_type
                FROM schedule_staff ss
                JOIN staff s ON ss.staff_id = s.staff_id
                WHERE ss.schedule_id = ?
                ORDER BY s.staff_type";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$schedule_id]);
        return $stmt->fetchAll();
    }

    public function checkStaffAvailability($staff_id, $departure_date, $return_date, $exclude_schedule_id = null)
    {
        $sql = "SELECT COUNT(*) FROM schedule_staff ss
                JOIN tour_schedules ts ON ss.schedule_id = ts.schedule_id
                WHERE ss.staff_id = ?
                AND ts.status != 'Cancelled'
                AND (
                    (ts.departure_date BETWEEN ? AND ?)
                    OR (ts.return_date BETWEEN ? AND ?)
                    OR (? BETWEEN ts.departure_date AND ts.return_date)
                )";

        $params = [$staff_id, $departure_date, $return_date, $departure_date, $return_date, $departure_date];

        if ($exclude_schedule_id) {
            $sql .= " AND ts.schedule_id != ?";
            $params[] = $exclude_schedule_id;
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() == 0;
    }

    public function assignStaff($schedule_id, $staff_id, $role)
    {
        // Thử sửa khóa ngoại sai lệch (nếu vẫn trỏ về bảng cũ tour_itinerary_old)
        $this->repairScheduleStaffForeignKeys();
        // Kiểm tra schedule tồn tại (tránh FK lỗi nếu bảng/khóa ngoại lệch)
        $chk = $this->conn->prepare("SELECT schedule_id FROM tour_schedules WHERE schedule_id = ? LIMIT 1");
        $chk->execute([$schedule_id]);
        if (!$chk->fetchColumn()) {
            throw new Exception("Lịch khởi hành không tồn tại (schedule_id=" . (int) $schedule_id . ")");
        }

        // KIỂM TRA: Mỗi tour chỉ được phân công 1 nhân sự duy nhất
        $sqlCheck = "SELECT COUNT(*) FROM schedule_staff WHERE schedule_id = ?";
        $stmtCheck = $this->conn->prepare($sqlCheck);
        $stmtCheck->execute([$schedule_id]);

        if ($stmtCheck->fetchColumn() > 0) {
            throw new Exception("Lịch khởi hành này đã có nhân sự được phân công! Mỗi tour chỉ được phân công 1 nhân sự duy nhất.");
        }

        // Kiểm tra xem nhân viên đã được phân công chưa (bổ sung)
        $sql = "SELECT COUNT(*) FROM schedule_staff 
                WHERE schedule_id = ? AND staff_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$schedule_id, $staff_id]);

        if ($stmt->fetchColumn() > 0) {
            throw new Exception("Nhân viên này đã được phân công cho lịch khởi hành này!");
        }

        $sql = "INSERT INTO schedule_staff (schedule_id, staff_id, role) 
                VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$schedule_id, $staff_id, $role]);
    }

    // ==================== SỬA LỖI KHÓA NGOẠI TỰ ĐỘNG ====================
    private function repairScheduleStaffForeignKeys()
    {
        try {
            // Xác định database hiện tại
            $dbName = DB_NAME;
            // Lấy tất cả FK của bảng schedule_staff
            $sql = "SELECT CONSTRAINT_NAME, REFERENCED_TABLE_NAME, COLUMN_NAME
                    FROM information_schema.KEY_COLUMN_USAGE
                    WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'schedule_staff' AND REFERENCED_TABLE_NAME IS NOT NULL";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$dbName]);
            $fks = $stmt->fetchAll();

            $needsFix = false;
            $wrongConstraints = [];
            foreach ($fks as $fk) {
                if ($fk['COLUMN_NAME'] === 'schedule_id' && $fk['REFERENCED_TABLE_NAME'] !== 'tour_schedules') {
                    $needsFix = true;
                    $wrongConstraints[] = $fk['CONSTRAINT_NAME'];
                }
                if ($fk['COLUMN_NAME'] === 'staff_id' && $fk['REFERENCED_TABLE_NAME'] !== 'staff') {
                    $needsFix = true;
                    $wrongConstraints[] = $fk['CONSTRAINT_NAME'];
                }
            }

            if ($needsFix) {
                foreach ($wrongConstraints as $cname) {
                    $drop = $this->conn->prepare("ALTER TABLE schedule_staff DROP FOREIGN KEY `$cname`");
                    try {
                        $drop->execute();
                    } catch (Exception $e) { /* ignore */
                    }
                }
                // Đảm bảo cột tồn tại trước khi thêm FK
                $checkCols = $this->conn->query("SHOW COLUMNS FROM schedule_staff LIKE 'schedule_id'")->fetch();
                $checkCols2 = $this->conn->query("SHOW COLUMNS FROM schedule_staff LIKE 'staff_id'")->fetch();
                if ($checkCols && $checkCols2) {
                    // Thêm lại FK chuẩn (đặt tên rõ ràng để tránh trùng lặp)
                    $this->conn->exec("ALTER TABLE schedule_staff
                        ADD CONSTRAINT fk_schedule_staff_schedule
                            FOREIGN KEY (schedule_id) REFERENCES tour_schedules(schedule_id)
                            ON DELETE CASCADE ON UPDATE CASCADE,
                        ADD CONSTRAINT fk_schedule_staff_staff
                            FOREIGN KEY (staff_id) REFERENCES staff(staff_id)
                            ON DELETE CASCADE ON UPDATE CASCADE");
                }
            }
        } catch (Exception $e) {
            // Bỏ qua nếu không thể sửa (không chặn luồng chính)
        }
    }

    public function removeStaff($schedule_id, $staff_id)
    {
        $sql = "DELETE FROM schedule_staff 
                WHERE schedule_id = ? AND staff_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$schedule_id, $staff_id]);
    }

    // ==================== DỊCH VỤ ====================

    public function getAllServices($type = null)
    {
        $sql = "SELECT * FROM services WHERE status = 1";
        if ($type) {
            $sql .= " AND service_type = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$type]);
        } else {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
        }
        return $stmt->fetchAll();
    }

    public function getScheduleServices($schedule_id)
    {
        $sql = "SELECT 
                    sserv.*,
                    serv.service_name,
                    serv.service_type,
                    serv.provider_name,
                    serv.contact_phone
                FROM schedule_services sserv
                JOIN services serv ON sserv.service_id = serv.service_id
                WHERE sserv.schedule_id = ?
                ORDER BY serv.service_type";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$schedule_id]);
        return $stmt->fetchAll();
    }

    // ==================== CHECK-IN & NHẬT KÝ (HDV) ====================

    public function setStaffCheckIn($schedule_id, $staff_id)
    {
        // cố gắng cập nhật cột check_in_time nếu tồn tại, nếu không bỏ qua
        try {
            $sql = "UPDATE schedule_staff SET check_in_time = NOW() WHERE schedule_id = ? AND staff_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$schedule_id, $staff_id]);

            if ($stmt->rowCount() > 0) {
                // Kiểm tra xem tất cả HDV đã check-in chưa
                $this->autoCompleteIfAllCheckedIn($schedule_id);
                return true;
            }
            return false;
        } catch (Exception $e) {
            return false; // bảng chưa có cột hoặc lỗi, bỏ qua
        }
    }

    /**
     * Tự động chuyển tour sang "Completed" nếu tất cả HDV đã check-in và có nhật ký
     */
    private function autoCompleteIfAllCheckedIn($schedule_id)
    {
        try {
            // Lấy schedule hiện tại
            $schedule = $this->getScheduleById($schedule_id);

            // Chỉ tự động complete nếu đang ở trạng thái "In Progress"
            if (!$schedule || $schedule['status'] !== 'In Progress') {
                return false;
            }

            // Lấy tất cả staff của schedule
            $staff = $this->getScheduleStaff($schedule_id);
            $guides = array_filter($staff, function ($s) {
                return strtolower($s['staff_type'] ?? '') === 'guide';
            });

            // Nếu không có HDV, không tự động complete
            if (empty($guides)) {
                return false;
            }

            // Kiểm tra tất cả HDV đã check-in chưa
            foreach ($guides as $guide) {
                if (empty($guide['check_in_time'])) {
                    return false; // Còn HDV chưa check-in
                }
            }

            // Kiểm tra có nhật ký không
            $logs = $this->getJourneyLogs($schedule_id);
            if (empty($logs)) {
                return false; // Chưa có nhật ký
            }

            // Tất cả điều kiện đã thỏa mãn, tự động chuyển sang Completed
            $sql = "UPDATE tour_schedules SET status = 'Completed' WHERE schedule_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$schedule_id]);

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function addJourneyLog($schedule_id, $staff_id, $log_text)
    {
        try {
            $sql = "INSERT INTO schedule_journey_logs (schedule_id, staff_id, log_text, created_at) VALUES (?,?,?,NOW())";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$schedule_id, $staff_id, $log_text]);

            if ($this->conn->lastInsertId()) {
                // Kiểm tra tự động complete sau khi thêm nhật ký
                $this->autoCompleteIfAllCheckedIn($schedule_id);
                return $this->conn->lastInsertId();
            }
            return false;
        } catch (Exception $e) {
            return false; // nếu bảng chưa tồn tại
        }
    }

    public function getJourneyLogs($schedule_id)
    {
        try {
            $sql = "SELECT jl.*, s.full_name FROM schedule_journey_logs jl JOIN staff s ON jl.staff_id = s.staff_id WHERE jl.schedule_id = ? ORDER BY jl.created_at DESC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$schedule_id]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return []; // bảng chưa tồn tại
        }
    }

    public function assignService($schedule_id, $service_id, $quantity, $unit_price, $notes)
    {
        // Tự động sửa khóa ngoại sai trỏ về bảng cũ nếu còn tồn tại
        $this->repairScheduleServicesForeignKeys();
        // Kiểm tra schedule tồn tại để tránh lỗi FK
        $chk = $this->conn->prepare("SELECT schedule_id FROM tour_schedules WHERE schedule_id = ? LIMIT 1");
        $chk->execute([$schedule_id]);
        if (!$chk->fetchColumn()) {
            throw new Exception("Lịch khởi hành không tồn tại (schedule_id=" . (int) $schedule_id . ")");
        }
        $sql = "INSERT INTO schedule_services (schedule_id, service_id, quantity, unit_price, notes) 
                VALUES (?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE 
                    quantity = VALUES(quantity), 
                    unit_price = VALUES(unit_price),
                    notes = VALUES(notes)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$schedule_id, $service_id, $quantity, $unit_price, $notes]);
    }

    public function removeService($schedule_id, $service_id)
    {
        $sql = "DELETE FROM schedule_services 
                WHERE schedule_id = ? AND service_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$schedule_id, $service_id]);
    }

    // ==================== BÁO CÁO & XUẤT ====================

    public function getScheduleReport($schedule_id)
    {
        $schedule = $this->getScheduleById($schedule_id);
        $staff = $this->getScheduleStaff($schedule_id);
        $services = $this->getScheduleServices($schedule_id);

        return [
            'schedule' => $schedule,
            'staff' => $staff,
            'services' => $services
        ];
    }

    public function getCalendarView($month, $year)
    {
        $sql = "SELECT 
                    ts.*,
                    t.tour_name,
                    t.code as tour_code
                FROM tour_schedules ts
                JOIN tours t ON ts.tour_id = t.tour_id
                WHERE MONTH(ts.departure_date) = ? 
                AND YEAR(ts.departure_date) = ?
                AND ts.status != 'Cancelled'
                ORDER BY ts.departure_date ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$month, $year]);
        return $stmt->fetchAll();
    }

    // ==================== LẤY TẤT CẢ PHÂN CÔNG NHÂN SỰ ====================

    public function getAllStaffAssignments($filters = [])
    {
        $sql = "SELECT 
                    ss.schedule_id,
                    ss.staff_id,
                    ss.role,
                    ss.assigned_at,
                    s.full_name as staff_name,
                    s.staff_type,
                    s.phone as staff_phone,
                    s.email as staff_email,
                    ts.departure_date,
                    ts.return_date,
                    ts.status as schedule_status,
                    t.tour_id,
                    t.tour_name,
                    t.code as tour_code
                FROM schedule_staff ss
                JOIN staff s ON ss.staff_id = s.staff_id
                JOIN tour_schedules ts ON ss.schedule_id = ts.schedule_id
                JOIN tours t ON ts.tour_id = t.tour_id
                WHERE 1=1";

        $params = [];

        if (!empty($filters['staff_id'])) {
            $sql .= " AND ss.staff_id = ?";
            $params[] = $filters['staff_id'];
        }

        if (!empty($filters['staff_type'])) {
            $sql .= " AND s.staff_type = ?";
            $params[] = $filters['staff_type'];
        }

        if (!empty($filters['from_date'])) {
            $sql .= " AND ts.departure_date >= ?";
            $params[] = $filters['from_date'];
        }

        if (!empty($filters['to_date'])) {
            $sql .= " AND ts.departure_date <= ?";
            $params[] = $filters['to_date'];
        }

        $sql .= " ORDER BY ts.departure_date ASC, s.full_name ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getStaffAssignmentStats($filters = [])
    {
        $assignments = $this->getAllStaffAssignments($filters);

        $uniqueStaff = [];
        $uniqueSchedules = [];
        $upcomingCount = 0;

        foreach ($assignments as $assignment) {
            $uniqueStaff[$assignment['staff_id']] = true;
            $uniqueSchedules[$assignment['schedule_id']] = true;

            if (strtotime($assignment['departure_date']) >= time()) {
                $upcomingCount++;
            }
        }

        return [
            'total_staff' => count($uniqueStaff),
            'total_schedules' => count($uniqueSchedules),
            'upcoming_schedules' => $upcomingCount,
            'total_assignments' => count($assignments)
        ];
    }

    // ==================== SỬA LỖI FK DỊCH VỤ ====================
    private function repairScheduleServicesForeignKeys()
    {
        try {
            $dbName = DB_NAME;
            $sql = "SELECT CONSTRAINT_NAME, REFERENCED_TABLE_NAME, COLUMN_NAME
                    FROM information_schema.KEY_COLUMN_USAGE
                    WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'schedule_services' AND REFERENCED_TABLE_NAME IS NOT NULL";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$dbName]);
            $fks = $stmt->fetchAll();

            $needsFix = false;
            $wrong = [];
            foreach ($fks as $fk) {
                if ($fk['COLUMN_NAME'] === 'schedule_id' && $fk['REFERENCED_TABLE_NAME'] !== 'tour_schedules') {
                    $needsFix = true;
                    $wrong[] = $fk['CONSTRAINT_NAME'];
                }
            }
            if ($needsFix) {
                foreach ($wrong as $cname) {
                    try {
                        $this->conn->exec("ALTER TABLE schedule_services DROP FOREIGN KEY `$cname`");
                    } catch (Exception $e) { /* ignore */
                    }
                }
                $colCheck = $this->conn->query("SHOW COLUMNS FROM schedule_services LIKE 'schedule_id'")->fetch();
                if ($colCheck) {
                    try {
                        $this->conn->exec("ALTER TABLE schedule_services
                            ADD CONSTRAINT fk_schedule_services_schedule
                            FOREIGN KEY (schedule_id) REFERENCES tour_schedules(schedule_id)
                            ON DELETE CASCADE ON UPDATE CASCADE");
                    } catch (Exception $e) { /* ignore */
                    }
                }
            }
        } catch (Exception $e) {
            // bỏ qua không chặn luồng chính
        }
    }

    // ==================== PHƯƠNG THỨC HỖ TRỢ HỒ SƠ HDV ====================

    /**
     * Đếm số tour sắp tới của HDV
     */
    public function countUpcomingToursForStaff($staff_id, $from_date)
    {
        $sql = "SELECT COUNT(DISTINCT ts.schedule_id) as total
                FROM tour_schedules ts
                INNER JOIN schedule_staff ss ON ts.schedule_id = ss.schedule_id
                WHERE ss.staff_id = ? 
                AND ts.departure_date >= ?
                AND ts.status != 'Cancelled'";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$staff_id, $from_date]);
        $result = $stmt->fetch();
        return $result ? (int) $result['total'] : 0;
    }

    /**
     * Đếm số tour đã hoàn thành trong tháng của HDV
     */
    public function countCompletedToursForStaff($staff_id, $month)
    {
        // $month format: 'Y-m' (e.g., '2025-01')
        $sql = "SELECT COUNT(DISTINCT ts.schedule_id) as total
                FROM tour_schedules ts
                INNER JOIN schedule_staff ss ON ts.schedule_id = ss.schedule_id
                WHERE ss.staff_id = ? 
                AND DATE_FORMAT(ts.departure_date, '%Y-%m') = ?
                AND ts.status = 'Completed'";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$staff_id, $month]);
        $result = $stmt->fetch();
        return $result ? (int) $result['total'] : 0;
    }

    /**
     * Lấy các tour sắp khởi hành của HDV trong N ngày tới
     */
    public function getUpcomingToursForStaff($staff_id, $days = 7)
    {
        $from_date = date('Y-m-d');
        $to_date = date('Y-m-d', strtotime("+$days days"));

        $sql = "SELECT 
                    ts.schedule_id,
                    ts.tour_id,
                    ts.departure_date,
                    ts.return_date,
                    ts.status,
                    t.tour_name,
                    t.code as tour_code
                FROM tour_schedules ts
                INNER JOIN schedule_staff ss ON ts.schedule_id = ss.schedule_id
                INNER JOIN tours t ON ts.tour_id = t.tour_id
                WHERE ss.staff_id = ? 
                AND ts.departure_date BETWEEN ? AND ?
                AND ts.status != 'Cancelled'
                ORDER BY ts.departure_date ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$staff_id, $from_date, $to_date]);
        return $stmt->fetchAll();
    }

    /**
     * Lấy các tour đang diễn ra của HDV
     */
    public function getInProgressToursForStaff($staff_id)
    {
        $sql = "SELECT 
                    ts.schedule_id,
                    ts.tour_id,
                    ts.departure_date,
                    ts.return_date,
                    ts.status,
                    t.tour_name,
                    t.code as tour_code
                FROM tour_schedules ts
                INNER JOIN schedule_staff ss ON ts.schedule_id = ss.schedule_id
                INNER JOIN tours t ON ts.tour_id = t.tour_id
                WHERE ss.staff_id = ? 
                AND ts.status = 'In Progress'
                ORDER BY ts.departure_date ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$staff_id]);
        return $stmt->fetchAll();
    }
}
