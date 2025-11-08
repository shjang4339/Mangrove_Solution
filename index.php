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
        </script>
    </body>
</html>