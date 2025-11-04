<?php 
    include '../db/dbconnect.php';
    header("Content-Type: application/json");

    $id = $_POST['id'];
    $password = $_POST['password'];
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $birth = $_POST['birth'];
    $email = $_POST['email'];
    $is_disease = $_POST['is_disease'];
    $disease_code = $_POST['disease_code'];

    $sql ="INSERT INTO member (id, password, name, phone, address, birth, email, is_disease, disease_code, date_account) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, now())";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array($id, $password, $name, $phone, $address, $birth, $email, $is_disease, $disease_code));

    if($stmt->rowCount() > 0) {
        $response = array( 'succ' => true);

        echo json_encode($response);
    }
    else {
        $response = array( 'succ' => false);

        echo json_encode($response);
    }
?>