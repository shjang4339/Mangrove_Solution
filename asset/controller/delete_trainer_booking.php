<?php
session_start();
require "../db/dbconnect.php";

// 로그인 확인
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'trainer') {
    echo json_encode(['succ' => false, 'message' => '트레이너 로그인이 필요합니다.']);
    exit;
}

// 파라미터 확인
if (!isset($_POST['type']) || !isset($_POST['booking_no'])) {
    echo json_encode(['succ' => false, 'message' => '필수 파라미터가 누락되었습니다.']);
    exit;
}

$type = $_POST['type']; // 'member' 또는 'unlogin'
$booking_no = $_POST['booking_no'];
$trainer_no = $_SESSION['user_no'];

try {
    if ($type === 'member') {
        // 회원 예약 삭제 - 해당 트레이너의 예약인지 확인 후 삭제
        $check_query = "SELECT no FROM book WHERE no = ? AND trainer_no = ?";
        $check_stmt = $pdo->prepare($check_query);
        $check_stmt->execute(array($booking_no, $trainer_no));
        $booking = $check_stmt->fetch(PDO::FETCH_ASSOC);

        if (!$booking) {
            echo json_encode(['succ' => false, 'message' => '해당 예약을 찾을 수 없거나 삭제 권한이 없습니다.']);
            exit;
        }

        // 예약 삭제
        $delete_query = "DELETE FROM book WHERE no = ?";
        $delete_stmt = $pdo->prepare($delete_query);
        $delete_stmt->execute(array($booking_no));

        echo json_encode(['succ' => true, 'message' => '예약이 삭제되었습니다.']);

    } elseif ($type === 'unlogin') {
        // 비회원 예약 삭제 - 해당 트레이너에게 할당된 예약인지 확인 후 삭제
        $check_query = "SELECT no FROM unlogin_book WHERE no = ? AND trainer_no = ?";
        $check_stmt = $pdo->prepare($check_query);
        $check_stmt->execute(array($booking_no, $trainer_no));
        $booking = $check_stmt->fetch(PDO::FETCH_ASSOC);

        if (!$booking) {
            echo json_encode(['succ' => false, 'message' => '해당 예약을 찾을 수 없거나 삭제 권한이 없습니다.']);
            exit;
        }

        // 예약 삭제
        $delete_query = "DELETE FROM unlogin_book WHERE no = ?";
        $delete_stmt = $pdo->prepare($delete_query);
        $delete_stmt->execute(array($booking_no));

        echo json_encode(['succ' => true, 'message' => '예약이 삭제되었습니다.']);

    } else {
        echo json_encode(['succ' => false, 'message' => '잘못된 타입입니다.']);
    }

} catch (PDOException $e) {
    echo json_encode(['succ' => false, 'message' => '데이터베이스 오류: ' . $e->getMessage()]);
}
?>
