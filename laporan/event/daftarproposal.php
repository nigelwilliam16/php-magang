<?php
header("Access-Control-Allow-Origin: *"); 
$arr=null;

$conn = new mysqli("localhost", "n1561248_staff_coronet","sU[=]bRd;jm$","n1561248_pt_coronet_crown");
if($conn->connect_error) {
  $arr= ["result"=>"error","message"=>"unable to connect"];
}
setlocale(LC_ALL, 'id_ID');
extract($_POST);
$sql = "SELECT event.id, event.nama, event.lokasi as alamat,event.tanggal_pengajuan, event.tanggal,
(SELECT CONCAT(account.nama_depan, ' ', account.nama_belakang) FROM personil_event INNER JOIN account ON 
personil_event.username = account.username WHERE personil_event.role = 1 AND personil_event.id_event = event.id) AS 'penanggung_jawab'
FROM event WHERE (event.id LIKE '%$cari%' OR event.nama LIKE '%$cari%')
AND event.tanggal_pengajuan BETWEEN ? AND ? GROUP BY event.id ORDER BY event.tanggal_pengajuan DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss",$startdate,$enddate);
$stmt->execute();
$result = $stmt->get_result();
$data=[];
if($result->num_rows > 0) {
  while($r=mysqli_fetch_assoc($result)) {
    $tahun = substr($r['tanggal'], 0,4);
    $bulan = substr($r['tanggal'], 5,2);
    $tanggal = substr($r['tanggal'], 8,2);
    $r['tanggal'] = strftime( "%A, %d %B %Y", mktime(0,0,0,$bulan,$tanggal,$tahun));
    $tahun_pengajuan = substr($r['tanggal_pengajuan'], 0,4);
    $bulan_pengajuan = substr($r['tanggal_pengajuan'], 5,2);
    $tanggal_pengajuan = substr($r['tanggal_pengajuan'], 8,2);
    $r['tanggal_pengajuan'] = strftime( "%A, %d %B %Y", mktime(0,0,0,$bulan_pengajuan,$tanggal_pengajuan,$tahun_pengajuan));

    $persetujuan = [];
    $sql2 = "SELECT jabatan.jabatan, persetujuan_event.status_proposal FROM persetujuan_event 
    INNER JOIN account ON persetujuan_event.username = account.username INNER JOIN jabatan ON account.id_jabatan = jabatan.id 
    WHERE persetujuan_event.id_event = ?";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param("s",$r['id']);
    $stmt2->execute();
    $result2 = $stmt2->get_result();
    if($result2->num_rows > 0) { 
        while($r2=mysqli_fetch_assoc($result2)) {
            array_push($persetujuan,$r2);
        }
      $r['persetujuan'] = $persetujuan;      
      array_push($data,$r);   
    }
  }
  $arr=["result"=>"success","data"=>$data];
} else {
  $arr=["result"=>"error","message"=>"sql error: $sql"];
}
  
echo json_encode($arr);

  $stmt->close();
  $conn->close();
