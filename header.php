<?php 
require "./asset/db/dbconnect.php";
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Mangrove Solution</title>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <meta property="og:locale" content="ko_KR" />
        <meta property="og:site_name" content="Mangrove Solution" />
        <meta property="og:type" content="website" />
        <meta property="og:image" content"http://mangrovesolution.co.kr/asset/image/logo.svg" />
        <meta property="og:title" content="Mangrove Solution" />
        <meta property="og:description" content="We are Mangrove" />
        <script src="https://code.jquery.com/jquery-latest.min.js"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css" />
        <script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>
        <link rel="stylesheet" type="text/css" href="asset/css/main.css" />
    </head>
    <body>
<?php
session_start();

// 로그인 상태 확인
$is_logged_in = isset($_SESSION['user_id']);
$user_name = $_SESSION['user_name'] ?? '';
$user_type = $_SESSION['user_type'] ?? '';
?>

<!-- 헤더 네비게이션 (로그인 상태 표시) -->
<header class="site-header">
    <div class="header-content">
        <button class="btn-home" onclick="location.href='index.php'">
            <img src="asset/image/home.svg" alt="홈">
        </button>
        <?php if ($is_logged_in): ?>
            <div class="user-info">
                <span class="user-name"><?= htmlspecialchars($user_name) ?></span>
                <span class="user-type">(<?= $user_type === 'member' ? '고객' : '트레이너' ?>)</span>
                <button class="btn-myinfo" onclick="location.href='my_info_<?= $user_type ?>.php'">내 정보</button>
                <button class="btn-logout" onclick="location.href='asset/controller/logout.php'">로그아웃</button>
            </div>
        <?php endif; ?>
    </div>
</header>