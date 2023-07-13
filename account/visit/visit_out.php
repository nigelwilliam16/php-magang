<?php
header("Access-Control-Allow-Origin: *"); 
$arr=null;

$conn = new mysqli("localhost", "n1561248_staff_coronet","sU[=]bRd;jm$","n1561248_pt_coronet_crown");
if($conn->connect_error) {
  $arr= ["result"=>"error","message"=>"unable to connect"];
}
    extract($_POST);
    $waktu_out = date("H:i:s");
    
    $sql="UPDATE visit SET waktu_out=?, status=1,bukti=? WHERE id = ?";
    $stmt=$conn->prepare($sql);
    $stmt->bind_param("sss",$waktu_out, $bukti, $id);
    $stmt->execute();
    if ($stmt->affected_rows > 0) {
        $arr=["result"=>"success","id"=>$conn->insert_id];
    } else {
        $arr=["result"=>"fail","Error"=>$conn->error];
    }
    echo json_encode($arr);
?>