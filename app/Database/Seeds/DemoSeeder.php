<?php
namespace App\Database\Seeds;
class DemoSeeder extends \CodeIgniter\Database\Seeder {
 public function run() {
  if ($this->db->table('products')->countAllResults()>0) return;
  foreach([['A5 Ruled Notebook','49.00',30],['Black Gel Pen','25.00',50],['Document Envelope','15.00',4],['Desk Organizer','189.00',8],['Sticky Notes Set','65.00',15],['Canvas Pencil Case','129.00',12]] as [$name,$price,$stock]) $this->db->table('products')->insert(['name'=>$name,'price'=>$price,'stock_quantity'=>$stock,'created_at'=>date('Y-m-d H:i:s')]);
 }
}
