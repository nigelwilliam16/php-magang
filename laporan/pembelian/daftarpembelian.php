<?php
header("Access-Control-Allow-Origin: *"); 
$arr=null;

$conn = new mysqli("localhost", "root","","pt_coronet_crown");
if($conn->connect_error) {
  $arr= ["result"=>"error","message"=>"unable to connect"];
}
setlocale(LC_ALL, 'IND');

extract($_POST);

$sql = "SELECT nota_beli.id, nota_beli.tanggal, nota_beli.waktu, SUM(nota_beli_product.quantity) AS jumlah_barang,
SUM(nota_beli_product.total) AS total_pembelian, nota_beli.id_cabang, cabang.nama_cabang FROM nota_beli INNER JOIN nota_beli_product 
ON nota_beli.id = nota_beli_product.id_nota_beli INNER JOIN cabang ON nota_beli.id_cabang = cabang.id GROUP BY nota_beli.id";

$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
$data=[];
if($result->num_rows > 0) {
  while($r=mysqli_fetch_assoc($result)) {    
    $r['tanggal'] = strftime( "%d %B %Y", $r['tanggal']->getTimestamp());
    array_push($data,$r);
  }
  $arr=["result"=>"success","data"=>$data];
} else {
  $arr=["result"=>"error","message"=>"sql error: $sql"];
}
  
echo json_encode($arr);

  $stmt->close();
  $conn->close();
?>