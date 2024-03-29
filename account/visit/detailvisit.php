<?php
header("Access-Control-Allow-Origin: *"); 
$arr=null;

$conn = new mysqli("localhost", "n1561248_staff_coronet","sU[=]bRd;jm$","n1561248_pt_coronet_crown");
if($conn->connect_error) {
  $arr= ["result"=>"error","message"=>"unable to connect"];
}
setlocale(LC_ALL, 'IND');

extract($_POST);
$sql = "SELECT visit.id, visit.waktu_in, visit.tanggal, visit.waktu_out, visit.status, visit.bukti, visit.deskripsi, visit.id_outlet, 
outlet.nama_toko, visit.username, account.nama_depan, account.nama_belakang 
FROM visit INNER JOIN account ON visit.username = account.username INNER JOIN outlet ON visit.id_outlet = outlet.id WHERE visit.id = ?";
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

  $sql2 = "SELECT notal_jual.id FROM notal_jual WHERE notal_jual.id_visit = ?";
  $stmt2 = $conn->prepare($sql2);
  $stmt2->bind_param("s",$id);
  $stmt2->execute();
  $result2 = $stmt2->get_result();
  if ($result2->num_rows > 0) {
        $r2=mysqli_fetch_assoc($result2);
        $r['id_nota']=$r2['id'];
  } else {
    $r['id_nota']=null;
  }

  $arr=["result"=>"success","data"=>$r];
} else {
  $arr=["result"=>"error","message"=>"sql error: $sql"];
}
  
echo json_encode($arr);

  $stmt->close();
  $conn->close();
?>