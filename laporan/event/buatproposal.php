<?php
header("Access-Control-Allow-Origin: *"); 
$arr=null;

$conn = new mysqli("localhost", "n1561248_staff_coronet","sU[=]bRd;jm$","n1561248_pt_coronet_crown");
if($conn->connect_error) {
  $arr= ["result"=>"error","message"=>"unable to connect"];
}
    ini_set('date.timezone', 'Asia/Jakarta');   
    $tanggal_pengajuan = date("Y-m-d");
    
    extract($_POST);   

    $total_biaya = 0;
    $target_penjualan = 0;
    $target_person = 0;

    $personil = json_decode($_POST['personil']);
    $kebutuhan_tambahan = json_decode($_POST['kebutuhan_tambahan']);
    $gimmick = json_decode($_POST['gimmick']);
    $target = json_decode($_POST['target']);

    $sql="INSERT INTO event(id, nama, lokasi, tanggal, waktu_mulai, waktu_selesai, strategi, tujuan, latar_belakang, status_proposal, tanggal_pengajuan, status_laporan, id_kelurahan, id_tipe, id_cabang) 
    VALUES(?,?,?,?,?,?,?,?,?,0,?,0,?,?,?)";
    $stmt=$conn->prepare($sql);
    $stmt->bind_param("sssssssssssis",$id,$nama,$lokasi,$tanggal,$waktu_mulai,$waktu_selesai,$strategi,$tujuan,$latar_belakang,$tanggal_pengajuan,$id_kelurahan,$id_tipe,$id_cabang);
    $stmt->execute();

    $sql2="INSERT INTO personil_event(id_event,username,role) VALUES(?,?,1)";
    $stmt2=$conn->prepare($sql2);
    $stmt2->bind_param("ss",$id,$username);
    $stmt2->execute();

    if ($stmt->affected_rows > 0) {
        for($p = 0; $p < (count($personil)); $p++) {
            $sql3="INSERT INTO personil_event(id_event,username,role) VALUES(?,?,2)";
            $stmt3=$conn->prepare($sql3);
            $stmt3->bind_param("ss",$id,$personil[$p]);
            $stmt3->execute();  
        } 
        for($k = 0; $k < (count($kebutuhan_tambahan)); $k++) {
            $sql4="INSERT INTO kebutuhan_event(komponen,estimasi,id_event) VALUES(?,?,?)";
            $stmt4=$conn->prepare($sql4);
            $stmt4->bind_param("sis",$kebutuhan_tambahan[$k][0],$kebutuhan_tambahan[$k][1],$id);
            $stmt4->execute();  
            $total_biaya += $kebutuhan_tambahan[$k][1];
        } 
        for($g = 0; $g < (count($gimmick)); $g++) {
            $sql5="INSERT INTO gimmick_event(id_event,id_gimmick,quantity_proposal) VALUES(?,?,?)";
            $stmt5=$conn->prepare($sql5);
            $stmt5->bind_param("sii",$id,$gimmick[$g][0],$gimmick[$g][2]);
            $stmt5->execute();  
            $total_biaya += ($gimmick[$g][1]*$gimmick[$g][2]);
        }  
        for($t = 0; $t < (count($target)); $t++) {
            if($target[$t][0] == 1 || $target[$t][0] == 4) {
                $sql6="INSERT INTO target_event(id_event,id_target,target_proposal) VALUES(?,?,?)";
                $stmt6=$conn->prepare($sql6);
                $stmt6->bind_param("sii",$id,$target[$t][0],$target[$t][1]);
                if($target[$t][0] == 1) {
                    $target_person = $target[$t][1];                    
                } else {
                    $target_penjualan = $target[$t][1];
                }
            } elseif($target[$t][0] == 7) {
                $sql6="INSERT INTO target_event(id_event,id_target,target_proposal) VALUES(?,?,?)";
                $stmt6=$conn->prepare($sql6);
                $stmt6->bind_param("sii",$id,$target[$t][0],$total_biaya);
            } elseif($target[$t][0] == 8) {
                $cost_ratio = ($total_biaya/$target_penjualan)*100;
                $sql6="INSERT INTO target_event(id_event,id_target,target_proposal) VALUES(?,?,?)";
                $stmt6=$conn->prepare($sql6);
                $stmt6->bind_param("sii",$id,$target[$t][0],$cost_ratio);
            } else {
                $sql7="SELECT * FROM target WHERE target.id = ?";
                $stmt7=$conn->prepare($sql7);
                $stmt7->bind_param("i",$target[$t][0]);
                $stmt7->execute(); 
                $result = $stmt7->get_result();
                $r=mysqli_fetch_assoc($result); 
                if($target[$t][0] == 2 || $target[$t][0] == 3) {
                    $perhitungan_pengguna = $target_person*($r['perhitungan']/100);
                    $sql6="INSERT INTO target_event(id_event,id_target,target_proposal) VALUES(?,?,?)";
                    $stmt6=$conn->prepare($sql6);
                    $stmt6->bind_param("sii",$id,$target[$t][0],$perhitungan_pengguna);
                } else {
                    $sql6="INSERT INTO target_event(id_event,id_target,target_proposal,target_realisasi) VALUES(?,?,?,?)";
                    $stmt6=$conn->prepare($sql6);
                    $stmt6->bind_param("siii",$id,$target[$t][0],$r['bobot'],$r['bobot']);
                }                
            }     
            $stmt6->execute();  
        } 
        
        $sql8 = "SELECT account.username FROM account INNER JOIN grup ON account.id_grup = grup.id 
        INNER JOIN cabang ON grup.id_cabang = cabang.id INNER JOIN kota ON cabang.id_kota = kota.id 
        INNER JOIN provinsi ON kota.id_provinsi = provinsi.id WHERE ((account.id_jabatan = 4 AND account.id_grup = ?) OR 
        (account.id_jabatan = 5 AND account.id_grup = ?) OR (account.id_jabatan = 7 AND provinsi.id = ?) OR 
        (account.id_jabatan = 6 AND provinsi.id_area = ?) OR account.id_jabatan = 1) AND account.id_divisi = 1";
        $stmt8 = $conn->prepare($sql8);
        $stmt8->bind_param("ssss",$id_grup,$id_grup,$id_provinsi,$id_area);
        $stmt8->execute();
        $result8 = $stmt8->get_result();
        if($result8->num_rows > 0) {
            while($r8=mysqli_fetch_assoc($result8)) {
                $sql5="INSERT INTO persetujuan_event(username,id_event,status_proposal,keterangan_proposal,status_laporan,keterangan_laporan) 
                VALUES(?,?,1,'-',1,'-')";
                $stmt5=$conn->prepare($sql5);
                $stmt5->bind_param("ss",$r8['username'],$id);
                $stmt5->execute();  
                $total_biaya += ($gimmick[$g][1]*$gimmick[$g][2]);
            }
        }
                 
        $arr=["result"=>"success","id"=>$conn->insert_id];
    } else {
        $arr=["result"=>"fail","Error"=>$conn->error];
    }
    echo json_encode($arr);   

    // if($target[$t][0] == 1 || $target[$t][0] == 4) {
    //     $sql6="INSERT INTO target_event(id_event,id_target,target_proposal) VALUES(?,?,?)";
    //     $stmt6=$conn->prepare($sql6);
    //     $stmt6->bind_param("sii",$id,$target[$t][0],$target[$t][1]);
    //     if($target[$t][0] == 1) {
    //         $target_person = $target[$t][1];                    
    //     } else {
    //         $target_penjualan = $target[$t][1];
    //     }
    //     $stmt6->execute();  
    // } elseif($target[$t][0] == 7) {
    //     $sql7="INSERT INTO target_event(id_event,id_target,target_proposal) VALUES(?,?,?)";
    //     $stmt7=$conn->prepare($sql7);
    //     $stmt7->bind_param("sii",$id,$target[$t][0],$total_biaya);
    //     $stmt7->execute();  
    // } elseif($target[$t][0] == 8) {
    //     $cost_ratio = ($total_biaya/$target_penjualan)*100;
    //     $sql8="INSERT INTO target_event(id_event,id_target,target_proposal) VALUES(?,?,?)";
    //     $stmt8=$conn->prepare($sql8);
    //     $stmt8->bind_param("sii",$id,$target[$t][0],$cost_ratio);
    //     $stmt8->execute(); 
    // } else {
    //     $sql9="SELECT * FROM target WHERE target.id = ?";
    //     $stmt9=$conn->prepare($sql9);
    //     $stmt9->bind_param("i",$$target[$t][0]);
    //     $stmt9->execute(); 
    //     $result = $stmt9->get_result();
    //     $r=mysqli_fetch_assoc($result); 
    //     if($target[$t][0] == 2 || $target[$t][0] == 3) {
    //         $perhitungan_pengguna = $target_person*($r['perhitungan']/100);
    //         $sql10="INSERT INTO target_event(id_event,id_target,target_proposal) VALUES(?,?,?)";
    //         $stmt10=$conn->prepare($sql10);
    //         $stmt10->bind_param("sii",$id,$target[$t][0],$perhitungan_pengguna);
    //         $stmt10->execute(); 
    //     } else {
    //         $sql11="INSERT INTO target_event(id_event,id_target,target_proposal) VALUES(?,?,?)";
    //         $stmt11=$conn->prepare($sql11);
    //         $stmt11->bind_param("sii",$id,$target[$t][0],$r['bobot']);
    //         $stmt11->execute(); 
    //     }

    
?>