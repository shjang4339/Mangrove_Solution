<?php include 'header.php'; ?>

<?php
// 로그인 확인 (회원과 트레이너 모두 접근 가능)
if (!$is_logged_in) {
    echo "<script>alert('로그인이 필요합니다.'); location.href='login.php?type=member';</script>";
    exit;
}

// 트레이너 번호 확인
$trainer_no = $_GET['no'] ?? 0;

if (empty($trainer_no)) {
    echo "<script>alert('잘못된 접근입니다.'); history.back();</script>";
    exit;
}

// 트레이너 정보 조회
$query = "SELECT * FROM trainer WHERE no = ?";
$stmt = $pdo->prepare($query);
$stmt->execute(array($trainer_no));
$trainer = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$trainer) {
    echo "<script>alert('트레이너를 찾을 수 없습니다.'); history.back();</script>";
    exit;
}

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

<section class="trainer-detail-container">
    <!-- 로고 -->
    <div class="trainer-detail-logo">
        <img src="asset/image/logo.svg" alt="Mangrove Solution 로고"/>
    </div>

    <!-- 트레이너 기본 정보 -->
    <div class="trainer-basic-info">
        <div class="trainer-name-info">
            <h1><?= htmlspecialchars($trainer['name']) ?></h1>
            <p class="trainer-experience">경력 / <?= htmlspecialchars(getRegionNames($pdo, $trainer['region'])) ?></p>
        </div>
        <div class="trainer-photo">
            <?php if (!empty($trainer['image'])): ?>
                <img src="image/<?= htmlspecialchars($trainer['image']) ?>" alt="<?= htmlspecialchars($trainer['name']) ?> 트레이너">
            <?php else: ?>
                <div class="no-image">No Image</div>
            <?php endif; ?>
        </div>
    </div>

    <!-- 학위 / 전공 -->
    <div class="trainer-detail-section">
        <h3 class="section-title">학위 / 전공</h3>
        <p class="section-content"><?= htmlspecialchars($trainer['major']) ?></p>
    </div>

    <!-- 대표 자격증 -->
    <div class="trainer-detail-section">
        <h3 class="section-title">대표 자격증</h3>
        <p class="section-content"><?= htmlspecialchars($trainer['license']) ?></p>
    </div>

    <!-- 자격증 -->
    <div class="trainer-detail-section">
        <h3 class="section-title">자격증</h3>
        <div class="license-list">
            <?php if (!empty($trainer['sublicense_1'])): ?>
                <p class="section-content"><?= htmlspecialchars($trainer['sublicense_1']) ?></p>
            <?php endif; ?>
            <?php if (!empty($trainer['sublicense_2'])): ?>
                <p class="section-content"><?= htmlspecialchars($trainer['sublicense_2']) ?></p>
            <?php endif; ?>
            <?php if (!empty($trainer['sublicense_3'])): ?>
                <p class="section-content"><?= htmlspecialchars($trainer['sublicense_3']) ?></p>
            <?php endif; ?>
        </div>
    </div>

    <!-- 트레이너 인삿말 -->
    <div class="trainer-detail-section">
        <h3 class="section-title">트레이너 인삿말</h3>
        <p class="section-content"><?= htmlspecialchars($trainer['greet']) ?></p>
    </div>

    <!-- 버튼 박스 -->
    <div class="trainer-detail-button-box">
        <?php if ($user_type === 'member'): ?>
            <button class="btn-book btn-booking" onclick="bookTrainer(<?= $trainer['no'] ?>, '<?= htmlspecialchars($trainer['name']) ?>')">상담 예약</button>
        <?php endif; ?>
        <button class="btn-book btn-cancel" onclick="history.back()">취소</button>
    </div>
</section>

<?php include 'footer.php'; ?>

<script>
    function bookTrainer(trainerNo, trainerName) {
        if (!confirm(trainerName + ' 트레이너에게 상담을 예약하시겠습니까?')) {
            return;
        }

        $.ajax({
            url: 'asset/controller/insert_booking.php',
            type: 'POST',
            data: {
                trainer_no: trainerNo
            },
            dataType: 'json',
            async: false,
            success: function(response) {
                if (response.succ) {
                    alert('상담 예약이 완료되었습니다.');
                    location.href = 'book.php';
                } else {
                    alert('예약 실패: ' + response.message);
                }
            },
            error: function() {
                alert('서버와의 통신에 실패했습니다.');
            }
        });
    }
</script>
</body>
</html>
