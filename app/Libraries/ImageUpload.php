<?php
namespace App\Libraries;
class ImageUpload {
 public function save($file, bool $avatar=false): ?string {
  if (!$file || $file->getError()===UPLOAD_ERR_NO_FILE) return null;
  if (!$file->isValid() || $file->getSize()>2097152) throw new \DomainException('Choose an image no larger than 2 MB.');
  if (!in_array($file->getMimeType(),['image/jpeg','image/png','image/webp'],true)) throw new \DomainException('Only JPEG, PNG and WebP images are accepted.');
  $info=@getimagesize($file->getTempName());
  if (!$info || $info[0]>6000 || $info[1]>6000 || $info[0]*$info[1]>16000000) throw new \DomainException('The image is invalid or exceeds 16 megapixels or 6000 pixels per side.');
  $name=bin2hex(random_bytes(20)).'.jpg'; $dir=WRITEPATH.'uploads';
  if (!is_dir($dir)) mkdir($dir,0750,true);
  $image=service('image')->withFile($file->getTempName());
  if ($avatar) $image->fit(256,256,'center'); else $image->resize(1000,1000,true,'auto');
  $image->convert(IMAGETYPE_JPEG)->save($dir.DIRECTORY_SEPARATOR.$name,85);
  return $name;
 }
}
