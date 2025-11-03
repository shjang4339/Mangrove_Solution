        <?php include 'header.php'; ?>
        
        <?php
        $query = "select * from disease_code";
        $stmt = $pdo -> prepare($query);
        $stmt -> execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        ?>

        <div>
            <div>
                <div>
                    <h1>회원가입</h1>
                    <p>회원이 되어 다양한 혜택을 경험해 보세요!</p>
                </div>
                <img src="" alt="Mangrove Solution 로고"/>
            </div>
            <div>
                <span>아이디</span>
                <input id="id" type="text" value=""/>
            </div>
            <div>
                <span>비밀번호</span>
                <input id="password" type="password" value=""/>
            </div>
            <div>
                <span>비밀번호 확인</span>
                <input id="password_check" type="password" value=""/>
            </div>
            <div>
                <span>이름</span>
                <input id="name" type="text" value=""/>
            </div>
            <div>
                <span>전화번호</span>
                <input id="phone" type="text" value=""/>
            </div>
            <div>
                <span>주소</span>
                <input id="address" type="text" value=""/>
            </div>
            <div>
                <span>생년월일</span>
                <input id="birth" type="date" value=""/>
            </div>
            <div>
                <span>이메일 주소</span>
                <input id="email" type="text" value=""/>
            </div>
            <div>
                <?php 
                for ($i=0; $i<count($result); $i++) :
                    ?>
                    <label>
                        <input type="checkbox" value="<?=$result[$i]['no']?>"/>
                        <?=$result[$i]['name']?>
                    </label>
                    <?php
                endfor;
                ?>
            </div>

            <div>
                <button>가입하기</button>
                <button>가입취소</button>
            </div>

        </div>
        <?php include 'footer.php'; ?>
    </body>
</html>