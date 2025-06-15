<?php
require "./stupid/init.php";
require "./blocks/head.phtml";
//!!!
//editing things INCLUDING TAGS...............................................................................................done, but you have to refresh the page for them to update :/
//creating tags on thing creation.............................................................................................DONE
//searching for text content (TEXT:something).................................................................................DONE
//GROUP functionality - connecting players to GROUP tags(just make a new column in thingtags) and connecting groups to things.DONE
//searching for Users (could maybe just be on tags?)..........................................................................
//searching using TEXT: (inclusion in thingText)..............................................................................DONE
//GROUP FUNCTIONS, like JOIN, LEAVE, ETC (+ have to be in group to use group tag on things they post).........................DONE (need to figure out how to consistently update page info)
//IF THING TEXT FIELD IS EMPTY, IT INPUTS AN EMPTY TAG, FUCK. (just go and check the input and compare it with an empty str)..              grass time tho :(

//!!!VERY IMPORTANT
//divide the listed things into pages (LIMIT 30 OFFSET ?*30)..................................................................DONE, but kinda janky :3
//PROBLEM - page select is not working on bottom of page for some reason :(...................................................DONE
//FIX PAGING YOU DUMBASS......................................................................................................DONE  working with js and php is like working with my siblings, but better
//INFO- to load input with the rest, put it before you get the stuff idk how it works, seemingly random

//adding counts of thingtag connection to each tag and displaying it in tags..................................................DONE
//need css and artwork........................................................................................................
//slonek logo (kinda like github :>)..........................................................................................
//searching with tags should account for null results (some tags aren't connected to things and some tags don't exist yet)....DONE
//adding tags to search input when clicking on them...........................................................................
//different stylization for tagTypes..........................................................................................
//figure out how to make text be over multiple rows, and not just in one row..................................................DONE (in css white-space:pre-wrap)
//switching between AND and OR statements, maybe even making the user use logic for this instead of just a select.............nah just gonna keep AND on everything
//make closing comments possible (would probably have to keep page right?)....................................................DONE (the if before show comms)

?>
<div class="documentation">
    <div class="documentation-Search">
        <div class="documentation-Text">
            <h2>searching</h2> <!-- Just go with ".documentationTextContent h2" for this one !-->
            <p>you can search using:</p>
            <p>1. THING: tags connected to things(posts as many recognise them)</p>
            <p>2. USER: tags made by user registration</p>
            <p>3. GROUP: tags connected to groups</p>
            <p>4. TEXT: searches for text inside of a things text</p>
            <br>
            <p>I also added a tagList next to the search box, it shows you all the tags that exist divided into the 3 main groups</p>
            <p>If you click on a tag, it gets added to the search box (you can only remove it manually tho)</p>
            <p>searching works with the regular AND stuff, where all things displayed have the searched attributes/tags</p>
        </div>
        <div>
            <h2>pages</h2>
            <p>things you get from searching are divided into pages of 10 things</p>
            <p>I hate js</p>
        </div>
        <div>
            <h2>comments</h2>
            <p>you can comment on things, I could've just made a thing ranking, and the option to connect things to other things, but that would actually be a smart thing to do</p>
            <p></p>
        </div>
    </div>
</div>

<?php
require "./about.phtml";

require "./blocks/tail.phtml"

?>