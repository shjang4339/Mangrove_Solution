<?php include 'header.php'; ?>

<?php
// 로그인 확인 (고객만 접근 가능)
if (!$is_logged_in || $user_type !== 'member') {
    echo "<script>alert('고객 로그인이 필요합니다.'); location.href='login.php?type=member';</script>";
    exit;
}

// 고객 정보 조회
$query = "SELECT * FROM member WHERE no = ?";
$stmt = $pdo->prepare($query);
$stmt->execute(array($_SESSION['user_no']));
$member = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$member) {
    echo "<script>alert('회원 정보를 찾을 수 없습니다.'); history.back();</script>";
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
$selected_regions = !empty($member['region']) ? explode(',', $member['region']) : [];
$selected_diseases = !empty($member['disease_code']) ? explode(',', $member['disease_code']) : [];
?>

<section>
    <div class="title-wrap">
        <div class="title">
            <div>
                <h1>내 정보 수정</h1>
                <p>회원 정보를 수정할 수 있습니다.</p>
            </div>
            <img class="small-logo" src="asset/image/logo.svg" alt="Mangrove Solution 로고"/>
        </div>
        
        <div style="padding:15px 0; border-bottom:1px solid #e0e0e0;">
            <span class="ntnll" style="display:block; margin-bottom:10px;">아이디</span>
            <p style="margin:0; font-size:16px; color:#000;"><?= htmlspecialchars($member['id']) ?></p>
        </div>
        <div style="padding:15px 0; border-bottom:1px solid #e0e0e0;">
            <span class="ntnll" style="display:block; margin-bottom:10px;">이름</span>
            <p style="margin:0; font-size:16px; color:#000;"><?= htmlspecialchars($member['name']) ?></p>
        </div>
        <div style="padding:15px 0; border-bottom:1px solid #e0e0e0;">
            <span class="ntnll" style="display:block; margin-bottom:10px;">전화번호</span>
            <p style="margin:0; font-size:16px; color:#000;"><?= htmlspecialchars($member['phone']) ?></p>
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
            <span class="ntnll">거주지</span>
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
        <label>
            <span class="ntnll">생년월일</span>
            <input id="birth" type="date" value="<?= htmlspecialchars($member['birth']) ?>"/>
        </label>
        <div>
            <span class="ntnll">이메일 주소</span>
            <div style="display:flex; align-items:center; gap:10px; margin-top:10px;">
                <input id="emailId" type="text" value="<?= htmlspecialchars(explode('@', $member['email'])[0] ?? '') ?>" style="flex:1; padding:15px 20px; font-size:16px; border:1px solid #ddd; border-radius:10px;"/>
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
            <span class="ntnll">질환/장애 유무</span>
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
        <div class="my-info-button-box">
            <button class="btn-large btn-primary" onclick="updateMemberInfo()">수정하기</button>
            <button class="btn-large btn-cancel" onclick="location.href='my_info_member.php'">취소</button>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>

<script>
    $(document).ready(function() {
        // 기존 이메일 주소 파싱하여 도메인 선택
        const currentEmail = "<?= htmlspecialchars($member['email']) ?>";
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

    function updateMemberInfo() {
        const password = $('#password').val();
        const passwordCheck = $('#password_check').val();
        const birth = $('#birth').val();
        const emailId = $('#emailId').val();
        const emailDomain = $('#emailDomain').val();
        const emailDomainDirect = $('#emailDomainDirect').val();

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

        // 생년월일 필수 확인
        if (!birth) {
            alert('생년월일을 입력해주세요.');
            $('#birth').focus();
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

        // 거주지 선택 확인
        const region = $('input[name="region"]:checked').val();
        if (!region) {
            alert('거주지를 선택해주세요.');
            return;
        }

        // 질환/장애 선택 확인
        const diseaseChecked = $('input[name="disease[]"]:checked').length;
        if (diseaseChecked === 0) {
            alert('질환/장애를 최소 1개 이상 선택해주세요.');
            return;
        }

        // 선택된 질환 값들 수집
        const diseases = [];
        $('input[name="disease[]"]:checked').each(function() {
            diseases.push($(this).val());
        });

        // AJAX로 정보 수정 요청
        $.ajax({
            type: 'POST',
            url: 'asset/controller/update_member_info.php',
            data: {
                password: password,
                birth: birth,
                email: email,
                region: region,
                disease_code: diseases.join(',')
            },
            dataType: 'json',
            async: false,
            success: function(res) {
                if (res.succ) {
                    alert('회원 정보가 수정되었습니다.');
                    location.href = 'my_info_member.php';
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
