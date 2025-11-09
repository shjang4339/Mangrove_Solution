<?php
    session_start();
    require "../db/dbconnect.php";

    // 로그인 확인
    if (!isset($_SESSION['user_no']) || $_SESSION['user_type'] !== 'member') {
        echo json_encode(array('succ' => false, 'message' => '로그인이 필요합니다.'));
        exit;
    }

    $member_no = $_SESSION['user_no'];

    try {
        // 트랜잭션 시작
        $pdo->beginTransaction();

        // 1. 예약 내역 삭제 (book 테이블에서 member_no가 일치하는 레코드)
        $query = "DELETE FROM book WHERE member_no = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute(array($member_no));

        // 2. 회원 정보 삭제
        $query = "DELETE FROM member WHERE no = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute(array($member_no));

        // 트랜잭션 커밋
        $pdo->commit();

        // 세션 삭제
        session_destroy();

        echo json_encode(array('succ' => true, 'message' => '회원탈퇴가 완료되었습니다.'));
    } catch (Exception $e) {
        // 트랜잭션 롤백
        $pdo->rollBack();
        echo json_encode(array('succ' => false, 'message' => '회원탈퇴 중 오류가 발생했습니다: ' . $e->getMessage()));
    }
?>
