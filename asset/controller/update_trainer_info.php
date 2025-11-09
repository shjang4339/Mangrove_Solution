<?php
session_start();
require "../db/dbconnect.php";

// 로그인 확인
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'trainer') {
    echo json_encode(['succ' => false, 'message' => '트레이너 로그인이 필요합니다.']);
    exit;
}

// 필수 파라미터 확인
if (!isset($_POST['password']) || !isset($_POST['email']) || !isset($_POST['major']) ||
    !isset($_POST['license']) || !isset($_POST['region']) || !isset($_POST['disease_code']) ||
    !isset($_POST['greet'])) {
    echo json_encode(['succ' => false, 'message' => '필수 파라미터가 누락되었습니다.']);
    exit;
}

$trainer_no = $_SESSION['user_no'];
$password = $_POST['password'];
$email = $_POST['email'];
$major = $_POST['major'];
$license = $_POST['license'];
$sublicense_1 = $_POST['sublicense_1'] ?? '';
$sublicense_2 = $_POST['sublicense_2'] ?? '';
$sublicense_3 = $_POST['sublicense_3'] ?? '';
$image = $_POST['image'] ?? '';
$region = $_POST['region'];
$disease_code = $_POST['disease_code'];
$greet = $_POST['greet'];
$image_changed = $_POST['image_changed'] ?? '0';

try {
    // 비밀번호 해싱 (현재는 평문 저장, 추후 password_hash 사용 권장)
    // $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // 이미지가 변경되지 않은 경우 기존 이미지 유지
    if ($image_changed === '1' && !empty($image)) {
        // 이미지 포함하여 업데이트
        $query = "UPDATE trainer SET password = ?, email = ?, major = ?, license = ?,
                  sublicense_1 = ?, sublicense_2 = ?, sublicense_3 = ?, image = ?,
                  region = ?, disease_code = ?, greet = ? WHERE no = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute(array($password, $email, $major, $license, $sublicense_1, $sublicense_2,
                             $sublicense_3, $image, $region, $disease_code, $greet, $trainer_no));
    } else {
        // 이미지 제외하고 업데이트
        $query = "UPDATE trainer SET password = ?, email = ?, major = ?, license = ?,
                  sublicense_1 = ?, sublicense_2 = ?, sublicense_3 = ?,
                  region = ?, disease_code = ?, greet = ? WHERE no = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute(array($password, $email, $major, $license, $sublicense_1, $sublicense_2,
                             $sublicense_3, $region, $disease_code, $greet, $trainer_no));
    }

    echo json_encode(['succ' => true, 'message' => '트레이너 정보가 수정되었습니다.']);

} catch (PDOException $e) {
    echo json_encode(['succ' => false, 'message' => '데이터베이스 오류: ' . $e->getMessage()]);
}
?>
