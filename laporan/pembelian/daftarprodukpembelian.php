<?php
header("Access-Control-Allow-Origin: *"); 
$arr=null;

$conn = new mysqli("localhost", "n1561248_staff_coronet","sU[=]bRd;jm$","n1561248_pt_coronet_crown");
if($conn->connect_error) {
  $arr= ["result"=>"error","message"=>"unable to connect"];
}
setlocale(LC_ALL, 'IND');

extract($_POST);
$sql = "SELECT nota_beli_product.id_product, product.jenis, nota_beli_product.quantity, nota_beli_product.harga 
FROM nota_beli_product INNER JOIN product ON nota_beli_product.id_product = product.id 
WHERE nota_beli_product.id_nota_beli = ?;";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s",$id);
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