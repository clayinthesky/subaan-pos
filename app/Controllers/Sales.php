<?php
namespace App\Controllers;
class Sales extends BaseController {
 public function index() {
  $m=(new \App\Models\SaleModel())->history();
  return view('sales/history',['title'=>'Sales history','rows'=>$m->paginate(20),'pager'=>$m->pager]);
 }
 public function form() {
  $token=bin2hex(random_bytes(24)); $tokens=session('sale_tokens')??[]; $tokens[$token]=time(); session()->set('sale_tokens',array_slice($tokens,-10,null,true));
  return view('sales/form',['title'=>'Record a sale','products'=>(new \App\Models\ProductModel())->active()->where('stock_quantity >',0)->findAll(),'customers'=>(new \App\Models\CustomerModel())->active()->findAll(),'token'=>$token]);
 }
 public function save() {
  $token=(string)$this->request->getPost('sale_token'); $tokens=session('sale_tokens')??[];
  if (!isset($tokens[$token]) || time()-$tokens[$token]>3600) return redirect()->to('/sales/new')->with('error','This sale form expired or was already submitted.');
  if (!$this->validateData($this->request->getPost(),['product_id'=>'required|is_natural_no_zero','customer_id'=>'permit_empty|is_natural_no_zero','quantity'=>'required|is_natural_no_zero|less_than_equal_to[2147483647]'])) return redirect()->to('/sales/new')->with('errors',$this->validator->getErrors());
  unset($tokens[$token]); session()->set('sale_tokens',$tokens);
  try { $id=(new \App\Libraries\SaleRecorder())->record((int)$this->request->getPost('product_id'),$this->request->getPost('customer_id')?(int)$this->request->getPost('customer_id'):null,(int)session('user_id'),(int)$this->request->getPost('quantity')); }
  catch (\DomainException $e) { return redirect()->to('/sales/new')->with('error',$e->getMessage()); }
  catch (\Throwable $e) { log_message('error','Sale failed: {message}',['message'=>$e->getMessage()]); return redirect()->to('/sales/new')->with('error','The sale could not be saved. No stock was deducted. Please try again.'); }
  return redirect()->to('/sales')->with('success','Sale #'.$id.' recorded. Inventory updated.');
 }
}
