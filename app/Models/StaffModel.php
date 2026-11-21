<?php
namespace App\Models;
class StaffModel extends \CodeIgniter\Model {
 protected $table = 'users';
 protected $allowedFields = ['username', 'full_name', 'password', 'avatar', 'created_at', 'deleted_at'];
 protected $returnType = 'array';
 public function active() { return $this->where('deleted_at',null); }
}
