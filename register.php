<?php
require 'config/database.php';require 'config/helpers.php';
if($_SERVER['REQUEST_METHOD']==='POST'){$name=trim($_POST['name']??'');$email=trim($_POST['email']??'');$pass=$_POST['password']??'';
 if($name===''||!filter_var($email,FILTER_VALIDATE_EMAIL)||strlen($pass)<6)flash('Isi data dengan benar. Password minimal 6 karakter.','danger');
 else{$hash=password_hash($pass,PASSWORD_DEFAULT);$s=$conn->prepare("INSERT INTO users(name,email,password,role) VALUES(?,?,?,'customer')");$s->bind_param('sss',$name,$email,$hash);if($s->execute()){flash('Pendaftaran berhasil. Silakan login.');redirect('login.php');}else flash('Email sudah digunakan atau pendaftaran gagal.','danger');}}
$page_title='Daftar';include 'includes/header.php';?>
<div class="row justify-content-center"><div class="col-md-6 col-lg-5"><div class="card p-4 shadow-sm"><h1 class="h4 mb-3">Buat akun</h1><form method="post"><label class="form-label">Nama</label><input class="form-control mb-3" name="name" required><label class="form-label">Email</label><input class="form-control mb-3" type="email" name="email" required><label class="form-label">Password (min. 6 karakter)</label><input class="form-control mb-3" type="password" name="password" minlength="6" required><button class="btn btn-success w-100">Daftar</button></form></div></div></div>
<?php include 'includes/footer.php';?>