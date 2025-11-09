<?php include 'header.php'; ?>

<?php
// 로그인 확인 (트레이너만 접근 가능)
if (!$is_logged_in || $user_type !== 'trainer') {
    echo "<script>alert('트레이너 로그인이 필요합니다.'); location.href='login.php?type=trainer';</script>";
    exit;
}

// 지역 코드를 이름으로 변환하는 함수
function getRegionName($pdo, $region_code) {
    if (empty($region_code)) return '';
    $query = "SELECT name FROM region WHERE no = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute(array($region_code));
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['name'] : '';
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

// 회원 상담 예정 리스트 조회 (is_meet = 0)
$member_query = "SELECT b.no, b.book_date, m.name, m.phone, m.region, m.disease_code
                 FROM book b
                 JOIN member m ON b.member_no = m.no
                 WHERE b.trainer_no = ? AND b.is_meet = 0
                 ORDER BY b.book_date DESC";
$member_stmt = $pdo->prepare($member_query);
$member_stmt->execute(array($_SESSION['user_no']));
$member_books = $member_stmt->fetchAll(PDO::FETCH_ASSOC);
$member_count = count($member_books);

// 비회원 상담 예정 리스트 조회 (trainer_no가 할당되고 is_meet = 0인 것만)
$unlogin_query = "SELECT no, name, phone, region, disease_code, insert_date
                  FROM unlogin_book
                  WHERE trainer_no = ? AND is_meet = 0
                  ORDER BY insert_date DESC";
$unlogin_stmt = $pdo->prepare($unlogin_query);
$unlogin_stmt->execute(array($_SESSION['user_no']));
$unlogin_books = $unlogin_stmt->fetchAll(PDO::FETCH_ASSOC);
$unlogin_count = count($unlogin_books);

// 총 상담 예정 수
$total_count = $member_count + $unlogin_count;

// 회원 상담 완료 리스트 조회 (is_meet = 1)
$member_complete_query = "SELECT b.no, b.book_date, m.name, m.phone, m.region, m.disease_code
                          FROM book b
                          JOIN member m ON b.member_no = m.no
                          WHERE b.trainer_no = ? AND b.is_meet = 1
                          ORDER BY b.book_date DESC";
$member_complete_stmt = $pdo->prepare($member_complete_query);
$member_complete_stmt->execute(array($_SESSION['user_no']));
$member_complete_books = $member_complete_stmt->fetchAll(PDO::FETCH_ASSOC);
$member_complete_count = count($member_complete_books);

// 비회원 상담 완료 리스트 조회 (is_meet = 1)
$unlogin_complete_query = "SELECT no, name, phone, region, disease_code, insert_date
                           FROM unlogin_book
                           WHERE trainer_no = ? AND is_meet = 1
                           ORDER BY insert_date DESC";
$unlogin_complete_stmt = $pdo->prepare($unlogin_complete_query);
$unlogin_complete_stmt->execute(array($_SESSION['user_no']));
$unlogin_complete_books = $unlogin_complete_stmt->fetchAll(PDO::FETCH_ASSOC);
$unlogin_complete_count = count($unlogin_complete_books);

// 총 상담 완료 수
$total_complete_count = $member_complete_count + $unlogin_complete_count;
?>

<section class="trainer-book-list-container">
    <!-- 로고 -->
    <div class="trainer-book-list-logo">
        <img src="asset/image/logo.svg" alt="Mangrove Solution 로고"/>
    </div>

    <!-- 타이틀 -->
    <h1 class="trainer-book-list-title">상담 예약 관리</h1>

    <!-- 총 상담 예정 수 -->
    <div class="total-booking-count">
        <p>총 상담 예정 수: <strong><?= $total_count ?></strong> 건</p>
    </div>

    <!-- 회원 상담 예정 리스트 -->
    <div class="booking-section-wrapper">
        <h2 class="section-title">회원 상담 예정 리스트 (<strong><?= $member_count ?></strong>건)</h2>

        <?php if (!empty($member_books)): ?>
        <div class="booking-table-wrapper">
            <table class="booking-table">
                <thead>
                    <tr>
                        <th>번호</th>
                        <th>예약일시</th>
                        <th>이름</th>
                        <th>전화번호</th>
                        <th>거주지</th>
                        <th>질병/증상</th>
                        <th>관리</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($member_books as $index => $book): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($book['book_date']) ?></td>
                        <td><?= htmlspecialchars($book['name']) ?></td>
                        <td><?= htmlspecialchars($book['phone']) ?></td>
                        <td><?= htmlspecialchars(getRegionName($pdo, $book['region'])) ?></td>
                        <td><?= htmlspecialchars(getDiseaseNames($pdo, $book['disease_code'])) ?></td>
                        <td>
                            <button class="btn-complete" onclick="completeBooking('member', <?= $book['no'] ?>, '<?= htmlspecialchars($book['name']) ?>')">완료</button>
                            <button class="btn-delete" onclick="deleteBooking('member', <?= $book['no'] ?>, '<?= htmlspecialchars($book['name']) ?>')">삭제</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="no-data">
            <p>회원 상담 예정이 없습니다.</p>
        </div>
        <?php endif; ?>
    </div>

    <!-- 비회원 상담 예정 리스트 -->
    <div class="booking-section-wrapper">
        <h2 class="section-title">비회원 상담 예정 리스트 (<strong><?= $unlogin_count ?></strong>건)</h2>

        <?php if (!empty($unlogin_books)): ?>
        <div class="booking-table-wrapper">
            <table class="booking-table">
                <thead>
                    <tr>
                        <th>번호</th>
                        <th>신청일시</th>
                        <th>이름</th>
                        <th>전화번호</th>
                        <th>거주지</th>
                        <th>질병/증상</th>
                        <th>관리</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($unlogin_books as $index => $book): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($book['insert_date']) ?></td>
                        <td><?= htmlspecialchars($book['name']) ?></td>
                        <td><?= htmlspecialchars($book['phone']) ?></td>
                        <td><?= htmlspecialchars(getRegionName($pdo, $book['region'])) ?></td>
                        <td><?= htmlspecialchars(getDiseaseNames($pdo, $book['disease_code'])) ?></td>
                        <td>
                            <button class="btn-complete" onclick="completeBooking('unlogin', <?= $book['no'] ?>, '<?= htmlspecialchars($book['name']) ?>')">완료</button>
                            <button class="btn-delete" onclick="deleteBooking('unlogin', <?= $book['no'] ?>, '<?= htmlspecialchars($book['name']) ?>')">삭제</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="no-data">
            <p>비회원 상담 예정이 없습니다.</p>
        </div>
        <?php endif; ?>
    </div>

    <!-- 총 상담 완료 수 -->
    <div class="total-booking-count" style="margin-top:40px;">
        <p>총 상담 완료 수: <strong><?= $total_complete_count ?></strong> 건</p>
    </div>

    <!-- 회원 상담 완료 리스트 -->
    <div class="booking-section-wrapper">
        <h2 class="section-title">회원 상담 완료 리스트 (<strong><?= $member_complete_count ?></strong>건)</h2>

        <?php if (!empty($member_complete_books)): ?>
        <div class="booking-table-wrapper">
            <table class="booking-table">
                <thead>
                    <tr>
                        <th>번호</th>
                        <th>예약일시</th>
                        <th>이름</th>
                        <th>전화번호</th>
                        <th>거주지</th>
                        <th>질병/증상</th>
                        <th>관리</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($member_complete_books as $index => $book): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($book['book_date']) ?></td>
                        <td><?= htmlspecialchars($book['name']) ?></td>
                        <td><?= htmlspecialchars($book['phone']) ?></td>
                        <td><?= htmlspecialchars(getRegionName($pdo, $book['region'])) ?></td>
                        <td><?= htmlspecialchars(getDiseaseNames($pdo, $book['disease_code'])) ?></td>
                        <td>
                            <button class="btn-delete" onclick="deleteBooking('member', <?= $book['no'] ?>, '<?= htmlspecialchars($book['name']) ?>')">삭제</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="no-data">
            <p>회원 상담 완료 내역이 없습니다.</p>
        </div>
        <?php endif; ?>
    </div>

    <!-- 비회원 상담 완료 리스트 -->
    <div class="booking-section-wrapper">
        <h2 class="section-title">비회원 상담 완료 리스트 (<strong><?= $unlogin_complete_count ?></strong>건)</h2>

        <?php if (!empty($unlogin_complete_books)): ?>
        <div class="booking-table-wrapper">
            <table class="booking-table">
                <thead>
                    <tr>
                        <th>번호</th>
                        <th>신청일시</th>
                        <th>이름</th>
                        <th>전화번호</th>
                        <th>거주지</th>
                        <th>질병/증상</th>
                        <th>관리</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($unlogin_complete_books as $index => $book): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($book['insert_date']) ?></td>
                        <td><?= htmlspecialchars($book['name']) ?></td>
                        <td><?= htmlspecialchars($book['phone']) ?></td>
                        <td><?= htmlspecialchars(getRegionName($pdo, $book['region'])) ?></td>
                        <td><?= htmlspecialchars(getDiseaseNames($pdo, $book['disease_code'])) ?></td>
                        <td>
                            <button class="btn-delete" onclick="deleteBooking('unlogin', <?= $book['no'] ?>, '<?= htmlspecialchars($book['name']) ?>')">삭제</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="no-data">
            <p>비회원 상담 완료 내역이 없습니다.</p>
        </div>
        <?php endif; ?>
    </div>

    <!-- 버튼 박스 -->
    <div class="trainer-book-list-button-box">
        <button class="btn-large btn-primary" onclick="location.href='trainer_book.php'">상담 관리로 돌아가기</button>
        <button class="btn-large btn-secondary" onclick="location.href='index.php'">처음으로</button>
    </div>
</section>

<?php include 'footer.php'; ?>

<script>
    function completeBooking(type, bookingNo, name) {
        if (!confirm(name + '님의 상담을 완료 처리하시겠습니까?')) {
            return;
        }

        $.ajax({
            url: 'asset/controller/complete_booking.php',
            type: 'POST',
            data: {
                type: type,
                booking_no: bookingNo
            },
            dataType: 'json',
            async: false,
            success: function(response) {
                if (response.succ) {
                    alert('상담이 완료 처리되었습니다.');
                    location.reload();
                } else {
                    alert('완료 처리 실패: ' + response.message);
                }
            },
            error: function() {
                alert('서버와의 통신에 실패했습니다.');
            }
        });
    }

    function deleteBooking(type, bookingNo, name) {
        if (!confirm(name + '님의 예약을 삭제하시겠습니까?')) {
            return;
        }

        $.ajax({
            url: 'asset/controller/delete_trainer_booking.php',
            type: 'POST',
            data: {
                type: type,
                booking_no: bookingNo
            },
            dataType: 'json',
            async: false,
            success: function(response) {
                if (response.succ) {
                    alert('예약이 삭제되었습니다.');
                    location.reload();
                } else {
                    alert('삭제 실패: ' + response.message);
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
