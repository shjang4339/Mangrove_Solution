<?php include 'header.php'; ?>

<?php
// 관리자 권한 확인
if (!$is_logged_in || $user_id !== 'admin') {
    echo "<script>alert('관리자만 접근 가능합니다.'); location.href='index.php';</script>";
    exit;
}

// GET 파라미터로 트레이너 번호 받기
$trainer_no = $_GET['no'] ?? '';

if (empty($trainer_no)) {
    echo "<script>alert('트레이너 번호가 필요합니다.'); history.back();</script>";
    exit;
}

// 트레이너 정보 조회
$query = "SELECT * FROM trainer WHERE no = ?";
$stmt = $pdo->prepare($query);
$stmt->execute(array($trainer_no));
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

<section class="admin-list-container">
    <div class="title-wrap">
        <div class="title">
            <div>
                <h1>트레이너 정보 수정</h1>
                <p>트레이너 정보를 수정할 수 있습니다.</p>
            </div>
            <img class="small-logo" src="asset/image/logo.svg" alt="Mangrove Solution 로고"/>
        </div>
        <label>
            <span class="ntnll">아이디</span>
            <input id="user_id" type="text" value="<?= htmlspecialchars($trainer['id']) ?>" style="padding:15px 20px; font-size:16px; border:1px solid #ddd; border-radius:10px;"/>
        </label>
        <div style="padding:15px 0; border-bottom:1px solid #e0e0e0;">
            <span class="ntnll" style="display:block; margin-bottom:10px;">이름</span>
            <p style="margin:0; font-size:16px; color:#000;"><?= htmlspecialchars($trainer['name']) ?></p>
        </div>
        <div style="padding:15px 0; border-bottom:1px solid #e0e0e0;">
            <span class="ntnll" style="display:block; margin-bottom:10px;">프로필 사진</span>
            <div style="margin-top:10px;">
                <?php if (!empty($trainer['image'])): ?>
                    <img src="image/<?= htmlspecialchars($trainer['image']) ?>" alt="프로필 사진" style="max-width:200px; max-height:200px; border-radius:10px; border:1px solid #ddd;"/>
                <?php else: ?>
                    <p style="margin:0; font-size:14px; color:#999;">등록된 프로필 사진이 없습니다.</p>
                <?php endif; ?>
            </div>
        </div>
        <div style="padding:15px 0; border-bottom:1px solid #e0e0e0;">
            <span class="ntnll" style="display:block; margin-bottom:10px;">전화번호</span>
            <p style="margin:0; font-size:16px; color:#000;"><?= htmlspecialchars($trainer['phone']) ?></p>
        </div>
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
            <input id="major" type="text" value="<?= htmlspecialchars($trainer['major']) ?>" style="padding:15px 20px; font-size:16px; border:1px solid #ddd; border-radius:10px;"/>
        </label>
        <label>
            <span class="ntnll">경력</span>
            <div style="display:flex; align-items:center; gap:10px; margin-top:10px;">
                <input id="teachday" type="number" min="0" value="<?= htmlspecialchars($trainer['teachday']) ?>" style="flex:1; padding:15px 20px; font-size:16px; border:1px solid #ddd; border-radius:10px; max-width: 200px;"/>
                <span style="font-size:16px; color:#000;">년</span>
            </div>
        </label>
        <label>
            <span class="ntnll">대표 자격증</span>
            <input id="license" type="text" value="<?= htmlspecialchars($trainer['license']) ?>" style="padding:15px 20px; font-size:16px; border:1px solid #ddd; border-radius:10px;"/>
        </label>
        <label>
            <span class="ntnll">보조 자격증 1</span>
            <input id="sublicense_1" type="text" value="<?= htmlspecialchars($trainer['sublicense_1']) ?>" style="padding:15px 20px; font-size:16px; border:1px solid #ddd; border-radius:10px;"/>
        </label>
        <label>
            <span class="ntnll">보조 자격증 2</span>
            <input id="sublicense_2" type="text" value="<?= htmlspecialchars($trainer['sublicense_2']) ?>" style="padding:15px 20px; font-size:16px; border:1px solid #ddd; border-radius:10px;"/>
        </label>
        <label>
            <span class="ntnll">보조 자격증 3</span>
            <input id="sublicense_3" type="text" value="<?= htmlspecialchars($trainer['sublicense_3']) ?>" style="padding:15px 20px; font-size:16px; border:1px solid #ddd; border-radius:10px;"/>
        </label>
        <div>
            <span class="ntnll">활동 가능 지역</span>
            <div class="region-box">
            <?php
            for ($i=0; $i<count($region_result); $i++) :
                $is_checked = in_array($region_result[$i]['no'], $selected_regions) ? 'checked' : '';
            ?>
                <label class="region-checkbox">
                    <input type="checkbox" name="region[]" value="<?= $region_result[$i]['no'] ?>" <?= $is_checked ?>/>
                    <span><?= htmlspecialchars($region_result[$i]['name']) ?></span>
                </label>
            <?php endfor; ?>
            </div>
        </div>
        <label>
            <span class="ntnll">인사말</span>
            <textarea id="greet" rows="5" style="width:100%; padding:15px 20px; font-size:16px; border:1px solid #ddd; border-radius:10px; resize:vertical;"><?= htmlspecialchars($trainer['greet']) ?></textarea>
        </label>
        <div>
            <span>전문 분야</span>
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

        <input type="hidden" id="trainer_no" value="<?= htmlspecialchars($trainer_no) ?>"/>

        <div class="admin-button-box" style="display:flex; 
    flex-direction: row; gap:20px; justify-content:center; margin-top:40px;">
            <button class="btn-large btn-secondary" onclick="updateTrainerInfo()">수정하기</button>
            <button class="btn-large btn-cancel" onclick="location.href='admin_trainer_list.php'">취소</button>
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
        const trainer_no = $('#trainer_no').val();
        const user_id = $('#user_id').val();
        const emailId = $('#emailId').val();
        const emailDomain = $('#emailDomain').val();
        const emailDomainDirect = $('#emailDomainDirect').val();
        const major = $('#major').val();
        const teachday = $('#teachday').val();
        const license = $('#license').val();
        const sublicense_1 = $('#sublicense_1').val();
        const sublicense_2 = $('#sublicense_2').val();
        const sublicense_3 = $('#sublicense_3').val();
        const greet = $('#greet').val();

        // 아이디 필수 확인
        if (!user_id) {
            alert('아이디를 입력해주세요.');
            $('#user_id').focus();
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
            alert('학위/전공을 입력해주세요.');
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

        // 활동 가능 지역 선택 확인
        const regionChecked = $('input[name="region[]"]:checked').length;
        if (regionChecked === 0) {
            alert('활동 가능 지역을 최소 1개 이상 선택해주세요.');
            return;
        }

        // 선택된 지역 값들 수집
        const regions = [];
        $('input[name="region[]"]:checked').each(function() {
            regions.push($(this).val());
        });

        // 인사말 필수 확인
        if (!greet) {
            alert('인사말을 입력해주세요.');
            $('#greet').focus();
            return;
        }

        // 전문 분야 선택 확인
        const diseaseChecked = $('input[name="disease[]"]:checked').length;
        if (diseaseChecked === 0) {
            alert('전문 분야를 최소 1개 이상 선택해주세요.');
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
            url: 'asset/controller/update_trainer_info_admin.php',
            data: {
                trainer_no: trainer_no,
                user_id: user_id,
                email: email,
                major: major,
                teachday: teachday,
                license: license,
                sublicense_1: sublicense_1,
                sublicense_2: sublicense_2,
                sublicense_3: sublicense_3,
                region: regions.join(','),
                greet: greet,
                disease_code: diseases.join(',')
            },
            dataType: 'json',
            success: function(res) {
                if (res.succ) {
                    alert('트레이너 정보가 수정되었습니다.');
                    location.href = 'admin_trainer_list.php';
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
