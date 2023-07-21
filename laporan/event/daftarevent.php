<?php
header("Access-Control-Allow-Origin: *"); 
$arr=null;

$conn = new mysqli("localhost", "n1561248_staff_coronet","sU[=]bRd;jm$","n1561248_pt_coronet_crown");
if($conn->connect_error) {
  $arr= ["result"=>"error","message"=>"unable to connect"];
}
setlocale(LC_ALL, 'IND');

extract($_POST);
$sql = "SELECT event.id, event.nama, event.lokasi, event.tanggal_pengajuan, event.status_laporan, event.tanggal,
SUM(event_jual_product.quantity) AS jumlah_penjualan, SUM(event_jual_product.harga*event_jual_product.quantity) AS total_penjualan
FROM event INNER JOIN event_jual_product ON event_jual_product.event_id = event.id 
WHERE (event.id LIKE '%$cari%' OR event.nama LIKE '%$cari%')
AND event.tanggal_pengajuan BETWEEN ? AND ? GROUP BY event.id ORDER BY event.tanggal_pengajuan DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss",$startdate,$enddate);
$stmt->execute();
$result = $stmt->get_result();
$data=[];
if($result->num_rows > 0) {
  while($r=mysqli_fetch_assoc($result)) {    
    $sql2 = "SELECT SUM(kebutuhan_event.quantity) AS jumlah_kebutuhan, 
    SUM(kebutuhan_event.quantity*kebutuhan_event.harga) AS total_pengeluaran 
    FROM kebutuhan_event WHERE event_id = ?";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param("s",$r['id']);
    $stmt2->execute();
    $result2 = $stmt2->get_result();
    if($result2->num_rows > 0) { 
        $r2=mysqli_fetch_assoc($result2);  
        $tahun = substr($r['tanggal'], 0,4);
        $bulan = substr($r['tanggal'], 5,2);
        $tanggal = substr($r['tanggal'], 8,2);
        $r['tanggal'] = strftime( "%A, %d %B %Y", mktime(0,0,0,$bulan,$tanggal,$tahun));
        $tahun_pengajuan = substr($r['tanggal_pengajuan'], 0,4);
        $bulan_pengajuan = substr($r['tanggal_pengajuan'], 5,2);
        $tanggal_pengajuan = substr($r['tanggal_pengajuan'], 8,2);
        $r['tanggal_pengajuan'] = strftime( "%A, %d %B %Y", mktime(0,0,0,$bulan_pengajuan,$tanggal_pengajuan,$tahun_pengajuan));
        $r['jumlah_kebutuhan'] = $r2['jumlah_kebutuhan'];
        $r['total_pengeluaran'] = $r2['total_pengeluaran'];

        $personil=[];
        $sql3 = "SELECT personil_event.account_username, account.nama_depan, account.nama_belakang, personil_event.role 
        FROM personil_event INNER JOIN account ON personil_event.account_username = account.username 
        WHERE personil_event.event_id = ? AND personil_event.role = 1";
        $stmt3 = $conn->prepare($sql3);
        $stmt3->bind_param("s",$r['id']);
        $stmt3->execute();
        $result3 = $stmt3->get_result();
        if($result3->num_rows > 0) { 
          $r3=mysqli_fetch_assoc($result3);
          $r["username"]=$r3['account_username'];  
          $r["nama_depan"]=$r3['nama_depan'];  
          $r["nama_belakang"]=$r3['nama_belakang'];  
          $r["role"]=$r3['role'];  
        }
        // while() { 
        //   array_push($personil,$r3);
        // }
        
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
