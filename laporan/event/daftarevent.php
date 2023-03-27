<?php
header("Access-Control-Allow-Origin: *"); 
$arr=null;

$conn = new mysqli("localhost", "root","","pt_coronet_crown");
if($conn->connect_error) {
  $arr= ["result"=>"error","message"=>"unable to connect"];
}
setlocale(LC_ALL, 'IND');

extract($_POST);
$sql = "SELECT event.id, event.nama, event.lokasi, event.tanggal, event.tujuan, event.proposal, event.status_proposal, event.laporan, 
SUM(event_jual_product.quantity) AS jumlah_penjualan, SUM(event_jual_product.harga*event_jual_product.quantity) AS total_penjualan, 
FROM event INNER JOIN event_jual_product ON event_jual_product.event_id = event.id 
WHERE (event.id LIKE '%$cari%' OR account.nama_depan LIKE '%$cari%' OR account.nama_belakang LIKE '%$cari%' OR event.nama LIKE '%$cari%')
AND event.tanggal BETWEEN ? AND ? GROUP BY event.id ORDER BY event.tanggal ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss",$startdate,$enddate);
$stmt->execute();
$result = $stmt->get_result();
$data=[];
$personil=[];
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
        $r['jumlah_kebutuhan'] = $r2['jumlah_kebutuhan'];
        $r['total_pengeluaran'] = $r2['total_pengeluaran'];
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
?>