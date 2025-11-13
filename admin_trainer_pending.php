<?php include 'header.php'; ?>

<?php
// 관리자 권한 확인
if (!$is_logged_in || $user_id !== 'admin') {
    echo "<script>alert('관리자만 접근 가능합니다.'); location.href='index.php';</script>";
    exit;
}

// 전화번호 포맷팅 함수
function formatPhoneNumber($phone) {
    $phone = preg_replace('/[^0-9]/', '', $phone);
    $length = strlen($phone);
    if ($length == 11) {
        return substr($phone, 0, 3) . '-' . substr($phone, 3, 4) . '-' . substr($phone, 7, 4);
    } elseif ($length == 10) {
        return substr($phone, 0, 3) . '-' . substr($phone, 3, 3) . '-' . substr($phone, 6, 4);
    }
    return $phone;
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

// 승인 대기 중인 트레이너 조회 (is_confirm = 0)
$query = "SELECT * FROM trainer WHERE is_confirm = 0 AND id != 'admin' ORDER BY no DESC";
$stmt = $pdo->prepare($query);
$stmt->execute();
$trainers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="search-result-container">
    <!-- 로고 -->
    <div class="search-logo">
        <img src="asset/image/logo.svg" alt="Mangrove Solution 로고"/>
    </div>

    <!-- 타이틀 -->
    <h1 class="search-title">트레이너 회원 승인</h1>

    <!-- 트레이너 수 -->
    <div class="search-count">
        <p>총 <strong><?= count($trainers) ?></strong>명이 승인 대기 중입니다.</p>
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
                    <p class="trainer-major"><strong>아이디:</strong> <?= htmlspecialchars($trainer['id']) ?></p>
                    <p class="trainer-major"><strong>전화번호:</strong> <?= htmlspecialchars(formatPhoneNumber($trainer['phone'])) ?></p>
                    <p class="trainer-major"><strong>이메일:</strong> <?= htmlspecialchars($trainer['email']) ?></p>
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
                    <p class="trainer-major"><strong>가입일:</strong> <?= htmlspecialchars($trainer['insert_date']) ?></p>
                    <p class="trainer-greet"><?= htmlspecialchars($trainer['greet']) ?></p>
                </div>
                <div style="display:flex; gap:10px; justify-content: flex-end;">
                    <button class="btn-approve" onclick="approveTrainer(<?= $trainer['no'] ?>, '<?= htmlspecialchars($trainer['name']) ?>')">승인</button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="trainer-list">
        <div class="no-result">
            <p>승인 대기 중인 트레이너가 없습니다.</p>
        </div>
    </div>
    <?php endif; ?>

    <!-- 버튼 박스 -->
    <div class="search-bottom-buttons">
        <button class="btn-large btn-primary" onclick="history.back()">이전으로</button>
        <button class="btn-large btn-cancel" onclick="location.href='admin_dashboard.php'">관리자 페이지</button>
    </div>
</section>

<?php include 'footer.php'; ?>

<script>
function approveTrainer(trainerNo, trainerName) {
    if (!confirm(trainerName + ' 트레이너의 가입을 승인하시겠습니까?')) {
        return;
    }

    $.ajax({
        type: 'POST',
        url: 'asset/controller/approve_trainer.php',
        data: {
            trainer_no: trainerNo
        },
        dataType: 'json',
        success: function(res) {
            if (res.succ) {
                alert('트레이너 가입이 승인되었습니다.');
                location.reload();
            } else {
                alert(res.message || '승인 중 오류가 발생했습니다.');
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
