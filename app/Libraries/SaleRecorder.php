<?php
namespace App\Libraries;
class SaleRecorder {
 public function record(int $productId, ?int $customerId, int $staffId, int $quantity): int {
  if ($quantity<1 || $quantity>2147483647) throw new \DomainException('Enter a positive whole-number quantity.');
  $db=db_connect(); $db->transBegin();
  try {
   if (!$db->table('users')->where('id',$staffId)->where('deleted_at',null)->get()->getRowArray()) throw new \DomainException('Your staff account is unavailable.');
   if ($customerId && !$db->table('customers')->where('id',$customerId)->where('deleted_at',null)->get()->getRowArray()) throw new \DomainException('Select an active customer.');
   // Conditional UPDATE takes a write lock and prevents concurrent overselling.
   $db->table('products')->where('id',$productId)->where('deleted_at',null)->where('stock_quantity >=',$quantity)->set('stock_quantity','stock_quantity - '.$quantity,false)->update();
   if ($db->affectedRows()!==1) throw new \DomainException('Sale rejected. The product is unavailable or the quantity exceeds available stock.');
   $product=$db->table('products')->where('id',$productId)->get()->getRowArray();
   $total=bcmul((string)$product['price'],(string)$quantity,2);
   if (bccomp($total,'99999999.99',2)>0) throw new \DomainException('The sale total exceeds the supported limit.');
   $db->table('sales')->insert(['product_id'=>$productId,'customer_id'=>$customerId,'sold_by'=>$staffId,'quantity'=>$quantity,'total_price'=>$total,'created_at'=>date('Y-m-d H:i:s')]);
   $id=(int)$db->insertID();
   if (!$db->transStatus() || !$db->transCommit()) throw new \RuntimeException('Transaction failed');
   return $id;
  } catch (\Throwable $e) { $db->transRollback(); throw $e; }
 }
}
