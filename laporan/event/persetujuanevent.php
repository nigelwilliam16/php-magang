<?php
header("Access-Control-Allow-Origin: *"); 
$arr=null;

$conn = new mysqli("localhost", "n1561248_staff_coronet","sU[=]bRd;jm$","n1561248_pt_coronet_crown");
if($conn->connect_error) {
  $arr= ["result"=>"error","message"=>"unable to connect"];
}
extract($_POST);   

if($type == "1") {
    $sql="UPDATE persetujuan_event SET status_proposal = ?, keterangan_proposal = ? WHERE username = ? AND id_event = ?";
    $stmt=$conn->prepare($sql);
    $stmt->bind_param("ssss",$value,$keterangan,$username,$id);    
} else {   
    $sql="UPDATE persetujuan_event SET status_laporan = ?, keterangan_laporan = ? WHERE username = ? AND id_event = ?";
    $stmt=$conn->prepare($sql);
    $stmt->bind_param("ssss",$value,$keterangan,$username,$id);    
}
$stmt->execute();

if ($stmt->affected_rows > 0) {
    $arr=["result"=>"success","id"=>$conn->insert_id];
} else {
    $arr=["result"=>"fail","Error"=>$conn->error];
}
echo json_encode($arr); 
?>