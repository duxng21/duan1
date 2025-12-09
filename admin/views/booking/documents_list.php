<?php require_once __DIR__ . '/../core/header.php'; ?>
<?php require_once __DIR__ . '/../core/menu.php'; ?>
<?php require_once __DIR__ . '/../core/alert.php'; ?>

<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">
                            <i class="feather icon-folder"></i> Tài liệu Booking #<?= $booking['booking_id'] ?>
                        </h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="?act=danh-sach-booking">Danh sách booking</a></li>
                                <li class="breadcrumb-item"><a
                                        href="?act=chi-tiet-booking&id=<?= $booking['booking_id'] ?>">Chi tiết
                                        #<?= $booking['booking_id'] ?></a></li>
                                <li class="breadcrumb-item active">Tài liệu</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-body">
            <!-- Booking Info Summary -->
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5><?= htmlspecialchars($booking['tour_name']) ?></h5>
                            <p class="text-muted mb-0">
                                Khách hàng:
                                <strong><?= htmlspecialchars($booking['customer_name'] ?? $booking['organization_name']) ?></strong><br>
                                Ngày khởi hành: <?= date('d/m/Y', strtotime($booking['tour_date'])) ?>
                            </p>
                        </div>
                        <div class="col-md-6 text-right">
                            <a href="?act=chi-tiet-booking&id=<?= $booking['booking_id'] ?>"
                                class="btn btn-outline-secondary">
                                <i class="feather icon-arrow-left"></i> Quay lại
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Documents List -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title"><i class="feather icon-file"></i> Danh sách tài liệu đã tạo</h4>
                </div>
                <div class="card-body">
                    <?php if (empty($documents)): ?>
                        <div class="alert alert-info">
                            <i class="feather icon-info"></i> Chưa có tài liệu nào được tạo.
                            <a href="?act=chi-tiet-booking&id=<?= $booking['booking_id'] ?>">Quay lại để tạo tài liệu</a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th style="width: 60px;">#</th>
                                        <th>Loại tài liệu</th>
                                        <th>Số chứng từ</th>
                                        <th>Ngày tạo</th>
                                        <th>Trạng thái</th>
                                        <th>Đã gửi</th>
                                        <th class="text-right" style="width: 200px;">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $stt = 1;
                                    foreach ($documents as $doc):
                                        $docTypes = [
                                            'QUOTE' => ['name' => 'Báo giá', 'icon' => 'file-text', 'color' => 'primary'],
                                            'CONTRACT' => ['name' => 'Hợp đồng', 'icon' => 'file', 'color' => 'success'],
                                            'INVOICE' => ['name' => 'Hóa đơn', 'icon' => 'file-plus', 'color' => 'warning']
                                        ];
                                        $docInfo = $docTypes[$doc['document_type']] ?? ['name' => $doc['document_type'], 'icon' => 'file', 'color' => 'secondary'];

                                        $statusColors = [
                                            'Draft' => 'secondary',
                                            'Sent' => 'info',
                                            'Paid' => 'success'
                                        ];
                                        $statusColor = $statusColors[$doc['status']] ?? 'secondary';
                                        ?>
                                        <tr>
                                            <td><?= $stt++ ?></td>
                                            <td>
                                                <i
                                                    class="feather icon-<?= $docInfo['icon'] ?> text-<?= $docInfo['color'] ?>"></i>
                                                <strong><?= $docInfo['name'] ?></strong>
                                            </td>
                                            <td>
                                                <code><?= htmlspecialchars($doc['document_number']) ?></code>
                                                <?php if (!empty($doc['invoice_series'])): ?>
                                                    <br><small class="text-muted">Ký hiệu: <?= $doc['invoice_series'] ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= date('d/m/Y H:i', strtotime($doc['created_at'])) ?></td>
                                            <td>
                                                <span class="badge badge-<?= $statusColor ?>">
                                                    <?= $doc['status'] ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if (!empty($doc['sent_at'])): ?>
                                                    <i class="feather icon-check-circle text-success"></i>
                                                    <?= date('d/m/Y H:i', strtotime($doc['sent_at'])) ?><br>
                                                    <small class="text-muted"><?= htmlspecialchars($doc['sent_to_email']) ?></small>
                                                <?php else: ?>
                                                    <i class="feather icon-x-circle text-muted"></i> Chưa gửi
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-right">
                                                <div class="btn-group">
                                                    <!-- Download PDF -->
                                                    <a href="?act=tai-tai-lieu&document_id=<?= $doc['document_id'] ?>"
                                                        class="btn btn-sm btn-outline-primary" title="Tải xuống">
                                                        <i class="feather icon-download"></i>
                                                    </a>

                                                    <!-- Print PDF -->
                                                    <a href="?act=in-tai-lieu&document_id=<?= $doc['document_id'] ?>"
                                                        class="btn btn-sm btn-outline-secondary" target="_blank"
                                                        title="In tài liệu">
                                                        <i class="feather icon-printer"></i>
                                                    </a>

                                                    <!-- Send Email -->
                                                    <button type="button" class="btn btn-sm btn-outline-success"
                                                        title="Gửi email"
                                                        onclick="showEmailModal(<?= $doc['document_id'] ?>, '<?= $docInfo['name'] ?>', '<?= htmlspecialchars($booking['customer_email'] ?? '', ENT_QUOTES) ?>')">
                                                        <i class="feather icon-mail"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Email Modal -->
<div class="modal fade" id="emailModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="?act=gui-tai-lieu-email">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="feather icon-mail"></i> Gửi <span id="docTypeName"></span> qua Email
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="document_id" id="modalDocumentId">
                    <input type="hidden" name="booking_id" value="<?= $booking['booking_id'] ?>">

                    <div class="form-group">
                        <label for="email_to">Email người nhận <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email_to" name="email_to" required
                            placeholder="customer@example.com">
                        <small class="form-text text-muted">
                            Tài liệu PDF sẽ được gửi kèm theo email
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-success">
                        <i class="feather icon-send"></i> Gửi Email
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function showEmailModal(documentId, docTypeName, defaultEmail) {
        document.getElementById('modalDocumentId').value = documentId;
        document.getElementById('docTypeName').textContent = docTypeName;
        document.getElementById('email_to').value = defaultEmail;
        $('#emailModal').modal('show');
    }
</script>

<?php require_once __DIR__ . '/../core/footer.php'; ?>