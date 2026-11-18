<?php
require 'config/database.php'; require 'config/helpers.php';
if($_SERVER['REQUEST_METHOD']==='POST'){
 $action=$_POST['action'] ?? '';
 if($action==='add'){ $id=(int)($_POST['product_id']??0); $qty=max(1,(int)($_POST['qty']??1)); $s=$conn->prepare("SELECT stock FROM products WHERE id=? AND is_active=1"); $s->bind_param('i',$id); $s->execute(); $p=$s->get_result()->fetch_assoc(); if($p && $p['stock']>0){$_SESSION['cart'][$id]=min((int)$p['stock'],($_SESSION['cart'][$id]??0)+$qty);flash('Produk ditambahkan ke keranjang.');}else flash('Produk tidak tersedia.','danger'); }
 if($action==='update'){foreach(($_POST['qty']??[]) as $id=>$qty){$id=(int)$id;$qty=(int)$qty;if($qty<=0)unset($_SESSION['cart'][$id]);else $_SESSION['cart'][$id]=$qty;}flash('Keranjang diperbarui.');}
 if($action==='remove'){unset($_SESSION['cart'][(int)$_POST['product_id']]);flash('Produk dihapus dari keranjang.');}
 redirect('keranjang.php');
}
$page_title='Keranjang'; $items=[];$total=0;
foreach(($_SESSION['cart']??[]) as $id=>$qty){$s=$conn->prepare("SELECT id,name,price,stock,image FROM products WHERE id=? AND is_active=1");$s->bind_param('i',$id);$s->execute();$p=$s->get_result()->fetch_assoc();if($p){$p['qty']=min((int)$qty,(int)$p['stock']);$p['subtotal']=$p['qty']*$p['price'];$total+=$p['subtotal'];$items[]=$p;}else unset($_SESSION['cart'][$id]);}
include 'includes/header.php'; ?>
<h1 class="h3 mb-4">Keranjang Belanja</h1>
<?php if(!$items): ?><div class="alert alert-info">Keranjang masih kosong. <a href="index.php">Mulai belanja</a>.</div><?php else: ?>
<form method="post"><input type="hidden" name="action" value="update"><div class="table-responsive"><table class="table align-middle bg-white"><thead><tr><th>Produk</th><th>Harga</th><th width="130">Jumlah</th><th>Subtotal</th><th></th></tr></thead><tbody>
<?php foreach($items as $p): ?><tr><td><div class="d-flex align-items-center gap-2"><img class="thumb" src="<?=e($p['image'] ?: 'https://placehold.co/100x80')?>" onerror="this.src='https://placehold.co/100x80'"><strong><?=e($p['name'])?></strong></div></td><td><?=rupiah($p['price'])?></td><td><input class="form-control" type="number" min="0" max="<?=$p['stock']?>" name="qty[<?=$p['id']?>]" value="<?=$p['qty']?>"></td><td><?=rupiah($p['subtotal'])?></td><td><button form="remove<?=$p['id']?>" class="btn btn-sm btn-outline-danger">Hapus</button></td></tr><?php endforeach; ?>
</tbody></table></div><button class="btn btn-outline-success">Perbarui keranjang</button></form>
<?php foreach($items as $p): ?><form id="remove<?=$p['id']?>" method="post" class="d-none"><input name="action" value="remove"><input name="product_id" value="<?=$p['id']?>"></form><?php endforeach; ?>
<div class="text-end mt-4"><h4>Total: <span class="price"><?=rupiah($total)?></span></h4><a href="checkout.php" class="btn btn-success btn-lg">Lanjut checkout</a></div>
<?php endif; include 'includes/footer.php'; ?>