<?php include 'header.php'; ?>

<?php
// 로그인 확인 (트레이너만 접근 가능)
if (!$is_logged_in || $user_type !== 'trainer') {
    echo "<script>alert('트레이너 로그인이 필요합니다.'); location.href='login.php?type=trainer';</script>";
    exit;
}

// 트레이너 정보 조회
$query = "SELECT * FROM trainer WHERE no = ?";
$stmt = $pdo->prepare($query);
$stmt->execute(array($_SESSION['user_no']));
$trainer = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$trainer) {
    echo "<script>alert('트레이너 정보를 찾을 수 없습니다.'); history.back();</script>";
    exit;
}

// 질환 코드 조회
$query = "SELECT * FROM disease_code";
$stmt = $pdo->prepare($query);
$stmt->execute();
$disease_result = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 지역 코드 조회
$query = "SELECT * FROM region";
$stmt = $pdo->prepare($query);
$stmt->execute();
$region_result = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 기존 선택된 지역 및 질병 코드
$selected_regions = !empty($trainer['region']) ? explode(',', $trainer['region']) : [];
$selected_diseases = !empty($trainer['disease_code']) ? explode(',', $trainer['disease_code']) : [];
?>

<section>
    <div class="title-wrap">
        <div class="title">
            <div>
                <h1>내 정보 수정</h1>
                <p>트레이너 정보를 수정할 수 있습니다.</p>
            </div>
            <img class="small-logo" src="asset/image/logo.svg" alt="Mangrove Solution 로고"/>
        </div>
        <div style="padding:15px 0; border-bottom:1px solid #e0e0e0;">
            <span class="ntnll" style="display:block; margin-bottom:10px;">아이디</span>
            <p style="margin:0; font-size:16px; color:#000;"><?= htmlspecialchars($trainer['id']) ?></p>
        </div>
        <div style="padding:15px 0; border-bottom:1px solid #e0e0e0;">
            <span class="ntnll" style="display:block; margin-bottom:10px;">이름</span>
            <p style="margin:0; font-size:16px; color:#000;"><?= htmlspecialchars($trainer['name']) ?></p>
        </div>
        <div style="padding:15px 0; border-bottom:1px solid #e0e0e0;">
            <span class="ntnll" style="display:block; margin-bottom:10px;">전화번호</span>
            <p style="margin:0; font-size:16px; color:#000;"><?= htmlspecialchars($trainer['phone']) ?></p>
        </div>
        <label>
            <span class="ntnll">비밀번호</span>
            <input id="password" type="password" value="" placeholder="변경할 비밀번호를 입력하세요" minlength="4" maxlength="20"/>
        </label>
        <label>
            <span class="ntnll">비밀번호 확인</span>
            <input id="password_check" type="password" value="" placeholder="비밀번호를 다시 입력하세요" minlength="4" maxlength="20"/>
        </label>
        <div>
            <span class="ntnll">이메일 주소</span>
            <div style="display:flex; align-items:center; gap:10px; margin-top:10px;">
                <input id="emailId" type="text" value="<?= htmlspecialchars(explode('@', $trainer['email'])[0] ?? '') ?>" style="flex:1; padding:15px 20px; font-size:16px; border:1px solid #ddd; border-radius:10px;"/>
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
            <input id="major" type="text" value="<?= htmlspecialchars($trainer['major']) ?>"/>
        </label>
        <label>
            <span class="ntnll">경력</span>
            <div style="display:flex; align-items:center; gap:10px; margin-top:10px;">
                <input id="teachday" type="number" min="0" value="<?= htmlspecialchars($trainer['teachday']) ?>" style="flex:1; padding:15px 20px; font-size:16px; border:1px solid #ddd; border-radius:10px; max-width: 200px;"/>
                <span style="font-size:16px; color:#000;">년</span>
            </div>
        </label>
        <label>
            <span class="ntnll">대표자격증</span>
            <input id="license" type="text" value="<?= htmlspecialchars($trainer['license']) ?>"/>
        </label>
        <label>
            <span>추가 자격증1</span>
            <input id="sublicense1" type="text" value="<?= htmlspecialchars($trainer['sublicense_1']) ?>"/>
        </label>
        <label>
            <span>추가 자격증2</span>
            <input id="sublicense2" type="text" value="<?= htmlspecialchars($trainer['sublicense_2']) ?>"/>
        </label>
        <label>
            <span>추가 자격증3</span>
            <input id="sublicense3" type="text" value="<?= htmlspecialchars($trainer['sublicense_3']) ?>"/>
        </label>
        <label>
            <span>사진</span>
            <input id="profileImage" type="file" accept=".jpg,.jpeg,.png"/>
            <?php if (!empty($trainer['image'])): ?>
                <div style="margin-top:10px;">
                    <img src="image/<?= htmlspecialchars($trainer['image']) ?>" alt="현재 프로필" style="max-width:200px; max-height:200px; border-radius:10px;">
                    <p style="font-size:14px; color:#7f7f7f;">현재 프로필 사진 (새로 업로드하면 변경됩니다)</p>
                </div>
            <?php endif; ?>
        </label>
        <div>
            <span class="ntnll">가능한 트레이너지역</span>
            <div class="region-box">
            <?php
            for ($i=0; $i<count($region_result); $i++) :
                $is_checked = in_array($region_result[$i]['no'], $selected_regions) ? 'checked' : '';
            ?>
                <label class="region-checkbox">
                    <input type="radio" name="region" value="<?= $region_result[$i]['no'] ?>" <?= $is_checked ?>/>
                    <span><?= htmlspecialchars($region_result[$i]['name']) ?></span>
                </label>
            <?php endfor; ?>
            </div>
        </div>
        <div>
            <span class="ntnll">전문 분야</span>
            <div class="disease-box">
            <?php
            for ($i=0; $i<count($disease_result); $i++) :
                $is_checked = in_array($disease_result[$i]['no'], $selected_diseases) ? 'checked' : '';
            ?>
                <label class="disease-checkbox">
                    <input type="checkbox" name="disease[]" value="<?= $disease_result[$i]['no'] ?>" <?= $is_checked ?>/>
                    <span><?= htmlspecialchars($disease_result[$i]['name']) ?></span>
                </label>
            <?php endfor; ?>
            </div>
        </div>
        <label>
            <span class="ntnll">트레이너 인삿말</span>
            <textarea id="greet" rows="4" style="padding:15px; font-size:16px; border:1px solid #ddd; border-radius:10px; width:100%; box-sizing:border-box;"><?= htmlspecialchars($trainer['greet']) ?></textarea>
        </label>
        <input type="hidden" id="currentImage" value="<?= htmlspecialchars($trainer['image']) ?>"/>
        <div class="my-info-button-box">
            <button class="btn-large btn-primary" onclick="updateTrainerInfo()">수정하기</button>
            <button class="btn-large btn-cancel" onclick="location.href='my_info_trainer.php'">취소</button>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>

<script>
    $(document).ready(function() {
        // 기존 이메일 주소 파싱하여 도메인 선택
        const currentEmail = "<?= htmlspecialchars($trainer['email']) ?>";
        const emailParts = currentEmail.split('@');
        if (emailParts.length === 2) {
            const domain = emailParts[1];
            const domainOptions = ['naver.com', 'nate.com', 'gmail.com', 'kakao.com'];
            if (domainOptions.includes(domain)) {
                $('#emailDomain').val(domain);
            } else {
                $('#emailDomain').val('direct');
                $('#emailDomainDirect').val(domain).show();
            }
        }

        // 도메인 선택 변경 이벤트
        $('#emailDomain').on('change', function() {
            if ($(this).val() === 'direct') {
                $('#emailDomainDirect').show();
            } else {
                $('#emailDomainDirect').hide().val('');
            }
        });
    });

    function updateTrainerInfo() {
        const password = $('#password').val();
        const passwordCheck = $('#password_check').val();
        const emailId = $('#emailId').val();
        const emailDomain = $('#emailDomain').val();
        const emailDomainDirect = $('#emailDomainDirect').val();
        const major = $('#major').val();
        const teachday = $('#teachday').val();
        const license = $('#license').val();
        const sublicense1 = $('#sublicense1').val();
        const sublicense2 = $('#sublicense2').val();
        const sublicense3 = $('#sublicense3').val();
        const greet = $('#greet').val();
        const profileImage = $('#profileImage')[0].files[0];
        const currentImage = $('#currentImage').val();

        // 비밀번호 필수 확인
        if (!password) {
            alert('비밀번호를 입력해주세요.');
            $('#password').focus();
            return;
        }

        // 비밀번호 길이 검증 (4~20자)
        if (password.length < 4 || password.length > 20) {
            alert('비밀번호는 4자리 이상 20자리 미만으로 입력해주세요.');
            $('#password').focus();
            return;
        }

        // 비밀번호 확인 일치 확인
        if (password !== passwordCheck) {
            alert('비밀번호가 일치하지 않습니다.');
            $('#password_check').focus();
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
        const finalDomain = emailDomain === 'direct' ? emailDomainDirect : emailDomain;
        const email = emailId + '@' + finalDomain;

        // 학위/전공 필수 확인
        if (!major) {
            alert('학위 / 전공명을 입력해주세요.');
            $('#major').focus();
            return;
        }

        // 경력 필수 확인
        if (!teachday) {
            alert('경력을 입력해주세요.');
            $('#teachday').focus();
            return;
        }

        // 대표 자격증 필수 확인
        if (!license) {
            alert('대표 자격증을 입력해주세요.');
            $('#license').focus();
            return;
        }

        // 인삿말 필수 확인
        if (!greet) {
            alert('트레이너 인삿말을 입력해주세요.');
            $('#greet').focus();
            return;
        }

        // 지역 선택 확인
        const region = $('input[name="region"]:checked').val();
        if (!region) {
            alert('가능한 트레이너 지역을 선택해주세요.');
            return;
        }

        // 전문 분야 선택 확인
        const diseaseChecked = $('input[name="disease[]"]:checked').length;
        if (diseaseChecked === 0) {
            alert('전문 분야를 최소 1개 이상 선택해주세요.');
            return;
        }

        // 선택된 전문 분야 값들 수집
        const diseases = [];
        $('input[name="disease[]"]:checked').each(function() {
            diseases.push($(this).val());
        });

        let uploadedImageName = currentImage; // 기존 이미지 이름

        // 이미지가 새로 선택된 경우에만 업로드
        if (profileImage) {
            // 파일 크기 확인 (5MB)
            if (profileImage.size > 5 * 1024 * 1024) {
                alert('파일 크기는 5MB를 초과할 수 없습니다.');
                return;
            }

            // 파일 확장자 확인
            const allowedExtensions = ['jpg', 'jpeg', 'png'];
            const fileExtension = profileImage.name.split('.').pop().toLowerCase();
            if (!allowedExtensions.includes(fileExtension)) {
                alert('JPG, JPEG, PNG 파일만 업로드 가능합니다.');
                return;
            }

            // 이미지 업로드
            const formData = new FormData();
            formData.append('image', profileImage);
            formData.append('current_image', currentImage); // 기존 이미지 이름 전달 (덮어쓰기용)

            $.ajax({
                type: 'POST',
                url: 'asset/controller/upload_trainer_image.php',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                async: false,
                success: function(res) {
                    if (res.succ) {
                        uploadedImageName = res.image_name;
                    } else {
                        alert('이미지 업로드에 실패했습니다: ' + res.message);
                        return false;
                    }
                },
                error: function() {
                    alert('이미지 업로드 중 오류가 발생했습니다.');
                    return false;
                }
            });
        }

        // AJAX로 정보 수정 요청
        $.ajax({
            type: 'POST',
            url: 'asset/controller/update_trainer_info.php',
            data: {
                password: password,
                email: email,
                major: major,
                teachday: teachday,
                license: license,
                sublicense_1: sublicense1,
                sublicense_2: sublicense2,
                sublicense_3: sublicense3,
                image: uploadedImageName,
                region: region,
                disease_code: diseases.join(','),
                greet: greet,
                image_changed: profileImage ? '1' : '0' // 이미지 변경 여부
            },
            dataType: 'json',
            async: false,
            success: function(res) {
                if (res.succ) {
                    alert('트레이너 정보가 수정되었습니다.');
                    location.href = 'my_info_trainer.php';
                } else {
                    alert('정보 수정에 실패했습니다: ' + res.message);
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
