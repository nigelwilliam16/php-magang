<?php
header("Access-Control-Allow-Origin: *"); 
$arr=null;

$conn = new mysqli("localhost", "n1561248_staff_coronet","sU[=]bRd;jm$","n1561248_pt_coronet_crown");
if($conn->connect_error) {
  $arr= ["result"=>"error","message"=>"unable to connect"];
}
    extract($_POST);
    
    if($_POST['namaBelakang'] == null || $_POST['namaBelakang'] == "")
    $namaBelakang = " ";
    else
    $namaBelakang =  $_POST['namaBelakang'] ;

    if($_POST['avatar'] == null || $_POST['avatar'] == "")
    $avatar = "";
    else
    $avatar =  $_POST['avatar'] ;

    // $avatar = base64_decode($_POST['avatar']);
    $current_date = date("j F Y ");
    
    $sql="INSERT INTO account(username,nama_depan,nama_belakang,email,password,avatar,gender,no_telp,tanggal_gabung,id_jabatan, id_cabang, id_grup)
    VALUES(?,?,?,?,?,?,?,?,?,?,?,?)";
    $stmt=$conn->prepare($sql);
    $stmt->bind_param("ssssssssssss",$username,$namaDepan,$namaBelakang,$email,$password,$avatar,$gender,$no_telp,$current_date,$jabatan, $cabang, $grup);
    $stmt->execute();
    if ($stmt->affected_rows > 0) {
        $arr=["result"=>"success","id"=>$conn->insert_id];
    } else {
        $arr=["result"=>"fail","Error"=>$conn->error];
    }
    echo json_encode($arr);
?>