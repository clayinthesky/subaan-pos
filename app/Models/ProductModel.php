<?php
namespace App\Models;
class ProductModel extends \CodeIgniter\Model {
 protected $table = 'products';
 protected $allowedFields = ['name', 'price', 'stock_quantity', 'image', 'created_at', 'deleted_at'];
 protected $returnType = 'array';
 public function active() { return $this->where('deleted_at',null); }
}
