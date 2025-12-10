<?php
include '../booksData.php';

$book = $books['Maragtas']; // select the book
$paragraph = " Maragtas is a literary and folkloric work published in 1907 that narrates the legendary migration of ten Bornean datus who fled the oppressive rule of Sultan Makatunaw. Seeking freedom, they sailed across the seas and eventually reached the island of Panay, where they encountered the indigenous Ati people. \n \nAccording to the story, the datus peacefully negotiated with the Ati chieftain Marikudo, offering a golden salakot and a long gold necklace in exchange for the lowland plains of the island. After the agreement, the Ati moved to the mountains while the Bornean settlers established new communities in the coastal and valley regions. \n \nThe narrative highlights one datu in particular, Datu Sumakwel, who is portrayed as the senior leader responsible for organizing laws, social customs, and early governance among the settlers. Maragtas also describes the early culture of Panay, including rituals, marriage traditions, and the blending of indigenous and migrant practices. Over time, this story has become an important cultural foundation for many Visayan groups, shaping local identity and origin beliefs. \n \nWhile cherished as folklore and cultural heritage, scholars consider Maragtas a mixture of oral traditions and creative writing rather than a strictly historical document.";
$content =  "<p>" . nl2br(htmlspecialchars($paragraph)) . "</p><br>".
'<iframe width="560" height="315" src="https://www.youtube.com/embed/K5N07eqfbz8?si=Q742ZvT7_knVmtQn" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>'
;

include 'bookTemplate.php';  // load the design
?>