<?php
session_start();
require "../db/dbconnect.php";
header("Content-Type: application/json");

// 관리자 권한 확인
$user_id = $_SESSION['user_id'] ?? '';
if ($user_id !== 'admin') {
    echo json_encode(array('succ' => false, 'message' => '관리자만 접근 가능합니다.'));
    exit;
}

// POST 데이터 받기
$trainer_no = $_POST['trainer_no'] ?? '';

// 입력값 검증
if (empty($trainer_no)) {
    echo json_encode(array('succ' => false, 'message' => '트레이너 번호가 필요합니다.'));
    exit;
}

try {
    // is_confirm 값을 1로 업데이트
    $query = "UPDATE trainer SET is_confirm = 1 WHERE no = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute(array($trainer_no));

    if ($stmt->rowCount() > 0) {
        echo json_encode(array('succ' => true, 'message' => '트레이너 승인이 완료되었습니다.'));
    } else {
        echo json_encode(array('succ' => false, 'message' => '트레이너 정보를 찾을 수 없습니다.'));
    }
} catch (PDOException $e) {
    echo json_encode(array('succ' => false, 'message' => '데이터베이스 오류가 발생했습니다.'));
}
?>
