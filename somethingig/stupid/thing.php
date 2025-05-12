
<?php

function listSomething($db, $tags): array{
    //tags later used for filtering searches
    $result = mysqli_query($db, "
    SELECT *
    FROM things
    JOIN Users ON things.thingOwnerId = Users.UserId;
    ");
    
    // need to add tags here



    if ($result===false){
        echo "<h2> database retrieval failed </h2>";
        exit;
    }
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function createThing($db, $thingOwner, $thingText, $thingFile, $thingTags){
    //checks file
    $isWhat= null;
    $filePath = null;
    if ($thingFile["error"] !== UPLOAD_ERR_NO_FILE){
        if ($thingFile["error"] !== UPLOAD_ERR_OK){//is file and not ok
            echo "<h2> something wrong with loading file </h2>";
            return;
        }
        if(str_starts_with($thingFile["type"], "image/") === true){
            $isWhat="image";
        }
        if (!file_exists("someFiles")){//if no place for file
            mkdir("someFiles");
        }
        $filePath = "someFiles/". $thingFile["name"];
        move_uploaded_file($thingFile["tmp_name"], $filePath);
    }
    
    //prepares statement and executes it
    $stmt = mysqli_prepare($db, "
        INSERT INTO things
        (thingOwnerId, thingText, thingFile, FileIsWhat)
        VALUES
        (?, ?, ?, ?)
    ");
    if ($stmt === false) {
        echo "<h1> statement failed /h1>";
        echo mysqli_error($db);
        exit;
    }
    mysqli_stmt_bind_param($stmt, "isss", $thingOwner, $thingText, $filePath, $isWhat);
    $result = mysqli_execute($stmt);
    var_dump($result);
    if ($result === false) {
        echo "<h1> execution failed </h1>";
        echo mysqli_error($db);
        exit;
    }
    header("Location: /somethingig/something.php");
    //it sends the stuff, into "things", but I still need to make the tag system with thingTags
   

}

function removeThing($db, $thingId){
    $stmt = mysqli_prepare($db, "
    DELETE FROM things
    WHERE thingID = ?
    ");
    if ($stmt === false){
        echo "<h2>statement failed</h2>";
        echo mysqli_error($db);
        exit;
    }
    mysqli_stmt_bind_param($stmt, "i", $thingId);
    $result = mysqli_execute($stmt);
    if ($result === false){
        echo "<h2> execution failed </h2>";
        echo mysqli_error($db);
        exit;
    }
    header("Location: /somethingig/something.php");
}


function editThing($db ,$thingId , $thingText, $thingFile, $thingTags){
    //asks server for file with thingId
    $check = mysqli_prepare($db, "
    SELECT * 
    FROM things
    WHERE thingID = ?;
    ");
    mysqli_stmt_bind_param($check, "i", $thingId);
    if (!mysqli_execute($check)) {echo "<h2>execution failed</h2>";exit;}
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($check));
    
    //if file is not there, assume they wanted to keep the file
    //checks file if img, shows as image, if not makes download link
    $isWhat= null;
    $filePath = null;
    if ($thingFile["error"] === UPLOAD_ERR_NO_FILE) {
        $filePath=$row["thingFile"];
        $isWhat = $row["FileIsWhat"];
    }
    else{
        if ($thingFile["error"] !== UPLOAD_ERR_OK){//is file and not ok
            echo "<h2> something wrong with loading file </h2>";
            return;
        }
        if(str_starts_with($thingFile["type"], "image/") === true){
            $isWhat="image";
        }
        if (!file_exists("someFiles")){//if no place for file
            mkdir("someFiles");
        }
        $filePath = "someFiles/". $thingFile["name"];
        move_uploaded_file($thingFile["tmp_name"], $filePath);
    }
    //if no file given, keep old file
    //what if wants to delete file? just delete whole message.
    //what if wants to keep message, but delete file? COPY THE MESSAGE IT'S JUST TEXT
    //prepares statement and executes it
    $stmt = mysqli_prepare($db, "
    UPDATE things
    SET thingText = ?, thingFile = ?, FileIsWhat = ?
    WHERE thingID = ?;
    ");
    if ($stmt === false) {
        echo "<h1> statement failed /h1>";
        echo mysqli_error($db);
        exit;
    }
    mysqli_stmt_bind_param($stmt, "sssi", $thingText, $filePath, $isWhat, $thingId );
    $result = mysqli_execute($stmt);
    var_dump($result);
    if ($result === false) {
        echo "<h1> execution failed </h1>";
        echo mysqli_error($db);
        exit;
    }
    header("Location: /somethingig/something.php");
    //it sends the stuff, into "things", but I still need to make the tag system with thingTags
    return $row;
}



?>