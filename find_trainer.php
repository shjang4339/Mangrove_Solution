<?php include 'header.php'; ?>

<?php
// 로그인 확인
if (!$is_logged_in || $user_type !== 'member') {
    echo "<script>alert('회원 로그인이 필요합니다.'); location.href='login.php?type=member';</script>";
    exit;
}

// 지역 목록 조회
$region_query = "SELECT * FROM region ORDER BY no";
$region_stmt = $pdo->prepare($region_query);
$region_stmt->execute();
$regions = $region_stmt->fetchAll(PDO::FETCH_ASSOC);

// 질환 코드 목록 조회
$disease_query = "SELECT * FROM disease_code ORDER BY no";
$disease_stmt = $pdo->prepare($disease_query);
$disease_stmt->execute();
$diseases = $disease_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="find-trainer-container">
    <!-- 로고 -->
    <div class="find-logo">
        <img src="asset/image/logo.svg" alt="Mangrove Solution 로고"/>
    </div>

    <!-- 타이틀 -->
    <h1 class="find-title">트레이너 바로 찾기</h1>
    <p class="find-subtitle">거주지와 질환을 선택하여 맞춤 트레이너를 찾으세요.</p>

    <!-- 검색 폼 -->
    <div class="find-form">
        <!-- 거주지 선택 -->
        <div class="form-section">
            <h3><span class="ntnll">거주지</span></h3>
            <div class="region-box">
                <?php foreach ($regions as $region): ?>
                <label>
                    <input type="checkbox" name="region[]" value="<?= $region['no'] ?>">
                    <?= htmlspecialchars($region['name']) ?>
                </label>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- 질환 선택 -->
        <div class="form-section">
            <h3><span class="ntnll">질환</span></h3>
            <div class="disease-box">
                <?php foreach ($diseases as $disease): ?>
                <label>
                    <input type="checkbox" name="disease[]" value="<?= $disease['no'] ?>">
                    <?= htmlspecialchars($disease['name']) ?>
                </label>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- 버튼 박스 -->
        <div class="find-button-box">
            <button class="btn-large btn-secondary" id="btn-search">찾기</button>
            <button class="btn-large btn-cancel" onclick="location.href='book.php'">취소</button>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>

<script>
    $('#btn-search').on('click', function() {
        // 거주지 선택 확인
        let regions = [];
        $('input[name="region[]"]:checked').each(function() {
            regions.push($(this).val());
        });

        // 질환 선택 확인
        let diseases = [];
        $('input[name="disease[]"]:checked').each(function() {
            diseases.push($(this).val());
        });

        // 유효성 검사
        if (regions.length === 0) {
            alert('거주지를 선택해주세요.');
            return;
        }

        if (diseases.length === 0) {
            alert('질환을 선택해주세요.');
            return;
        }

        // 검색 실행
        $.ajax({
            url: 'asset/controller/search_trainer.php',
            type: 'POST',
            data: {
                regions: regions.join(','),
                diseases: diseases.join(',')
            },
            dataType: 'json',
            success: function(response) {
                if (response.succ) {
                    // 검색 결과 페이지로 이동 (파라미터로 전달)
                    location.href = 'search_trainer.php?regions=' + regions.join(',') + '&diseases=' + diseases.join(',');
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
