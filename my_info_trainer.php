<?php include 'header.php'; ?>

<?php
// 로그인 확인 (트레이너만 접근 가능)
if (!$is_logged_in || $user_type !== 'trainer') {
    echo "<script>alert('트레이너 로그인이 필요합니다.'); location.href='login.php?type=trainer';</script>";
    exit;
}

// 트레이너 정보 조회
$query = "SELECT * FROM trainer WHERE no = ?";
$stmt = $pdo->prepare($query);
$stmt->execute(array($_SESSION['user_no']));
$trainer = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$trainer) {
    echo "<script>alert('트레이너 정보를 찾을 수 없습니다.'); history.back();</script>";
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
            <span class="info-value"><?= htmlspecialchars($trainer['id']) ?></span>
        </div>

        <div class="info-item">
            <span class="info-label">이름</span>
            <span class="info-value"><?= htmlspecialchars($trainer['name']) ?></span>
        </div>

        <div class="info-item">
            <span class="info-label">전화번호</span>
            <span class="info-value"><?= htmlspecialchars($trainer['phone']) ?></span>
        </div>

        <div class="info-item">
            <span class="info-label">이메일 주소</span>
            <span class="info-value"><?= htmlspecialchars($trainer['email']) ?></span>
        </div>

        <div class="info-item">
            <span class="info-label">학위 / 전공명</span>
            <span class="info-value"><?= htmlspecialchars($trainer['major']) ?></span>
        </div>

        <div class="info-item">
            <span class="info-label">대표 자격증</span>
            <span class="info-value"><?= htmlspecialchars($trainer['license']) ?></span>
        </div>

        <?php if (!empty($trainer['sublicense_1'])): ?>
        <div class="info-item">
            <span class="info-label">추가 자격증 1</span>
            <span class="info-value"><?= htmlspecialchars($trainer['sublicense_1']) ?></span>
        </div>
        <?php endif; ?>

        <?php if (!empty($trainer['sublicense_2'])): ?>
        <div class="info-item">
            <span class="info-label">추가 자격증 2</span>
            <span class="info-value"><?= htmlspecialchars($trainer['sublicense_2']) ?></span>
        </div>
        <?php endif; ?>

        <?php if (!empty($trainer['sublicense_3'])): ?>
        <div class="info-item">
            <span class="info-label">추가 자격증 3</span>
            <span class="info-value"><?= htmlspecialchars($trainer['sublicense_3']) ?></span>
        </div>
        <?php endif; ?>

        <?php if (!empty($trainer['image'])): ?>
        <div class="info-item">
            <span class="info-label">프로필 사진</span>
            <div class="info-image">
                <img src="image/<?= htmlspecialchars($trainer['image']) ?>" alt="프로필 사진">
            </div>
        </div>
        <?php endif; ?>

        <div class="info-item">
            <span class="info-label">가능한 트레이너 지역</span>
            <span class="info-value"><?= htmlspecialchars(getRegionNames($pdo, $trainer['region'])) ?></span>
        </div>

        <div class="info-item">
            <span class="info-label">전문 분야</span>
            <span class="info-value"><?= htmlspecialchars(getDiseaseNames($pdo, $trainer['disease_code'])) ?></span>
        </div>

        <div class="info-item">
            <span class="info-label">트레이너 인삿말</span>
            <span class="info-value"><?= htmlspecialchars($trainer['greet']) ?></span>
        </div>
        
        <!-- 회원탈퇴 버튼 -->
        <div style="text-align:right; margin-top:20px;">
            <button class="btn-large" style="background:#dc3545; color:#fff; padding:10px 20px; font-size:14px;" onclick="deleteTrainerAccount()">회원탈퇴</button>
        </div>
    </div>

    <!-- 버튼 박스 -->
    <div class="my-info-button-box">
        <button class="btn-large btn-primary" onclick="location.href='edit_info_trainer.php'">수정하기</button>
        <button class="btn-large btn-cancel" onclick="history.back()">취소</button>
    </div>
</section>

<?php include 'footer.php'; ?>

<script>
    function deleteTrainerAccount() {
        if (!confirm('정말로 회원탈퇴를 하시겠습니까?\n탈퇴 시 모든 정보가 삭제되며 복구할 수 없습니다.')) {
            return;
        }

        if (!confirm('탈퇴하시면 프로필 이미지, 예약 내역 등 모든 데이터가 삭제됩니다.\n정말로 탈퇴하시겠습니까?')) {
            return;
        }

        $.ajax({
            type: 'POST',
            url: 'asset/controller/delete_trainer.php',
            dataType: 'json',
            success: function(res) {
                if (res.succ) {
                    alert('회원탈퇴가 완료되었습니다.');
                    location.href = 'index.php';
                } else {
                    alert('회원탈퇴에 실패했습니다: ' + res.message);
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
