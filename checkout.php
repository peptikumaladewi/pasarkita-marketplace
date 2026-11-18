<?php
require 'config/database.php'; require 'config/helpers.php';
if(empty($_SESSION['user'])){flash('Login atau daftar terlebih dahulu untuk checkout.','warning');redirect('login.php');}
$cart=$_SESSION['cart']??[];if(!$cart){flash('Keranjang kosong.','warning');redirect('keranjang.php');}
if($_SERVER['REQUEST_METHOD']==='POST'){
 $recipient=trim($_POST['recipient']??'');$phone=trim($_POST['phone']??'');$address=trim($_POST['address']??'');$notes=trim($_POST['notes']??'');
 if($recipient===''||$phone===''||$address===''){flash('Nama penerima, telepon, dan alamat wajib diisi.','danger');}
 else {
  $conn->begin_transaction();
  try{$total=0;$valid=[];
   foreach($cart as $id=>$qty){$id=(int)$id;$s=$conn->prepare("SELECT id,name,price,stock FROM products WHERE id=? AND is_active=1 FOR UPDATE");$s->bind_param('i',$id);$s->execute();$p=$s->get_result()->fetch_assoc();if(!$p || $qty<1 || $qty>$p['stock'])throw new Exception('Stok produk berubah atau tidak mencukupi.');$p['qty']=(int)$qty;$p['subtotal']=$p['price']*$qty;$total+=$p['subtotal'];$valid[]=$p;}
   $uid=(int)$_SESSION['user']['id'];$s=$conn->prepare("INSERT INTO orders(user_id,recipient,phone,address,notes,total,status) VALUES(?,?,?,?,?,?,'Menunggu')");$s->bind_param('issssd',$uid,$recipient,$phone,$address,$notes,$total);$s->execute();$order_id=$conn->insert_id;
   // Insert detail dan kurangi stok
   foreach($valid as $p){$s=$conn->prepare("INSERT INTO order_items(order_id,product_id,product_name,price,qty,subtotal) VALUES(?,?,?,?,?,?)");$s->bind_param('iisidi',$order_id,$p['id'],$p['name'],$p['price'],$p['qty'],$p['subtotal']);$s->execute();$u=$conn->prepare("UPDATE products SET stock=stock-? WHERE id=?");$u->bind_param('ii',$p['qty'],$p['id']);$u->execute();}
   $conn->commit();unset($_SESSION['cart']);flash('Pesanan berhasil dibuat. Nomor pesanan #'.$order_id);redirect('pesanan.php?id='.$order_id);
  }catch(Throwable $ex){$conn->rollback();flash($ex->getMessage(),'danger');}
 }
}
$page_title='Checkout';include 'includes/header.php'; ?>
<h1 class="h3 mb-4">Checkout</h1><form method="post" class="row g-3"><div class="col-md-6"><label class="form-label">Nama penerima *</label><input class="form-control" name="recipient" required value="<?=e($_POST['recipient']??$_SESSION['user']['name'])?>"></div><div class="col-md-6"><label class="form-label">Nomor telepon *</label><input class="form-control" name="phone" required value="<?=e($_POST['phone']??'')?>"></div><div class="col-12"><label class="form-label">Alamat lengkap *</label><textarea class="form-control" name="address" rows="3" required><?=e($_POST['address']??'')?></textarea></div><div class="col-12"><label class="form-label">Catatan (opsional)</label><textarea class="form-control" name="notes" rows="2"><?=e($_POST['notes']??'')?></textarea></div><div class="col-12"><button class="btn btn-success btn-lg">Buat pesanan</button> <a class="btn btn-link" href="keranjang.php">Kembali</a></div></form>
<?php include 'includes/footer.php'; ?>