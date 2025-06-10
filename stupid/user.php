<?php
function registerUser($db, $name, $pass, $image){
    //packed function
    //1. checks for used names
    //2. inserts user
    //3. automatically logs user in

    //checks if file sent is image, if none: uses default, if other than file: tells user to try agian
    $imagePath = null;
    if ($image["error"] !== UPLOAD_ERR_NO_FILE){
        if ($image["error"] !== UPLOAD_ERR_OK){
            echo "<h2> failed to get file </h2>";
            return;
        }
        if(str_starts_with($image["type"], "image/") === false){
            echo "<h2> only images pls </h2>";
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
    WHERE userName = ?;
    ");
    mysqli_stmt_bind_param($check,"s", $name);
    if (!mysqli_execute($check)){//if false something went wrong
        echo "<h2> exec 1 failed </h2>";
        exit;
    }
    $chen = mysqli_fetch_assoc(mysqli_stmt_get_result($check));
    if ($chen!==null){
        echo "<h2>Name already in use</h2>";
        echo mysqli_error($db);
        exit;
    }


    
    $stmt = mysqli_prepare($db, "
    INSERT INTO users (userName, userPass, userImage)
    VALUES (?, ?, ?);
    ");
    if ($stmt===false){
        echo "<h2> stmt failed </h2>";
        echo mysqli_error($db);
        exit;
    }
    
    $hashedPassword = password_hash($pass, PASSWORD_BCRYPT);
    if (!mysqli_stmt_bind_param($stmt, "sss", $name, $hashedPassword, $imagePath)){
        echo "<h2>bind error</h2>";
        echo mysqli_error($db);
        exit;
    }
    if (!mysqli_execute($stmt)){
        echo "<h2>failed to add user (execution err)</h2>";
        echo mysqli_error($db);
        exit;
    }
    //gets the newly made account info, because mysqli can't get results from INSERT, UPDATE, etc.
    $stmt = mysqli_prepare($db, "
    SELECT * FROM users
    WHERE userName=?;
    ");
    if (!mysqli_stmt_bind_param($stmt, "s", $name)){//finally makes use of unique names
        echo "<h2>bind error</h2>";
        echo mysqli_error($db);
        exit;
    }
    if (!mysqli_execute($stmt)){
        echo "<h2>failed to add user (execution err)</h2>";
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
    $_SESSION["user"] = $user;//makes session of $user, so that it retains info of user over the different pages
    $tagType = "USER";
    //makes userTag  should it add userDesc? sure...
    $tagStmt = mysqli_prepare($db, "
    INSERT INTO tags (tagName, tagType, tagCreator)
    VALUES (?, ?, ?);
    ");
    mysqli_stmt_bind_param($tagStmt, "sss", $name,$tagType, $_SESSION["user"]["userId"]);
    if (!mysqli_execute($tagStmt)){
        echo "<h2> tag execution failed </h2>";
        exit;
    }
    header("Location: /somethingig/something.php");//redirects to main page after registration
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
    if (!mysqli_execute($stmt)) {
        echo "<h2> failed to login (execution err)</h2>";
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
    
    if (!mysqli_execute($check)){
        echo "<h2>execution failed</h2>";
        exit;
    }
    
    if (mysqli_fetch_assoc(mysqli_stmt_get_result($check))!==null){
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
    
    if (!mysqli_execute($stmt)){
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
    if (!mysqli_execute($stmt)){
        echo "<h2>execution failed</h2>";
        exit;
    }
    $_SESSION["user"]["userDesc"]=$desc;

    $USER = "USER";
    $tagStmt = mysqli_prepare($db, "
    UPDATE tags
    SET tagDesc = ?
    WHERE tagName = ? AND tagType = ?;
    ");
    mysqli_stmt_bind_param($tagStmt, "sss", $desc, $_SESSION["user"]["userName"], $USER);
    if(!mysqli_execute($tagStmt)){
        echo "<h2> tag execution failed</h2>";
        exit;
    }

   header("Location: /somethingig/profile.php");
}
function changeImage($db, $image){
    $imagePath = null;
    if ($image["error"] !== UPLOAD_ERR_NO_FILE){
        //if not okay, end
        if ($image["error"] !== UPLOAD_ERR_OK){
            echo "<h2> failed to get file </h2>";
            return;
        }
        //if not image end
        if(str_starts_with($image["type"], "image/") === false){
            echo "<h2> only pictures please </h2>";
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

function changePass($db, $pass, $newPass){
    //check the active pass
    if (!password_verify($pass, $_SESSION["user"]["userPass"])){
        echo "<h2> wrong active password</h2>";
        exit;
    }

    //change pass
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
    
    $hashPass = password_hash($newPass, PASSWORD_BCRYPT);
    mysqli_stmt_bind_param($stmt, "si", $hashPass, $_SESSION["user"]["userId"]);

    if (!mysqli_execute($stmt)) {
        echo "<h2>failed to execute</h2>";
        echo mysqli_error($db);
        exit;
    }

    //get new pass in session
    $czech = mysqli_prepare($db, "
    Select *  FROM users
    WHERE userId = ?;
    ");
    mysqli_stmt_bind_param($czech,"s", $_SESSION["user"]["userId"]);
    if (!mysqli_execute($czech)){//if false something went wrong
        echo "<h2> exec failed </h2>";
        exit;
    }
    $user = mysqli_fetch_assoc(mysqli_stmt_get_result($czech));
    
    //checks if got response
    if ($user===null){
        echo "<h2> fetching failed<h2> ";
        echo mysqli_error($db);
        exit;
    }
    $_SESSION["user"] = $user;//makes session of $user, so that it retains info of user over the different pages
    header("Location: /somethingig/something.php");//redirects to main page after login

}

?>