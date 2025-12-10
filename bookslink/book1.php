<?php
include '../booksData.php';

$book = $books['Hinilawod'];
$paragraph = "Hinilawod is an ancient epic poem of the Sulodnon people from the highlands of Central Panay, particularly in Calinog, Lambunao, and Janiuay in Iloilo. \n \nIt is one of the oldest and longest epics in the Philippines, traditionally chanted by binukot maidens and master chanters, and can take more than 30 hours to perform. The story centers on the heroic adventures of three brothers—Labaw Donggon, Humadapnon, and Dumalapdap—who journey through the earthworld, underworld, and skyworld to face monsters, sorcerers, and rival deities. \n \nLabaw Donggon seeks powerful brides across realms, Humadapnon battles enchantments and rescues maidens from evil beings, while Dumalapdap overcomes giant creatures to find his destined wife. The epic highlights themes of heroism, love, divine intervention, and the rich pre-colonial beliefs of the Panay Indigenous peoples.
";

$content = "<p>" . nl2br(htmlspecialchars($paragraph)) . "</p><br>".
           '<iframe width="560" height="315" src="https://www.youtube.com/embed/q3gFBN57rYM?si=9Vcfn-WP1CS-XGs4" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>';

include 'bookTemplate.php';  // load the design
?>