<?php
header("Access-Control-Allow-Origin: *"); 
$arr=null;

$conn = new mysqli("localhost", "root","","pt_coronet_crown");
if($conn->connect_error) {
  $arr= ["result"=>"error","message"=>"unable to connect"];
}
extract($_POST);

$sql = "SELECT account.nama_depan, account.nama_belakang, account.id_grup, grup.nama_grup, grup.id_cabang, cabang.nama_cabang, 
COUNT(account.username) AS 'jumlah_pegawai' FROM account INNER JOIN grup ON account.id_grup = grup.id 
INNER JOIN cabang ON account.id_cabang = cabang.id WHERE account.id_jabatan = 4 AND NOT account.id_grup = '-' GROUP BY account.id_grup";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
$data=[];
if($result->num_rows > 0) {
  while($r=mysqli_fetch_assoc($result)) {
    $sql2 = "SELECT COUNT(account.username) AS 'jumlah_pegawai' FROM account WHERE id_grup = '".$r['id_grup']."'";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->execute();
    $result2 = $stmt2->get_result();
    if ($result2->num_rows > 0) {
      $r2=mysqli_fetch_assoc($result2);
      $r['jumlah_pegawai'] = $r2['jumlah_pegawai'];
    }
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