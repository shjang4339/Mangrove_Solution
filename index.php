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
            <h1 class="main-title">방문 재활운동 서비스</h1>

            <!-- 서브 타이틀 -->
            <p class="main-subtitle">장애 아동, 성인 장애인, 질환 및 희귀질환 전문 재활 운동 서비스 제공</p>

            <!-- 상담 예약 버튼 -->
            <div class="booking-section">
                <button class="btn-large btn-booking" onclick="location.href='book.php'">상담 예약</button>
            </div>

            <!-- 로그인 섹션 -->
            <div class="login-section">
                <button class="btn-large btn-secondary" onclick="location.href='join_member.php'">회원가입</button>
                <button class="btn-large btn-primary" onclick="location.href='login.php?type=member'">로그인</button>
            </div>

            <!-- 신규 재활 운동 트레이너 -->
            <div class="trainer-header">
                <h2>신규 재활 운동 트레이너</h2>
                <button class="btn-trainer-login" onclick="location.href='login.php?type=trainer'">트레이너 로그인</button>
            </div>

            <!-- 트레이너 스와이퍼 -->
            <div class="swiper trainerSwiper">
                <div class="swiper-wrapper">
                    <?php foreach ($trainers as $trainer): ?>
                    <div class="swiper-slide">
                        <div class="trainer-card">
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
                <h3>후원사</h3>
                <div class="sponsor-logos">
                    <div class="sponsor-logo">
                        <!-- 후원사 로고 1 -->
                        <img src="asset/image/sponsor1.png" alt="후원사 1" onerror="this.style.display='none'">
                    </div>
                    <div class="sponsor-logo">
                        <!-- 후원사 로고 2 -->
                        <img src="asset/image/sponsor2.png" alt="후원사 2" onerror="this.style.display='none'">
                    </div>
                </div>
            </div>
        </section>

        <?php include 'footer.php'; ?>

        <script>
            // Swiper 초기화
            const swiper = new Swiper('.trainerSwiper', {
                // 가로 방향 슬라이드
                direction: 'horizontal',

                // 드래그/스와이프 활성화
                grabCursor: true,
                touchRatio: 1,
                touchAngle: 45,

                // 슬라이드 설정
                slidesPerView: 1,
                spaceBetween: 20,
                loop: true,

                // 네비게이션 버튼
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },

                // 페이지네이션
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },

                // 반응형 브레이크포인트
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
                        slidesPerView: 4,
                        spaceBetween: 30,
                    },
                }
            });
        </script>
    </body>
</html>