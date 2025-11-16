<?php
    class TourController{
    public $modelTour;
    public function __construct()
    {
        $this->modelTour = new Tour();
    }
        public function home(){
        require_once './views/home.php';
        }
        public function ListTour(){
        $tours = $this->modelTour->getAll();
        require_once './views/quanlytour/list_tour.php';
        }
        public function MenuTour(){
        require_once './views/quanlytour/menu_tour.php';
        }
        public function AddMenu(){
        require_once './views/quanlytour/add_menu.php';
        }
        public function AddBooking(){
        require_once './views/quanlytour/add_booking.php';
        }
        public function ListBooking(){
        require_once './views/quanlytour/list_booking.php';
        }
    }