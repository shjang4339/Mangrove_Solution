<?php
session_start();
require "../db/dbconnect.php";

// 로그인 확인
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'member') {
    echo json_encode(['succ' => false, 'message' => '고객 로그인이 필요합니다.']);
    exit;
}

// 필수 파라미터 확인
if (!isset($_POST['password']) || !isset($_POST['birth']) || !isset($_POST['email']) ||
    !isset($_POST['region']) || !isset($_POST['disease_code'])) {
    echo json_encode(['succ' => false, 'message' => '필수 파라미터가 누락되었습니다.']);
    exit;
}

$member_no = $_SESSION['user_no'];
$password = $_POST['password'];
$birth = $_POST['birth'];
$email = $_POST['email'];
$region = $_POST['region'];
$disease_code = $_POST['disease_code'];

try {
    // 비밀번호 해싱 (현재는 평문 저장, 추후 password_hash 사용 권장)
    // $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // 회원 정보 업데이트
    $query = "UPDATE member SET password = ?, birth = ?, email = ?, region = ?, disease_code = ? WHERE no = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute(array($password, $birth, $email, $region, $disease_code, $member_no));

    echo json_encode(['succ' => true, 'message' => '회원 정보가 수정되었습니다.']);

} catch (PDOException $e) {
    echo json_encode(['succ' => false, 'message' => '데이터베이스 오류: ' . $e->getMessage()]);
}
?>
