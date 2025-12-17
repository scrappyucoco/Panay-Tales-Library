<?php
include '../booksData.php';

$book = $books['Tumao']; // select the book
$paragraph = " 'The Mystical Tumao and the Hidden City of Iloilo' is a Panay folk tale that tells of Tumao, a mysterious figure believed to guard a secret city said to lie beneath or within the remote highlands of Iloilo. In the story, Tumao is often described as a supernatural being or an enchanted guardian who protects the entrance to this hidden realm, allowing only the pure of heart to glimpse its wonders. \n \nThe Hidden City itself is portrayed as a place untouched by time, filled with golden structures, flourishing gardens, and a peaceful people who live in harmony with nature. According to legend, travelers who accidentally wander close to the city’s boundaries experience strange phenomena—soft glowing lights, enchanting music, or sudden changes in the landscape. Tumao appears to warn outsiders, guiding them away to ensure the city remains undisturbed by greed or human conflict. The tale emphasizes the idea that spiritual worlds and earthly realms exist side by side, separated only by purity, respect, and intention. \n \nMany versions portray the Hidden City as a symbol of Iloilo’s ancient cultural richness, hinting at lost civilizations or enchanted communities believed to inhabit the mountains. Today, the story continues to be told as a reminder of the island’s mystical heritage and the enduring belief that unseen worlds still dwell within Panay’s landscapes.

";
$content = "<p>" . nl2br(htmlspecialchars($paragraph)) . "</p>";

include 'bookTemplate.php';  // load the design
?>