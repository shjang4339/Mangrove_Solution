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

// 필수 파라미터 확인
if (!isset($_POST['trainer_no']) || !isset($_POST['user_id']) || !isset($_POST['email']) ||
    !isset($_POST['major']) || !isset($_POST['teachday']) || !isset($_POST['license']) ||
    !isset($_POST['region']) || !isset($_POST['greet']) || !isset($_POST['disease_code'])) {
    echo json_encode(array('succ' => false, 'message' => '필수 항목이 누락되었습니다.'));
    exit;
}

// POST 데이터 받기
$trainer_no = $_POST['trainer_no'];
$user_id = $_POST['user_id'];
$email = $_POST['email'];
$major = $_POST['major'];
$teachday = $_POST['teachday'];
$license = $_POST['license'];
$sublicense_1 = $_POST['sublicense_1'] ?? '';
$sublicense_2 = $_POST['sublicense_2'] ?? '';
$sublicense_3 = $_POST['sublicense_3'] ?? '';
$region = $_POST['region'];
$greet = $_POST['greet'];
$disease_code = $_POST['disease_code'];

// 입력값 검증
if (empty($trainer_no) || empty($user_id) || empty($email) || empty($major) ||
    empty($teachday) || empty($license) || empty($region) || empty($greet)) {
    echo json_encode(array('succ' => false, 'message' => '모든 필수 항목을 입력해주세요.'));
    exit;
}

try {
    // 트레이너 정보 업데이트
    $query = "UPDATE trainer SET
              id = ?,
              email = ?,
              major = ?,
              teachday = ?,
              license = ?,
              sublicense_1 = ?,
              sublicense_2 = ?,
              sublicense_3 = ?,
              region = ?,
              greet = ?,
              disease_code = ?
              WHERE no = ?";

    $stmt = $pdo->prepare($query);
    $stmt->execute(array(
        $user_id,
        $email,
        $major,
        $teachday,
        $license,
        $sublicense_1,
        $sublicense_2,
        $sublicense_3,
        $region,
        $greet,
        $disease_code,
        $trainer_no
    ));

    if ($stmt->rowCount() > 0) {
        echo json_encode(array('succ' => true, 'message' => '트레이너 정보가 수정되었습니다.'));
    } else {
        echo json_encode(array('succ' => false, 'message' => '변경된 정보가 없거나 트레이너를 찾을 수 없습니다.'));
    }
} catch (PDOException $e) {
    echo json_encode(array('succ' => false, 'message' => '데이터베이스 오류가 발생했습니다.'));
}
?>
