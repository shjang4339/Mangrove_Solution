        <?php include 'header.php'; ?>
        
        <?php
        $query = "select * from disease_code";
        $stmt = $pdo -> prepare($query);
        $stmt -> execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                <label>
                    <span class="ntnll">거주지</span>
                    <input id="address" type="text" value=""/>
                </label>
                <label>
                    <span class="ntnll">생년월일</span>
                    <input id="birth" type="date" value=""/>
                </label>
                <label>
                    <span class="ntnll">이메일 주소</span>
                    <input id="email" type="text" value=""/>
                </label>
                <div>
                    <span>질환 / 장애 유무</span>
                    <div class="disease-box">

                    <?php 
                    for ($i=0; $i<count($result); $i++) :
                        ?>
                        <label>
                            <input class="disease_check" type="checkbox" value="<?=$result[$i]['no']?>"/>
                            <?=$result[$i]['name']?>
                        </label>
                        <?php
                    endfor;
                    ?>
                    
                    </div>
                </div>

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