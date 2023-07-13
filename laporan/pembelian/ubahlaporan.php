<?php
header("Access-Control-Allow-Origin: *"); 
$arr=null;

$conn = new mysqli("localhost", "n1561248_staff_coronet","sU[=]bRd;jm$","n1561248_pt_coronet_crown");
if($conn->connect_error) {
  $arr= ["result"=>"error","message"=>"unable to connect"];
}
    extract($_POST);
    if($_POST['type'] == "1") {
        $sql="UPDATE nota_beli SET nota_beli.tanggal=? WHERE nota_beli.id = ?";
        $stmt=$conn->prepare($sql);
        $stmt->bind_param("ss",$tanggal, $id);
    }
    else if($_POST['type'] == "2") {
        $sql="UPDATE nota_beli SET nota_beli.diskon=? WHERE nota_beli.id = ?";
        $stmt=$conn->prepare($sql);
        $stmt->bind_param("ss",$diskon, $id);
    }
    else if($_POST['type'] == "3") {
        $sql="UPDATE nota_beli SET nota_beli.ppn=? WHERE nota_beli.id = ?";
        $stmt=$conn->prepare($sql);
        $stmt->bind_param("ss",$ppn, $id);
    }   
    else if($_POST['type'] == "4") {
        $sql="UPDATE nota_beli SET nota_beli.id_supplier=? WHERE nota_beli.id = ?";
        $stmt=$conn->prepare($sql);
        $stmt->bind_param("ss",$id_supplier, $id);
    }  
    $stmt->execute();
    if ($stmt->affected_rows > 0) {
        $arr=["result"=>"success","id"=>$conn->insert_id];
    } else {
        $arr=["result"=>"fail","Error"=>$conn->error];
    }
    echo json_encode($arr);
?>