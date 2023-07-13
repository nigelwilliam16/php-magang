<?php
header("Access-Control-Allow-Origin: *"); 
$arr=null;

$conn = new mysqli("localhost", "n1561248_staff_coronet","sU[=]bRd;jm$","n1561248_pt_coronet_crown");
if($conn->connect_error) {
  $arr= ["result"=>"error","message"=>"unable to connect"];
}
    extract($_POST);    
    $sql="INSERT INTO grup(id,nama_grup,id_cabang)
    VALUES(?,?,?)";
    $stmt=$conn->prepare($sql);
    $stmt->bind_param("sss",$id,$nama_group,$id_cabang);
    $stmt->execute();
    if ($stmt->affected_rows > 0) {
        $sql2="UPDATE account SET account.id_grup = ? WHERE account.username = ?";
        $stmt2=$conn->prepare($sql2);
        $stmt2->bind_param("ss",$id,$username);
        $stmt2->execute();
        $arr=["result"=>"success","id"=>$conn->insert_id];
    } else {
        $arr=["result"=>"fail","Error"=>$conn->error];
    }
    echo json_encode($arr);
?>