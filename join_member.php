        <?php include 'header.php'; ?>

        <?php
        // 질환 코드 조회
        $query = "select * from disease_code";
        $stmt = $pdo -> prepare($query);
        $stmt -> execute();
        $disease_result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 지역 코드 조회
        $query = "select * from region";
        $stmt = $pdo -> prepare($query);
        $stmt -> execute();
        $region_result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        ?>

        <section>
            <div class="title-wrap">
                <div class="title">
                    <div>
                        <h1>회원가입</h1>
                        <p>회원이 되어 다양한 혜택을 경험해 보세요!</p>
                    </div>
                    <img class="small-logo" src="asset/image/logo.svg" alt="Mangrove Solution 로고"/>
                </div>
                <label>
                    <span class="ntnll">아이디</span>
                    <input id="id" type="text" value=""/>
                </label>
                <label>
                    <span class="ntnll">비밀번호</span>
                    <input id="password" type="password" value=""/>
                </label>
                <label>
                    <span class="ntnll">비밀번호 확인</span>
                    <input id="password_check" type="password" value=""/>
                </label>
                <label>
                    <span class="ntnll">이름</span>
                    <input id="name" type="text" value=""/>
                </label>
                <label>
                    <span class="ntnll">전화번호</span>
                    <input id="phone" type="text" value=""/>
                </label>
                <div>
                    <span class="ntnll">거주지</span>
                    <div class="region-box">
                    <?php
                    for ($i=0; $i<count($region_result); $i++) :
                        ?>
                        <label>
                            <input class="region_check" type="radio" name="region" value="<?=$region_result[$i]['no']?>"/>
                            <?=$region_result[$i]['name']?>
                        </label>
                        <?php
                    endfor;
                    ?>
                    </div>
                </div>
                <label>
                    <span class="ntnll">생년월일</span>
                    <input id="birth" type="date" value=""/>
                </label>
                <div>
                    <span class="ntnll">이메일 주소</span>
                    <div style="display:flex; align-items:center; gap:10px; margin-top:10px; flex-wrap: wrap;">
                        <input id="emailId" type="text" style="flex:1; padding:15px 20px; font-size:16px; border:1px solid #ddd; border-radius:10px; min-width: 150px;"/>

                        <span style="font-size:18px; color:#000;">@</span>

                        <select id="emailDomain" style="flex:1; padding:15px 20px; font-size:16px; border:1px solid #ddd; border-radius:10px; background:#fff; cursor:pointer;">
                            <option value="">선택하세요</option>
                            <option value="naver.com">naver.com</option>
                            <option value="nate.com">nate.com</option>
                            <option value="gmail.com">gmail.com</option>
                            <option value="kakao.com">kakao.com</option>
                            <option value="direct">직접 입력</option>
                        </select>
                        <input id="emailDomainDirect" type="text" placeholder="도메인 입력" style="flex:1; padding:15px 20px; font-size:16px; border:1px solid #ddd; border-radius:10px; display:none;"/>
                    </div>
                </div>
                <div>
                    <span>질환 / 장애 유무</span>
                    <div class="disease-box">

                    <?php
                    for ($i=0; $i<count($disease_result); $i++) :
                        ?>
                        <label>
                            <input class="disease_check" type="checkbox" value="<?=$disease_result[$i]['no']?>"/>
                            <?=$disease_result[$i]['name']?>
                        </label>
                        <?php
                    endfor;
                    ?>
                    
                    </div>
                </div>

                <div class="my-info-button-box">
                    <button class="btn-large btn-primary confirm">가입하기</button>
                    <button class="btn-large btn-cancel" onclick="location.href='index.php'">가입취소</button>
                </div>
            </div>
            

        </section>
        <?php include 'footer.php'; ?>
        <script>
            $(document).ready(function() {
                // 도메인 선택 변경 이벤트
                $('#emailDomain').on('change', function() {
                    if ($(this).val() === 'direct') {
                        $('#emailDomainDirect').show();
                    } else {
                        $('#emailDomainDirect').hide().val('');
                    }
                });
            });

            $('.confirm').on('click', function() {
                let id = $('#id').val().trim();
                let password = $('#password').val();
                let password_check = $('#password_check').val();
                let name = $('#name').val().trim();
                let phone = $('#phone').val().trim();
                let birth = $('#birth').val();
                let emailId = $('#emailId').val().trim();
                let emailDomain = $('#emailDomain').val();
                let emailDomainDirect = $('#emailDomainDirect').val().trim();

                // 필수 입력 검증
                if (!id) {
                    alert('아이디를 입력해주세요.');
                    $('#id').focus();
                    return;
                }

                if (!password) {
                    alert('비밀번호를 입력해주세요.');
                    $('#password').focus();
                    return;
                }

                if (!password_check) {
                    alert('비밀번호 확인을 입력해주세요.');
                    $('#password_check').focus();
                    return;
                }

                // 비밀번호 일치 검증
                if (password !== password_check) {
                    alert('비밀번호가 일치하지 않습니다.');
                    $('#password_check').focus();
                    return;
                }

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

                // 거주지 라디오 버튼 검증
                let region_code = $('.region_check:checked').val();

                if (!region_code) {
                    alert('거주지를 선택해주세요.');
                    return;
                }

                if (!birth) {
                    alert('생년월일을 입력해주세요.');
                    $('#birth').focus();
                    return;
                }

                if (!emailId) {
                    alert('이메일 아이디를 입력해주세요.');
                    $('#emailId').focus();
                    return;
                }

                if (!emailDomain) {
                    alert('이메일 도메인을 선택해주세요.');
                    $('#emailDomain').focus();
                    return;
                }

                if (emailDomain === 'direct' && !emailDomainDirect) {
                    alert('이메일 도메인을 입력해주세요.');
                    $('#emailDomainDirect').focus();
                    return;
                }

                // 최종 이메일 조합
                let finalDomain = emailDomain === 'direct' ? emailDomainDirect : emailDomain;
                let email = emailId + '@' + finalDomain;

                let disease_code = $('.disease_check:checked').map(function() {
                    return $(this).val();
                }).get().join(',');

                let is_disease = Number(disease_code.length > 0);

                $.ajax({
                    url: 'asset/controller/insert_join_member.php',
                    data: {
                        id: id,
                        password: password,
                        name: name,
                        phone: phone,
                        region: region_code,
                        birth: birth,
                        email: email,
                        is_disease: is_disease,
                        disease_code: disease_code
                    },
                    type: "POST",
                    async: false,
                    dataType: "json",
                    success: function(data){
                        if(data.succ) {
                            alert('회원가입이 완료되었습니다.');
                            window.location.href = 'index.php';
                        } else {
                            alert('오류가 발생했습니다. 다시 시도해주세요.');
                        }
                    },
                    error: function() {
                        alert('서버와의 통신에 실패했습니다.');
                    }
                });

            })
        </script>
    </body>
</html>