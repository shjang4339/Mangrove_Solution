<?php
require "../db/dbconnect.php";

// POST 데이터 가져오기
$name = $_POST['name'] ?? '';
$phone = $_POST['phone'] ?? '';
$region = $_POST['region'] ?? '';
$disease_code = $_POST['disease_code'] ?? '';

// 유효성 검사
if (empty($name) || empty($phone) || empty($region) || empty($disease_code)) {
    echo json_encode(array('succ' => false, 'message' => '모든 필드를 입력해주세요.'));
    exit;
}

try {
    // 중복 전화번호 검증
    $check_query = "SELECT COUNT(*) as count FROM unlogin_book WHERE phone = ?";
    $check_stmt = $pdo->prepare($check_query);
    $check_stmt->execute(array($phone));
    $check_result = $check_stmt->fetch(PDO::FETCH_ASSOC);

    if ($check_result['count'] > 0) {
        echo json_encode(array('succ' => false, 'message' => '이미 등록된 전화번호입니다.'));
        exit;
    }

    // 데이터 삽입
    $query = "INSERT INTO unlogin_book (name, phone, region, disease_code, insert_date)
              VALUES (?, ?, ?, ?, NOW())";
    $stmt = $pdo->prepare($query);
    $stmt->execute(array($name, $phone, $region, $disease_code));

    echo json_encode(array('succ' => true));

} catch (PDOException $e) {
    echo json_encode(array('succ' => false, 'message' => '데이터베이스 오류가 발생했습니다.', 'error' => $e->getMessage()));
}
?>
