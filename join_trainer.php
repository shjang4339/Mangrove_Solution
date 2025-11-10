        <?php include 'header.php'; ?>
        
        <?php
        $query = "select * from disease_code";
        $stmt = $pdo -> prepare($query);
        $stmt -> execute();
        $disease_result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $query = "select * from region";
        $stmt = $pdo -> prepare($query);
        $stmt -> execute();
        $region_result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        ?>

        <section>
            <div class="title-wrap">
                <div class="title">
                    <div>
                        <h1>트레이너 회원가입</h1>
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
                <label>
                    <span class="ntnll">학위 / 전공명</span>
                    <input id="major" type="text" value=""/>
                </label>
                <label>
                    <span class="ntnll">대표자격증</span>
                    <input id="license" type="text" value=""/>
                </label>
                <label>
                    <span>추가 자격증1</span>
                    <input id="sublicense1" type="text" value=""/>
                </label>
                <label>
                    <span>추가 자격증2</span>
                    <input id="sublicense2" type="text" value=""/>
                </label>
                <label>
                    <span>추가 자격증3</span>
                    <input id="sublicense3" type="text" value=""/>
                </label>
                <div>
                    <span class="ntnll">사진</span>
                    <div style="display: flex; flex-direction: column; gap: 10px; margin-top: 10px;">
                        <input type="file" id="trainerImage" name="image" accept="image/jpeg,image/jpg,image/png" style="max-width: 400px;" />
                        <span id="imagePreview" style="color: #666; font-size: 14px;"></span>
                    </div>
                </div>
                <div>
                    <span class="ntnll">가능한 트레이닝 지역</span>
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
                <div>
                    <span>전문 분야</span>
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
                
                <label>
                    <span class="ntnll">트레이너 인삿말</span>
                    <textarea id="greet" rows="4" style="width: 100%; max-width: 400px; padding: 10px; border-radius: 10px;"></textarea>
                </label>

                <div class="my-info-button-box">
                    <button class="btn-large btn-primary confirm">가입하기</button>
                    <button class="btn-large btn-cancel" onclick="location.href='index.php'">가입취소</button>
                </div>
            </div>
            

        </section>
        <?php include 'footer.php'; ?>
        <script>
            $(document).ready(function() {
                // 이메일 도메인 선택 변경 이벤트
                $('#emailDomain').on('change', function() {
                    if ($(this).val() === 'direct') {
                        $('#emailDomainDirect').show();
                    } else {
                        $('#emailDomainDirect').hide().val('');
                    }
                });
            });

            // 이미지 파일 선택 시 미리보기
            $('#trainerImage').on('change', function() {
                let file = this.files[0];

                if (!file) {
                    $('#imagePreview').text('');
                    return;
                }

                // 파일 크기 확인 (5MB)
                if (file.size > 5 * 1024 * 1024) {
                    alert('파일 크기는 5MB 이하여야 합니다.');
                    $(this).val('');
                    $('#imagePreview').text('');
                    return;
                }

                // 파일 타입 확인
                let allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                if (!allowedTypes.includes(file.type)) {
                    alert('이미지 파일만 업로드 가능합니다. (JPG, PNG)');
                    $(this).val('');
                    $('#imagePreview').text('');
                    return;
                }

                // 선택된 파일 표시
                $('#imagePreview').text('선택된 파일: ' + file.name).css('color', '#666');
            });

            $('.my-info-button-box > .confirm').on('click', function() {
                let id = $('#id').val().trim();
                let password = $('#password').val();
                let password_check = $('#password_check').val();
                let name = $('#name').val().trim();
                let phone = $('#phone').val().trim();
                let emailId = $('#emailId').val().trim();
                let emailDomain = $('#emailDomain').val();
                let emailDomainDirect = $('#emailDomainDirect').val().trim();
                let major = $('#major').val().trim();
                let license = $('#license').val().trim();
                let sublicense1 = $('#sublicense1').val().trim();
                let sublicense2 = $('#sublicense2').val().trim();
                let sublicense3 = $('#sublicense3').val().trim();
                let greet = $('#greet').val().trim();

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

                // 이메일 ID 필수 확인
                if (!emailId) {
                    alert('이메일 주소를 입력해주세요.');
                    $('#emailId').focus();
                    return;
                }

                // 이메일 도메인 선택 확인
                if (!emailDomain) {
                    alert('이메일 도메인을 선택해주세요.');
                    $('#emailDomain').focus();
                    return;
                }

                // 직접 입력 선택 시 도메인 입력 확인
                if (emailDomain === 'direct' && !emailDomainDirect) {
                    alert('이메일 도메인을 입력해주세요.');
                    $('#emailDomainDirect').focus();
                    return;
                }

                // 최종 이메일 조합
                let finalDomain = emailDomain === 'direct' ? emailDomainDirect : emailDomain;
                let email = emailId + '@' + finalDomain;

                if (!major) {
                    alert('학위/전공명을 입력해주세요.');
                    $('#major').focus();
                    return;
                }

                if (!license) {
                    alert('대표자격증을 입력해주세요.');
                    $('#license').focus();
                    return;
                }

                // 트레이닝 지역 라디오 버튼 검증
                let region_code = $('.region_check:checked').val();

                if (!region_code) {
                    alert('가능한 트레이닝 지역을 선택해주세요.');
                    return;
                }

                if (!greet) {
                    alert('트레이너 인삿말을 입력해주세요.');
                    $('#greet').focus();
                    return;
                }

                // 이미지 파일 확인
                let imageFile = $('#trainerImage')[0].files[0];
                if (!imageFile) {
                    alert('트레이너 사진을 선택해주세요.');
                    return;
                }

                let disease_code = $('.disease_check:checked').map(function() {
                    return $(this).val();
                }).get().join(',');

                // 1단계: 이미지 업로드
                let formData = new FormData();
                formData.append('image', imageFile);

                $.ajax({
                    url: 'asset/controller/upload_trainer_image.php',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    async: false,
                    dataType: 'json',
                    success: function(uploadResponse) {
                        if (uploadResponse.succ) {
                            // 2단계: 이미지 업로드 성공 시 회원가입 진행
                            $.ajax({
                                url: 'asset/controller/insert_join_trainer.php',
                                data: {
                                    id: id,
                                    password: password,
                                    name: name,
                                    phone: phone,
                                    email: email,
                                    major: major,
                                    image: uploadResponse.image_name,
                                    license: license,
                                    sublicense_1: sublicense1,
                                    sublicense_2: sublicense2,
                                    sublicense_3: sublicense3,
                                    region: region_code,
                                    greet: greet,
                                    disease_code: disease_code
                                },
                                type: "POST",
                                async: false,
                                dataType: "json",
                                success: function(data){
                                    if(data.succ) {
                                        alert('트레이너 회원가입이 완료되었습니다.');
                                        window.location.href = 'index.php';
                                    } else {
                                        alert('회원가입 중 오류가 발생했습니다: ' + data.message);
                                    }
                                },
                                error: function() {
                                    alert('서버와의 통신에 실패했습니다.');
                                }
                            });
                        } else {
                            alert('이미지 업로드 실패: ' + uploadResponse.message);
                        }
                    },
                    error: function() {
                        alert('이미지 업로드 중 오류가 발생했습니다.');
                    }
                });

            })
        </script>
    </body>
</html>