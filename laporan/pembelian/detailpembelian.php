<?php
header("Access-Control-Allow-Origin: *"); 
$arr=null;

$conn = new mysqli("localhost", "n1561248_staff_coronet","sU[=]bRd;jm$","n1561248_pt_coronet_crown");
if($conn->connect_error) {
  $arr= ["result"=>"error","message"=>"unable to connect"];
}
setlocale(LC_ALL, 'IND');

extract($_POST);
$sql = "SELECT nota_beli.id, nota_beli.tanggal, nota_beli.waktu, SUM(nota_beli_product.quantity) AS jumlah_barang, nota_beli.foto,
SUM(nota_beli_product.harga * nota_beli_product.quantity) AS total_pembelian, nota_beli.diskon, nota_beli.ppn, nota_beli.id_cabang, 
nota_beli.username, account.nama_depan, account.nama_belakang, nota_beli.id_supplier, supplier.nama_supplier, cabang.nama_cabang 
FROM nota_beli INNER JOIN nota_beli_product ON nota_beli.id = nota_beli_product.id_nota_beli INNER JOIN cabang ON 
nota_beli.id_cabang = cabang.id INNER JOIN account ON nota_beli.username = account.username INNER JOIN supplier ON 
nota_beli.id_supplier = supplier.id WHERE nota_beli_product.id_nota_beli = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s",$id);
$stmt->execute();
$result = $stmt->get_result();
if($result->num_rows > 0) {
  $r=mysqli_fetch_assoc($result);  
  $tahun = substr($r['tanggal'], 0,4);
  $bulan = substr($r['tanggal'], 5,2);
  $tanggal = substr($r['tanggal'], 8,2);
  $r['tanggal'] = strftime( "%A %d %B %Y", mktime(0,0,0,$bulan,$tanggal,$tahun));

  $sql2 = "SELECT nota_beli_product.id_product, product.jenis, nota_beli_product.harga, nota_beli_product.quantity, 
  (nota_beli_product.quantity*nota_beli_product.harga) AS total_harga 
  FROM nota_beli_product INNER JOIN product ON nota_beli_product.id_product = product.id WHERE nota_beli_product.id_nota_beli = ?";

  $stmt2 = $conn->prepare($sql2);
  $stmt2->bind_param("s",$id);
  $stmt2->execute();
  $product=[];
  $result2 = $stmt2->get_result();
  if ($result2->num_rows > 0) {
          while($r2=mysqli_fetch_assoc($result2))
          {
            array_push($product,$r2);
          }
  }
  $r["produk"]=$product;  
  $arr=["result"=>"success","data"=>$r];
} else {
  $arr=["result"=>"error","message"=>"sql error: $sql"];
}
  
echo json_encode($arr);

  $stmt->close();
  $conn->close();
?>