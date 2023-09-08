<?php
header("Access-Control-Allow-Origin: *"); 
$arr=null;

$conn = new mysqli("localhost", "n1561248_staff_coronet","sU[=]bRd;jm$","n1561248_pt_coronet_crown");
if($conn->connect_error) {
  $arr= ["result"=>"error","message"=>"unable to connect"];
}
setlocale(LC_ALL, 'id_ID');
extract($_POST);

$sql3 = "UPDATE event SET event.status = 1 WHERE event.tanggal = CURRENT_DATE AND 
(CURRENT_TIME BETWEEN event.waktu_mulai AND event.waktu_selesai)";
$stmt3 = $conn->prepare($sql3);
$stmt3->execute();

$sql4 = "UPDATE event SET event.status = 2 WHERE ((event.tanggal = CURRENT_DATE AND 
CURRENT_TIME >= event.waktu_mulai) OR CURRENT_DATE > event.tanggal) AND NOT event.status = 3";
$stmt4 = $conn->prepare($sql4);
$stmt4->execute();


$sql = "SELECT event.id, event.nama, event.lokasi as alamat, event.tanggal, event.status, event.tanggal_pengajuan,
(SELECT CONCAT(account.nama_depan, ' ', account.nama_belakang) FROM personil_event INNER JOIN account ON 
personil_event.username = account.username WHERE personil_event.role = 1 AND personil_event.id_event = event.id) AS 'penanggung_jawab'
FROM event WHERE (event.id LIKE '%$cari%' OR event.nama LIKE '%$cari%')
AND event.tanggal BETWEEN ? AND ? GROUP BY event.id ORDER BY event.tanggal DESC";
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

    $persetujuan = [];
    $sql2 = "SELECT jabatan.jabatan, persetujuan_event.status_laporan FROM persetujuan_event 
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
