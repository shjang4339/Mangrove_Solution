<?php include 'header.php'; ?>

<?php
// 관리자 권한 확인 (admin 계정만 접근 가능)
if (!$is_logged_in || $user_id !== 'admin') {
    echo "<script>alert('관리자만 접근 가능합니다.'); location.href='index.php';</script>";
    exit;
}

// 전체 상담예약 건수 조회
$query = "SELECT COUNT(*) as total FROM book";
$stmt = $pdo->prepare($query);
$stmt->execute();
$book_count = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// 승인 대기 중인 트레이너 수 조회
$query = "SELECT COUNT(*) as total FROM trainer WHERE is_confirm = 0 AND id != 'admin'";
$stmt = $pdo->prepare($query);
$stmt->execute();
$pending_trainer_count = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// 승인된 트레이너 수 조회
$query = "SELECT COUNT(*) as total FROM trainer WHERE is_confirm = 1 AND id != 'admin'";
$stmt = $pdo->prepare($query);
$stmt->execute();
$approved_trainer_count = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// 가입 고객 수 조회
$query = "SELECT COUNT(*) as total FROM member";
$stmt = $pdo->prepare($query);
$stmt->execute();
$member_count = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
?>

<section class="admin-dashboard-container">
    <!-- 로고 -->
    <div class="admin-logo">
        <img src="asset/image/logo.svg" alt="Mangrove Solution 로고"/>
    </div>

    <!-- 타이틀 -->
    <h1 class="admin-title">맹그로브 솔루션 ADMIN 페이지입니다.</h1>

    <!-- 관리 메뉴 박스 -->
    <div class="admin-menu-box">
        <!-- 상담예약 관리 -->
        <button class="admin-menu-btn" onclick="location.href='admin_book_list.php'">
            <span class="menu-title">상담예약</span>
            <span class="menu-count"><?= $book_count ?> 건</span>
        </button>

        <!-- 트레이너 회원 승인 -->
        <button class="admin-menu-btn" onclick="location.href='admin_trainer_pending.php'">
            <span class="menu-title">트레이너 회원 승인</span>
            <?php if ($pending_trainer_count > 0): ?>
                <span class="menu-count badge"><?= $pending_trainer_count ?> 건 대기중</span>
            <?php else: ?>
                <span class="menu-count">대기 없음</span>
            <?php endif; ?>
        </button>

        <!-- 트레이너 리스트 -->
        <button class="admin-menu-btn" onclick="location.href='admin_trainer_list.php'">
            <span class="menu-title">트레이너 리스트</span>
            <span class="menu-count"><?= $approved_trainer_count ?> 명</span>
        </button>

        <!-- 가입 고객 리스트 -->
        <button class="admin-menu-btn" onclick="location.href='admin_member_list.php'">
            <span class="menu-title">가입 고객 리스트</span>
            <span class="menu-count"><?= $member_count ?> 명</span>
        </button>
    </div>

    <!-- 버튼 박스 -->
    <div class="admin-button-box">
        <button class="btn-large btn-cancel" onclick="location.href='index.php'">처음으로</button>
    </div>
</section>

<?php include 'footer.php'; ?>
</body>
</html>
