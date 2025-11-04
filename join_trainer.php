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
                <label>
                    <span class="ntnll">이메일 주소</span>
                    <input id="email" type="text" value=""/>
                </label>
                <label>
                    <span class="ntnll">학위 / 전공명</span>
                    <input id="major" type="text" value=""/>
                </label>
                <label>
                    <span class="ntnll">대표자격증</span>
                    <input id="major" type="text" value=""/>
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
                <label>
                    <span class="ntnll">사진</span>
                    <form id="img_uploadForm" class="img_uploadForm" name="reqform"  method="post" enctype="multipart/form-data" action="asset/controller/insert_trainer_photo.php">
                        <input type="file" id="thumbnail-file" name="imgFile" class="thumbnail-file" accept="image/png" />
                        <input type="hidden" id="imgName" name="imgName" value="" />
                    </form>
                </label>
                <div>
                    <span class="ntnll">가능한 트레이닝 지역</span>
                    <div class="region-box">
                    <?php 
                    for ($i=0; $i<count($region_result); $i++) :
                        ?>
                        <label>
                            <input class="region_check" type="checkbox" value="<?=$region_result[$i]['no']?>"/>
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
                    <textarea value=""></textarea>
                </label>

                <div class="btn-box">
                    <button class="confirm">가입하기</button>
                    <button>가입취소</button>
                </div>
            </div>
            

        </section>
        <?php include 'footer.php'; ?>
        <script>
            $('.btn-box > .confirm').on('click', function() {
                let id = $('#id').val();
                let password = $('#password').val();
                let password_check = $('#password_check').val();
                let name = $('#name').val();
                let phone = $('#phone').val();
                let address = $('#address').val();
                let birth = $('#birth').val();
                let email = $('#email').val();

                let disease_code = $('.disease_check:checked').map(function() {
                    return $(this).val();
                }).get().join(',');

                let is_disease = Number(disease_code.length > 0);

                console.log(is_disease);
            
                $.ajax({
                    url: 'asset/controller/insert_join_member.php',
                    data: {
                        id: id,
                        password: password,
                        name: name,
                        phone: phone,
                        address: address,
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
                            alert('추가되었습니다');
                            window.location.reload();
                        } else {
                            alert('오류');
                        }
                    }
                });

            })
        </script>
    </body>
</html>