<?php
/**
 * TourPricing Model
 * Quản lý giá và gói tour (Tiêu chuẩn, Cao cấp, VIP...)
 * Use Case 2: Quản lý thông tin chi tiết tour
 */
class TourPricing
{
    private $conn;

    public function __construct()
    {
        $this->conn = connectDB();
    }

    /**
     * Lấy tất cả gói giá của 1 tour
     */
    public function getPackagesByTourId($tour_id)
    {
        return $this->getPricingByTour($tour_id, false);
    }

    public function getPricingByTour($tour_id, $active_only = true)
    {
        $sql = "SELECT * FROM tour_pricing WHERE tour_id = ?";
        if ($active_only) {
            $sql .= " AND is_active = 1";
        }
        $sql .= " ORDER BY display_order ASC, pricing_id ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$tour_id]);
        return $stmt->fetchAll();
    }

    /**
     * Lấy gói giá theo season/date
     */
    public function getSeasonalPricing($tour_id, $date)
    {
        $sql = "SELECT * FROM tour_pricing 
                WHERE tour_id = ? 
                AND is_active = 1
                AND (
                    (season_start IS NULL AND season_end IS NULL)
                    OR (season_start <= ? AND season_end >= ?)
                )
                ORDER BY 
                    CASE 
                        WHEN season_start IS NOT NULL THEN 1 
                        ELSE 2 
                    END,
                    display_order ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$tour_id, $date, $date]);
        return $stmt->fetchAll();
    }

    /**
     * Lấy 1 gói giá by ID
     */
    public function getById($pricing_id)
    {
        $sql = "SELECT * FROM tour_pricing WHERE pricing_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$pricing_id]);
        return $stmt->fetch();
    }

    /**
     * Thêm gói giá mới
     */
    public function addPackage($data)
    {
        $sql = "INSERT INTO tour_pricing (
                    tour_id, package_name, adult_price, child_price, infant_price,
                    season_type, season_start, season_end,
                    min_group_size, max_group_size,
                    discount_percent, discount_amount, promo_code,
                    single_room_surcharge, holiday_surcharge,
                    included_services, excluded_services,
                    is_active, display_order, notes, created_by
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($sql);
        $result = $stmt->execute([
            $data['tour_id'],
            $data['package_name'],
            $data['adult_price'] ?? 0,
            $data['child_price'] ?? 0,
            $data['infant_price'] ?? 0,
            $data['season_type'] ?? 'Normal',
            $data['season_start'] ?? null,
            $data['season_end'] ?? null,
            $data['min_group_size'] ?? 1,
            $data['max_group_size'] ?? null,
            $data['discount_percent'] ?? 0,
            $data['discount_amount'] ?? 0,
            $data['promo_code'] ?? null,
            $data['single_room_surcharge'] ?? 0,
            $data['holiday_surcharge'] ?? 0,
            $data['included_services'] ?? null,
            $data['excluded_services'] ?? null,
            $data['is_active'] ?? 1,
            $data['display_order'] ?? 0,
            $data['notes'] ?? null,
            $_SESSION['user_id'] ?? null
        ]);

        if ($result) {
            $pricing_id = $this->conn->lastInsertId();
            $this->logPricingHistory($pricing_id, 'Created', null, $data);
            return $pricing_id;
        }
        return false;
    }

    /**
     * Cập nhật gói giá
     */
    public function updatePackage($pricing_id, $data)
    {
        $old_data = $this->getById($pricing_id);

        $sql = "UPDATE tour_pricing SET
                    package_name = ?,
                    adult_price = ?,
                    child_price = ?,
                    infant_price = ?,
                    season_type = ?,
                    season_start = ?,
                    season_end = ?,
                    min_group_size = ?,
                    max_group_size = ?,
                    discount_percent = ?,
                    discount_amount = ?,
                    promo_code = ?,
                    single_room_surcharge = ?,
                    holiday_surcharge = ?,
                    included_services = ?,
                    excluded_services = ?,
                    is_active = ?,
                    display_order = ?,
                    notes = ?
                WHERE pricing_id = ?";

        $stmt = $this->conn->prepare($sql);
        $result = $stmt->execute([
            $data['package_name'],
            $data['adult_price'] ?? 0,
            $data['child_price'] ?? 0,
            $data['infant_price'] ?? 0,
            $data['season_type'] ?? 'Normal',
            $data['season_start'] ?? null,
            $data['season_end'] ?? null,
            $data['min_group_size'] ?? 1,
            $data['max_group_size'] ?? null,
            $data['discount_percent'] ?? 0,
            $data['discount_amount'] ?? 0,
            $data['promo_code'] ?? null,
            $data['single_room_surcharge'] ?? 0,
            $data['holiday_surcharge'] ?? 0,
            $data['included_services'] ?? null,
            $data['excluded_services'] ?? null,
            $data['is_active'] ?? 1,
            $data['display_order'] ?? 0,
            $data['notes'] ?? null,
            $pricing_id
        ]);

        if ($result) {
            $this->logPricingHistory($pricing_id, 'Updated', $old_data, $data);
        }
        return $result;
    }

    /**
     * Xóa gói giá
     */
    public function deletePackage($pricing_id)
    {
        $old_data = $this->getById($pricing_id);

        $sql = "DELETE FROM tour_pricing WHERE pricing_id = ?";
        $stmt = $this->conn->prepare($sql);
        $result = $stmt->execute([$pricing_id]);

        if ($result) {
            $this->logPricingHistory($pricing_id, 'Deleted', $old_data, null);
        }
        return $result;
    }

    /**
     * Tính giá cho booking/quote
     */
    public function calculatePrice($pricing_id, $adult_count, $child_count, $infant_count, $options = [])
    {
        $pricing = $this->getById($pricing_id);
        if (!$pricing) {
            return false;
        }

        $total_people = $adult_count + $child_count + $infant_count;

        // Kiểm tra số lượng tối thiểu
        if ($total_people < $pricing['min_group_size']) {
            return [
                'error' => true,
                'message' => "Số lượng tối thiểu: {$pricing['min_group_size']} người"
            ];
        }

        // Kiểm tra số lượng tối đa
        if ($pricing['max_group_size'] && $total_people > $pricing['max_group_size']) {
            return [
                'error' => true,
                'message' => "Số lượng tối đa: {$pricing['max_group_size']} người"
            ];
        }

        // Tính giá cơ bản
        $base_price = 0;
        $base_price += $adult_count * $pricing['adult_price'];
        $base_price += $child_count * $pricing['child_price'];
        $base_price += $infant_count * $pricing['infant_price'];

        // Phụ thu phòng đơn
        $single_room_surcharge = 0;
        if (!empty($options['single_rooms'])) {
            $single_room_surcharge = $options['single_rooms'] * $pricing['single_room_surcharge'];
        }

        // Phụ thu ngày lễ
        $holiday_surcharge = 0;
        if (!empty($options['is_holiday'])) {
            $holiday_surcharge = $pricing['holiday_surcharge'];
        }

        // Tổng trước chiết khấu
        $subtotal = $base_price + $single_room_surcharge + $holiday_surcharge;

        // Chiết khấu
        $discount = 0;
        if ($pricing['discount_percent'] > 0) {
            $discount = $subtotal * ($pricing['discount_percent'] / 100);
        } elseif ($pricing['discount_amount'] > 0) {
            $discount = $pricing['discount_amount'];
        }

        // Tổng cộng
        $total = $subtotal - $discount;

        return [
            'error' => false,
            'pricing_id' => $pricing_id,
            'package_name' => $pricing['package_name'],
            'adult_count' => $adult_count,
            'child_count' => $child_count,
            'infant_count' => $infant_count,
            'adult_price' => $pricing['adult_price'],
            'child_price' => $pricing['child_price'],
            'infant_price' => $pricing['infant_price'],
            'base_price' => $base_price,
            'single_room_surcharge' => $single_room_surcharge,
            'holiday_surcharge' => $holiday_surcharge,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'discount_percent' => $pricing['discount_percent'],
            'total' => $total,
            'breakdown' => [
                ['name' => "Người lớn x{$adult_count}", 'amount' => $adult_count * $pricing['adult_price']],
                ['name' => "Trẻ em x{$child_count}", 'amount' => $child_count * $pricing['child_price']],
                ['name' => "Em bé x{$infant_count}", 'amount' => $infant_count * $pricing['infant_price']],
                ['name' => 'Phụ thu phòng đơn', 'amount' => $single_room_surcharge],
                ['name' => 'Phụ thu ngày lễ', 'amount' => $holiday_surcharge],
                ['name' => 'Chiết khấu', 'amount' => -$discount]
            ]
        ];
    }

    /**
     * Log lịch sử thay đổi giá
     */
    private function logPricingHistory($pricing_id, $action, $old_values, $new_values)
    {
        $sql = "INSERT INTO tour_pricing_history (pricing_id, action, old_values, new_values, changed_by, notes)
                VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $pricing_id,
            $action,
            $old_values ? json_encode($old_values, JSON_UNESCAPED_UNICODE) : null,
            $new_values ? json_encode($new_values, JSON_UNESCAPED_UNICODE) : null,
            $_SESSION['user_id'] ?? null,
            null
        ]);
    }

    /**
     * Lấy lịch sử thay đổi
     */
    public function getPricingHistory($pricing_id, $limit = 50)
    {
        $sql = "SELECT * FROM tour_pricing_history 
                WHERE pricing_id = ? 
                ORDER BY changed_at DESC 
                LIMIT ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$pricing_id, $limit]);
        return $stmt->fetchAll();
    }
}
