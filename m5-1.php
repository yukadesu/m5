<!DOCTYPE html>
<html lang = "ja">
<head>
    <meta charset = "UTF-8">
    <title>mission_5-1</title>
</head>

<body>
    
    
<?php


//記入例；以下は <?php から ?.で挟まれるPHP領域に記載すること。
    //4-2以降でも毎回接続は必要。
    //$dsnの式の中にスペースを入れないこと！

    // 【サンプル】
    // ・データベース名：tb219876db
    // ・ユーザー名：tb-219876
    // ・パスワード：ZzYyXxWwVv
    // の学生の場合：

    // DB接続設定
    $dsn = 'mysql:dbname=データベース名;host=localhost';
    $user = 'ユーザー名';
    $password = 'パスワード';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    
    
    $sql = "CREATE TABLE IF NOT EXISTS みんなの投稿"
    ." ("
    . "id INT AUTO_INCREMENT PRIMARY KEY,"
    . "name CHAR(32),"
    . "comment TEXT,"
    . "date TEXT,"
    . "pass TEXT"
    .");";
    $stmt = $pdo->query($sql);
   
    
    
    // テーブルにデータを登録
    
  if(!empty($_POST["name"])&& (!empty($_POST["comment"])) && (!empty($_POST["password"]))){
    $name = $_POST["name"];
    $comment = $_POST["comment"];
    $date = date("Y/m/d H:i:s" );
    $pass = $_POST["password"];
    
    if (!empty($_POST["mode"])){  //編集実行機能
        $id = $_POST["mode"]; //変更する投稿番号
        $sql = 'UPDATE みんなの投稿 SET name=:name,comment=:comment,date=:date WHERE id=:id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':date', $date, PDO::PARAM_STR);
        $stmt->execute();

    }
     //$rowの添字（[ ]内）は、4-2で作成したカラムの名称に合わせる必要があります。
    else{
        $sql = "INSERT INTO みんなの投稿 (name, comment,date,pass) VALUES (:name, :comment, :date, :pass)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
        $stmt->bindParam(':date', $date, PDO::PARAM_STR);
        $stmt->bindParam(':pass', $pass, PDO::PARAM_STR);
        $stmt->execute();
        //bindParamの引数名（:name など）はテーブルのカラム名に併せるとミスが少なくなります。最適なものを適宜決めよう。
    }
  }
    
 elseif (!empty($_POST["delete"])){//削除部分
    $pass_delete = $_POST["delepass"];
    $id_delete = $_POST["delete"];
    $pass = 'SELECT * FROM みんなの投稿 where id=:id'; //指定した行のパスワードを引っ張ってきている
        $stmt = $pdo->prepare($pass);
        $stmt->bindParam(':id', $id_delete, PDO::PARAM_INT);//書き換える
        $stmt->execute();//実行
        $results = $stmt->fetchAll();//検索結果を取得
        foreach ($results as $row){
            $tomato  = $row["pass"];
        }
        
    if ($tomato == $pass_delete){
        $sql = 'delete from みんなの投稿 where id=:id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id_delete, PDO::PARAM_INT);
        $stmt->execute();
    }
 }
 
 elseif(!empty($_POST["edit"])){
    $edit_delete = $_POST["editpass"];
    $sql = 'SELECT * FROM みんなの投稿'; //編集表示部分
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    $edit = $_POST["edit"];
    
    $edit_delete = $_POST["editpass"];
       $pass = 'SELECT * FROM みんなの投稿 where id=:id'; //指定した行のパスワードを引っ張ってきている
        $stmt = $pdo->prepare($pass);
        $stmt->bindParam(':id', $edit, PDO::PARAM_INT);//書き換える
        $stmt->execute();//実行
        $results = $stmt->fetchAll();//検索結果を取得
        foreach ($results as $row){
            $egg  = $row["pass"];
        }
        
    if ($egg == $edit_delete){
    foreach ($results as $row){
        if ($row["id"] == $edit){
        $rename = $row["name"];
        $recomment = $row["comment"];
        $mode = $_POST["edit"];
        }
    }
    }
 }
  elseif(!empty($_POST["alldelete"])){
    // 【！この SQLは userData テーブルを削除します！】
    $sql = 'DROP TABLE みんなの投稿';
    $stmt = $pdo->query($sql);
  }
?>
 
<h3>投稿フォーム</h3>
    <form action = "" method = "post" >
        <input type = "text" name = "name" value ="<?php if (isset($rename)) {echo $rename;} ?>" placeholder = "名前"><br>
        <input type = "text" name = "comment" value= "<?php if (isset($recomment)) {echo $recomment;} ?>" placeholder = "コメント"><br>
        <input type = "password" name = "password" placeholder = "パスワード">
        <input type = "submit" name = "submit"><br><br>
        
        <input type = "number" name = "delete" placeholder = "削除したい番号"><br>
        <input type = "text" name = "delepass" placeholder = "パスワード">
        <input type = "submit" name = "button" value = "削除"><br><br>
        
        <input type = "number" name = "edit" placeholder = "編集したい番号"><br>
        <input type = "text" name = "editpass" placeholder = "パスワード">
        <input type = "submit" name = "button2" value = "編集">
        <input type = "hidden" name = "mode" value = "<?php if (isset($mode)) {echo $edit;}?>"><br><br>
    <h4 style="display:inline">この投稿を全て消したいときは、このボタンを押してください！！！</h4><br>
        <input type = "submit" name = "alldelete" value = "すべて削除">
    </form>


　　<h3>投稿一覧</h3>
　　
<?php

   $sql = 'SELECT * FROM みんなの投稿'; //表示部分
  $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    foreach ($results as $row){
        //$rowの中にはテーブルのカラム名が入る
        echo $row['id'].'. ';
        echo $row['name'].': ';
        echo $row['comment'].' (';
        echo $row['date'].')<br>';

    echo "<hr>";
    }
?>
 
</body>
</html>