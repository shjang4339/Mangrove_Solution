<?php include 'header.php'; ?>

<?php
// 로그인 확인
if (!$is_logged_in || $user_type !== 'member') {
    echo "<script>alert('회원 로그인이 필요합니다.'); location.href='login.php?type=member';</script>";
    exit;
}

// 현재 회원의 상담 예약 건수 조회 (대기중인 예약만)
$query = "SELECT COUNT(*) as booking_count FROM book WHERE member_no = ? AND is_meet = 0";
$stmt = $pdo->prepare($query);
$stmt->execute(array($_SESSION['user_no']));
$booking_info = $stmt->fetch(PDO::FETCH_ASSOC);
$booking_count = $booking_info['booking_count'] ?? 0;
?>

<section class="book-container">
    <!-- 로고 -->
    <div class="book-logo">
        <img src="asset/image/logo.svg" alt="Mangrove Solution 로고"/>
    </div>

    <!-- 환영 메시지 -->
    <div class="welcome-message">
        <h1><?= htmlspecialchars($user_name) ?> 회원님 안녕하세요!</h1>
        <p>회원님에 맞는 트레이너를 찾으세요.</p>
    </div>

    <!-- 현재 상담 예약 건수 -->
    <div class="booking-count">
        <p>현재 상담예약: <strong><?= $booking_count ?></strong> 건</p>
    </div>

    <!-- 버튼 박스 -->
    <div class="book-button-box">
        <button class="btn-book btn-gray" id="btn-nearby-trainer">집 근처 지역 트레이너 찾기</button>
        <button class="btn-book btn-yellow" onclick="location.href='find_trainer.php'">트레이너 바로 찾기</button>
        <button class="btn-book btn-cancel" onclick="location.href='index.php'">취소</button>
    </div>
</section>

<?php include 'footer.php'; ?>

<script>
    // 집 근처 지역 트레이너 찾기
    $('#btn-nearby-trainer').on('click', function() {
        // 회원의 거주지 정보를 가져와서 매칭되는 트레이너 검색
        $.ajax({
            url: 'asset/controller/search_nearby_trainer.php',
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                if (response.succ) {
                    // 검색 결과 페이지로 이동
                    location.href = 'search_trainer.php?type=nearby';
                } else {
                    alert('트레이너 검색 중 오류가 발생했습니다.');
                }
            },
            error: function() {
                alert('서버와의 통신에 실패했습니다.');
            }
        });
    });
</script>
</body>
</html>
