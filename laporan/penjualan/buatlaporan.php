<?php
header("Access-Control-Allow-Origin: *"); 
$arr=null;

$conn = new mysqli("localhost", "root","","pt_coronet_crown");
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

    $sql="INSERT INTO notal_jual(id,tanggal,waktu,foto,diskon,ppn,id_outlet,username) VALUES(?,?,?,?,?,?,?,?)";
    $stmt=$conn->prepare($sql);
    $stmt->bind_param("ssssiiss",$nota_jual_id,$date,$time,$foto,$diskon,$ppn,$id_outlet,$username);
    $stmt->execute();
    if ($stmt->affected_rows > 0) {
        for($i = 0; $i < (count($id_produk)); $i++) {
            $sql2="INSERT INTO notal_jual_product(id_nota_jual,id_product,quantity,harga) VALUES(?,?,?,?)";
            $stmt2=$conn->prepare($sql2);
            $stmt2->bind_param("ssii",$nota_jual_id,$id_produk[$i],$quantity[$i],$harga[$i]);
            $stmt2->execute();
        }
        $arr=["result"=>"success","id"=>$conn->insert_id];
    } else {
        $arr=["result"=>"fail","Error"=>$conn->error];
    }
    echo json_encode($arr);

    

?>