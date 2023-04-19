<?php
header("Access-Control-Allow-Origin: *"); 
$arr=null;

$conn = new mysqli("localhost", "root","","pt_coronet_crown");
if($conn->connect_error) {
  $arr= ["result"=>"error","message"=>"unable to connect"];
}
extract($_POST);
  $sql = "SELECT * FROM kota WHERE kota.id_provinsi = ? AND kota.kota LIKE '%$cari%'";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("s",$id_provinsi);
  $stmt->execute();
  $result = $stmt->get_result();
  $data=[];
if($result->num_rows > 0) {
  while($r=mysqli_fetch_assoc($result)) {
    $sql2 = "SELECT * FROM kecamatan WHERE kecamatan.id_kota = ? ";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param("s",$r['id']);
    $stmt2->execute();
    $result2 = $stmt2->get_result();
    $kecamatan = [];
    if($result2->num_rows > 0) {
        while($r2=mysqli_fetch_assoc($result2)) {
            array_push($kecamatan,$r2);  
        }
    }
    $r['kecamatan'] = $kecamatan;
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