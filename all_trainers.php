<?php include 'header.php'; ?>

<?php
// 로그인 확인
if (!$is_logged_in || $user_type !== 'member') {
    echo "<script>alert('회원 로그인이 필요합니다.'); location.href='login.php?type=member';</script>";
    exit;
}

// 모든 트레이너 조회 (admin 제외)
$query = "SELECT no, name, image, major, teachday, license, sublicense_1, sublicense_2, sublicense_3, region, greet, disease_code
          FROM trainer
          WHERE id != 'admin'
          ORDER BY no DESC";
$stmt = $pdo->prepare($query);
$stmt->execute();
$trainers = $stmt->fetchAll(PDO::FETCH_ASSOC);

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

// 질병 코드를 이름으로 변환하는 함수
function getDiseaseNames($pdo, $disease_codes) {
    if (empty($disease_codes)) return '없음';
    $codes = explode(',', $disease_codes);
    $placeholders = str_repeat('?,', count($codes) - 1) . '?';
    $query = "SELECT name FROM disease_code WHERE no IN ($placeholders)";
    $stmt = $pdo->prepare($query);
    $stmt->execute($codes);
    $diseases = $stmt->fetchAll(PDO::FETCH_COLUMN);
    return implode(', ', $diseases);
}
?>

<section class="search-result-container">
    <!-- 로고 -->
    <div class="search-logo">
        <img src="asset/image/logo.svg" alt="Mangrove Solution 로고"/>
    </div>

    <!-- 타이틀 -->
    <h1 class="search-title">모든 트레이너 리스트</h1>

    <!-- 트레이너 수 -->
    <div class="search-count">
        <p>총 <strong><?= count($trainers) ?></strong>명의 트레이너가 있습니다.</p>
    </div>

    <!-- 트레이너 리스트 -->
    <?php if (!empty($trainers)): ?>
    <div class="trainer-list">
        <?php foreach ($trainers as $trainer): ?>
        <div class="trainer-item">
            <!-- 트레이너 이미지 -->
            <div class="trainer-item-image">
                <?php if (!empty($trainer['image'])): ?>
                    <img src="image/<?= htmlspecialchars($trainer['image']) ?>" alt="<?= htmlspecialchars($trainer['name']) ?> 트레이너">
                <?php else: ?>
                    <div class="no-image">No Image</div>
                <?php endif; ?>
            </div>

            <!-- 트레이너 정보 -->
            <div class="trainer-item-info">
                <div>
                    <h3><?= htmlspecialchars($trainer['name']) ?> 트레이너</h3>
                    <p class="trainer-major"><strong>학위/전공:</strong> <?= htmlspecialchars($trainer['major']) ?></p>
                    <p class="trainer-major"><strong>경력:</strong> <?= htmlspecialchars($trainer['teachday']) ?> 년</p>
                    <p class="trainer-major"><strong>대표자격증:</strong> <?= htmlspecialchars($trainer['license']) ?></p>
                    <?php if (!empty($trainer['sublicense_1'])): ?>
                        <p class="trainer-major"><strong>추가자격증:</strong>
                            <?= htmlspecialchars($trainer['sublicense_1']) ?>
                            <?php if (!empty($trainer['sublicense_2'])): ?>, <?= htmlspecialchars($trainer['sublicense_2']) ?><?php endif; ?>
                            <?php if (!empty($trainer['sublicense_3'])): ?>, <?= htmlspecialchars($trainer['sublicense_3']) ?><?php endif; ?>
                        </p>
                    <?php endif; ?>
                    <p class="trainer-major"><strong>활동지역:</strong> <?= htmlspecialchars(getRegionNames($pdo, $trainer['region'])) ?></p>
                    <p class="trainer-major"><strong>전문분야:</strong> <?= htmlspecialchars(getDiseaseNames($pdo, $trainer['disease_code'])) ?></p>
                    <p class="trainer-greet"><?= htmlspecialchars($trainer['greet']) ?></p>
                </div>
                <div style="display:flex; gap:10px; justify-content: flex-end;">
                    <button class="btn-book-trainer" data-trainer-no="<?= $trainer['no'] ?>" data-trainer-name="<?= htmlspecialchars($trainer['name']) ?>">상담 예약</button>
                    <button class="btn-view-detail" onclick="location.href='trainer_detail.php?no=<?= $trainer['no'] ?>'">상세보기</button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="trainer-list">
        <div class="no-result">
            <p>등록된 트레이너가 없습니다.</p>
        </div>
    </div>
    <?php endif; ?>

    <!-- 버튼 박스 -->
    <div class="search-bottom-buttons">
        <button class="btn-large btn-primary" onclick="history.back()">이전으로</button>
        <button class="btn-large btn-cancel" onclick="location.href='book.php'">처음으로</button>
    </div>
</section>

<?php include 'footer.php'; ?>

<script>
    // 상담 예약 버튼 클릭
    $('.btn-book-trainer').on('click', function() {
        const trainerNo = $(this).data('trainer-no');
        const trainerName = $(this).data('trainer-name');

        if (confirm(trainerName + ' 트레이너님께 상담을 예약하시겠습니까?')) {
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
                        alert(response.message || '예약 중 오류가 발생했습니다.');
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
