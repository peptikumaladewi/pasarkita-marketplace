<?php
require 'config/database.php'; require 'config/helpers.php';
$id=(int)($_GET['id'] ?? 0); $stmt=$conn->prepare("SELECT p.*,c.name category FROM products p LEFT JOIN categories c ON c.id=p.category_id WHERE p.id=? AND p.is_active=1"); $stmt->bind_param('i',$id); $stmt->execute(); $p=$stmt->get_result()->fetch_assoc();
if(!$p){http_response_code(404); die('Produk tidak ditemukan. <a href="index.php">Kembali</a>');}
$page_title=$p['name']; include 'includes/header.php';
?>
<div class="row g-4"><div class="col-md-6"><img class="img-fluid rounded-4 w-100" style="max-height:430px;object-fit:cover" src="<?=e($p['image'] ?: 'https://placehold.co/800x600?text=Produk')?>" onerror="this.src='https://placehold.co/800x600?text=Produk'"></div>
<div class="col-md-6"><span class="badge bg-success"><?=e($p['category'] ?? 'Umum')?></span><h1 class="mt-3"><?=e($p['name'])?></h1><h3 class="price"><?=rupiah($p['price'])?></h3><p class="text-muted">Stok tersedia: <?= (int)$p['stock'] ?></p><p><?=nl2br(e($p['description']))?></p>
<?php if($p['stock']>0): ?><form method="post" action="keranjang.php" class="d-flex gap-2 align-items-center"><input type="hidden" name="action" value="add"><input type="hidden" name="product_id" value="<?=$id?>"><input type="number" class="form-control" name="qty" value="1" min="1" max="<?=(int)$p['stock']?>" style="max-width:100px"><button class="btn btn-success">Tambah ke keranjang</button></form><?php else: ?><button class="btn btn-secondary" disabled>Stok habis</button><?php endif; ?>
<a href="index.php" class="btn btn-link mt-3">← Kembali ke produk</a></div></div>
<?php include 'includes/footer.php'; ?>