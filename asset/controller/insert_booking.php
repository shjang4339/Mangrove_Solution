<?php
    session_start();
    include '../db/dbconnect.php';
    header("Content-Type: application/json");

    // 로그인 확인
    if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'member') {
        echo json_encode(array('succ' => false, 'message' => '로그인이 필요합니다.'));
        exit;
    }

    // POST 데이터 받기
    $trainer_no = $_POST['trainer_no'] ?? '';
    $member_no = $_SESSION['user_no'];

    // 입력값 검증
    if (empty($trainer_no)) {
        echo json_encode(array('succ' => false, 'message' => '트레이너 정보가 없습니다.'));
        exit;
    }

    try {
        // 중복 예약 확인 (대기중인 예약만 체크)
        $check_query = "SELECT * FROM book WHERE member_no = ? AND trainer_no = ? AND is_meet = 0";
        $check_stmt = $pdo->prepare($check_query);
        $check_stmt->execute(array($member_no, $trainer_no));
        $existing_booking = $check_stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing_booking) {
            echo json_encode(array('succ' => false, 'message' => '이미 예약된 트레이너입니다.'));
            exit;
        }

        // 예약 등록 (is_meet = 0: 대기중)
        $query = "INSERT INTO book (member_no, trainer_no, book_date, is_meet) VALUES (?, ?, NOW(), 0)";
        $stmt = $pdo->prepare($query);
        $result = $stmt->execute(array($member_no, $trainer_no));

        if ($result) {
            echo json_encode(array('succ' => true, 'message' => '예약이 완료되었습니다.'));
        } else {
            echo json_encode(array('succ' => false, 'message' => '예약 등록에 실패했습니다.'));
        }
    } catch (PDOException $e) {
        echo json_encode(array('succ' => false, 'message' => '데이터베이스 오류가 발생했습니다.', 'error' => $e->getMessage()));
    }
?>
