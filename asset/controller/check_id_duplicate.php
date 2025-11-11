<?php
require "../db/dbconnect.php";
header("Content-Type: application/json");

// POST 데이터 받기
$id = $_POST['id'] ?? '';

// 유효성 검사
if (empty($id)) {
    echo json_encode(array('succ' => false, 'message' => '아이디를 입력해주세요.'));
    exit;
}

// admin 문자열 포함 검사 (대소문자 구분 없이)
if (stripos($id, 'admin') !== false) {
    echo json_encode(array('succ' => false, 'message' => '사용할 수 없는 아이디입니다.(admin 포함 불가)'));
    exit;
}

try {
    // member 테이블에서 중복 확인
    $member_query = "SELECT COUNT(*) as count FROM member WHERE id = ?";
    $member_stmt = $pdo->prepare($member_query);
    $member_stmt->execute(array($id));
    $member_result = $member_stmt->fetch(PDO::FETCH_ASSOC);

    if ($member_result['count'] > 0) {
        echo json_encode(array('succ' => false, 'message' => '이미 사용 중인 아이디입니다.'));
        exit;
    }

    // trainer 테이블에서 중복 확인
    $trainer_query = "SELECT COUNT(*) as count FROM trainer WHERE id = ?";
    $trainer_stmt = $pdo->prepare($trainer_query);
    $trainer_stmt->execute(array($id));
    $trainer_result = $trainer_stmt->fetch(PDO::FETCH_ASSOC);

    if ($trainer_result['count'] > 0) {
        echo json_encode(array('succ' => false, 'message' => '이미 사용 중인 아이디입니다.'));
        exit;
    }

    // 중복이 없으면 사용 가능
    echo json_encode(array('succ' => true, 'message' => '사용 가능한 아이디입니다.'));

} catch (PDOException $e) {
    echo json_encode(array('succ' => false, 'message' => '데이터베이스 오류가 발생했습니다.', 'error' => $e->getMessage()));
}
?>
