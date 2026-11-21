<?php
namespace App\Filters;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
class AuthFilter implements \CodeIgniter\Filters\FilterInterface {
 public function before(RequestInterface $request, $arguments=null) {
  $user=(new \App\Models\StaffModel())->find(session('user_id') ?? 0);
  if (!$user || $user['deleted_at']) { session()->destroy(); return redirect()->to('/login'); }
 }
 public function after(RequestInterface $request, ResponseInterface $response, $arguments=null) {
  $response->setHeader('Cache-Control','no-store, private')->setHeader('X-Content-Type-Options','nosniff')->setHeader('X-Frame-Options','DENY')->setHeader('Referrer-Policy','same-origin');
 }
}
