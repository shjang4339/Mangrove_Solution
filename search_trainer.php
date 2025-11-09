<?php include 'header.php'; ?>

<?php
// 로그인 확인
if (!$is_logged_in || $user_type !== 'member') {
    echo "<script>alert('회원 로그인이 필요합니다.'); location.href='login.php?type=member';</script>";
    exit;
}

$trainers = [];
$search_type = $_GET['type'] ?? 'custom';

if ($search_type === 'nearby') {
    // 집 근처 지역 트레이너 검색 (회원의 거주지와 매칭)
    $member_region = $_SESSION['member_region'] ?? '';

    if (!empty($member_region)) {
        // 회원의 거주지 코드 배열로 변환
        $member_regions = explode(',', $member_region);

        // 트레이너 검색 - 거주지가 하나라도 일치하는 경우
        $placeholders = implode(',', array_fill(0, count($member_regions), '?'));
        $query = "SELECT * FROM trainer WHERE ";

        $conditions = [];
        foreach ($member_regions as $region) {
            $conditions[] = "FIND_IN_SET(?, region) > 0";
        }
        $query .= implode(' OR ', $conditions);

        $stmt = $pdo->prepare($query);
        $stmt->execute($member_regions);
        $trainers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} else {
    // 사용자 지정 검색 (거주지 + 질환)
    $regions = $_GET['regions'] ?? '';
    $diseases = $_GET['diseases'] ?? '';

    if (!empty($regions) && !empty($diseases)) {
        $region_arr = explode(',', $regions);
        $disease_arr = explode(',', $diseases);

        // 트레이너 검색 - 거주지와 질환 코드가 모두 일치하는 경우
        $region_conditions = [];
        foreach ($region_arr as $region) {
            $region_conditions[] = "FIND_IN_SET(?, region) > 0";
        }

        $disease_conditions = [];
        foreach ($disease_arr as $disease) {
            $disease_conditions[] = "FIND_IN_SET(?, disease_code) > 0";
        }

        $query = "SELECT * FROM trainer WHERE (" . implode(' OR ', $region_conditions) . ") AND (" . implode(' OR ', $disease_conditions) . ")";

        $params = array_merge($region_arr, $disease_arr);
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $trainers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>

<section class="search-result-container">
    <!-- 로고 -->
    <div class="search-logo">
        <img src="asset/image/logo.svg" alt="Mangrove Solution 로고"/>
    </div>

    <!-- 타이틀 -->
    <h1 class="search-title">트레이너 검색 결과</h1>
    <p class="search-count">총 <strong><?= count($trainers) ?></strong>명의 트레이너를 찾았습니다.</p>

    <!-- 트레이너 리스트 -->
    <?php if (!empty($trainers)): ?>
    <div class="booked-trainers-container">
        <h2>검색된 트레이너</h2>
        <div class="booked-trainers-list">
            <?php foreach ($trainers as $trainer): ?>
            <div class="booked-trainer-item" onclick="location.href='trainer_detail.php?no=<?= $trainer['no'] ?>'">
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
                        <?= htmlspecialchars($trainer['greet']) ?>
                    </p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php else: ?>
    <div class="no-result">
        <p>검색 조건에 맞는 트레이너를 찾지 못했습니다.</p>
    </div>
    <?php endif; ?>

    <!-- 하단 버튼 -->
    <div class="my-info-button-box">
        <button class="btn-large btn-primary" onclick="location.href='find_trainer.php'">다시 검색하기</button>
        <button class="btn-large btn-cancel" onclick="location.href='book.php'">돌아가기</button>
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
