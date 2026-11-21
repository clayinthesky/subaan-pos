<?php
namespace App\Database\Migrations;
class CreatePos extends \CodeIgniter\Database\Migration {
 public function up() {
  $id=['type'=>'INT','unsigned'=>true,'auto_increment'=>true];
  $text=static fn($n)=>['type'=>'VARCHAR','constraint'=>$n];
  $date=['type'=>'DATETIME']; $nullable=['type'=>'DATETIME','null'=>true];
  $tables=[
   'products'=>['name'=>$text(100),'price'=>['type'=>'DECIMAL','constraint'=>'10,2'],'stock_quantity'=>['type'=>'INT','default'=>0],'image'=>$text(255)+['null'=>true]],
   'customers'=>['full_name'=>$text(100),'email'=>$text(100),'phone'=>$text(20)+['null'=>true]],
   'users'=>['username'=>$text(50),'full_name'=>$text(100),'password'=>$text(255),'avatar'=>$text(255)+['null'=>true]],
  ];
  foreach($tables as $table=>$fields) {
   $this->forge->addField(['id'=>$id]+$fields+['created_at'=>$date,'deleted_at'=>$nullable]);
   $this->forge->addKey('id',true);
   if($table==='users') $this->forge->addUniqueKey('username');
   if (!$this->forge->createTable($table,true,($this->db->DBDriver==='MySQLi'?['ENGINE'=>'InnoDB']:[]))) throw new \RuntimeException('Table creation failed: '.$table);
  }
  $ref=['type'=>'INT','unsigned'=>true];
  $this->forge->addField(['id'=>$id,'product_id'=>$ref,'customer_id'=>$ref+['null'=>true],'sold_by'=>$ref,'quantity'=>['type'=>'INT'],'total_price'=>['type'=>'DECIMAL','constraint'=>'10,2'],'created_at'=>$date]);
  $this->forge->addKey('id',true);
  $this->forge->addKey('created_at');
  foreach(['product_id'=>'products','customer_id'=>'customers','sold_by'=>'users'] as $field=>$table) $this->forge->addForeignKey($field,$table,'id','RESTRICT','RESTRICT');
  $this->forge->createTable('sales',true,($this->db->DBDriver==='MySQLi'?['ENGINE'=>'InnoDB']:[]));
 }
 public function down() { foreach(['sales','users','customers','products'] as $t) $this->forge->dropTable($t,true); }
}
