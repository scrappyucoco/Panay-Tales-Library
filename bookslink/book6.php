<?php
include '../booksData.php';

$book = $books['Bulalakaw']; // select the book
$paragraph = "The Legend of Bulalakaw” is a well-known Aklanon folk tale that tells of a fiery celestial being, often described as a spirit or deity, who descends from the sky in the form of a blazing ball of light. According to the story, the Bulalakaw would streak across the heavens like a falling star, bringing awe, fear, and wonder to the people who witnessed its glowing tail. In many versions, the Bulalakaw is believed to come down to the rivers or forests of Aklan, either to warn the community of danger or to test their courage and unity. \n \nSome say the spirit appeared before times of calamity, serving as a sign for villagers to prepare for storms, droughts, or sickness. Other variations portray the Bulalakaw as a guardian being who rewards kind-hearted people with luck and punishes those who act with cruelty or disrespect toward nature. Because of its blazing form, locals associated the Bulalakaw with fire, lightning, and the heavens, treating it with a mixture of reverence and caution. Parents would often warn their children not to wander out at night, saying the Bulalakaw might appear suddenly from the sky. \n \nToday, the legend remains an important part of Aklan’s folklore, symbolizing the connection between the natural world and the mystical forces believed to watch over the community.






";
$content = "<p>" . nl2br(htmlspecialchars($paragraph)) . "</p>";

include 'bookTemplate.php';  // load the design
?>