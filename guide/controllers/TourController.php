<?php
// có class chứa các function thực thi xử lý logic 
class TourController
{
    public $modelProduct;

    public function __construct()
    {
        // $this->modelProduct = new ProductModel();
    }

    public function Home()
    {
        require_once './views/home.php';
    }
}
