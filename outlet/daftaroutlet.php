<?php
header("Access-Control-Allow-Origin: *"); 
$arr=null;

$conn = new mysqli("localhost", "n1561248_staff_coronet","sU[=]bRd;jm$","n1561248_pt_coronet_crown");
if($conn->connect_error) {
  $arr= ["result"=>"error","message"=>"unable to connect"];
}
extract($_POST);
$sql = "SELECT outlet.id, outlet.nama_toko, outlet.alamat, outlet.kodepos, outlet.id_tipe, tipe_outlet.keterangan, 
id_kelurahan, kelurahan.kelurahan FROM outlet INNER JOIN kelurahan ON outlet.id_kelurahan = kelurahan.id 
INNER JOIN tipe_outlet ON tipe_outlet.id = outlet.id_tipe WHERE outlet.nama_toko LIKE '%$cari%'";

$stmt = $conn->prepare($sql);
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

  //SELECT outlet.id, outlet.nama_toko, outlet.alamat, outlet.kodepos, outlet.id_tipe, tipe_outlet.keterangan, id_kelurahan, kelurahan.kelurahan, kelurahan.kecamatan_id, kecamatan.kecamatan FROM outlet INNER JOIN kelurahan ON outlet.id_kelurahan = kelurahan.id INNER JOIN tipe_outlet ON tipe_outlet.id = outlet.id_tipe INNER JOIN kecamatan ON kelurahan.kecamatan_id = kecamatan.id;

?>