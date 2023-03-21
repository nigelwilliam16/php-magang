<?php
header("Access-Control-Allow-Origin: *"); 
$arr=null;

$conn = new mysqli("localhost", "root","","pt_coronet_crown");
if($conn->connect_error) {
  $arr= ["result"=>"error","message"=>"unable to connect"];
}
setlocale(LC_ALL, 'IND');

extract($_POST);
$sql = "SELECT notal_jual.id, notal_jual.tanggal, notal_jual.waktu, SUM(notal_jual_product.quantity) AS jumlah_barang,
notal_jual.ppn, notal_jual.diskon, SUM(notal_jual_product.harga*notal_jual_product.quantity) AS total_penjualan, notal_jual.foto, notal_jual.id_outlet,
outlet.nama_toko, outlet.alamat, notal_jual.username, account.nama_depan, account.nama_belakang 
FROM notal_jual INNER JOIN notal_jual_product ON notal_jual.id = notal_jual_product.id_nota_jual 
INNER JOIN outlet ON notal_jual.id_outlet = outlet.id INNER JOIN account ON notal_jual.username = account.username 
WHERE (notal_jual.id LIKE '%$cari%' OR account.nama_depan LIKE '%$cari%' OR account.nama_belakang LIKE '%$cari%' OR 
outlet.nama_toko LIKE '%$cari%') AND notal_jual.tanggal BETWEEN ? AND ? GROUP BY notal_jual.id ORDER BY notal_jual.waktu DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss",$startdate,$enddate);
$stmt->execute();
$result = $stmt->get_result();
$data=[];
if($result->num_rows > 0) {
  while($r=mysqli_fetch_assoc($result)) {    
    $tahun = substr($r['tanggal'], 0,4);
    $bulan = substr($r['tanggal'], 5,2);
    $tanggal = substr($r['tanggal'], 8,2);
    $r['tanggal'] = strftime( "%A, %d %B %Y", mktime(0,0,0,$bulan,$tanggal,$tahun));
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