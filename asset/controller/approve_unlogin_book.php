<?php
session_start();
require "../db/dbconnect.php";

// 로그인 확인
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'trainer') {
    echo json_encode(array('succ' => false, 'message' => '트레이너 로그인이 필요합니다.'));
    exit;
}

// POST 데이터 가져오기
$book_no = $_POST['book_no'] ?? '';
$trainer_no = $_SESSION['user_no'];

// 유효성 검사
if (empty($book_no)) {
    echo json_encode(array('succ' => false, 'message' => '예약 번호가 필요합니다.'));
    exit;
}

try {
    // trainer_no 업데이트
    $query = "UPDATE unlogin_book SET trainer_no = ? WHERE no = ? AND trainer_no IS NULL";
    $stmt = $pdo->prepare($query);
    $stmt->execute(array($trainer_no, $book_no));

    // 업데이트된 행 수 확인
    if ($stmt->rowCount() > 0) {
        echo json_encode(array('succ' => true));
    } else {
        echo json_encode(array('succ' => false, 'message' => '이미 승인된 예약이거나 존재하지 않는 예약입니다.'));
    }

} catch (PDOException $e) {
    echo json_encode(array('succ' => false, 'message' => '데이터베이스 오류가 발생했습니다.', 'error' => $e->getMessage()));
}
?>
