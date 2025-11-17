<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="feather icon-check-circle"></i>
        <strong>Thành công!</strong> <?= $_SESSION['success'] ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="feather icon-x-circle"></i>
        <strong>Lỗi!</strong> <?= $_SESSION['error'] ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['warning'])): ?>
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="feather icon-alert-triangle"></i>
        <strong>Cảnh báo!</strong> <?= $_SESSION['warning'] ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <?php unset($_SESSION['warning']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['info'])): ?>
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <i class="feather icon-info"></i>
        <strong>Thông tin!</strong> <?= $_SESSION['info'] ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <?php unset($_SESSION['info']); ?>
<?php endif; ?>

<script>
    // Auto-hide alerts after 5 seconds
    document.addEventListener('DOMContentLoaded', function () {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function (alert) {
            setTimeout(function () {
                const closeBtn = alert.querySelector('.close');
                if (closeBtn) {
                    closeBtn.click();
                }
            }, 5000);
        });
    });
</script>