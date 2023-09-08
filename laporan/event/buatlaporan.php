<?php
header("Access-Control-Allow-Origin: *"); 
$arr=null;

$conn = new mysqli("localhost", "n1561248_staff_coronet","sU[=]bRd;jm$","n1561248_pt_coronet_crown");
if($conn->connect_error) {
  $arr= ["result"=>"error","message"=>"unable to connect"];
}

    function rollbackServer($pesan,$connection) {
        $connection -> rollback();
        $arr=["result"=>"Error","message"=>$pesan, "SQL Error:"=>$connection->error];
        $connection -> autocommit(TRUE);
        echo json_encode($arr);  
        $connection->close();   
        exit();
    }

    ini_set('date.timezone', 'Asia/Jakarta');   
    $tanggal_pengajuan = date("Y-m-d");
    
    extract($_POST);   

    $total_biaya = 0;
    $target_penjualan = 0;
    $target_person = 0;

    $kebutuhan_tambahan = json_decode($_POST['kebutuhan_tambahan']);
    $gimmick = json_decode($_POST['gimmick']);
    $target = json_decode($_POST['target']);
    // $pengunjungBaru = json_decode($_POST['pengunjung_baru']);
    // $pengunjungLama = json_decode($_POST['pengunjung_lama']);
    // $stok = json_decode($_POST['stok']);
    // $dokumentasi = json_decode($_POST['dokumentasi']);
    // $pengunjungBaru = array(array("tim","Timothy","08226138432","22","Kapasari","0",array(array(1,10,8000),array(2,5,12000))));
    // //$pengunjungBaru =[];
    // $stok = array(array(1,10),array(2,5));
    // $pengunjungLama = array(array("robotkambing",array(array(1,10,8000),array(2,5,12000))),array("tim_othy",array(array(2,2,12000))));
    // //$kebutuhan_tambahan = array(array(13, 150000),array(14, 100000));
    // $kebutuhan_tambahan = [];
    // $gimmick = array(array(1,8,1250),array(2,11,210));
    // $target = array(array(1,35),array(2,""),array(3,""),array(4,""),array(7,""),array(8,""));
    // $dokumentasi = array(array("sadwaxasdasxwadxasx","11:00:00","afafas"));

    $isAkunNotExist = false;
    $isOnStokProduct = false;
    $isOnStokGimmick = false;
    $message = "";
    

    $conn -> autocommit(FALSE);

    //cek stok produk (aman)
    // for($s = 0; $s < (count($stok)); $s++) {
    //     $sql0 = "SELECT cabang_product.id_product, cabang_product.stok, product.jenis 
    //     FROM cabang_product INNER JOIN product ON product.id = cabang_product.id_product 
    //     WHERE id_cabang = ? AND id_product = ?";
    //     $stmt0 = $conn->prepare($sql0);
    //     $stmt0->bind_param("si",$id_cabang, $stok[$s][0]);
    //     $stmt0->execute();
    //     $result0 = $stmt0->get_result();

    //     $r0=mysqli_fetch_assoc($result0); 
    //     $stok2 = $r0['stok'] - $stok[$s][1]; 
    //     if($stok2 < 0) {
    //         $isOnStokProduct = false;
    //         $message = "Jumlah stok produk ".$r0['jenis']." kurang.\nSilahkan hubungi penanggung jawab cabang anda.";            
    //         break;
    //     } else {
    //         $isOnStokProduct = true;  
    //         // $sql01="UPDATE cabang_product SET stok=? WHERE id_cabang = ? AND id_product = ?";
    //         // $stmt01=$conn->prepare($sql01);
    //         // $stmt01->bind_param("isi",$stok2,$id_cabang,$stok[$s][0]);
    //         // $stmt01->execute();   
    //         // if ($stmt01->affected_rows > 0) { 
    //         //     $isOnStokProduct = true;  
    //         // } else {
    //         //     $isOnStokProduct = false;  
    //         //     $message = "Gagal memperbarui data stok produk, silahkan coba lagi";            
    //         //     break;
    //         // }
    //     }
    // } 
    
    

    // //Count stock gimmick (aman)
    // for($g = 0; $g < (count($gimmick)); $g++) {
    //     $sql10 = "SELECT gimmick_cabang.id_gimmick, gimmick_cabang.stok, gimmick.barang 
    //     FROM gimmick_cabang INNER JOIN gimmick ON gimmick.id = gimmick_cabang.id_gimmick 
    //     WHERE gimmick_cabang.id_cabang = ? AND gimmick_cabang.id_gimmick = ?";
    //     $stmt10 = $conn->prepare($sql10);
    //     $stmt10->bind_param("si",$id_cabang,$gimmick[$g][0]);
    //     $stmt10->execute();
    //     $result10 = $stmt10->get_result();

    //     $r10=mysqli_fetch_assoc($result10); 
    //     $stok10 = $r10['stok'] - $gimmick[$g][1]; 
        
    //     if($stok10 < 0) {
    //         $isOnStokGimmick = false;
    //         $message = "Jumlah stok gimmick ".$r10['barang']." kurang.\nSilahkan hubungi penanggung jawab cabang anda.";
    //         break;
    //     } else {
    //         $isOnStokGimmick = true;
    //         $sql11="UPDATE gimmick_cabang SET stok=? WHERE id_cabang = ? AND id_gimmick = ?";
    //         $stmt11=$conn->prepare($sql11);
    //         $stmt11->bind_param("isi",$stok10,$id_cabang,$gimmick[$g][0]);
    //         $stmt11->execute();
    //         $isOnStokGimmick = true;  
    //         // if ($stmt11->affected_rows > 0) {     
    //         //     $isOnStokGimmick = true;  
    //         // } else {
    //         //     $isOnStokGimmick = false;
    //         //     $message = "Gagal memperbarui data stok gimmick, silahkan coba lagi";            
    //         //     break;
    //         // }   
    //     }
    // }

    // //Cek User Baru (Aman)
    // if($pengunjungBaru != null) {
    //     for($pb = 0; $pb < (count($pengunjungBaru)); $pb++) {
    //         $sql20 = "SELECT username FROM akun_pengunjung WHERE username = ?";
    //         $stmt20 = $conn->prepare($sql20);
    //         $stmt20->bind_param("s",$pengunjungBaru[$pb][0]);
    //         $stmt20->execute();
    //         $result20 = $stmt20->get_result();
    //         if($result20->num_rows > 0) {
    //             $isAkunNotExist = false;
    //             $message = "Pengunjung dengan username '".$pengunjungBaru[$pb][0]."' sudah pernah berkunjung.\nHarap ganti username pengunjung baru tersebut terlebih dahulu";
    //             break;
    //         } else {
    //             $isAkunNotExist = true;
    //         }
    //     }
    // } else {
    //     $isAkunNotExist = true;
    // }

    // if($isOnStokProduct == true && $isOnStokGimmick == true && $isAkunNotExist == true) {
        $sql="UPDATE event SET status = 3, evaluasi = ? WHERE id = ?";
        $stmt=$conn->prepare($sql);
        $stmt->bind_param("ss",$evaluasi,$id);
        $stmt->execute();       
         

        if ($stmt->affected_rows > 0) {
            // Buat laporan pengunjung baru (aman)
            // if($pengunjungBaru != null) {
            //     for($pb = 0; $pb < (count($pengunjungBaru)); $pb++) {
            //         $sql2="INSERT INTO akun_pengunjung(username,nama,nomor_telepon,usia,gender,alamat,status) VALUES(?,?,?,?,?,?,0)";
            //         $stmt2=$conn->prepare($sql2);
            //         $stmt2->bind_param("sssiis",$pengunjungBaru[$pb][0],$pengunjungBaru[$pb][1],$pengunjungBaru[$pb][2],$pengunjungBaru[$pb][3],$pengunjungBaru[$pb][5],$pengunjungBaru[$pb][4]);
            //         $stmt2->execute();                  
            //         if ($stmt2->affected_rows > 0) {   
                        
            //             for($p = 0; $p <(count($pengunjungBaru[$pb][6])); $p++) {       
            //                 $sql21="INSERT INTO event_jual_product(id_product,id_event,username,quantity) VALUES(?,?,?,?)";
            //                 $stmt21=$conn->prepare($sql21);
            //                 $stmt21->bind_param("issi",$pengunjungBaru[$pb][6][$p][0],$id,$pengunjungBaru[$pb][0],$pengunjungBaru[$pb][6][$p][1]);
            //                 $stmt21->execute();  
            //                 $total_penjualan += ($pengunjungBaru[$pb][6][$p][1]*$pengunjungBaru[$pb][6][$p][2]);

            //                 if ($stmt21->affected_rows <= 0) { 
                                
            //                     rollbackServer("Gagal menambahkan data penjualan, silahkan coba lagi",$conn);   
            //                     break;
            //                 } 
            //             }
            //         } else {
            //             rollbackServer("Gagal menambahkan data pengunjung baru, silahkan coba lagi",$conn);   
            //             break;                          
            //         }     
            //     }
            // }
            // //Buat laporan pengunjung lama (aman)
            // if($pengunjungLama != null) {
            //     for($pl = 0; $pl < (count($pengunjungLama)); $pl++) {
            //         $sql3="UPDATE akun_pengunjung SET status = 1 WHERE username = ?";
            //         $stmt3=$conn->prepare($sql3);
            //         $stmt3->bind_param("s",$pengunjungLama[$pl][0]);
            //         $stmt3->execute();  
                                    
            //         for($p = 0; $p <(count($pengunjungLama[$pl][1])); $p++) {
            //             $sql31="INSERT INTO event_jual_product(id_product,id_event,username,quantity) VALUES(?,?,?,?)";
            //             $stmt31=$conn->prepare($sql31);
            //             $stmt31->bind_param("issi",$pengunjungLama[$pl][1][$p][0],$id,$pengunjungLama[$pl][0],$pengunjungLama[$pl][1][$p][1]);
            //             $stmt31->execute();  
            //             $total_penjualan += ($pengunjungLama[$pl][1][$p][1]*$pengunjungLama[$pl][1][$p][2]);

            //             if ($stmt31->affected_rows <= 0) { 
            //                 rollbackServer("Gagal menambahkan data penjualan, silahkan coba lagi",$conn);   
            //                 break;
            //             } 
            //         }
                    
            //     }
            // }
            

            //Buat laporan kebutuhan tambahan
            if($kebutuhan_tambahan != null) {
                for($k = 0; $k < (count($kebutuhan_tambahan)); $k++) {
                    $sql4="UPDATE kebutuhan_event SET realisasi = ? WHERE id_event = ? AND id = ?";
                    $stmt4=$conn->prepare($sql4);
                    $stmt4->bind_param("sis",$kebutuhan_tambahan[$k][1],$id,$kebutuhan_tambahan[$k][0]);
                    $stmt4->execute();  
                    $total_biaya += $kebutuhan_tambahan[$k][1];

                    if ($stmt4->affected_rows <= 0) { 
                        rollbackServer("Gagal memperbarui data realisasi kebutuhan tambahan, silahkan coba lagi",$conn);   
                        break; 
                    } 
                }
            }
            
            // Buat laporan gimmick (aman)
            if($gimmick != null) {
                for($g = 0; $g < (count($gimmick)); $g++) {
                    $sql5="UPDATE gimmick_event SET quantity_realisasi = ? WHERE id_event = ? AND id_gimmick = ?";
                    $stmt5=$conn->prepare($sql5);
                    $stmt5->bind_param("isi",$gimmick[$g][1],$id,$gimmick[$g][0]);
                    $stmt5->execute();  
                    $total_biaya += ($gimmick[$g][1]*$gimmick[$g][2]);

                    if ($stmt5->affected_rows <= 0) { 
                        rollbackServer("Gagal memperbarui data realisasi gimmick, silahkan coba lagi",$conn);   
                        break; 
                    } 
                }
            }
            
            
            // // Buat laporan target (aman)
            // for($t = 0; $t < (count($target)); $t++) {                
            //     if($target[$t][0] == 1) {
            //         $sql6="UPDATE target_event SET target_realisasi = ? WHERE id_event = ? AND id_target = ?";
            //         $stmt6=$conn->prepare($sql6);
            //         $stmt6->bind_param("isi",$target[$t][1],$id,$target[$t][0]);
            //     } 
            //     elseif($target[$t][0] == 2) {
            //         $value = count($pengunjungBaru);
            //         $sql6="UPDATE target_event SET target_realisasi = ? WHERE id_event = ? AND id_target = ?";
            //         $stmt6=$conn->prepare($sql6);
            //         $stmt6->bind_param("isi",$value,$id,$target[$t][0]);        
            //     } 
            //     elseif($target[$t][0] == 3) {
            //         $value = count($pengunjungLama);
            //         $sql6="UPDATE target_event SET target_realisasi = ? WHERE id_event = ? AND id_target = ?";
            //         $stmt6=$conn->prepare($sql6);
            //         $stmt6->bind_param("isi", $value,$id,$target[$t][0]);
            //     } elseif($target[$t][0] == 4) {
            //         $sql6="UPDATE target_event SET target_realisasi = ? WHERE id_event = ? AND id_target = ?";
            //         $stmt6=$conn->prepare($sql6);
            //         $stmt6->bind_param("isi",$total_penjualan,$id,$target[$t][0]);
                
            //     } elseif($target[$t][0] == 7) {
            //         $sql6="UPDATE target_event SET target_realisasi = ? WHERE id_event = ? AND id_target = ?";
            //         $stmt6=$conn->prepare($sql6);
            //         $stmt6->bind_param("isi",$total_biaya,$id,$target[$t][0]);
            //     } elseif($target[$t][0] == 8) {
            //         $cost_ratio = ($total_biaya/$total_penjualan)*100;
            //         $sql6="UPDATE target_event SET target_realisasi = ? WHERE id_event = ? AND id_target = ?";
            //         $stmt6=$conn->prepare($sql6);
            //         $stmt6->bind_param("isi",$cost_ratio,$id,$target[$t][0]);
            //     }
            //     $stmt6->execute();  
            //     if ($stmt6->affected_rows <= 0) { 
            //         rollbackServer("Gagal memperbarui data target, silahkan coba lagi",$conn);   
            //         break; 
            //     } 
            // }

            // //Buat laporan dokumentasi (aman)
            // for($d = 0; $d < (count($dokumentasi)); $d++) {
            //     $sql7="INSERT INTO dokumentasi_event(gambar,waktu,alamat,id_event) VALUES (?,?,?,?)";
            //     $stmt7=$conn->prepare($sql7);
            //     $stmt7->bind_param("ssss",$dokumentasi[$d][0],$dokumentasi[$d][1],$dokumentasi[$d][2],$id);
            //     $stmt7->execute();  
            //     if ($stmt7->affected_rows <= 0) { 
            //         rollbackServer("Gagal menambahkan gambar dokumentasi, silahkan coba lagi",$conn);   
            //         break; 
            //     }
            // } 
            $conn -> rollback();                    
            $arr=["result"=>"success","id"=>$conn->insert_id, $target];
        } else {
            rollbackServer("Terjadi masalah pada server, silahkan coba lagi",$conn);  
        }
                 
    //  else {
    //     $conn -> rollback();
    //     $arr=["result"=>"Error","message"=>$message];
    //}
    echo json_encode($arr);   
    $conn -> autocommit(TRUE);
    $conn->close();   
?>