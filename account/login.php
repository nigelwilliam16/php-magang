<?php
header("Access-Control-Allow-Origin: *"); 
$arr=null;

$conn = new mysqli("localhost", "root","","pt_coronet_crown");
if($conn->connect_error) {
  $arr= ["result"=>"error","message"=>"unable to connect"];
}
extract($_POST);

$sql = "SELECT account.username, account.nama_depan, account.nama_belakang, account.email, account.password, 
account.avatar, account.tanggal_gabung, account.id_jabatan, jabatan.jabatan, account.id_cabang, cabang.nama_cabang 
FROM account INNER JOIN jabatan ON account.id_jabatan = jabatan.id INNER JOIN cabang ON account.id_cabang = cabang.id 
WHERE account.username = ? AND account.password = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss",$username,$password);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
      $r=mysqli_fetch_assoc($result);
      $arr=["result"=>"success","username"=>$r['username'],"nama_depan"=>$r['nama_depan'], "nama_belakang"=>$r['nama_belakang'], 
      "email"=>$r['email'], "tanggal_gabung"=>$r['tanggal_gabung'], "avatar"=>$r['avatar'], "id_jabatan"=>$r['id_jabatan'],
      "jabatan"=>$r['jabatan'],"id_cabang"=>$r['id_cabang'], "cabang"=>$r['nama_cabang']];
  } else {
      $arr= ["result"=>"error","message"=>"sql error: $sql"];
  }
  
echo json_encode($arr);

  $stmt->close();
  $conn->close();
?>