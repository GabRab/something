
<?php
/*
08.05.2025................................................................................................................29.05.2025
I need:
-main stream (sorted by age: new on top, old on bottom)...................................................................done, just add time stamp options
-many sorting options, with additive tags, which show what they mean......................................................done, kinda, still have to show their desc with css
-everything has a universal tag for "all".................................................................................not needed at all
-advanced tags include images, videos, text-only which get applied based on the contents of the message...................just make them normal tags, not needed to make it so hard
-time tags based on age of messages.......................................................................................kinda good idea, but I'm too lazy to add something like this, so Imma just let the user search through a bottomless hole :)
-not being able to send messages, or like them if not signed in...........................................................done
-tags like user:something when searching..................................................................................done (on newly made things atleast)
-18+ tags for logged in users behind bars.................................................................................I like the idea, but this is a school project ;-;, also not just logged in users, but hidden in some "advanced" tags, which aren't normally shown unless you specifically request them
-groups/fanbases (just make it a tag bruh)................................................................................I did make it a tag bruh, now I just gotta connect it with users somehow :)
-themes/customization.....................................................................................................later css (maybe save it in the users table, but that's for later)


17.05.2025
-special profile options which add extraInfo or other miscellanious things................................................I have userDesc and userImage, maybe add more stuff later, like background image in steam profiles?
-maybe a modification date, which will eliminate the possibility of constantly changing your thing to stay on top of .....good idea, but this is once again a school project, if bots or script kiddies are a problem, just add a timer between things added
search results (later -medictf2)..........................................................................................
-connect tags to things, users and groups.................................................................................I have userTags which can get connected to things (I should just move things.userId to thingtags.userId, so it is in one place and so thingtags can act as a "connection hub" table)
?instead of isAdmin in the user database, I could make an admin group :D - would have to hide it from public tho :x.......very bad idea, it would get absolutely vandalised with messages and admins would get swarmed, I just have an isAdmin value
-make groups creating page................................................................................................done, though I need to add a bunch of stuff to those groups, like logo, members, I don't even have a table for it, just a tag


18.05.2025
tag connections...........................................................................................................DONE
userId | thingId | tagId
14 | null    | 15

-userId to connect with user, thingId to connect to thing.................................................................thinking I guess?
- should probably make 2 connection tables, 1 for users (groups, thingtags maybe?, their USER tag) .......................that's cool and all, but what about another column in thingtags, which connects all the things and users
  the other for things (groups, thingtags, creator).......................................................................I can also delete things.userId, it would also help a bit

how to add tags?..........................................................................................................I went with text input (main inspiration is the great tag system at hitomi.la (:   )
-text input, where you do 'enter' and the tag get's added?
-special "tag symbol"?
!make sure to change params after finishing tags!


20.05.2025 
I had an idea of comments being a part of the things table, just differentiated by a thingType. Though that would mean....that I can do, but it is just so annoying and I don't want to add more filters
I would have to somehow add more stuff....................................................................................so I just made another table <3
Thus I am keeping to making a new table for comments......................................................................oh, good

21.05.2025
I NEED a new page for the post with comments!.............................................................................yes
otherwise it would take up too much space :(
also I don't want to do js mysql queries.................................................................................still haven't touched those :D

I NEED TO FIGURE OUT HOW TO GET TO ANOTHER PAGE !WITH! VALUES... oh no :(................................................oh no indeed and yes, into the session it goes, since whatever else is the session for?
guess that's one more thing to store in session?

27.05.2025
I should make an ad corner... it is a bad idea, but the thought of children going to a corner to increase their .........too hilarious to not add when I figure out how to add adds
"time spent in the ad corner" timer and then flexing it to others is hilarious...........................................lol

05.06.2025
someThingComms.php is just for show now. I don't need it :)
*/
?>