<?php
    session_start();
    include '../db/dbconnect.php';
    header("Content-Type: application/json");

    // POST 데이터 받기
    $id = $_POST['id'] ?? '';
    $password = $_POST['password'] ?? '';
    $type = $_POST['type'] ?? ''; // 'member' 또는 'trainer'

    // 입력값 검증
    if (empty($id) || empty($password) || empty($type)) {
        echo json_encode(array('succ' => false, 'message' => '아이디와 비밀번호를 입력해주세요.'));
        exit;
    }

    // type 검증
    if (!in_array($type, ['member', 'trainer'])) {
        echo json_encode(array('succ' => false, 'message' => '잘못된 로그인 유형입니다.'));
        exit;
    }

    try {
        // 테이블 선택
        $table = ($type === 'member') ? 'member' : 'trainer';

        // 사용자 조회
        $query = "SELECT * FROM {$table} WHERE id = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute(array($id));
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // 비밀번호 확인
            if ($user['password'] === $password) {
                // 트레이너의 경우 승인 여부 확인
                if ($type === 'trainer' && isset($user['is_confirm']) && $user['is_confirm'] == 0) {
                    echo json_encode(array('succ' => false, 'message' => '관리자에게 승인 대기중인 계정입니다.'));
                    exit;
                }

                // 로그인 성공 - 세션에 사용자 정보 저장
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_no'] = $user['no'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_type'] = $type;
                $_SESSION['user_phone'] = $user['phone'];
                $_SESSION['user_email'] = $user['email'];

                // 트레이너의 경우 추가 정보 저장
                if ($type === 'trainer') {
                    $_SESSION['trainer_major'] = $user['major'] ?? '';
                    $_SESSION['trainer_image'] = $user['image'] ?? '';
                    $_SESSION['trainer_region'] = $user['region'] ?? '';
                }

                // 고객의 경우 추가 정보 저장
                if ($type === 'member') {
                    $_SESSION['member_region'] = $user['region'] ?? '';
                    $_SESSION['member_birth'] = $user['birth'] ?? '';
                }

                echo json_encode(array(
                    'succ' => true,
                    'message' => '로그인 성공',
                    'user_type' => $type,
                    'user_name' => $user['name']
                ));
            } else {
                // 비밀번호 불일치
                echo json_encode(array('succ' => false, 'message' => '아이디 또는 비밀번호가 일치하지 않습니다.'));
            }
        } else {
            // 사용자 없음
            echo json_encode(array('succ' => false, 'message' => '아이디 또는 비밀번호가 일치하지 않습니다.'));
        }
    } catch (PDOException $e) {
        echo json_encode(array('succ' => false, 'message' => '데이터베이스 오류가 발생했습니다.'));
    }
?>
