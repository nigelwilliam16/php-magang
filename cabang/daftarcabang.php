<?php
header("Access-Control-Allow-Origin: *"); 
$arr=null;

$conn = new mysqli("localhost", "root","","pt_coronet_crown");
if($conn->connect_error) {
  $arr= ["result"=>"error","message"=>"unable to connect"];
}
extract($_POST);

$sql = "SELECT cabang.id, cabang.nama_cabang, cabang.alamat, cabang.kodepos, cabang.id_kota, kota.kota 
FROM cabang INNER JOIN kota ON cabang.id_kota = kota.id";
// $sql = "SELECT cabang.id, cabang.nama_cabang, cabang.alamat, cabang.kodepos, cabang.id_kota, kota.kota, product.jenis, cabang_product.stok 
// FROM cabang INNER JOIN kota ON cabang.id_kota = kota.id INNER JOIN cabang_product ON cabang_product.id_cabang = cabang.id 
// INNER JOIN product ON cabang_product.id_product = product.id";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result(); 
$data=[];
if ($result->num_rows > 0) {
    while($r=$result->fetch_assoc())
        {
            array_push($data,$r);
        }
        $arr=["result"=>"success","data"=>$data];
  } else {
      $arr= ["result"=>"error","message"=>"sql error: $sql"];
  }
  
echo json_encode($arr);

  $stmt->close();
  $conn->close();
?>