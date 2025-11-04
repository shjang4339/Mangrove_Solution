<?php
    include '../db/dbconnect.php';
    header("Content-Type: application/json");

    // POST 데이터 받기
    $id = $_POST['id'] ?? '';
    $password = $_POST['password'] ?? '';
    $name = $_POST['name'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $email = $_POST['email'] ?? '';
    $major = $_POST['major'] ?? '';
    $image = $_POST['image'] ?? '';
    $license = $_POST['license'] ?? '';
    $sublicense_1 = $_POST['sublicense_1'] ?? '';
    $sublicense_2 = $_POST['sublicense_2'] ?? '';
    $sublicense_3 = $_POST['sublicense_3'] ?? '';
    $region = $_POST['region'] ?? '';
    $greet = $_POST['greet'] ?? '';
    $disease_code = $_POST['disease_code'] ?? '';

    // 서버측 검증
    if (empty($id) || empty($password) || empty($name) || empty($phone) ||
        empty($email) || empty($major) || empty($license) || empty($region) || empty($greet)) {
        $response = array('succ' => false, 'message' => '필수 입력값이 누락되었습니다.');
        echo json_encode($response);
        exit;
    }

    try {
        $sql = "INSERT INTO trainer (id, password, name, phone, email, major, image, license, sublicense_1, sublicense_2, sublicense_3, region, greet, disease_code)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array($id, $password, $name, $phone, $email, $major, $image, $license, $sublicense_1, $sublicense_2, $sublicense_3, $region, $greet, $disease_code));

        if($stmt->rowCount() > 0) {
            $response = array('succ' => true, 'message' => '트레이너 회원가입 성공');
        } else {
            $response = array('succ' => false, 'message' => '트레이너 회원가입 실패');
        }
    } catch (PDOException $e) {
        $response = array('succ' => false, 'message' => '데이터베이스 오류: ' . $e->getMessage());
    }

    echo json_encode($response);
?>