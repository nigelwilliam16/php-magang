<?php
header("Access-Control-Allow-Origin: *"); 
$arr=null;

$conn = new mysqli("localhost", "n1561248_staff_coronet","sU[=]bRd;jm$","n1561248_pt_coronet_crown");
if($conn->connect_error) {
  $arr= ["result"=>"error","message"=>"unable to connect"];
}
setlocale(LC_ALL, 'id_ID');

extract($_POST);
$sql = "SELECT event.id, event.nama,event.tanggal_pengajuan, event.tanggal, event.tujuan, event.strategi, 
event.latar_belakang, event.tanggal FROM event WHERE event.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s",$id);
$stmt->execute();
$result = $stmt->get_result();
if($result->num_rows > 0) {
    $r=mysqli_fetch_assoc($result);  
    $tahun = substr($r['tanggal'], 0,4);
    $bulan = substr($r['tanggal'], 5,2);
    $tanggal = substr($r['tanggal'], 8,2);
    $r['tanggal'] = strftime( "%A, %d %B %Y", mktime(0,0,0,$bulan,$tanggal,$tahun));
    $tahun_pengajuan = substr($r['tanggal_pengajuan'], 0,4);
    $bulan_pengajuan = substr($r['tanggal_pengajuan'], 5,2);
    $tanggal_pengajuan = substr($r['tanggal_pengajuan'], 8,2);
    $r['tanggal_pengajuan'] = strftime( "%A, %d %B %Y", mktime(0,0,0,$bulan_pengajuan,$tanggal_pengajuan,$tahun_pengajuan));
    $lokasi=[];
    $sql2 = "SELECT event.lokasi as alamat, kelurahan.kelurahan, kecamatan.kecamatan, kota.kota, provinsi.provinsi 
    FROM event INNER JOIN kelurahan ON event.id_kelurahan = kelurahan.id INNER JOIN kecamatan ON kelurahan.kecamatan_id = kecamatan.id 
    INNER JOIN kota ON kecamatan.id_kota = kota.id INNER JOIN provinsi ON kota.id_provinsi = provinsi.id WHERE event.id = ?";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param("s",$id);
    $stmt2->execute();
    $result2 = $stmt2->get_result();
    if($result2->num_rows > 0) { 
        while($r2=mysqli_fetch_assoc($result2)) {
            array_push($lokasi,$r2);
        }
    $r['lokasi'] = $lokasi;
    }
    $personil=[];
    $sql3 = "SELECT personil_event.username, account.nama_depan, account.nama_belakang, account.email, account.no_telp, jabatan.jabatan, personil_event.role 
    FROM personil_event INNER JOIN account ON personil_event.username = account.username INNER JOIN jabatan ON account.id_jabatan = jabatan.id
    WHERE personil_event.id_event = ? AND NOT personil_event.role = 1";
    $stmt3 = $conn->prepare($sql3);
    $stmt3->bind_param("s",$id);
    $stmt3->execute();
    $result3 = $stmt3->get_result();
    if($result3->num_rows > 0) { 
        while($r3=mysqli_fetch_assoc($result3)) {
            array_push($personil,$r3);
        }
    $r['personil'] = $personil;
    }  
    $target = [];
    $sql4 = "SELECT target.id, target.parameter, target.perhitungan, target.bobot, target_event.target_proposal 
    FROM target_event INNER JOIN target ON target_event.id_target = target.id WHERE target_event.id_event = ?";
    $stmt4 = $conn->prepare($sql4);
    $stmt4->bind_param("s",$id);
    $stmt4->execute();
    $result4 = $stmt4->get_result();
    if($result4->num_rows > 0) { 
        while($r4=mysqli_fetch_assoc($result4)) {
            array_push($target,$r4);
        }
    $r['target'] = $target;
    }   
    $kebutuhan = [];
    $sql5 = "SELECT * FROM kebutuhan_event WHERE kebutuhan_event.id_event = ?";
    $stmt5 = $conn->prepare($sql5);
    $stmt5->bind_param("s",$id);
    $stmt5->execute();
    $result5 = $stmt5->get_result();
    if($result5->num_rows > 0) { 
        while($r5=mysqli_fetch_assoc($result5)) {
            array_push($kebutuhan,$r5);
        }
    $r['kebutuhan'] = $kebutuhan;
    }   
    
    $gimmick = [];
    $sql6 = "SELECT gimmick.id, gimmick.barang, gimmick.harga, gimmick_event.quantity_proposal, (gimmick.harga*gimmick_event.quantity_proposal) AS total_biaya
    FROM gimmick_event INNER JOIN gimmick ON gimmick_event.id_gimmick = gimmick.id WHERE gimmick_event.id_event = ?";
    $stmt6 = $conn->prepare($sql6);
    $stmt6->bind_param("s",$id);
    $stmt6->execute();
    $result6 = $stmt6->get_result();
    if($result6->num_rows > 0) { 
        while($r6=mysqli_fetch_assoc($result6)) {
            array_push($gimmick,$r6);
        }
    $r['gimmick'] = $gimmick;
    } 

    $persetujuan = [];
    $sql7 = "SELECT jabatan.jabatan, persetujuan_event.username, persetujuan_event.id_event, persetujuan_event.status_proposal, persetujuan_event.keterangan_proposal
    FROM persetujuan_event INNER JOIN account ON persetujuan_event.username = account.username 
    INNER JOIN jabatan ON account.id_jabatan = jabatan.id WHERE persetujuan_event.id_event = ?";
    $stmt7 = $conn->prepare($sql7);
    $stmt7->bind_param("s",$id);
    $stmt7->execute();
    $result7 = $stmt7->get_result();
    if($result7->num_rows > 0) { 
        while($r7=mysqli_fetch_assoc($result7)) {
            array_push($persetujuan,$r7);
        }
    $r['persetujuan'] = $persetujuan;
    } 

    // $new_user = [];
    // $sql6 = "SELECT * FROM akun_pengunjung WHERE akun_pengunjung.id_event = ? AND ";
    // $stmt6 = $conn->prepare($sql6);
    // $stmt6->bind_param("s",$id);
    // $stmt6->execute();
    // $result6 = $stmt6->get_result();
    // if($result6->num_rows > 0) { 
    //     while($r6=mysqli_fetch_assoc($result6)) {
    //         array_push($gimmick,$r6);
    //     }
    // $r['new_user'] = $new_user;
    // } 

    // $old_user = [];
    // $sql6 = "SELECT * FROM gimmick WHERE gimmick.id_event = ?";
    // $stmt6 = $conn->prepare($sql6);
    // $stmt6->bind_param("s",$id);
    // $stmt6->execute();
    // $result6 = $stmt6->get_result();
    // if($result6->num_rows > 0) { 
    //     while($r6=mysqli_fetch_assoc($result6)) {
    //         array_push($gimmick,$r6);
    //     }
    // $r['old_user'] = $old_user;
    // } 
    
//   }
  $arr=["result"=>"success","data"=>$r];
} else {
  $arr=["result"=>"error","message"=>"sql error: $sql"];
}
  
echo json_encode($arr);

  $stmt->close();
  $conn->close();
  ?>