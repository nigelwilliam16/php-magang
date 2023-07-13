<?php
header("Access-Control-Allow-Origin: *"); 
$arr=null;

$conn = new mysqli("localhost", "n1561248_staff_coronet","sU[=]bRd;jm$","n1561248_pt_coronet_crown");
if($conn->connect_error) {
  $arr= ["result"=>"error","message"=>"unable to connect"];
}
extract($_POST);

$sql = "SELECT account.username, account.nama_depan, account.nama_belakang, account.email, account.avatar, 
account.gender, account.no_telp, account.tanggal_gabung, account.id_jabatan, jabatan.jabatan, account.id_grup, 
grup.nama_grup, account.id_cabang, cabang.nama_cabang FROM account INNER JOIN jabatan ON account.id_jabatan = jabatan.id 
INNER JOIN grup ON account.id_grup = grup.id INNER JOIN cabang ON account.id_cabang = cabang.id WHERE jabatan.id = 4 AND 
account.id_grup = '-'  AND account.id_cabang = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s",$id_cabang);
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