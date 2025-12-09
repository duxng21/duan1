<?php require_once __DIR__ . '/../core/header.php'; ?>
<?php require_once __DIR__ . '/../core/menu.php'; ?>
<!-- BEGIN: Content-->
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">Quản lý danh sách đoàn</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="?act=danh-sach-lich-khoi-hanh">Danh sách lịch</a>
                                </li>
                                <li class="breadcrumb-item"><a
                                        href="?act=chi-tiet-lich-khoi-hanh&id=<?= $schedule['schedule_id'] ?>">Chi tiết
                                        lịch</a></li>
                                <li class="breadcrumb-item active">Danh sách đoàn</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">
            <!-- Thông báo -->
            <?php require_once __DIR__ . '/../core/alert.php'; ?>

            <section id="group-members-management">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">
                                    <i class="feather icon-users"></i>
                                    <?= htmlspecialchars($schedule['tour_name']) ?>
                                </h4>
                                <p class="mb-0">
                                    <span class="badge badge-info">Khởi hành:
                                        <?= date('d/m/Y', strtotime($schedule['departure_date'])) ?></span>
                                </p>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <form action="?act=luu-danh-sach-doan&schedule_id=<?= $schedule['schedule_id'] ?>"
                                        method="POST">

                                        <div class="alert alert-info">
                                            <i class="feather icon-info"></i>
                                            <strong>Hướng dẫn:</strong> Nhập danh sách thành viên trong đoàn. Ít nhất
                                            phải có 1 thành viên.
                                        </div>

                                        <!-- Tìm kiếm -->
                                        <div class="row mb-2">
                                            <div class="col-md-6">
                                                <div class="input-group">
                                                    <input type="text" id="search-member" class="form-control"
                                                        placeholder="Tìm theo tên, SĐT, CMND...">
                                                    <div class="input-group-append">
                                                        <button class="btn btn-primary" type="button"
                                                            onclick="searchMembers()">
                                                            <i class="feather icon-search"></i> Tìm
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 text-right">
                                                <span class="badge badge-info" id="member-count">
                                                    Tổng: <?= count($groupMembers ?? []) ?> thành viên
                                                </span>
                                            </div>
                                        </div>

                                        <div id="group-members-container">
                                            <?php if (!empty($groupMembers)): ?>
                                                <?php foreach ($groupMembers as $index => $member): ?>
                                                    <div class="member-row card mb-2 p-2" data-member-index="<?= $index ?>">
                                                        <div class="row">
                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label>Họ tên <span class="text-danger">*</span></label>
                                                                    <input type="text" name="members[<?= $index ?>][full_name]"
                                                                        class="form-control" placeholder="Nguyễn Văn A"
                                                                        value="<?= htmlspecialchars($member['full_name']) ?>"
                                                                        required>
                                                                    <input type="hidden"
                                                                        name="members[<?= $index ?>][member_id]"
                                                                        value="<?= $member['member_id'] ?>">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div class="form-group">
                                                                    <label>SĐT</label>
                                                                    <input type="tel" name="members[<?= $index ?>][phone]"
                                                                        class="form-control" placeholder="0901234567"
                                                                        value="<?= htmlspecialchars($member['phone'] ?? '') ?>">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div class="form-group">
                                                                    <label>Email</label>
                                                                    <input type="email" name="members[<?= $index ?>][email]"
                                                                        class="form-control" placeholder="email@domain.com"
                                                                        value="<?= htmlspecialchars($member['email'] ?? '') ?>">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div class="form-group">
                                                                    <label>CMND/CCCD</label>
                                                                    <input type="text" name="members[<?= $index ?>][id_number]"
                                                                        class="form-control" placeholder="001234567890"
                                                                        value="<?= htmlspecialchars($member['id_number'] ?? '') ?>">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div class="form-group">
                                                                    <label>Ngày sinh</label>
                                                                    <input type="date"
                                                                        name="members[<?= $index ?>][date_of_birth]"
                                                                        class="form-control"
                                                                        value="<?= $member['date_of_birth'] ?? '' ?>">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-1">
                                                                <label>&nbsp;</label>
                                                                <button type="button"
                                                                    class="btn btn-sm btn-danger btn-block remove-member"
                                                                    style="margin-top:2px;" <?= count($groupMembers) === 1 ? 'disabled' : '' ?>>
                                                                    <i class="feather icon-x"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label>Ghi chú</label>
                                                                    <textarea name="members[<?= $index ?>][note]"
                                                                        class="form-control" rows="2"
                                                                        placeholder="VD: Ăn chay, dị ứng hải sản..."><?= htmlspecialchars($member['note'] ?? '') ?></textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <div class="member-row card mb-2 p-2" data-member-index="0">
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label>Họ tên <span class="text-danger">*</span></label>
                                                                <input type="text" name="members[0][full_name]"
                                                                    class="form-control" placeholder="Nguyễn Văn A"
                                                                    required>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <label>SĐT</label>
                                                                <input type="tel" name="members[0][phone]"
                                                                    class="form-control" placeholder="0901234567">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <label>Email</label>
                                                                <input type="email" name="members[0][email]"
                                                                    class="form-control" placeholder="email@domain.com">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <label>CMND/CCCD</label>
                                                                <input type="text" name="members[0][id_number]"
                                                                    class="form-control" placeholder="001234567890">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <label>Ngày sinh</label>
                                                                <input type="date" name="members[0][date_of_birth]"
                                                                    class="form-control">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-1">
                                                            <label>&nbsp;</label>
                                                            <button type="button"
                                                                class="btn btn-sm btn-danger btn-block remove-member"
                                                                style="margin-top:2px;" disabled>
                                                                <i class="feather icon-x"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label>Ghi chú</label>
                                                                <textarea name="members[0][note]" class="form-control"
                                                                    rows="2"
                                                                    placeholder="VD: Ăn chay, dị ứng hải sản..."></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <button type="button" class="btn btn-sm btn-info mb-3" id="add-member-btn">
                                            <i class="feather icon-plus"></i> Thêm thành viên
                                        </button>

                                        <div class="form-group mt-3">
                                            <button type="submit" class="btn btn-primary mr-1">
                                                <i class="feather icon-save"></i> Lưu danh sách
                                            </button>
                                            <a href="?act=chi-tiet-lich-khoi-hanh&id=<?= $schedule['schedule_id'] ?>"
                                                class="btn btn-secondary">
                                                <i class="feather icon-arrow-left"></i> Quay lại
                                            </a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<script>
    let memberIndex = <?= !empty($groupMembers) ? count($groupMembers) : 1 ?>;

    // Add new member row
    document.getElementById('add-member-btn').addEventListener('click', function () {
        const container = document.getElementById('group-members-container');
        const newRow = document.createElement('div');
        newRow.className = 'member-row card mb-2 p-2';
        newRow.setAttribute('data-member-index', memberIndex);
        newRow.innerHTML = `
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label>Họ tên <span class="text-danger">*</span></label>
                    <input type="text" name="members[${memberIndex}][full_name]" class="form-control" 
                           placeholder="Nguyễn Văn A" required>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label>SĐT</label>
                    <input type="tel" name="members[${memberIndex}][phone]" class="form-control" 
                           placeholder="0901234567">
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="members[${memberIndex}][email]" class="form-control" 
                           placeholder="email@domain.com">
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label>CMND/CCCD</label>
                    <input type="text" name="members[${memberIndex}][id_number]" class="form-control" 
                           placeholder="001234567890">
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label>Ngày sinh</label>
                    <input type="date" name="members[${memberIndex}][date_of_birth]" class="form-control">
                </div>
            </div>
            <div class="col-md-1">
                <label>&nbsp;</label>
                <button type="button" class="btn btn-sm btn-danger btn-block remove-member" style="margin-top:2px;">
                    <i class="feather icon-x"></i>
                </button>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Ghi chú</label>
                    <textarea name="members[${memberIndex}][note]" class="form-control" rows="2" 
                              placeholder="VD: Ăn chay, dị ứng hải sản..."></textarea>
                </div>
            </div>
        </div>
    `;
        container.appendChild(newRow);
        memberIndex++;
        updateRemoveButtons();
    });

    // Remove member row
    document.addEventListener('click', function (e) {
        if (e.target.closest('.remove-member')) {
            const row = e.target.closest('.member-row');
            if (row) {
                row.remove();
                updateRemoveButtons();
            }
        }
    });

    // Update remove button state
    function updateRemoveButtons() {
        const rows = document.querySelectorAll('.member-row');
        const removeButtons = document.querySelectorAll('.remove-member');

        if (rows.length === 1) {
            removeButtons[0].disabled = true;
        } else {
            removeButtons.forEach(btn => btn.disabled = false);
        }
    }

    // Search members by name, phone, email, or ID number
    function searchMembers() {
        const searchTerm = document.getElementById('search-member').value.toLowerCase().trim();
        const memberRows = document.querySelectorAll('.member-row');
        let visibleCount = 0;

        memberRows.forEach(row => {
            const fullName = row.querySelector('input[name*="[full_name]"]')?.value.toLowerCase() || '';
            const phone = row.querySelector('input[name*="[phone]"]')?.value.toLowerCase() || '';
            const email = row.querySelector('input[name*="[email]"]')?.value.toLowerCase() || '';
            const idNumber = row.querySelector('input[name*="[id_number]"]')?.value.toLowerCase() || '';

            const matches = fullName.includes(searchTerm) ||
                phone.includes(searchTerm) ||
                email.includes(searchTerm) ||
                idNumber.includes(searchTerm);

            if (matches || searchTerm === '') {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        // Update member count badge
        const countBadge = document.getElementById('member-count');
        if (countBadge) {
            countBadge.textContent = `Tổng: ${visibleCount} thành viên`;
        }
    }

    // Allow search on Enter key
    document.getElementById('search-member')?.addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            searchMembers();
        }
    });
</script>

<!-- END: Content-->
<?php require_once __DIR__ . '/../core/footer.php'; ?>