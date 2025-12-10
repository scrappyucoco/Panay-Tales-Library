<?php
include '../booksData.php';

$book = $books['Gimo']; // select the book
$paragraph = " “The Legend of the Aswang of Dueñas” is a well-known Ilonggo folk tale that recounts the eerie presence of an aswang said to haunt the quiet town of Dueñas in Iloilo. The story often centers on a mysterious woman who appears kind and gentle during the day but transforms into a fearsome creature at night, preying on livestock, infants, or unsuspecting travelers. Residents describe hearing strange scratching on rooftops, animal-like footsteps in the dark, or spotting a shadowy figure lurking near isolated houses. \n \nAccording to the tale, the aswang of Dueñas could detach its upper body or take the form of a dog, pig, or large bird to move silently across the countryside. Despite its terror, many versions portray the creature with hints of tragedy—some say she was cursed, betrayed, or forced into hiding due to a forbidden love or an old family secret. The legend served as a cautionary reminder for villagers to stay indoors at night, guard their children, and be wary of strangers who behaved oddly. \n \nOver time, stories of the Dueñas aswang spread to neighboring towns, strengthening its reputation as one of the most feared supernatural beings in Iloilo folklore. Today, the tale remains part of the province’s cultural memory, reflecting both local fears and the enduring belief in unseen forces that roam rural communities after dark."
;
$content = "<p>" . nl2br(htmlspecialchars($paragraph)) . "</p><br>".
           '<iframe width="560" height="315" src="https://www.youtube.com/embed/q3gFBN57rYM?si=9Vcfn-WP1CS-XGs4" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>'
;

include 'bookTemplate.php';  // load the design
?>