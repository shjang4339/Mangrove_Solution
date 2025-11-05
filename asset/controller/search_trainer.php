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
    $regions = $_POST['regions'] ?? '';
    $diseases = $_POST['diseases'] ?? '';

    // 입력값 검증
    if (empty($regions) || empty($diseases)) {
        echo json_encode(array('succ' => false, 'message' => '거주지와 질환을 선택해주세요.'));
        exit;
    }

    // 검색 성공 응답 (실제 검색은 search_trainer.php에서 수행)
    echo json_encode(array('succ' => true, 'message' => '검색을 시작합니다.'));
?>
