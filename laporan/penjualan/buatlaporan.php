<?php
header("Access-Control-Allow-Origin: *"); 
$arr=null;

$conn = new mysqli("localhost", "n1561248_staff_coronet","sU[=]bRd;jm$","n1561248_pt_coronet_crown");
if($conn->connect_error) {
  $arr= ["result"=>"error","message"=>"unable to connect"];
}
    ini_set('date.timezone', 'Asia/Jakarta');   
    $date = date("Y-m-d");
    $time = date("H:i:s");
    
    extract($_POST);   

    $nota_jual_id = "$id-$id_outlet-$username-$date";

    $id_produk = json_decode($_POST['id_produk']);
    $quantity = json_decode($_POST['quantity']);
    $harga = json_decode($_POST['harga']);

    for($i = 0; $i < (count($id_produk)); $i++) {
        $sql3 = "SELECT cabang_product.id_product, cabang_product.stok, product.jenis 
        FROM cabang_product INNER JOIN product ON product.id = cabang_product.id_product 
        WHERE id_cabang = ? AND id_product = ?";
        $stmt3 = $conn->prepare($sql3);
        $stmt3->bind_param("si",$id_cabang, $id_produk[$i]);
        $stmt3->execute();
        $result3 = $stmt3->get_result();

        $r3=mysqli_fetch_assoc($result3); 
        $stok = $r3['stok'] - $quantity[$i]; 
        
        if($stok < 0) {
            $arr=["result"=>"fail","Error"=>"Jumlah stok ".$r3['jenis']." kurang"];
            echo json_encode($arr);
            $conn->close();
            exit();
            break;
        } else {
            $sql4="UPDATE cabang_product SET stok=? WHERE id_cabang = ? AND id_product = ?";
                $stmt4=$conn->prepare($sql4);
                $stmt4->bind_param("isi",$stok,$id_cabang,$id_produk[$i]);
                $stmt4->execute();
        }
    }

    if($_POST["foto"] == "") {
        $sql="INSERT INTO notal_jual(id,tanggal,waktu,diskon,ppn,id_outlet,username) VALUES(?,?,?,?,?,?,?)";
        $stmt=$conn->prepare($sql);
        $stmt->bind_param("sssiiss",$nota_jual_id,$date,$time,$diskon,$ppn,$id_outlet,$username);
        $stmt->execute();
    }
    else{
        $sql="INSERT INTO notal_jual(id,tanggal,waktu,foto,diskon,ppn,id_outlet,username) VALUES(?,?,?,?,?,?,?,?)";
        $stmt=$conn->prepare($sql);
        $stmt->bind_param("ssssiiss",$nota_jual_id,$date,$time,$foto,$diskon,$ppn,$id_outlet,$username);
        $stmt->execute();
    }
    if ($stmt->affected_rows > 0) {
        for($i = 0; $i < (count($id_produk)); $i++) {

            $sql2="INSERT INTO notal_jual_product(id_nota_jual,id_product,quantity,harga) VALUES(?,?,?,?)";
            $stmt2=$conn->prepare($sql2);
            $stmt2->bind_param("ssii",$nota_jual_id,$id_produk[$i],$quantity[$i],$harga[$i]);
            $stmt2->execute();        
            
            $sql5 = "SELECT * FROM outlet_product WHERE id_outlet = ? AND id_product = ?";
            $stmt5 = $conn->prepare($sql5);
            $stmt5->bind_param("si",$id_outlet, $id_produk[$i]);
            $stmt5->execute();
            $result5 = $stmt5->get_result();

            if($result5->num_rows > 0) {
                $r5=mysqli_fetch_assoc($result5); 
                $stok = $r5['stok'] + $quantity[$i]; 

                $sql5="UPDATE outlet_product SET stok=? WHERE id_outlet = ? AND id_product = ?";
                $stmt5=$conn->prepare($sql5);
                $stmt5->bind_param("isi",$stok,$id_outlet,$id_produk[$i]);
                $stmt5->execute();
            } else {
                $sql6="INSERT INTO outlet_product (id_outlet, id_product, stok) VALUES (?,?,?)";
                $stmt6=$conn->prepare($sql6);
                $stmt6->bind_param("ssi",$id_outlet,$id_produk[$i],$quantity[$i]);
                $stmt6->execute();
            }  
        }
        $arr=["result"=>"success","id"=>$conn->insert_id];
    } else {
        $arr=["result"=>"fail","Error"=>$conn->error];
    }
    echo json_encode($arr);   
?>