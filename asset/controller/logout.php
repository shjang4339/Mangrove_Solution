<?php
    session_start();

    // 세션 변수 모두 제거
    $_SESSION = array();

    // 세션 쿠키 삭제
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time()-42000, '/');
    }

    // 세션 파괴
    session_destroy();

    // 메인 페이지로 리다이렉트
    header('Location: ../../index.php');
    exit;
?>
