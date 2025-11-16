<?php include 'header.php'; ?>

<?php
// 로그인 확인 (트레이너만 접근 가능)
if (!$is_logged_in || $user_type !== 'trainer') {
    echo "<script>alert('트레이너 로그인이 필요합니다.'); location.href='login.php?type=trainer';</script>";
    exit;
}

// 트레이너가 담당하는 회원 예약 건수 조회 (is_meet = 0)
$member_book_query = "SELECT COUNT(*) as count FROM book WHERE trainer_no = ? AND is_meet = 0";
$member_book_stmt = $pdo->prepare($member_book_query);
$member_book_stmt->execute(array($_SESSION['user_no']));
$member_book_result = $member_book_stmt->fetch(PDO::FETCH_ASSOC);
$member_book_count = $member_book_result['count'] ?? 0;

// 비회원 예약 리스트 건수 조회 (trainer_no가 할당되고 is_meet = 0인 것만)
$unlogin_book_query = "SELECT COUNT(*) as count FROM unlogin_book WHERE trainer_no = ? AND is_meet = 0";
$unlogin_book_stmt = $pdo->prepare($unlogin_book_query);
$unlogin_book_stmt->execute(array($_SESSION['user_no']));
$unlogin_book_result = $unlogin_book_stmt->fetch(PDO::FETCH_ASSOC);
$unlogin_book_count = $unlogin_book_result['count'] ?? 0;

// 비회원 상담 대기 리스트 건수 조회 (trainer_no IS NULL)
$unlogin_book_query = "SELECT COUNT(*) as count FROM unlogin_book WHERE trainer_no IS NULL";
$unlogin_book_stmt = $pdo->prepare($unlogin_book_query);
$unlogin_book_stmt->execute();
$unlogin_book_wait_result = $unlogin_book_stmt->fetch(PDO::FETCH_ASSOC);
$unlogin_book_wait_count = $unlogin_book_wait_result['count'] ?? 0;
?>

<section class="trainer-book-container">
    <!-- 로고 -->
    <div class="trainer-book-logo">
        <img src="asset/image/logo.svg" alt="Mangrove Solution 로고"/>
    </div>

    <!-- 환영 메시지 -->
    <div class="welcome-message">
        <h1><?= htmlspecialchars($user_name) ?> 트레이너님 <br class="mo">안녕하세요!</h1>
        <p>예약 관리 페이지입니다</p>
    </div>

    <!-- 담당 상담 예약 -->
    <div class="member-book-waiting" onclick="location.href='trainer_book_list.php'" style="cursor: pointer;">
        <h2>담당 상담 예약</h2>
        <p class="waiting-count"><strong><?= $member_book_count ?></strong> 건 / <strong><?= $unlogin_book_count ?></strong> 건</p>
        <p class="waiting-desc">클릭하여 회원 예약을 확인하세요</p>
    </div>

    <!-- 비회원 상담 대기 리스트 -->
    <div class="unlogin-book-waiting" onclick="location.href='unlogin_book_list.php'" style="cursor: pointer;">
        <h2>비회원 상담 대기 리스트</h2>
        <p class="waiting-count"><strong><?= $unlogin_book_wait_count ?></strong> 건</p>
        <p class="waiting-desc">클릭하여 대기 중인 비회원 예약을 확인하세요</p>
    </div>

    <!-- 버튼 박스 -->
    <div class="my-info-button-box">
        <button class="btn-large btn-primary" onclick="location.href='trainer_book_list.php'">예약 관리</button>
        <button class="btn-large btn-secondary" onclick="location.href='index.php'">처음으로</button>
    </div>
</section>

<?php include 'footer.php'; ?>
</body>
</html>
