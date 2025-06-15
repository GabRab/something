<?php
//list all tags... I only add thingId XOR userId, so searching for userId null wanting all thing tags is fine and when looking for userId, you get only groups :)
function listTags($db, $tagType){
    $list = "
    SELECT *, tags.tagId , COUNT(thingtags.tagId) AS tagCount
    FROM tags
    LEFT JOIN thingtags ON thingtags.tagId = tags.tagId
    WHERE tagType = ?
    GROUP BY tags.tagId
    ORDER BY COUNT(thingtags.tagId) DESC
    ";
    $type=[$tagType];
    $fetch =mysqli_execute_query($db, $list, $type);
    $list = mysqli_fetch_all($fetch, MYSQLI_ASSOC);
    //if (!$list){
    //}
    //JOIN for "extra info" option in profile settings or something, where stuff like creators of tags, or something bonus :)
    //maybe even developer commentary like valve, but this is only a website :>
    return $list;
}
function createTag($db, $tagName, $tagDesc, $tagType){
    
    //checks if thingTag with this name exists and alerts if it does
    $checkName = "
    SELECT * FROM tags
    WHERE tagName = ? AND tagType = ?
    ";//checks for tags of same name and type
    $vals = [$tagName, $tagType];
    $some =mysqli_execute_query($db, $checkName, $vals);
    //only needed for groups now
    if (mysqli_fetch_all($some, MYSQLI_ASSOC)!==null){
        echo "tag with this name already exists";
        exit;
    }
    
    //makes new Tag
    $tagStmt = mysqli_prepare($db, "
    INSERT INTO tags (tagName, tagDesc, tagType, tagCreator)
    VALUES (?, ?, ?, ?)
    ");
    $valsIn = [$tagName, $tagDesc, $tagType, $_SESSION["user"]["userId"]];
    if (mysqli_execute_query($db, $tagStmt, $valsIn)===false){
        echo "tag insertion failed <br><br><br>".mysqli_error($db);
        exit;
    }
}

//makes 1 connetion to tag from either thing or user
function joinTag ($db, $tagId, $userId=null, $thingId=null){
    $stmt = "
    INSERT INTO thingtags (userId, thingId, tagId)
    VALUES (?, ?, ?)";
    $vals = [$userId, $thingId, $tagId];
    if (mysqli_execute_query($db, $stmt, $vals)===false){
        echo "separateTag failed <br><br>".mysqli_error($db);
        exit;
    };
}
//deletes all connections with specified values
function separateTag ($db, $tagId, $userId=null, $thingId=null){
    $tagId =intval($tagId);
    //hate that I have to check for null because of sql
    if($userId===null){
        $stmt = "
        DELETE FROM thingtags
        WHERE userId IS NULL AND thingId = ? AND tagId = ?";
        $vals = [$thingId, $tagId];
    }
    if ($thingId ===null){
        $stmt = "
        DELETE FROM thingtags
        WHERE userId = ? AND thingId IS NULL AND tagId = ?";
        $vals = [$userId, $tagId];
    }
    var_dump($vals);
    if (mysqli_execute_query($db, $stmt, $vals)===false){
        echo "separateTag failed <br><br>".mysqli_error($db);
        exit;
    };
}
//shows all groups a user has joined
function listJoinedGroups($db, $userId){
    $stmt = "
    SELECT * FROM tags
    JOIN thingtags ON tags.tagId = thingtags.tagId
    WHERE thingtags.userId = ?";
    $val=[$userId];
    return mysqli_fetch_all(mysqli_execute_query($db, $stmt, $val), MYSQLI_ASSOC);//WHY DOES IT RETURN A "44" STRING
    //THERE IS NOTHING RELATED TO "44" IN THE DATABASE ANYWHERE, THE STATEMENT WORKS IN MYSQL INTERFACE, WHAT
}


?>