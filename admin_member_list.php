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

// 전체 고객 조회
$query = "SELECT * FROM member ORDER BY no DESC";
$stmt = $pdo->prepare($query);
$stmt->execute();
$members = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="admin-list-container">
    <!-- 로고 -->
    <div class="admin-logo">
        <img src="asset/image/logo.svg" alt="Mangrove Solution 로고"/>
    </div>

    <!-- 타이틀 -->
    <h1 class="admin-title">가입 고객 리스트</h1>
    <p class="admin-subtitle">총 <strong><?= count($members) ?></strong>명의 고객이 있습니다</p>

    <!-- 테이블 -->
    <?php if (!empty($members)): ?>
    <div class="admin-table-wrapper">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>번호</th>
                    <th>아이디</th>
                    <th>이름</th>
                    <th>전화번호</th>
                    <th>이메일</th>
                    <th>생년월일</th>
                    <th>가입일</th>
                    <th>관리</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($members as $index => $member): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= htmlspecialchars($member['id']) ?></td>
                    <td><?= htmlspecialchars($member['name']) ?></td>
                    <td><?= htmlspecialchars(formatPhoneNumber($member['phone'])) ?></td>
                    <td><?= htmlspecialchars($member['email']) ?></td>
                    <td><?= htmlspecialchars($member['birth']) ?></td>
                    <td><?= htmlspecialchars($member['date_account']) ?></td>
                    <td>
                        <button class="btn-edit-small" onclick="location.href='admin_edit_member.php?no=<?= $member['no'] ?>'">수정</button>
                        <button class="btn-delete-small" onclick="deleteMember(<?= $member['no'] ?>, '<?= htmlspecialchars($member['name']) ?>')">삭제</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="no-data">
        <p>등록된 고객이 없습니다.</p>
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
function deleteMember(memberNo, memberName) {
    if (!confirm(memberName + ' 고객을 삭제하시겠습니까?\n삭제된 데이터는 복구할 수 없습니다.')) {
        return;
    }

    if (!confirm('정말로 삭제하시겠습니까?')) {
        return;
    }

    $.ajax({
        type: 'POST',
        url: 'asset/controller/delete_member_admin.php',
        data: {
            member_no: memberNo
        },
        dataType: 'json',
        success: function(res) {
            if (res.succ) {
                alert('고객이 삭제되었습니다.');
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
