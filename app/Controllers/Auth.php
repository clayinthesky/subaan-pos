<?php
namespace App\Controllers;
class Auth extends BaseController {
 public function index() { return view('login',['title'=>'Staff sign in']); }
 public function login() {
  $key='login_'.hash('sha256',$this->request->getIPAddress());
  $attempts=cache($key) ?? 0;
  if ($attempts>=8) return redirect()->back()->with('error','Too many attempts. Try again in 15 minutes.');
  cache()->save($key,$attempts+1,900);
  $user=(new \App\Models\StaffModel())->active()->where('username',strtolower(trim((string)$this->request->getPost('username'))))->first();
  $hash=$user['password'] ?? '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi';
  if (!password_verify((string)$this->request->getPost('password'),$hash) || !$user) return redirect()->back()->with('error','The username or password is incorrect.');
  cache()->delete($key); session()->regenerate(true); session()->set('user_id',(int)$user['id']);
  return redirect()->to('/');
 }
 public function logout() { session()->destroy(); return redirect()->to('/login'); }
}
