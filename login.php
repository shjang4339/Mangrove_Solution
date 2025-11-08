    <?php include 'header.php'; ?>

    <section class="login-container">
        <!-- 로고 -->
        <div class="login-logo">
            <img src="asset/image/logo.svg" alt="Mangrove Solution 로고"/>
        </div>

        <!-- 로그인 폼 -->
        <div class="login-form">
            <label>
                <span class="ntnll">아이디</span>
                <input id="login_id" type="text" placeholder="아이디를 입력하세요"/>
            </label>
            <label>
                <span class="ntnll">비밀번호</span>
                <input id="login_password" type="password" placeholder="비밀번호를 입력하세요"/>
            </label>

            <!-- 로그인 버튼 그룹 -->
            <div class="login-buttons">
                <button class="btn-large btn-primary" id="btn-member-login">고객 로그인</button>
                <button class="btn-large btn-secondary" id="btn-trainer-login">트레이너 로그인</button>
                <button class="btn-large btn-cancel" onclick="location.href='index.php'">취소</button>
            </div>
        </div>
    </section>

    <?php include 'footer.php'; ?>

    <script>
        // URL 파라미터에서 type 가져오기
        const urlParams = new URLSearchParams(window.location.search);
        const loginType = urlParams.get('type');

        // 페이지 로드 시 type에 따라 버튼 강조 및 자동 선택
        if (loginType === 'trainer') {
            $('#btn-trainer-login').addClass('active');
            $('#btn-member-login').hide();
        } else if (loginType === 'member') {
            $('#btn-member-login').addClass('active');
            $('#btn-trainer-login').hide();
        } else {
            // type이 없으면 둘 다 표시
            $('#btn-member-login').addClass('active');
        }

        // 고객 로그인
        $('#btn-member-login').on('click', function() {
            let id = $('#login_id').val().trim();
            let password = $('#login_password').val();

            if (!id) {
                alert('아이디를 입력해주세요.');
                $('#login_id').focus();
                return;
            }

            if (!password) {
                alert('비밀번호를 입력해주세요.');
                $('#login_password').focus();
                return;
            }

            $.ajax({
                url: 'asset/controller/login.php',
                type: 'POST',
                data: {
                    id: id,
                    password: password,
                    type: 'member'
                },
                dataType: 'json',
                success: function(response) {
                    if (response.succ) {
                        alert('로그인 되었습니다.');
                        window.location.href = 'index.php';
                    } else {
                        alert('로그인 실패: ' + response.message);
                    }
                },
                error: function() {
                    alert('서버와의 통신에 실패했습니다.');
                }
            });
        });

        // 트레이너 로그인
        $('#btn-trainer-login').on('click', function() {
            let id = $('#login_id').val().trim();
            let password = $('#login_password').val();

            if (!id) {
                alert('아이디를 입력해주세요.');
                $('#login_id').focus();
                return;
            }

            if (!password) {
                alert('비밀번호를 입력해주세요.');
                $('#login_password').focus();
                return;
            }

            $.ajax({
                url: 'asset/controller/login.php',
                type: 'POST',
                data: {
                    id: id,
                    password: password,
                    type: 'trainer'
                },
                dataType: 'json',
                success: function(response) {
                    if (response.succ) {
                        alert('로그인 되었습니다.');
                        window.location.href = 'index.php';
                    } else {
                        alert('로그인 실패: ' + response.message);
                    }
                },
                error: function() {
                    alert('서버와의 통신에 실패했습니다.');
                }
            });
        });

        // 엔터키로 로그인 (현재 활성화된 버튼으로)
        $('#login_id, #login_password').on('keypress', function(e) {
            if (e.which === 13) {
                if ($('#btn-trainer-login').hasClass('active')) {
                    $('#btn-trainer-login').click();
                } else {
                    $('#btn-member-login').click();
                }
            }
        });
    </script>
</body>
</html>
