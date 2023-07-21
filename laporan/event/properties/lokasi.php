<?php
header("Access-Control-Allow-Origin: *"); 
$arr=null;

$conn = new mysqli("localhost", "n1561248_staff_coronet","sU[=]bRd;jm$","n1561248_pt_coronet_crown");
if($conn->connect_error) {
  $arr= ["result"=>"error","message"=>"unable to connect"];
}
extract($_POST);

if($_POST['type'] == "0") {
    $sql = "SELECT cabang.id_kota, kota.kota, kota.id_provinsi, provinsi.provinsi
    FROM cabang INNER JOIN kota ON cabang.id_kota = kota.id INNER JOIN provinsi ON kota.id_provinsi = provinsi.id 
    WHERE cabang.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s",$id);
  } elseif ($_POST['type'] == "1") {
    $sql = "SELECT kecamatan.id, kecamatan.kecamatan, cabang.id_kota, kota.kota, kota.id_provinsi FROM cabang 
    INNER JOIN kota ON cabang.id_kota = kota.id INNER JOIN kecamatan ON kecamatan.id_kota = kota.id WHERE cabang.id = ? 
    AND kecamatan.kecamatan LIKE '%$cari%'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s",$id);
  } else {
    $sql = "SELECT * FROM kelurahan WHERE kelurahan.kecamatan_id = ? AND kelurahan.kelurahan LIKE '%$cari%'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s",$id);
  }

$stmt->execute();
$result = $stmt->get_result();
$data=[];
if($result->num_rows > 0) {
  while($r=mysqli_fetch_assoc($result)) {
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