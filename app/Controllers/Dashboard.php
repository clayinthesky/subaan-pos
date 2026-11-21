<?php
namespace App\Controllers;
class Dashboard extends BaseController {
 public function index() {
  $db=db_connect();
  return view('dashboard',['title'=>'Overview','products'=>(new \App\Models\ProductModel())->active()->countAllResults(),'customers'=>(new \App\Models\CustomerModel())->active()->countAllResults(),'revenue'=>$db->table('sales')->selectSum('total_price')->get()->getRow()->total_price??0,'count'=>$db->table('sales')->countAllResults(),'recent'=>(new \App\Models\SaleModel())->history()->limit(5)->findAll(),'low'=>(new \App\Models\ProductModel())->active()->where('stock_quantity <=',5)->findAll()]);
 }
}
