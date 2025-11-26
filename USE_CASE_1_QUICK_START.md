# Use Case 1 Quick Start Guide

## 🎯 Mục Tiêu

Hướng dẫn viên (HDV) xem lịch làm việc, chi tiết tour, nhiệm vụ, lịch tháng, và xuất lịch trình.

## 🚀 Bắt Đầu

### 1. Đăng nhập

- URL: `http://localhost/admin/?act=login` (hoặc tương ứng)
- Nhập username/password với role **GUIDE**
- Sau khi đăng nhập, bạn sẽ thấy menu HDV

### 2. Truy Cập Menu HDV

Trên menu, tìm mục "Lịch của tôi" hoặc truy cập trực tiếp:

- `?act=hdv-lich-cua-toi` - Danh sách tour

### 3. Các Chức Năng Chính

#### 📋 Danh Sách Tour (`?act=hdv-lich-cua-toi`)

- Xem tất cả tour được phân công
- Lọc theo: Tháng, Năm, Trạng thái
- Hành động: "Chi tiết" hoặc "Nhiệm vụ"

#### 🏆 Chi Tiết Tour (`?act=hdv-chi-tiet-tour&id=<schedule_id>`)

- Tab 1: Lịch trình (từng ngày)
- Tab 2: Hình ảnh (gallery)
- Tab 3: Nhiệm vụ
- Tab 4: Chính sách (Hủy, Thay đổi, Thanh toán)
- Tab 5: Đội ngũ (nhân viên tham gia)
- Nút: Xuất PDF, Xuất Excel

#### ✅ Nhiệm Vụ (`?act=hdv-nhiem-vu-cua-toi&schedule_id=<id>`)

- Tab 1: Tất cả nhiệm vụ
- Tab 2: Hướng dẫn đoàn
- Tab 3: Ghi chú đặc biệt
- Mỗi task: loại, thời gian, địa điểm, người phụ trách

#### 📅 Lịch Tháng (`?act=hdv-xem-lich-thang`)

- Lịch 7 cột (Thứ Hai - Chủ Nhật)
- Ngày có tour: Badge xanh
- Hôm nay: Badge đỏ
- Click ngày → Popup chi tiết
- Chọn tháng/năm ở đầu trang

#### 💾 Xuất Lịch (`?act=hdv-xuat-lich&schedule_id=<id>&format=pdf|excel`)

- Tự động tải file
- Format: PDF hoặc Excel (.xls)

## 📊 Quyền & Bảo Mật

### Role: GUIDE

✅ Được phép:

- Xem tour được phân công
- Xem lịch riêng
- Xem nhiệm vụ
- Xem lịch tháng
- Xuất lịch

❌ Không được:

- Xem lịch của HDV khác
- Quản lý tour
- Xóa/Sửa dữ liệu

### Role: ADMIN

✅ Được phép: Mọi thứ

## 🐛 Xử Lý Lỗi

### E1: Đăng nhập thất bại

**Hiển thị:** "Sai tài khoản hoặc mật khẩu"
**Giải pháp:** Kiểm tra username/password, click "Quên mật khẩu"

### E2: Không có tour

**Hiển thị:** "Hiện tại bạn chưa được phân công tour nào"
**Giải pháp:** Liên hệ quản lý để yêu cầu phân công

### E3: Lỗi tải dữ liệu

**Hiển thị:** "Không thể tải dữ liệu"
**Giải pháp:** Làm mới trang, kiểm tra kết nối database

### E4: Lỗi xuất file

**Hiển thị:** "Tải xuống thất bại"
**Giải pháp:** Thử xuất lại, thử format khác (PDF ↔ Excel)

## 📋 Filter & Search

### Lọc Danh Sách Tour

```
Tháng: 1-12
Năm: Năm hiện tại ± 2
Trạng thái:
  - Sắp diễn ra (Open)
  - Đang diễn ra (In Progress)
  - Đã kết thúc (Completed)
  - Đã hủy (Cancelled)
```

### Dữ Liệu Hiển Thị

- Mã tour
- Tên tour
- Ngày khởi hành - Kết thúc
- Điểm đến chính
- Trạng thái

## 📱 Responsive Design

- Desktop: Bảng đầy đủ
- Tablet: Bảng cuộn ngang
- Mobile: Bảng tối ưu hóa

## 🎨 Giao Diện

### Theme

- **Primary Color**: Xanh dương (#0d6efd)
- **Success**: Xanh lá (#198754)
- **Warning**: Vàng (#ffc107)
- **Danger**: Đỏ (#dc3545)

### Biểu tượng (FontAwesome 6)

- 📋 `fa-list`
- 👁️ `fa-eye`
- 📅 `fa-calendar`
- 📍 `fa-map-marker-alt`
- 👤 `fa-user`
- ⏰ `fa-clock`
- 📄 `fa-file-pdf`
- 📊 `fa-file-excel`

## 🔄 Luồng Người Dùng

```
LOGIN (GUIDE role)
  ↓
Trang Chủ HDV
  ↓
→ Xem Danh Sách Tour
  ├→ Lọc (Tháng/Năm/Trạng thái)
  └→ Click Chi Tiết/Nhiệm vụ

→ Chi Tiết Tour
  ├→ Tab Lịch Trình (Ngày 1, 2, 3...)
  ├→ Tab Ảnh (Gallery)
  ├→ Tab Nhiệm Vụ (Công việc)
  ├→ Tab Chính Sách
  ├→ Tab Đội Ngũ
  └→ Xuất PDF/Excel

→ Nhiệm Vụ
  ├→ Tab Tất Cả
  ├→ Tab Hướng Dẫn
  └→ Tab Ghi Chú

→ Lịch Tháng
  ├→ Chọn Tháng/Năm
  ├→ Click Ngày → Popup
  └→ Danh Sách Tour Tháng

→ Xuất Lịch
  └→ PDF / Excel → Download
```

## 📝 Ví Dụ URL

| Chức Năng      | URL                                             |
| -------------- | ----------------------------------------------- |
| Danh sách tour | `?act=hdv-lich-cua-toi`                         |
| Chi tiết tour  | `?act=hdv-chi-tiet-tour&id=5`                   |
| Nhiệm vụ       | `?act=hdv-nhiem-vu-cua-toi&schedule_id=5`       |
| Lịch tháng     | `?act=hdv-xem-lich-thang&month=11&year=2025`    |
| Xuất PDF       | `?act=hdv-xuat-lich&schedule_id=5&format=pdf`   |
| Xuất Excel     | `?act=hdv-xuat-lich&schedule_id=5&format=excel` |

## ✨ Tính Năng Nổi Bật

✅ **Lọc Linh Hoạt** - Tháng, tuần, trạng thái
✅ **Lịch Trực Quan** - Ngày được đánh dấu
✅ **Thông Tin Chi Tiết** - 5 tab với đầy đủ dữ liệu
✅ **Xuất Offline** - PDF & Excel
✅ **Giao Diện Thân Thiện** - Bootstrap 5
✅ **Bảo Mật** - Chỉ xem dữ liệu của mình

## 📞 Hỗ Trợ

- **Quên Mật Khẩu**: Click "Quên mật khẩu" ở trang login
- **Báo Cáo Lỗi**: Liên hệ admin
- **Yêu Cầu Phân Công**: Liên hệ quản lý

---

**Phiên Bản**: 1.0
**Ngày Cập Nhật**: 26/11/2025
**Tác Giả**: AI Assistant
