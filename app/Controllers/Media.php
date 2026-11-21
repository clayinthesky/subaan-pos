<?php
namespace App\Controllers;
class Media extends BaseController {
 public function show($name) {
  if (!preg_match('/^[a-f0-9]{40}\.jpg$/D',$name) || !is_file(WRITEPATH.'uploads/'.$name)) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
  return $this->response->setHeader('Content-Type','image/jpeg')->setBody(file_get_contents(WRITEPATH.'uploads/'.$name));
 }
}
