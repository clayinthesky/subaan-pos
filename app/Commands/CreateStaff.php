<?php
namespace App\Commands;
use CodeIgniter\CLI\CLI;
class CreateStaff extends \CodeIgniter\CLI\BaseCommand {
 protected $group='POS'; protected $name='pos:staff'; protected $description='Create a staff account interactively.';
 public function run(array $params) {
  $username=strtolower(trim(CLI::prompt('Username'))); $name=trim(CLI::prompt('Full name')); $password=CLI::prompt('Password (12 to 72 characters)');
  $v=service('validation');
  if(!$v->setRules(['username'=>'required|min_length[3]|max_length[50]|regex_match[/^[a-z0-9._-]+$/]','full_name'=>'required|max_length[100]','password'=>'required|min_length[12]|max_length[72]'])->run(['username'=>$username,'full_name'=>$name,'password'=>$password])) { CLI::error(implode(' ',$v->getErrors())); return; }
  $m=new \App\Models\StaffModel(); if($m->where('username',$username)->first()){CLI::error('Username already exists.');return;}
  $m->insert(['username'=>$username,'full_name'=>$name,'password'=>password_hash($password,PASSWORD_DEFAULT),'created_at'=>date('Y-m-d H:i:s')]); CLI::write('Staff account created.','green');
 }
}
