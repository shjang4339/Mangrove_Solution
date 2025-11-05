<?php
    session_start();
    include '../db/dbconnect.php';
    header("Content-Type: application/json");

    // 로그인 확인
    if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'member') {
        echo json_encode(array('succ' => false, 'message' => '로그인이 필요합니다.'));
        exit;
    }

    // 회원의 거주지 정보 확인
    $member_region = $_SESSION['member_region'] ?? '';

    if (empty($member_region)) {
        echo json_encode(array('succ' => false, 'message' => '회원님의 거주지 정보가 없습니다.'));
        exit;
    }

    // 검색 성공 응답 (실제 검색은 search_trainer.php에서 수행)
    echo json_encode(array('succ' => true, 'message' => '근처 트레이너를 검색합니다.'));
?>
