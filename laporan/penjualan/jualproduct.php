<?php
header("Access-Control-Allow-Origin: *"); 
$arr=null;

$conn = new mysqli("localhost", "root","","pt_coronet_crown");
if($conn->connect_error) {
  $arr= ["result"=>"error","message"=>"unable to connect"];
}
    ini_set('date.timezone', 'Asia/Jakarta');   
    $date = date("Y-m-d");
    $time = date("H:i:s");
    
    extract($_POST);   
    
    $nota_jual_id = "$id-$id_outlet-$username-$date";
    
    $sql="INSERT INTO notal_jual_product(id_nota_jual,id_product,quantity,harga) VALUES(?,?,?,?)";
    $stmt=$conn->prepare($sql);
    $stmt->bind_param("ssii",$nota_jual_id,$product,$quantity,$harga);
    $stmt->execute();
    if ($stmt->affected_rows > 0) {
        $arr=["result"=>"success","id"=>$conn->insert_id];
    } else {
        $arr=["result"=>"fail","Error"=>$conn->error];
    }
    echo json_encode($arr);
?>