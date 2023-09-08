<?php
header("Access-Control-Allow-Origin: *"); 
$arr=null;

$conn = new mysqli("localhost", "n1561248_staff_coronet","sU[=]bRd;jm$","n1561248_pt_coronet_crown");
if($conn->connect_error) {
  $arr= ["result"=>"error","message"=>"unable to connect"];
}
extract($_POST);
$parameter = json_decode($_POST['parameter']);
$concenateParameter = "";

if(!empty($parameter)) {
  $concenateParameter = "AND NOT event_product.id_product = ".implode(" AND NOT event_product.id_product = ",$parameter);
}

$sql = "SELECT event_product.id_product AS id, product.jenis, event_product.harga FROM event_product 
INNER JOIN product ON event_product.id_product = product.id WHERE event_product.id_event = ? ".$concenateParameter;
$stmt = $conn->prepare($sql);
$stmt->bind_param("s",$id_event);
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