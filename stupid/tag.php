<?php
function listTags($db, $tagType){
    $list = mysqli_prepare($db, "
    SELECT tagName, tagDesc, COUNT(thingtags.tagId) AS tagCount
    FROM tags
    JOIN thingtags ON thingtags.tagId = tags.tagId
    WHERE tagType = ?
    GROUP BY tags.tagId
    ");
    if ($list===false){
        echo "<h2> query failed </h2>";
        exit;
    }
    mysqli_stmt_bind_param($list, "s", $tagType);
    if (!mysqli_execute($list)){
        echo "<h2> list execution failed</h2>";
        exit;
    }
    $result = mysqli_fetch_all(mysqli_stmt_get_result($list), MYSQLI_ASSOC);
    if (!$result){
        echo "<h2> failed fetching info </h2>";
        exit;
    }
    //JOIN for "extra info" option in profile settings or something, where stuff like creators of tags, or something bonus :)
    //maybe even developer commentary like valve, but this is only a website :>
    return $result;
}
function createTag($db, $tagName, $tagDesc, $tagType){
    
    //checks if thingTag with this name exists and alerts if it does
    $checkName = mysqli_prepare($db, "
    SELECT * FROM tags
    WHERE tagName = ? AND tagType = ?
    ");//checks for tags of same name and type
    mysqli_stmt_bind_param($checkName, "ss", $tagName, $tagType);
    if (!mysqli_execute($checkName)){
        echo "<h2> execution failed </h2>";
        echo mysqli_error($db);
        exit;
    };
    if (mysqli_fetch_assoc(mysqli_stmt_get_result($checkName))!==null){
        echo "<h2> thingTag with this name already exists </h2>";
        exit;
    }
    
    //makes new Tag
    $tagStmt = mysqli_prepare($db, "
    INSERT INTO tags (tagName, tagDesc, tagType, tagCreator)
    VALUES (?, ?, ?, ?)
    ");
    mysqli_stmt_bind_param($tagStmt, "sssi", $tagName, $tagDesc, $tagType, $_SESSION["user"]["userId"]);
    if (mysqli_execute($tagStmt)===false){
        echo "<h2> execution failed </h2>";
        echo mysqli_error($db);
        exit;
    }
    header("Location: /somethingig/something.php");
}


?>