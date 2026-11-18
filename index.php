<?php
require 'config/database.php'; require 'config/helpers.php';
$page_title='Beranda';
$q=trim($_GET['q'] ?? '');
$sql="SELECT p.*, c.name category FROM products p LEFT JOIN categories c ON c.id=p.category_id WHERE p.is_active=1";
$params=[]; $types='';
if($q!==''){ $sql.=" AND (p.name LIKE ? OR p.description LIKE ? OR c.name LIKE ?)"; $like="%$q%"; $params=[$like,$like,$like]; $types='sss'; }
$sql.=" ORDER BY p.created_at DESC";
$stmt=$conn->prepare($sql); if($params)$stmt->bind_param($types,...$params); $stmt->execute(); $products=$stmt->get_result();
include 'includes/header.php';
?>
<section class="hero mb-4"><div class="row align-items-center"><div class="col-md-8"><span class="badge bg-success mb-2">Belanja produk lokal</span><h1 class="display-6 fw-bold">Temukan produk tradisional pilihan</h1><p class="lead mb-0">Dukung penjual lokal dan nikmati produk khas daerah.</p></div><div class="col-md-4 text-center fs-1">🥬 🧺 🌶️</div></div></section>
<form class="row g-2 mb-4"><div class="col"><input class="form-control" name="q" value="<?=e($q)?>" placeholder="Cari nama produk atau kategori..."></div><div class="col-auto"><button class="btn btn-success">Cari</button></div></form>
<div class="d-flex justify-content-between align-items-center mb-3"><h2 class="h4 mb-0">Daftar Produk</h2><?php if(is_admin()): ?><a class="btn btn-outline-success btn-sm" href="admin/produk.php">Kelola produk</a><?php endif; ?></div>
<div class="row g-4">
<?php while($p=$products->fetch_assoc()): ?>
<div class="col-6 col-md-4 col-lg-3"><div class="card product-card h-100">
<img class="card-img-top product-img" src="<?=e($p['image'] ?: 'https://placehold.co/600x400?text=Produk')?>" alt="<?=e($p['name'])?>" onerror="this.src='https://placehold.co/600x400?text=Produk'">
<div class="card-body d-flex flex-column"><small class="text-muted"><?=e($p['category'] ?? 'Umum')?></small><h3 class="h6 mt-1"><?=e($p['name'])?></h3><div class="price mb-2"><?=rupiah($p['price'])?></div><small class="text-muted mb-3">Stok: <?= (int)$p['stock'] ?></small><a class="btn btn-outline-success mt-auto" href="detail.php?id=<?=(int)$p['id']?>">Lihat detail</a></div></div></div>
<?php endwhile; if($products->num_rows===0): ?><div class="col-12"><div class="alert alert-info">Produk belum tersedia.</div></div><?php endif; ?>
</div>
<?php include 'includes/footer.php'; ?>