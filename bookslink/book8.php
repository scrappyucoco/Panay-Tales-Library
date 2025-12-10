<?php
include '../booksData.php';

$book = $books['Mansion']; // select the book
$paragraph = " The Vanishing Mansion of Iloilo” is a popular folk tale that tells of a grand old house said to mysteriously appear and disappear in the remote parts of the province. The mansion is often described as elegant and brightly lit, with music and lively gatherings taking place inside whenever travelers unexpectedly stumble upon it. \n \nAccording to the legend, those who see the mansion usually encounter it at night, only to watch it fade, dissolve, or vanish completely when they attempt to approach. Some stories claim that the mansion is inhabited by enchanted beings or spirits who momentarily allow humans to witness their world before hiding it again. Others say it is a remnant of a once-powerful family cursed to live in a shifting, invisible realm, forever separated from the living. Villagers believe that the mansion appears only to those who are lost, troubled, or in need of guidance, serving as a mystical warning or sign. \n \nThe tale reflects Panay’s deep tradition of spirit lore, where hidden realms coexist with everyday life, and enchanted structures can reveal themselves at unexpected moments. Today, the Vanishing Mansion remains one of Iloilo’s most intriguing stories, symbolizing mystery, caution, and the belief that unseen worlds surround the islands.";

$content = "<p>" . nl2br(htmlspecialchars($paragraph)) . "</p><br>".
'<iframe width="560" height="315" src="https://www.youtube.com/embed/33Wi2GWAP_M?si=CFvkdnk9aigGwKs4" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>'
;	

include 'bookTemplate.php';  // load the design
?>