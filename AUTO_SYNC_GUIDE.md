# HƯỚNG DẪN ĐỒNG BỘ TỰ ĐỘNG BOOKING TỪ LỊCH

## Tính năng
Khi bạn chỉnh sửa **BẤT KỲ THÔNG TIN NÀO** trong lịch khởi hành, tất cả các booking liên quan sẽ **TỰ ĐỘNG CẬP NHẬT**.

## Các trường được đồng bộ

### 1. Cập nhật TRỰC TIẾP trong database booking:
- ✅ `num_adults` - Số người lớn
- ✅ `num_children` - Số trẻ em
- ✅ `num_infants` - Số em bé
- ✅ `total_amount` - **Tự động tính** theo công thức:
  ```
  total = (num_adults × price_adult) + (num_children × price_child) + (num_infants × price_child × 0.1)
  ```
- ✅ `contact_name` - Tên người liên hệ
- ✅ `contact_phone` - Số điện thoại
- ✅ `contact_email` - Email

### 2. Hiển thị ĐỘNG qua JOIN (tự động cập nhật khi làm mới trang):
- ✅ `meeting_point` - Điểm tập trung
- ✅ `meeting_time` - Giờ tập trung  
- ✅ `return_date` - Ngày kết thúc
- ✅ `max_participants` - Số chỗ tối đa
- ✅ `schedule_status` - Trạng thái lịch
- ✅ `price_adult`, `price_child` - Giá hiển thị
- ✅ `notes` - Ghi chú

## Cách sử dụng

1. **Vào trang "Sửa lịch khởi hành"**
2. **Thay đổi bất kỳ thông tin nào**: giá, số lượng khách, thông tin liên hệ, điểm tập trung, v.v.
3. **Click "Cập nhật"**
4. Hệ thống tự động:
   - Cập nhật lịch khởi hành
   - Tìm tất cả booking liên quan (cùng tour + ngày khởi hành, status ≠ 'Hủy')
   - Cập nhật TẤT CẢ booking với thông tin mới từ lịch
   - Tính lại tổng tiền tự động
5. **Vào trang chi tiết booking và nhấn nút "Làm mới"** để xem thay đổi

## Ví dụ thực tế

### Tình huống:
- Lịch #22: Tour "Cần Thơ", ngày 08/12/2025
- Có 2 booking (#13, #14) cho lịch này

### Thay đổi lịch:
```
Số lượng: 15 người lớn, 10 trẻ em, 0 em bé → 20 người lớn, 15 trẻ em, 3 em bé
Giá: 15M/người lớn, 7M/trẻ em → 12M/người lớn, 6M/trẻ em
Liên hệ: Đào Văn Tài - 0353049242 → Trần Thị B - 0999888777
```

### Kết quả tự động:
**Cả Booking #13 và #14:**
- Số lượng: 20 người lớn, 15 trẻ em, 3 em bé ✓
- Tổng tiền: 331,800,000₫ (20×12M + 15×6M + 3×0.6M) ✓
- Liên hệ: Trần Thị B - 0999888777 ✓

## Lưu ý quan trọng

### ✅ Booking nào được đồng bộ?
- Có cùng `tour_id` và `tour_date` = `departure_date` của lịch
- Status ≠ 'Hủy'

### ❌ Booking nào KHÔNG được đồng bộ?
- Booking đã hủy (status = 'Hủy')
- Booking của tour khác hoặc ngày khác

### 📍 Các thông tin hiển thị động
Điểm tập trung, giờ tập trung, ngày kết thúc, v.v. KHÔNG lưu trong bảng booking mà được hiển thị qua JOIN. Khi bạn thay đổi trong lịch và làm mới trang (F5), chúng tự động hiển thị giá trị mới.

### 💰 Tổng tiền tự động
Tổng tiền được tính lại HOÀN TOÀN dựa trên số lượng khách và giá TỪ LỊCH, không dùng giá trị cũ từ booking.
