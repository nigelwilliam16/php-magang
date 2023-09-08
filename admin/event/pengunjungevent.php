<?php
header("Access-Control-Allow-Origin: *"); 
$arr=null;

$conn = new mysqli("localhost", "n1561248_staff_coronet","sU[=]bRd;jm$","n1561248_pt_coronet_crown");
if($conn->connect_error) {
  $arr= ["result"=>"error","message"=>"unable to connect"];
}
extract($_POST);
$pengunjung = json_decode($_POST['existedPengunjung']);
$concenateParameter = "";

if(!empty($pengunjung)) {
  $concenateParameter = "AND NOT username = ".implode(" AND NOT username = ",$pengunjung);
}
$sql = "SELECT * FROM akun_pengunjung WHERE (username OR nama) LIKE  '%$cari%' ".$concenateParameter;
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
  $arr=["result"=>"error","data"=>$data];
}
  
echo json_encode($arr);

  $stmt->close();
  $conn->close();
?>