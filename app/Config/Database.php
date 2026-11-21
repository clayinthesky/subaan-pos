<?php
namespace Config;
class Database extends \CodeIgniter\Database\Config {
 public string $filesPath = APPPATH.'Database'.DIRECTORY_SEPARATOR;
 public string $defaultGroup = 'default';
 public array $default = [
  'DSN'=>'','hostname'=>'127.0.0.1','username'=>'','password'=>'',
  'database'=>WRITEPATH.'pos.sqlite','DBDriver'=>'SQLite3','DBPrefix'=>'',
  'pConnect'=>false,'DBDebug'=>false,'charset'=>'utf8mb4','DBCollat'=>'utf8mb4_general_ci',
  'swapPre'=>'','encrypt'=>false,'compress'=>false,'strictOn'=>true,'failover'=>[],
  'port'=>3306,'foreignKeys'=>true,'busyTimeout'=>5000,
  'dateFormat'=>['date'=>'Y-m-d','datetime'=>'Y-m-d H:i:s','time'=>'H:i:s']
 ];
}
