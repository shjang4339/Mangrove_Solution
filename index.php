        <?php include 'header.php'; ?>

        <?php
        // 최신 트레이너 6명 조회 (admin 제외, 승인된 트레이너만)
        $query = "SELECT no, name, image, major, region FROM trainer WHERE id != 'admin' AND is_confirm = 1 ORDER BY no DESC LIMIT 6";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $trainers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 회원 로그인 시 예약 정보 조회
        $booking_count = 0;
        $booked_trainers = [];
        if ($is_logged_in && $user_type === 'member') {
            // 현재 회원의 상담 예약 건수 조회 (대기중인 예약만)
            $query = "SELECT COUNT(*) as booking_count FROM book WHERE member_no = ? AND is_meet = 0";
            $stmt = $pdo->prepare($query);
            $stmt->execute(array($_SESSION['user_no']));
            $booking_info = $stmt->fetch(PDO::FETCH_ASSOC);
            $booking_count = $booking_info['booking_count'] ?? 0;

            // 예약된 트레이너 목록 조회
            $query = "SELECT b.no as book_no, t.no, t.name, t.image, t.major, t.region, t.greet, t.license, t.sublicense_1, t.sublicense_2, t.sublicense_3
                      FROM book b
                      JOIN trainer t ON b.trainer_no = t.no
                      WHERE b.member_no = ? AND b.is_meet = 0
                      ORDER BY b.book_date DESC";
            $stmt = $pdo->prepare($query);
            $stmt->execute(array($_SESSION['user_no']));
            $booked_trainers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // 트레이너 로그인 시 예약 정보 조회
        $member_book_count = 0;
        $unlogin_book_count = 0;
        $unlogin_book_wait_count = 0;
        if ($is_logged_in && $user_type === 'trainer') {
            // 트레이너가 담당하는 회원 예약 건수 조회 (is_meet = 0)
            $member_book_query = "SELECT COUNT(*) as count FROM book WHERE trainer_no = ? AND is_meet = 0";
            $member_book_stmt = $pdo->prepare($member_book_query);
            $member_book_stmt->execute(array($_SESSION['user_no']));
            $member_book_result = $member_book_stmt->fetch(PDO::FETCH_ASSOC);
            $member_book_count = $member_book_result['count'] ?? 0;

            // 비회원 예약 리스트 건수 조회 (trainer_no가 할당되고 is_meet = 0인 것만)
            $unlogin_book_query = "SELECT COUNT(*) as count FROM unlogin_book WHERE trainer_no = ? AND is_meet = 0";
            $unlogin_book_stmt = $pdo->prepare($unlogin_book_query);
            $unlogin_book_stmt->execute(array($_SESSION['user_no']));
            $unlogin_book_result = $unlogin_book_stmt->fetch(PDO::FETCH_ASSOC);
            $unlogin_book_count = $unlogin_book_result['count'] ?? 0;

            // 비회원 상담 대기 리스트 건수 조회 (trainer_no IS NULL)
            $unlogin_book_query = "SELECT COUNT(*) as count FROM unlogin_book WHERE trainer_no IS NULL";
            $unlogin_book_stmt = $pdo->prepare($unlogin_book_query);
            $unlogin_book_stmt->execute();
            $unlogin_book_wait_result = $unlogin_book_stmt->fetch(PDO::FETCH_ASSOC);
            $unlogin_book_wait_count = $unlogin_book_wait_result['count'] ?? 0;
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
        ?>

        <section class="main-container">
            <!-- 로고 -->
            <div class="main-logo">
                <img src="asset/image/logo.svg" alt="Mangrove Solution 로고"/>
            </div>

            <!-- 타이틀 -->
            <h1 class="main-title"><b>방문 재활운동</b> 서비스</h1>

            <!-- 서브 타이틀 -->
            <p class="main-subtitle">
                장애 아동, 성인 장애인, 질환 및 희귀질환자를<br class="mo">
                위한 전문 재활 운동을 제공합니다.
            </p>

            <!-- 상담 예약 버튼 (비로그인 시만) -->
            <?php if (!$is_logged_in): ?>
            <div class="booking-section">
                <button class="btn-large btn-booking" onclick="location.href='book.php'">상담 예약</button>
            </div>
            <?php endif; ?>

            <!-- 회원 로그인 시 예약 정보 표시 -->
            <?php if ($is_logged_in && $user_type === 'member'): ?>
            <!-- 환영 메시지 -->
            <div class="welcome-message">
                <h1><?= htmlspecialchars($user_name) ?> 회원님 <br class="mo">안녕하세요!</h1>
                <p>회원님에 맞는 트레이너를 찾으세요.</p>
            </div>

            <!-- 현재 상담 예약 건수 -->
            <div class="booking-count">
                <p>현재 상담예약: <strong><?= $booking_count ?></strong> 건</p>
            </div>

            <!-- 예약된 트레이너 목록 -->
            <?php if (!empty($booked_trainers)): ?>
            <div class="booked-trainers-container">
                <h2>예약된 트레이너</h2>
                <div class="booked-trainers-list">
                    <?php foreach ($booked_trainers as $trainer): ?>
                    <div class="booked-trainer-item" data-book-no="<?= $trainer['book_no'] ?>" data-trainer-name="<?= htmlspecialchars($trainer['name']) ?>">
                        <!-- 트레이너 이미지 -->
                        <div class="booked-trainer-image">
                            <?php if (!empty($trainer['image'])): ?>
                                <img src="image/<?= htmlspecialchars($trainer['image']) ?>" alt="<?= htmlspecialchars($trainer['name']) ?> 트레이너">
                            <?php else: ?>
                                <div class="no-image">No Image</div>
                            <?php endif; ?>
                        </div>

                        <!-- 트레이너 정보 -->
                        <div class="booked-trainer-info">
                            <h3><?= htmlspecialchars($trainer['name']) ?></h3>
                            <p class="trainer-details">
                                <?= htmlspecialchars($trainer['major']) ?> ·
                                <?= htmlspecialchars($trainer['license']) ?> ·
                                <?= htmlspecialchars($trainer['greet']) ?> ·
                                <?= htmlspecialchars(getRegionNames($pdo, $trainer['region'])) ?>
                            </p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- 버튼 박스 -->
            <div class="book-button-box">
                <button class="btn-book btn-secondary" onclick="location.href='all_trainers.php'">모든 트레이너 리스트</button>
                <button class="btn-book btn-primary" id="btn-nearby-trainer">집 근처 지역 트레이너 찾기</button>
                <button class="btn-book btn-tertiary" onclick="location.href='find_trainer.php'">나에게 맞는 트레이너 바로 찾기</button>
            </div>
            <?php endif; ?>

            <!-- 트레이너 로그인 시 예약 정보 표시 -->
            <?php if ($is_logged_in && $user_type === 'trainer'): ?>
            <!-- 환영 메시지 -->
            <div class="welcome-message">
                <h1><?= htmlspecialchars($user_name) ?> 트레이너님 <br class="mo">안녕하세요!</h1>
                <p>예약 관리 페이지입니다</p>
            </div>

            <!-- 담당 상담 예약 -->
            <div class="member-book-waiting" onclick="location.href='trainer_book_list.php'" style="cursor: pointer;">
                <h2>담당 상담 예약</h2>
                <p class="waiting-count"><strong><?= $member_book_count ?></strong> 건 / <strong><?= $unlogin_book_count ?></strong> 건</p>
                <p class="waiting-desc">클릭하여 회원 예약을 확인하세요</p>
            </div>

            <!-- 비회원 상담 대기 리스트 -->
            <div class="unlogin-book-waiting" onclick="location.href='unlogin_book_list.php'" style="cursor: pointer;">
                <h2>비회원 상담 대기 리스트</h2>
                <p class="waiting-count"><strong><?= $unlogin_book_wait_count ?></strong> 건</p>
                <p class="waiting-desc">클릭하여 대기 중인 비회원 예약을 확인하세요</p>
            </div>

            <!-- 버튼 박스 -->
            <div class="my-info-button-box">
                <button class="btn-large btn-primary" onclick="location.href='trainer_book_list.php'">예약 관리</button>
            </div>
            <?php endif; ?>

            <!-- 회원 유형 선택 섹션 (로그인하지 않은 경우만 표시) -->
            <?php if (!$is_logged_in): ?>
            <div class="user-type-section">
                <!-- 고객 박스 -->
                <div class="user-type-box">
                    <h3>고객</h3>
                    <p>재활 운동 서비스를 받고 싶으신가요?</p>
                    <div class="box-buttons">
                        <button class="btn-box btn-primary" onclick="location.href='join_member.php'">회원가입</button>
                        <button class="btn-box btn-secondary" onclick="location.href='login.php?type=member'">로그인</button>
                        <button class="btn-box btn-tertiary" onclick="location.href='unlogin_book_form.php'">비회원 전화예약</button>
                    </div>
                </div>

                <!-- 트레이너 박스 -->
                <div class="user-type-box">
                    <h3>트레이너</h3>
                    <p>재활 운동 전문가로 활동하고 싶으신가요?</p>
                    <div class="box-buttons">
                        <button class="btn-box btn-primary" onclick="location.href='join_trainer.php'">회원가입</button>
                        <button class="btn-box btn-secondary" onclick="location.href='login.php?type=trainer'">로그인</button>
                        <p>
                            * 트레이너 최종 회원 가입은<br>
                            경력 및 학위 등에 대한 진위 검토 후, 최종승인 됨.
                        </p>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- 신규 재활 운동 트레이너 -->
            <div class="trainer-header">
                <h2>신규 재활 운동 트레이너</h2>
            </div>

            <!-- 트레이너 스와이퍼 -->
            <div class="swiper gallery-swiper">
                <div class="swiper-wrapper">
                    <?php foreach ($trainers as $trainer): ?>
                    <div class="swiper-slide">
                        <div class="trainer-card" data-trainer-no="<?= $trainer['no'] ?>">
                            <div class="trainer-image">
                                <?php if (!empty($trainer['image'])): ?>
                                    <img src="image/<?= htmlspecialchars($trainer['image']) ?>" alt="<?= htmlspecialchars($trainer['name']) ?> 트레이너">
                                <?php else: ?>
                                    <div class="no-image">No Image</div>
                                <?php endif; ?>
                            </div>
                            <div class="trainer-info">
                                <h3><?= htmlspecialchars($trainer['name']) ?></h3>
                                <p class="trainer-major"><?= htmlspecialchars($trainer['major']) ?></p>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
                <div class="swiper-pagination"></div>
            </div>

            <!-- 후원사 섹션 -->
            <div class="sponsor-section">
                <h3>장애운동재활 전문센터</h3>
                <div class="sponsor-logos">
                    <div class="sponsor-item">
                        <div class="sponsor-logo">
                            <img src="asset/image/sponsor_1.svg" alt="칠드런스 드림 발달센터">
                        </div>
                        <div class="sponsor-info">
                            <p class="sponsor-category">장애아동 전문</p>
                            <h4 class="sponsor-name">칠드런스 드림</h4>
                            <p class="sponsor-subtitle">발달센터</p>
                        </div>
                    </div>
                    <div class="sponsor-item">
                        <div class="sponsor-logo">
                            <img src="asset/image/sponsor_2.svg" alt="Go Fit LAB">
                        </div>
                        <div class="sponsor-info">
                            <p class="sponsor-category">성인장애 전문</p>
                            <h4 class="sponsor-name">Go Fit</h4>
                            <p class="sponsor-subtitle">LAB</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <?php include 'footer.php'; ?>

        <script>
            // Swiper 초기화
            const swiper = new Swiper('.gallery-swiper', {
                slidesPerView: 1,
                spaceBetween: 20,
                loop: false,
                watchOverflow: true,
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
                breakpoints: {
                    640: {
                        slidesPerView: 2,
                        spaceBetween: 20,
                    },
                    768: {
                        slidesPerView: 3,
                        spaceBetween: 30,
                    },
                    1024: {
                        slidesPerView: 3,
                        spaceBetween: 30,
                    },
                }
            });

            // 트레이너 카드 클릭 이벤트
            $('.trainer-card').on('click', function() {
                const trainerNo = $(this).data('trainer-no');
                <?php if ($is_logged_in): ?>
                    // 로그인 상태: 트레이너 상세 페이지로 이동
                    location.href = 'trainer_detail.php?no=' + trainerNo;
                <?php else: ?>
                    // 비로그인 상태: 로그인 필요 안내 후 회원가입 페이지로 이동
                    if (confirm('트레이너 정보를 확인하려면 로그인이 필요합니다.\n고객 회원가입 페이지로 이동하시겠습니까?')) {
                        location.href = 'join_member.php';
                    }
                <?php endif; ?>
            });

            <?php if ($is_logged_in && $user_type === 'member'): ?>
            // 집 근처 지역 트레이너 찾기
            $('#btn-nearby-trainer').on('click', function() {
                // 회원의 거주지 정보를 가져와서 매칭되는 트레이너 검색
                $.ajax({
                    url: 'asset/controller/search_nearby_trainer.php',
                    type: 'POST',
                    dataType: 'json',
                    success: function(response) {
                        if (response.succ) {
                            // 검색 결과 페이지로 이동
                            location.href = 'search_trainer.php?type=nearby';
                        } else {
                            alert('트레이너 검색 중 오류가 발생했습니다.');
                        }
                    },
                    error: function() {
                        alert('서버와의 통신에 실패했습니다.');
                    }
                });
            });

            // 예약 취소 기능 - div 클릭 시
            $('.booked-trainer-item').on('click', function() {
                const bookNo = $(this).data('book-no');
                const trainerName = $(this).data('trainer-name');

                if (confirm(trainerName + '님 트레이너분께 요청한 상담을 취소하시겠습니까?')) {
                    $.ajax({
                        url: 'asset/controller/cancel_booking.php',
                        type: 'POST',
                        data: { book_no: bookNo },
                        dataType: 'json',
                        async: false,
                        success: function(response) {
                            if (response.succ) {
                                alert('예약이 취소되었습니다.');
                                location.reload();
                            } else {
                                alert(response.message || '예약 취소 중 오류가 발생했습니다.');
                            }
                        },
                        error: function() {
                            alert('서버와의 통신에 실패했습니다.');
                        }
                    });
                }
            });
            <?php endif; ?>
        </script>
    </body>
</html>