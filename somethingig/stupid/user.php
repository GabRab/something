<?php
function registerUser($db, $name, $pass, $image){
    $imagePath = null;
    if ($image["error"] !== UPLOAD_ERR_NO_FILE){
        if ($image["error"] !== UPLOAD_ERR_OK){
            echo "<h1> Nepodarilo se nahrat soubor </h1>";
            return;
        }
        if(str_starts_with($image["type"], "image/") === false){
            echo "<h1> prosim jenom obrazky</h1>";
            return;
        }
        if (!file_exists("userImages")){
            mkdir("userImages");
        }
        $imagePath = "userImages/".uniqid().$image["name"];
        move_uploaded_file($image["tmp_name"], $imagePath);
    }
    else {//if no file, then use default
        $imagePath = "userImages/default.png";
    }
    

    $check = mysqli_prepare($db, "
    Select *  FROM users
    WHERE userName = ?
    ");
    mysqli_stmt_bind_param($check,"s", $name);
    $chen = mysqli_execute($check);
    if ($chen !== false){//false if no found
        echo "<h2> user with this name already exists </h2>";
        exit;
    }

    $stmt = mysqli_prepare($db, "
    INSERT INTO users
    (userName, userPass, userImage)
    VALUES
    (?, ?, ?)
    ");
    if ($stmt===false){
        echo "<h2> stmt failed </h2>";
        echo mysqli_error($db);
        exit;
    }

    $hashedPassword = password_hash($pass, PASSWORD_BCRYPT);
    mysqli_stmt_bind_param($stmt, "sss", $name, $hashedPassword, $imagePath);
    $result = mysqli_execute($stmt);
    
    if ($result === false) {
        echo "<h1>Nepodařilo se přidat uzivatele</h1>";
        echo mysqli_error($db);
        exit;
    }
    //gives user to session aswell
    $user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    if ($user===null || !password_verify($pass, $user["userPass"])){
        echo "<h2> neco jsi zadal spatne <h2> ";
        echo mysqli_error($db);
        exit;
    }
    echo "<h2> vitej </h2>";//not needed
    $_SESSION["user"] = $user;//makes session of $user, so that it retains info of user over the different pages
    header("Location: /somethingig/something.php");//redirects to main page after login//redirects to main page after registration
}

function loginUser($db, $name, $pass){
    
    $stmt = mysqli_prepare($db, "
    SELECT * FROM users
    WHERE userName=?
    ");
    if ($stmt===false){
        echo "<h2> stmt failed </h2>";
        echo mysqli_error($db);
        exit;
    }

    mysqli_stmt_bind_param($stmt, "s", $name);
    $result = mysqli_execute($stmt);
    
    if ($result === false) {
        echo "<h1>Nepodařilo se přidat uzivatele</h1>";
        echo mysqli_error($db);
        exit;
    }

    $user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if ($user===null || !password_verify($pass, $user["userPass"])){
        echo "<h2> neco jsi zadal spatne <h2> ";
        echo mysqli_error($db);
        exit;
    }
    echo "<h2> vitej </h2>";//not needed
    $_SESSION["user"] = $user;//makes session of $user, so that it retains info of user over the different pages
    header("Location: /somethingig/something.php");//redirects to main page after login

}

function changeName($db, $name){

    if (!isset($name)){
        echo "<h2>give new name first</h2>";
        exit;
    }
    //checks if name exists, if not, continue and change name, if yes exit
    $check = mysqli_prepare($db, "
    SELECT * FROM users
    WHERE userName = ?;
    ");
    mysqli_stmt_bind_param($check, "s", $name);
    $resulting = mysqli_execute($check);
    if ($resulting===false){
        echo "<h2>execution failed</h2>";
        exit;
    }
    $fletcher = mysqli_fetch_assoc(mysqli_stmt_get_result($check));
    if ($fletcher!==null){
        echo "<h2>name already in use</h2>";
        exit;
    }
    //changes name of logged in user
    $stmt = mysqli_prepare($db, "
    UPDATE users
    SET userName = ?
    WHERE userId = ?;
    ");
    if ($stmt===false){
        echo "<h2> stmt failed </h2>";
        echo mysqli_error($db);
        exit;
    }
    
    mysqli_stmt_bind_param($stmt, "si",$name,$_SESSION["user"]["userId"]);//sets the params into the statement
    $exec = mysqli_execute($stmt);
    if ($exec===false){
        echo "<h2>execution failed</h2>";
        exit;
    }
    $_SESSION["user"]["userName"]=$name;
    header("Location: /somethingig/profile.php");
}
function changeDesc($db, $desc){

    if (!isset($desc)){
        echo "<h2>give new desc</h2>";
        exit;
    }
    
    $stmt = mysqli_prepare($db, "
    UPDATE users
    SET userDesc = ?
    WHERE userId = ?;
    ");
    if ($stmt===false){
        echo "<h2> stmt failed </h2>";
        echo mysqli_error($db);
        exit;
    }
    
    mysqli_stmt_bind_param($stmt, "si",$desc,$_SESSION["user"]["userId"]);//sets the params into the statement
    $exec = mysqli_execute($stmt);
    if ($exec===false){
        echo "<h2>execution failed</h2>";
        exit;
    }
    $_SESSION["user"]["userDesc"]=$desc;
   header("Location: /somethingig/profile.php");
}
function changeImage($db, $image){
    $imagePath = null;
    if ($image["error"] !== UPLOAD_ERR_NO_FILE){
        //if not okay, end
        if ($image["error"] !== UPLOAD_ERR_OK){
            echo "<h1> Nepodarilo se nahrat soubor </h1>";
            return;
        }
        //if not image end
        if(str_starts_with($image["type"], "image/") === false){
            echo "<h1> prosim jenom obrazky</h1>";
            return;
        }
        //if no place for images, make one
        if (!file_exists("userimages")){
            mkdir("userimages");
        }
        $imagePath = "userImages/".uniqid().$image["name"];
        move_uploaded_file($image["tmp_name"], $imagePath);
    }
    else {//if no file, then use default
        $imagePath = "userImages/default.png";
    }

    $stmt= mysqli_prepare($db,"
    UPDATE users
    SET userImage = ?
    WHERE userId = ?;
    ");
    mysqli_stmt_bind_param($stmt, "si", $imagePath, $_SESSION["user"]["userId"]);
    $result = mysqli_execute($stmt);
    if (!$result){
        echo "<h2> execution failed </h2>";
        echo mysqli_error($db);
        exit;
    }

    $_SESSION["user"]["userImage"]=$imagePath;
    header("Location: /somethingig/profile.php");
}

function changePass($db, $pass){

    $stmt = mysqli_prepare($db, "
    UPDATE users
    SET userPass = ?
    WHERE userId = ?;
    ");
    if ($stmt===false){
        echo "<h2> stmt failed </h2>";
        echo mysqli_error($db);
        exit;
    }
    
    $hashPass = password_hash($pass, PASSWORD_BCRYPT);
    mysqli_stmt_bind_param($stmt, "si", $hashPass, $_SESSION["user"]["userId"]);
    $result = mysqli_execute($stmt);
    
    if ($result === false) {
        echo "<h1>failed to execute</h1>";
        echo mysqli_error($db);
        exit;
    }

    $user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if ($user===null || !password_verify($pass, $user["userPass"])){
        echo "<h2> neco jsi zadal spatne <h2> ";
        echo mysqli_error($db);
        exit;
    }
    $_SESSION["user"] = $user;//makes session of $user, so that it retains info of user over the different pages
    header("Location: /somethingig/something.php");//redirects to main page after login

}

?>