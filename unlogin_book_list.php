<?php include 'header.php'; ?>

<?php
// 로그인 확인 (트레이너만 접근 가능)
if (!$is_logged_in || $user_type !== 'trainer') {
    echo "<script>alert('트레이너 로그인이 필요합니다.'); location.href='login.php?type=trainer';</script>";
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
    if (empty($disease_codes)) return '';
    $codes = explode(',', $disease_codes);
    $placeholders = str_repeat('?,', count($codes) - 1) . '?';
    $query = "SELECT name FROM disease_code WHERE no IN ($placeholders)";
    $stmt = $pdo->prepare($query);
    $stmt->execute($codes);
    $diseases = $stmt->fetchAll(PDO::FETCH_COLUMN);
    return implode(', ', $diseases);
}

// 비회원 예약 리스트 조회 (trainer_no가 NULL인 대기 중인 예약만)
$query = "SELECT no, name, phone, region, disease_code, insert_date
          FROM unlogin_book
          WHERE trainer_no IS NULL
          ORDER BY insert_date DESC";
$stmt = $pdo->prepare($query);
$stmt->execute();
$unlogin_books = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="unlogin-list-container">
    <!-- 로고 -->
    <div class="unlogin-list-logo">
        <img src="asset/image/logo.svg" alt="Mangrove Solution 로고"/>
    </div>

    <!-- 타이틀 -->
    <h1 class="unlogin-list-title">비회원 상담 대기 리스트</h1>
    <p class="unlogin-list-subtitle">총 <strong><?= count($unlogin_books) ?></strong>건의 예약이 대기 중입니다</p>

    <!-- 테이블 -->
    <?php if (!empty($unlogin_books)): ?>
    <div class="unlogin-table-wrapper">
        <table class="unlogin-table">
            <thead>
                <tr>
                    <th>번호</th>
                    <th>이름</th>
                    <th>전화번호</th>
                    <th>거주지</th>
                    <th>질병/증상</th>
                    <th>신청일시</th>
                    <th>승인</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($unlogin_books as $index => $book): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= htmlspecialchars($book['name']) ?></td>
                    <td><?= htmlspecialchars($book['phone']) ?></td>
                    <td><?= htmlspecialchars(getRegionNames($pdo, $book['region'])) ?></td>
                    <td><?= htmlspecialchars(getDiseaseNames($pdo, $book['disease_code'])) ?></td>
                    <td><?= htmlspecialchars($book['insert_date']) ?></td>
                    <td>
                        <button class="btn-approve" onclick="approveBook(<?= $book['no'] ?>, '<?= htmlspecialchars($book['name']) ?>')">승인</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="no-data">
        <p>대기 중인 비회원 예약이 없습니다.</p>
    </div>
    <?php endif; ?>

    <!-- 버튼 박스 -->
    <div class="unlogin-list-button-box">
        <button class="btn-large btn-primary" onclick="location.href='trainer_book.php'">예약관리로 돌아가기</button>
        <button class="btn-large btn-secondary" onclick="location.href='index.php'">처음으로</button>
    </div>
</section>

<?php include 'footer.php'; ?>

<script>
function approveBook(bookNo, bookName) {
    if (!confirm(bookName + '님의 예약을 승인하시겠습니까?\n승인 후 전화 상담을 진행해주세요.')) {
        return;
    }

    $.ajax({
        type: 'POST',
        url: 'asset/controller/approve_unlogin_book.php',
        data: {
            book_no: bookNo
        },
        dataType: 'json',
        success: function(res) {
            if (res.succ) {
                alert('예약이 승인되었습니다.\n해당 고객에게 전화 상담을 진행해주세요.');
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
