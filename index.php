        <?php include 'header.php'; ?>
        
        <?php
        $query = "select * from disease_code";
        $stmt = $pdo -> prepare($query);
        $stmt -> execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        ?>

        <?php 
        for ($i=0; $i<count($result); $i++) :
            echo $tmpcategory = $result[$i]['no'];
        endfor;
        
        // $result
        ?>
        <?php include 'footer.php'; ?>
    </body>
</html>