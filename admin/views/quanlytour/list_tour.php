<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/menu.php'; ?>
<!-- BEGIN: Content-->
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
        </div>
        <div class="content-body">
            <section id="basic-datatable">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Danh sách Tour</h4>
                                <a href="add_menu_tour.php"><button type="button"
                                        class="btn btn-primary mr-1 mb-1 waves-effect waves-light">Thêm danh
                                        sách</button></a>
                            </div>
                            <div class="card-content">
                                <div class="card-body card-dashboard">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered complex-headers">
                                            <thead>
                                                <tr>
                                                    
                                                    <th>ID</th>
                                                    <th>Tên tour</th>
                                                    <th>Mã</th>
                                                    <th>Danh mục</th>
                                                    <th>Số ngày</th>
                                                    <th>Điểm xuất phát</th>
                                                    <th>Trạng thái</th>
                                                    
                                                    <th>Hành động</th>
                                                    
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($tours as $row): ?>
                                                   <td><?= $row['tour_id'] ?></td>
                                                <td><?= $row['tour_name'] ?></td>
                                                <td><?= $row['code'] ?></td>
                                                <td><?= $row['category_name'] ?></td>
                                                <td><?= $row['duration_days'] ?> ngày</td>
                                                <td><?= $row['start_location'] ?></td>
                                                <td><?= $row['status'] ?></td>
                                                <td>
                                                    <a href="index.php?controller=tour&action=edit&id=<?= $row['tour_id'] ?>" class="btn btn-warning btn-sm">Sửa</a>
                                                
                                                    <a onclick="return confirm('Xóa tour này?')"
                                                        href="index.php?controller=tour&action=delete&id=<?= $row['tour_id'] ?>" class="btn btn-danger btn-sm">Xóa</a>
                                                </td>
                                                </tr>
                                                <?php endforeach; ?>
                                                <!-- <tr>
                                                        <td>1</td>
                                                        <td>Trong nước</td>
                                                        <td><img class="img-fluid mx-auto d-block object-fit-cover" src="https://dreamtravel.com.vn/Data/ResizeImage/uploads/images/thanh%20-%20mar%20outbound/inbound/83x298x220x2.jpg" alt=""></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td><button type="button" class="btn btn-sm bg-gradient-warning mr-1 mb-1 waves-effect waves-light">Sửa</button>  <button type="button" class="btn btn-sm bg-gradient-danger mr-1 mb-1 waves-effect waves-light">Xoá</button></td>
                                                    </tr>
                                                    <tr>
                                                        <td>2</td>
                                                        <td>Quốc tế</td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td><button type="button" class="btn btn-sm bg-gradient-warning mr-1 mb-1 waves-effect waves-light">Sửa</button>  <button type="button" class="btn btn-sm bg-gradient-danger mr-1 mb-1 waves-effect waves-light">Xoá</button></td> -->
                                                <!-- </tr>
                                                    <tr>
                                                        <td>3</td>
                                                        <td>Đà Lạt</td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td><button type="button" class="btn btn-sm bg-gradient-warning mr-1 mb-1 waves-effect waves-light">Sửa</button>  <button type="button" class="btn btn-sm bg-gradient-danger mr-1 mb-1 waves-effect waves-light">Xoá</button></td>
                                                    </tr>
                                                    <tr>
                                                        <td>4s</td>
                                                        <td>Trong nước</td>
                                                        <td><img class="img-fluid mx-auto d-block object-fit-cover" src="https://dreamtravel.com.vn/Data/ResizeImage/uploads/images/thanh%20-%20mar%20outbound/inbound/83x298x220x2.jpg" alt=""></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td><button type="button" class="btn btn-sm bg-gradient-warning mr-1 mb-1 waves-effect waves-light">Sửa</button>  <button type="button" class="btn btn-sm bg-gradient-danger mr-1 mb-1 waves-effect waves-light">Xoá</button></td>
                                                    </tr> -->
                                            </tbody>
                                            <tfoot>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
<!-- END: Content-->
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>