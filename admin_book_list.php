<?php include 'header.php'; ?>

<?php
// 관리자 권한 확인
if (!$is_logged_in || $user_id !== 'admin') {
    echo "<script>alert('관리자만 접근 가능합니다.'); location.href='index.php';</script>";
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

// 전체 상담예약 조회 (날짜 내림차순)
$query = "SELECT b.*, m.name as member_name, m.phone as member_phone, t.name as trainer_name
          FROM book b
          LEFT JOIN member m ON b.member_no = m.no
          LEFT JOIN trainer t ON b.trainer_no = t.no
          ORDER BY b.book_date DESC";
$stmt = $pdo->prepare($query);
$stmt->execute();
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="admin-list-container">
    <!-- 로고 -->
    <div class="admin-logo">
        <img src="asset/image/logo.svg" alt="Mangrove Solution 로고"/>
    </div>

    <!-- 타이틀 -->
    <h1 class="admin-title">전체 상담예약 리스트</h1>
    <p class="admin-subtitle">총 <strong><?= count($books) ?></strong>건의 예약이 있습니다</p>

    <!-- 테이블 -->
    <?php if (!empty($books)): ?>
    <div class="admin-table-wrapper">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>번호</th>
                    <th>예약번호</th>
                    <th>고객명</th>
                    <th>전화번호</th>
                    <th>담당 트레이너</th>
                    <th>예약일시</th>
                    <th>상태</th>
                    <th>관리</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($books as $index => $book): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= htmlspecialchars($book['no']) ?></td>
                    <td><?= htmlspecialchars($book['member_name']) ?></td>
                    <td><?= htmlspecialchars(formatPhoneNumber($book['member_phone'])) ?></td>
                    <td><?= htmlspecialchars($book['trainer_name'] ?? '미배정') ?></td>
                    <td><?= htmlspecialchars($book['book_date']) ?></td>
                    <td><?= $book['trainer_no'] ? '완료' : '대기' ?></td>
                    <td>
                        <button class="btn-delete-small" onclick="deleteBook(<?= $book['no'] ?>, '<?= htmlspecialchars($book['member_name']) ?>')">삭제</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="no-data">
        <p>등록된 상담예약이 없습니다.</p>
    </div>
    <?php endif; ?>

    <!-- 버튼 박스 -->
    <div class="admin-button-box">
        <button class="btn-large btn-secondary" onclick="history.back()">이전으로</button>
        <button class="btn-large btn-cancel" onclick="location.href='admin_dashboard.php'">관리자 페이지</button>
    </div>
</section>

<?php include 'footer.php'; ?>

<script>
function deleteBook(bookNo, memberName) {
    if (!confirm(memberName + '님의 예약을 삭제하시겠습니까?\n삭제된 데이터는 복구할 수 없습니다.')) {
        return;
    }

    if (!confirm('정말로 삭제하시겠습니까?')) {
        return;
    }

    $.ajax({
        type: 'POST',
        url: 'asset/controller/delete_book_admin.php',
        data: {
            book_no: bookNo
        },
        dataType: 'json',
        success: function(res) {
            if (res.succ) {
                alert('예약이 삭제되었습니다.');
                location.reload();
            } else {
                alert(res.message || '삭제 중 오류가 발생했습니다.');
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
