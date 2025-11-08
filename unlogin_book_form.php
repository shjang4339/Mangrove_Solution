<?php include 'header.php'; ?>

<?php
// 지역 정보 조회
$region_query = "SELECT no, name FROM region ORDER BY no";
$region_stmt = $pdo->prepare($region_query);
$region_stmt->execute();
$regions = $region_stmt->fetchAll(PDO::FETCH_ASSOC);

// 질병 정보 조회
$disease_query = "SELECT no, name FROM disease_code ORDER BY no";
$disease_stmt = $pdo->prepare($disease_query);
$disease_stmt->execute();
$diseases = $disease_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="unlogin-book-container">
    <!-- 로고 -->
    <div class="unlogin-book-logo">
        <img src="asset/image/logo.svg" alt="Mangrove Solution 로고"/>
    </div>

    <!-- 타이틀 -->
    <h1 class="unlogin-book-title">비회원 전화 상담 예약</h1>
    <p class="unlogin-book-subtitle">전문 트레이너가 전화로 상담해드립니다</p>

    <!-- 입력 폼 -->
    <div class="unlogin-book-form">
        <div class="form-group">
            <label for="name">
                <span class="ntnll">이름</span>
            </label>
            <input type="text" id="name" placeholder="이름을 입력하세요">
        </div>

        <div class="form-group">
            <label for="phone">
                <span class="ntnll">전화번호</span>
            </label>
            <input type="text" id="phone" placeholder="전화번호를 입력하세요 (예: 010-1234-5678)">
        </div>

        <div class="form-group">
            <label>
                <span class="ntnll">거주지</span>
            </label>
            <div class="region-box">
                <?php foreach ($regions as $region): ?>
                    <label>
                        <input type="radio" name="region" value="<?= $region['no'] ?>">
                        <?= htmlspecialchars($region['name']) ?>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="form-group">
            <label>
                <span class="ntnll">질병/증상</span>
            </label>
            <div class="disease-box">
                <?php foreach ($diseases as $disease): ?>
                    <label>
                        <input type="checkbox" name="disease" value="<?= $disease['no'] ?>">
                        <?= htmlspecialchars($disease['name']) ?>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- 버튼 박스 -->
        <div class="unlogin-book-button-box">
            <button class="btn-large btn-primary" id="btn-submit">전화상담예약</button>
            <button class="btn-large btn-cancel" onclick="location.href='index.php'">취소</button>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>

<script>
$(document).ready(function() {
    $('#btn-submit').on('click', function() {
        // 입력값 가져오기
        const name = $('#name').val().trim();
        const phone = $('#phone').val().trim();
        const diseases = [];

        // 선택된 지역 수집
        const region = $('input[name="region"]:checked').val();

        // 선택된 질병 수집
        $('input[name="disease"]:checked').each(function() {
            diseases.push($(this).val());
        });

        // 유효성 검사
        if (!name) {
            alert('이름을 입력해주세요.');
            $('#name').focus();
            return;
        }

        if (!phone) {
            alert('전화번호를 입력해주세요.');
            $('#phone').focus();
            return;
        }

        // 전화번호 형식 검증 (간단한 검증)
        const phonePattern = /^[\d-]+$/;
        if (!phonePattern.test(phone)) {
            alert('올바른 전화번호 형식을 입력해주세요.');
            $('#phone').focus();
            return;
        }

        if (!region) {
            alert('거주지를 선택해주세요.');
            return;
        }

        if (diseases.length === 0) {
            alert('질병/증상을 최소 1개 이상 선택해주세요.');
            return;
        }

        // AJAX 요청
        $.ajax({
            type: 'POST',
            url: 'asset/controller/insert_unlogin_book.php',
            data: {
                name: name,
                phone: phone,
                region: region,
                disease_code: diseases.join(',')
            },
            dataType: 'json',
            success: function(res) {
                if (res.succ) {
                    alert('전화 상담 예약이 완료되었습니다.\n트레이너가 빠른 시일 내에 연락드리겠습니다.');
                    location.href = 'index.php';
                } else {
                    alert(res.message || '예약 중 오류가 발생했습니다.');
                }
            },
            error: function() {
                alert('서버와의 통신에 실패했습니다.');
            }
        });
    });
});
</script>
</body>
</html>
