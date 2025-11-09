<?php
session_start();
require "../db/dbconnect.php";

// 로그인 확인
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'member') {
    echo json_encode(['succ' => false, 'message' => '회원 로그인이 필요합니다.']);
    exit;
}

// 예약 번호 확인
if (!isset($_POST['book_no']) || empty($_POST['book_no'])) {
    echo json_encode(['succ' => false, 'message' => '예약 번호가 전달되지 않았습니다.']);
    exit;
}

$book_no = $_POST['book_no'];
$member_no = $_SESSION['user_no'];

try {
    // 해당 예약이 현재 로그인한 회원의 예약인지 확인
    $check_query = "SELECT no FROM book WHERE no = ? AND member_no = ? AND is_meet = 0";
    $check_stmt = $pdo->prepare($check_query);
    $check_stmt->execute(array($book_no, $member_no));
    $booking = $check_stmt->fetch(PDO::FETCH_ASSOC);

    if (!$booking) {
        echo json_encode(['succ' => false, 'message' => '해당 예약을 찾을 수 없거나 취소할 수 없습니다.']);
        exit;
    }

    // 예약 삭제
    $delete_query = "DELETE FROM book WHERE no = ?";
    $delete_stmt = $pdo->prepare($delete_query);
    $delete_stmt->execute(array($book_no));

    echo json_encode(['succ' => true, 'message' => '예약이 취소되었습니다.']);

} catch (PDOException $e) {
    echo json_encode(['succ' => false, 'message' => '데이터베이스 오류: ' . $e->getMessage()]);
}
?>
