<?php
header("Access-Control-Allow-Origin: *"); 
$arr=null;

$conn = new mysqli("localhost", "n1561248_staff_coronet","sU[=]bRd;jm$","n1561248_pt_coronet_crown");
if($conn->connect_error) {
  $arr= ["result"=>"error","message"=>"unable to connect"];
}
setlocale(LC_ALL, 'IND');

extract($_POST);
if($_POST['idjabatan'] == "1" || $_POST['idjabatan'] == "2") {
  $sql = "SELECT visit.id, visit.waktu_in, visit.tanggal, visit.waktu_out, visit.status, visit.deskripsi, visit.id_outlet, 
  outlet.nama_toko, visit.username, account.nama_depan, account.nama_belakang FROM visit INNER JOIN account ON 
  visit.username = account.username INNER JOIN outlet ON visit.id_outlet = outlet.id 
  WHERE (visit.id LIKE '%$cari%' OR account.nama_depan LIKE '%$cari%' OR account.nama_belakang LIKE '%$cari%' OR 
  outlet.nama_toko LIKE '%$cari%') AND visit.tanggal BETWEEN ? AND ? ORDER BY visit.tanggal DESC";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("ss",$startdate, $enddate);
}
elseif($_POST['idjabatan'] == "3") {
  $sql = "SELECT visit.id, visit.waktu_in, visit.tanggal, visit.waktu_out, visit.status, visit.deskripsi, visit.id_outlet, 
  outlet.nama_toko, visit.username, account.nama_depan, account.nama_belakang FROM visit INNER JOIN account ON 
  visit.username = account.username INNER JOIN outlet ON visit.id_outlet = outlet.id WHERE visit.username = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("s",$username);
}
elseif($_POST['idjabatan'] == "3") {
    $sql = "SELECT visit.id, visit.waktu_in, visit.tanggal, visit.waktu_out, visit.status, visit.deskripsi, visit.id_outlet, 
    outlet.nama_toko, visit.username, account.nama_depan, account.nama_belakang FROM visit INNER JOIN account ON 
    visit.username = account.username INNER JOIN outlet ON visit.id_outlet = outlet.id WHERE visit.username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s",$username);
}
$stmt->execute();
$result = $stmt->get_result();
$data = [];
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