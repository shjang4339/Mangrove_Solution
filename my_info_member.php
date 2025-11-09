<?php include 'header.php'; ?>

<?php
// 로그인 확인 (고객만 접근 가능)
if (!$is_logged_in || $user_type !== 'member') {
    echo "<script>alert('고객 로그인이 필요합니다.'); location.href='login.php?type=member';</script>";
    exit;
}

// 고객 정보 조회
$query = "SELECT * FROM member WHERE no = ?";
$stmt = $pdo->prepare($query);
$stmt->execute(array($_SESSION['user_no']));
$member = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$member) {
    echo "<script>alert('회원 정보를 찾을 수 없습니다.'); history.back();</script>";
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

<section class="my-info-container">
    <!-- 로고 -->
    <div class="my-info-logo">
        <img src="asset/image/logo.svg" alt="Mangrove Solution 로고"/>
    </div>

    <!-- 타이틀 -->
    <h1 class="my-info-title">내 정보</h1>

    <!-- 정보 섹션 -->
    <div class="info-box">
        <div class="info-item">
            <span class="info-label">아이디</span>
            <span class="info-value"><?= htmlspecialchars($member['id']) ?></span>
        </div>

        <div class="info-item">
            <span class="info-label">이름</span>
            <span class="info-value"><?= htmlspecialchars($member['name']) ?></span>
        </div>

        <div class="info-item">
            <span class="info-label">전화번호</span>
            <span class="info-value"><?= htmlspecialchars($member['phone']) ?></span>
        </div>

        <div class="info-item">
            <span class="info-label">거주지</span>
            <span class="info-value"><?= htmlspecialchars(getRegionNames($pdo, $member['region'])) ?></span>
        </div>

        <div class="info-item">
            <span class="info-label">생년월일</span>
            <span class="info-value"><?= htmlspecialchars($member['birth']) ?></span>
        </div>

        <div class="info-item">
            <span class="info-label">이메일 주소</span>
            <span class="info-value"><?= htmlspecialchars($member['email']) ?></span>
        </div>

        <div class="info-item">
            <span class="info-label">질환/장애</span>
            <span class="info-value"><?= htmlspecialchars(getDiseaseNames($pdo, $member['disease_code'])) ?></span>
        </div>
    </div>

    <!-- 버튼 박스 -->
    <div class="my-info-button-box">
        <button class="btn-large btn-primary" onclick="location.href='edit_info_member.php'">수정하기</button>
        <button class="btn-large btn-cancel" onclick="history.back()">취소</button>
    </div>
</section>

<?php include 'footer.php'; ?>
</body>
</html>
