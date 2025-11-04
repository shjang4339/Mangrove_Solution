<?php
    include '../db/dbconnect.php';
    header("Content-Type: application/json");

    // POST 데이터 받기
    $id = $_POST['id'] ?? '';
    $password = $_POST['password'] ?? '';
    $name = $_POST['name'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $region = $_POST['region'] ?? '';
    $birth = $_POST['birth'] ?? '';
    $email = $_POST['email'] ?? '';
    $is_disease = $_POST['is_disease'] ?? 0;
    $disease_code = $_POST['disease_code'] ?? '';

    // 서버측 검증
    if (empty($id) || empty($password) || empty($name) || empty($phone) ||
        empty($region) || empty($birth) || empty($email)) {
        $response = array('succ' => false, 'message' => '필수 입력값이 누락되었습니다.');
        echo json_encode($response);
        exit;
    }

    try {
        $sql = "INSERT INTO member (id, password, name, phone, region, birth, email, is_disease, disease_code, date_account)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, now())";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array($id, $password, $name, $phone, $region, $birth, $email, $is_disease, $disease_code));

        if($stmt->rowCount() > 0) {
            $response = array('succ' => true, 'message' => '회원가입 성공');
        } else {
            $response = array('succ' => false, 'message' => '회원가입 실패');
        }
    } catch (PDOException $e) {
        $response = array('succ' => false, 'message' => '데이터베이스 오류: ' . $e->getMessage());
    }

    echo json_encode($response);
?>