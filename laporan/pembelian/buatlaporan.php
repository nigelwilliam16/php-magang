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

    $nota_beli_id = "$id-$id_cabang-$username-$date";

    $id_produk = json_decode($_POST['id_produk']);
    $quantity = json_decode($_POST['quantity']);
    $harga = json_decode($_POST['harga']);

    // $id_produk = [1,2];
    // $quantity = [5,5];
    // $harga = [5000,10000];

    $sql="INSERT INTO nota_beli(id,tanggal,waktu,foto,diskon,ppn,id_cabang,username,id_supplier) VALUES(?,?,?,?,?,?,?,?,?)";
    $stmt=$conn->prepare($sql);
    $stmt->bind_param("ssssiisss",$nota_beli_id,$date,$time,$foto,$diskon,$ppn,$id_cabang,$username,$id_supplier);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        for($i = 0; $i < (count($id_produk)); $i++) {
            $sql2="INSERT INTO nota_beli_product(id_nota_beli,id_product,quantity,harga) VALUES(?,?,?,?)";
            $stmt2=$conn->prepare($sql2);
            $stmt2->bind_param("ssii",$nota_beli_id,$id_produk[$i],$quantity[$i],$harga[$i]);
            $stmt2->execute();
            
            $sql3 = "SELECT * FROM cabang_product WHERE id_cabang = ? AND id_product = ?";
            $stmt3 = $conn->prepare($sql3);
            $stmt3->bind_param("si",$id_cabang, $id_produk[$i]);
            $stmt3->execute();
            $result3 = $stmt3->get_result();

            if($result3->num_rows > 0) {
                $r3=mysqli_fetch_assoc($result3); 
                $stok = $r3['stok'] + $quantity[$i]; 

                $sql5="UPDATE cabang_product SET stok=? WHERE id_cabang = ? AND id_product = ?";
                $stmt5=$conn->prepare($sql5);
                $stmt5->bind_param("isi",$stok,$id_cabang,$id_produk[$i]);
                $stmt5->execute();
            } else {
                $sql6="INSERT INTO cabang_product (id_cabang, id_product, stok) VALUES (?,?,?)";
                $stmt6=$conn->prepare($sql6);
                $stmt6->bind_param("ssi",$id_cabang,$id_produk[$i],$quantity[$i]);
                $stmt6->execute();
            }  
        }
        $arr=["result"=>"success","id"=>$conn->insert_id];
    } else {
        $arr=["result"=>"fail","Error"=>$conn->error];
    }
    echo json_encode($arr);

    

?>