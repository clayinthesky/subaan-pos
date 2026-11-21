<?php
namespace App\Controllers;
class Management extends BaseController {
 private function model($type) { return match($type) {'products'=>new \App\Models\ProductModel(),'customers'=>new \App\Models\CustomerModel(),'staff'=>new \App\Models\StaffModel()}; }
 public function index($type) {
  $m=$this->model($type)->active(); $q=trim((string)$this->request->getGet('q'));
  if ($q!=='') $m->like($type==='products'?'name':'full_name',mb_substr($q,0,100));
  return view('management/list',['title'=>ucfirst($type),'type'=>$type,'rows'=>$m->orderBy('id','DESC')->paginate(12),'pager'=>$m->pager,'q'=>$q]);
 }
 public function form($type,$id=null) {
  $row=$id?$this->model($type)->active()->find($id):[];
  if ($id && !$row) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
  return view('management/form',['title'=>($id?'Edit ':'Add ').match($type){'products'=>'product','customers'=>'customer','staff'=>'staff member'},'type'=>$type,'row'=>$row]);
 }
 public function save($type) {
  $m=$this->model($type); $id=$this->request->getPost('id');
  if ($id && (!ctype_digit((string)$id) || !$m->active()->find($id))) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
  $rules=match($type) {
   'products'=>['name'=>'required|max_length[100]','price'=>'required|regex_match[/^\d{1,8}(\.\d{1,2})?$/]|greater_than_equal_to[0]','stock_quantity'=>'required|is_natural|less_than_equal_to[2147483647]'],
   'customers'=>['full_name'=>'required|max_length[100]','email'=>'required|valid_email|max_length[100]','phone'=>'permit_empty|max_length[20]|regex_match[/^[0-9+() .-]+$/]'],
   'staff'=>['username'=>'required|min_length[3]|max_length[50]|regex_match[/^[a-z0-9._-]+$/]','full_name'=>'required|max_length[100]','password'=>($id?'permit_empty':'required').'|min_length[12]|max_length[72]']
  };
  $data=[]; foreach($rules as $key=>$rule) { $v=(string)$this->request->getPost($key); $data[$key]=$key==='password'?$v:trim($v); }
  if (!$this->validateData($data,$rules)) return redirect()->back()->with('errors',$this->validator->getErrors());
  if ($type==='staff') {
   $other=$m->where('username',$data['username'])->first();
   if ($other && (int)$other['id']!==(int)$id) return redirect()->back()->with('error','This username is already in use.');
   if ($data['password']==='') unset($data['password']); else $data['password']=password_hash($data['password'],PASSWORD_DEFAULT);
  }
  $file=null;
  try {
   if ($type!=='customers') { $field=$type==='staff'?'avatar':'image'; $file=(new \App\Libraries\ImageUpload())->save($this->request->getFile($field),$type==='staff'); if($file) $data[$field]=$file; }
   if (!$id) $data['created_at']=date('Y-m-d H:i:s');
   $ok=$id?$m->update($id,$data):$m->insert($data);
   if (!$ok) throw new \RuntimeException('Save failed');
  } catch (\DomainException $e) { return redirect()->back()->with('error',$e->getMessage()); }
  catch (\Throwable $e) { if($file) @unlink(WRITEPATH.'uploads/'.$file); log_message('error','Save failed: {message}',['message'=>$e->getMessage()]); return redirect()->back()->with('error','Could not save this record. Check the values and try again.'); }
  return redirect()->to('/'.$type)->with('success','Record saved.');
 }
 public function delete($type,$id) {
  $m=$this->model($type); if (!$m->active()->find($id)) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
  if ($type==='staff' && (int)$id===(int)session('user_id')) return redirect()->back()->with('error','You cannot delete your own signed-in account.');
  $m->update($id,['deleted_at'=>date('Y-m-d H:i:s')]);
  return redirect()->to('/'.$type)->with('success',$type==='products'?'Product archived.':'Record deleted.');
 }
}
