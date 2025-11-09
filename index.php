        <?php include 'header.php'; ?>

        <?php
        // 최신 트레이너 6명 조회
        $query = "SELECT no, name, image, major, region FROM trainer ORDER BY no DESC LIMIT 6";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $trainers = $stmt->fetchAll(PDO::FETCH_ASSOC);
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

            <!-- 상담 예약/관리 버튼 -->
            <div class="booking-section">
                <?php if ($is_logged_in && $user_type === 'trainer'): ?>
                    <button class="btn-large btn-booking" onclick="location.href='trainer_book.php'">상담 관리</button>
                <?php else: ?>
                    <button class="btn-large btn-booking" onclick="location.href='book.php'">상담 예약</button>
                <?php endif; ?>
            </div>

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
        </script>
    </body>
</html>