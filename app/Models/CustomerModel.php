<?php
namespace App\Models;
class CustomerModel extends \CodeIgniter\Model {
 protected $table = 'customers';
 protected $allowedFields = ['full_name', 'email', 'phone', 'created_at', 'deleted_at'];
 protected $returnType = 'array';
 public function active() { return $this->where('deleted_at',null); }
}
