<?php 
// Require toàn bộ các file khai báo môi trường, thực thi,...(không require view)

// Require file Common
require_once '../commons/env.php'; // Khai báo biến môi trường
require_once '../commons/function.php'; // Hàm hỗ trợ

// Require toàn bộ file Controllers
require_once './controllers/TourController.php';
require_once './controllers/AuthController.php';

// Require toàn bộ file Models
require_once './models/Tour.php';
// require_once './models/ProductModel.php';

// Route
$act = $_GET['act'] ?? '/';


// Để bảo bảo tính chất chỉ gọi 1 hàm Controller để xử lý request thì mình sử dụng match

match ($act) {
    // Trang chủ
    '/'               =>(new TourController())->Home(),
    'list-tour'       =>(new TourController())->ListTour(),
    'menu-tour'       =>(new TourController())->MenuTour(),
    'them-danh-muc'   =>(new TourController())->AddMenu(),
    'them-booking'    =>(new TourController())->AddBooking(),
    'list-booking'    =>(new TourController())->ListBooking(),
    'add-list'        =>(new TourController())->AddList(),
    'edit-list'       =>(new TourController())->EditList(),


    //auth
    'login'                 =>(new AuthController())->login(),
};