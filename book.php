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

// 예약된 트레이너 목록 조회
$query = "SELECT b.no as book_no, t.no, t.name, t.image, t.major, t.region, t.greet, t.license, t.sublicense_1, t.sublicense_2, t.sublicense_3
          FROM book b
          JOIN trainer t ON b.trainer_no = t.no
          WHERE b.member_no = ? AND b.is_meet = 0
          ORDER BY b.book_date DESC";
$stmt = $pdo->prepare($query);
$stmt->execute(array($_SESSION['user_no']));
$booked_trainers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 지역 코드를 이름으로 변환하는 함수
function getRegionNames($pdo, $region_codes) {
    if (empty($region_codes)) return '';
    $codes = explode(',', $region_codes);
    $placeholders = str_repeat('?,', count($codes) - 1) . '?';
    $query = "SELECT name FROM region WHERE no IN ($placeholders)";
    $stmt = $pdo->prepare($query);
    $stmt->execute($codes);
    $regions = $stmt->fetchAll(PDO::FETCH_COLUMN);
    return implode(', ', $regions);
}
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

    <!-- 예약된 트레이너 목록 -->
    <?php if (!empty($booked_trainers)): ?>
    <div class="booked-trainers-container">
        <h2>예약된 트레이너</h2>
        <div class="booked-trainers-list">
            <?php foreach ($booked_trainers as $trainer): ?>
            <div class="booked-trainer-item" data-book-no="<?= $trainer['book_no'] ?>" data-trainer-name="<?= htmlspecialchars($trainer['name']) ?>">
                <!-- 트레이너 이미지 -->
                <div class="booked-trainer-image">
                    <?php if (!empty($trainer['image'])): ?>
                        <img src="image/<?= htmlspecialchars($trainer['image']) ?>" alt="<?= htmlspecialchars($trainer['name']) ?> 트레이너">
                    <?php else: ?>
                        <div class="no-image">No Image</div>
                    <?php endif; ?>
                </div>

                <!-- 트레이너 정보 -->
                <div class="booked-trainer-info">
                    <h3><?= htmlspecialchars($trainer['name']) ?></h3>
                    <p class="trainer-details">
                        <?= htmlspecialchars($trainer['major']) ?> ·
                        <?= htmlspecialchars($trainer['license']) ?> ·
                        <?= htmlspecialchars($trainer['greet']) ?> ·
                        <?= htmlspecialchars(getRegionNames($pdo, $trainer['region'])) ?>
                    </p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- 버튼 박스 -->
    <div class="book-button-box">
        <button class="btn-book btn-secondary" onclick="location.href='all_trainers.php'">모든 트레이너 리스트</button>
        <button class="btn-book btn-primary" id="btn-nearby-trainer">집 근처 지역 트레이너 찾기</button>
        <button class="btn-book btn-tertiary" onclick="location.href='find_trainer.php'">나에게 맞는 트레이너 바로 찾기</button>
        <button class="btn-book btn-gray" onclick="location.href='index.php'">처음으로</button>
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

    // 예약 취소 기능 - div 클릭 시
    $('.booked-trainer-item').on('click', function() {
        const bookNo = $(this).data('book-no');
        const trainerName = $(this).data('trainer-name');

        if (confirm(trainerName + '님 트레이너분께 요청한 상담을 취소하시겠습니까?')) {
            $.ajax({
                url: 'asset/controller/cancel_booking.php',
                type: 'POST',
                data: { book_no: bookNo },
                dataType: 'json',
                async: false,
                success: function(response) {
                    if (response.succ) {
                        alert('예약이 취소되었습니다.');
                        location.reload();
                    } else {
                        alert(response.message || '예약 취소 중 오류가 발생했습니다.');
                    }
                },
                error: function() {
                    alert('서버와의 통신에 실패했습니다.');
                }
            });
        }
    });
</script>
</body>
</html>
