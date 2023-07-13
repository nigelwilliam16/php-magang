<?php
header("Access-Control-Allow-Origin: *"); 
$arr=null;

$conn = new mysqli("localhost", "n1561248_staff_coronet","sU[=]bRd;jm$","n1561248_pt_coronet_crown");
if($conn->connect_error) {
  $arr= ["result"=>"error","message"=>"unable to connect"];
}
setlocale(LC_ALL, 'IND');

extract($_POST);
$sql = "SELECT event.id, event.nama, event.lokasi as alamat, event.tanggal, event.status_proposal, 
SUM(event_jual_product.quantity) AS jumlah_penjualan, SUM(event_jual_product.harga*event_jual_product.quantity) AS total_penjualan
FROM event INNER JOIN event_jual_product ON event_jual_product.event_id = event.id 
WHERE (event.id LIKE '%$cari%' OR event.nama LIKE '%$cari%')
AND event.tanggal BETWEEN ? AND ? GROUP BY event.id ORDER BY event.tanggal ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss",$startdate,$enddate);
$stmt->execute();
$result = $stmt->get_result();
$data=[];
if($result->num_rows > 0) {
  while($r=mysqli_fetch_assoc($result)) {
    
    $personil=[];
    $sql3 = "SELECT personil_event.account_username, account.nama_depan, account.nama_belakang, personil_event.role 
    FROM personil_event INNER JOIN account ON personil_event.account_username = account.username 
    WHERE personil_event.event_id = ? AND personil_event.role = 1";
    $stmt3 = $conn->prepare($sql3);
    $stmt3->bind_param("s",$r['id']);
    $stmt3->execute();
    $result3 = $stmt3->get_result();
    if($result3->num_rows > 0) { 
      $r3=mysqli_fetch_assoc($result3);
      $r["username"]=$r3['account_username'];  
      $r["nama_depan"]=$r3['nama_depan'];  
      $r["nama_belakang"]=$r3['nama_belakang'];  
      $r["role"]=$r3['role'];  
    }           
    array_push($data,$r);   
  }
  $arr=["result"=>"success","data"=>$data];
} else {
  $arr=["result"=>"error","message"=>"sql error: $sql"];
}
  
echo json_encode($arr);

  $stmt->close();
  $conn->close();
