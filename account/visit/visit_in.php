<?php
header("Access-Control-Allow-Origin: *"); 
$arr=null;

$conn = new mysqli("localhost", "n1561248_staff_coronet","sU[=]bRd;jm$","n1561248_pt_coronet_crown");
if($conn->connect_error) {
  $arr= ["result"=>"error","message"=>"unable to connect"];
}
    extract($_POST);
    $waktu_in = date("H:i:s");
    
    $sql="INSERT INTO visit(id, tanggal, waktu_in, status, deskripsi, id_outlet, username) VALUES(?,?,?,0,?,?,?)";
    $stmt=$conn->prepare($sql);
    $stmt->bind_param("ssssss",$id, $tanggal, $waktu_in, $deskripsi, $id_outlet, $username);
    $stmt->execute();
    if ($stmt->affected_rows > 0) {
        $arr=["result"=>"success","id"=>$conn->insert_id];
    } else {
        $arr=["result"=>"fail","Error"=>$conn->error];
    }
    echo json_encode($arr);
?>