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
$book_no = $_POST['book_no'] ?? '';

// 입력값 검증
if (empty($book_no)) {
    echo json_encode(array('succ' => false, 'message' => '예약 번호가 필요합니다.'));
    exit;
}

try {
    // 예약 삭제
    $query = "DELETE FROM book WHERE no = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute(array($book_no));

    if ($stmt->rowCount() > 0) {
        echo json_encode(array('succ' => true, 'message' => '예약이 삭제되었습니다.'));
    } else {
        echo json_encode(array('succ' => false, 'message' => '예약 정보를 찾을 수 없습니다.'));
    }
} catch (PDOException $e) {
    echo json_encode(array('succ' => false, 'message' => '데이터베이스 오류가 발생했습니다.'));
}
?>
