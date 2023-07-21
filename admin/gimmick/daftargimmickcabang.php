<?php
header("Access-Control-Allow-Origin: *"); 
$arr=null;

$conn = new mysqli("localhost", "n1561248_staff_coronet","sU[=]bRd;jm$","n1561248_pt_coronet_crown");
if($conn->connect_error) {
  $arr= ["result"=>"error","message"=>"unable to connect"];
}
extract($_POST);

$sql = "SELECT gimmick_cabang.id_gimmick AS id, gimmick.barang FROM gimmick_cabang 
INNER JOIN gimmick ON gimmick_cabang.id_gimmick = gimmick.id WHERE gimmick_cabang.id_cabang = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s",$idCabang);
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