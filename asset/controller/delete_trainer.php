<?php
    session_start();
    require "../db/dbconnect.php";

    // 로그인 확인
    if (!isset($_SESSION['user_no']) || $_SESSION['user_type'] !== 'trainer') {
        echo json_encode(array('succ' => false, 'message' => '로그인이 필요합니다.'));
        exit;
    }

    $trainer_no = $_SESSION['user_no'];

    try {
        // 트랜잭션 시작
        $pdo->beginTransaction();

        // 1. 트레이너 정보 조회 (이미지 파일명 가져오기)
        $query = "SELECT image FROM trainer WHERE no = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute(array($trainer_no));
        $trainer = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$trainer) {
            throw new Exception('트레이너 정보를 찾을 수 없습니다.');
        }

        $image_filename = $trainer['image'];

        // 2. 예약 내역 삭제 (book 테이블에서 trainer_no가 일치하는 레코드)
        $query = "DELETE FROM book WHERE trainer_no = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute(array($trainer_no));

        // 3. 비회원 예약 내역 삭제 (unlogin_book 테이블에서 trainer_no가 일치하는 레코드)
        $query = "DELETE FROM unlogin_book WHERE trainer_no = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute(array($trainer_no));

        // 4. 트레이너 정보 삭제
        $query = "DELETE FROM trainer WHERE no = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute(array($trainer_no));

        // 트랜잭션 커밋
        $pdo->commit();

        // 5. 프로필 이미지 파일 삭제
        if (!empty($image_filename)) {
            $image_path = __DIR__ . '/../../image/' . $image_filename;
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }

        // 세션 삭제
        session_destroy();

        echo json_encode(array('succ' => true, 'message' => '회원탈퇴가 완료되었습니다.'));
    } catch (Exception $e) {
        // 트랜잭션 롤백
        $pdo->rollBack();
        echo json_encode(array('succ' => false, 'message' => '회원탈퇴 중 오류가 발생했습니다: ' . $e->getMessage()));
    }
?>
