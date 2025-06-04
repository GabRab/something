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

//!!!VERY IMPORTANT
//divide the listed things into pages (LIMIT 30 OFFSET ?*30)..................................................................DONE, but kinda janky :3
//PROBLEM - page select is not working on bottom of page for some reason :(...................................................
//FIX PAGING YOU DUMBASS......................................................................................................DONE  working with js and php is like working with my siblings, but better

//adding counts of thingtag connection to each tag and displaying it in tags..................................................DONE
//need css and artwork........................................................................................................
//slonek logo (kinda like github :>)..........................................................................................
//searching with tags should account for null results (some tags aren't connected to things and some tags don't exist yet)....DONE
//adding tags to search input when clicking on them...........................................................................
//different stylization for tagTypes..........................................................................................
//figure out how to make text be over multiple rows, and not just in one row..................................................
//switching between AND and OR statements, maybe even making the user use logic for this instead of just a select.............nah just gonna keep AND on everything
?>
<div class="documentation">
    <div class="documentation-Search">
        <div class="documentation-Text">
            <h2>searching</h2> <!-- Just go with ".documentationTextContent h2" for this one !-->
            <p>searching is divided into 3 groups:</p>
            <p>1. THING: tags connected to things(posts as many recognise them)</p>
            <p>2. USER: tags made by user registration</p>
            <p>3. GROUP: tags connected to groups</p>
            <br>
            <p>these are the 3 groups, they can be used to search through things, so that you don't get very lost</p>
            <p>I also made a text filter called TEXT: , which filters out words in a thing's text</p>
            <br>
            <p>searching works with the regular AND stuff, where all things displayed have the searched attributes/tags</p>
        </div>
        <div>
            <h2>pages</h2>
            <p>I have made it so that something sends you only 10 things to reduce the amount of scrolling and maybe ease down on server usage</p>
            <p>you can change pages only at the bottom of the page, because I didn't want to send another request, just to reduce the size of another</p>
            <p>quality over quantity</p>
        </div>
        <div>
            <h2>comments</h2>
            <p>you can comment on things, though it will send you to another page, so you are going to have to go back</p>
            <p>atleast the pageNumber is in session, so you don't lose that. adding searchStr into session would just be annoying</p>
            <p>like hey you need to search for NOTHING, so you get all things... that doesn't sound like a bad idea</p>
            <p></p>
        </div>
    </div>
</div>

<?php
require "./about.phtml";

require "./blocks/tail.phtml"

?>