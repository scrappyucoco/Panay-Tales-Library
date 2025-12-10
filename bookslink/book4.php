<?php
include '../booksData.php';

$book = $books['Tikbalang']; // select the book
$paragraph = "Tikbalang sa Bukid ng Madya-as” is a folk tale from Antique that tells of a powerful tikbalang said to roam the slopes and forest trails surrounding the sacred mountain of Madya-as. In the story, the tikbalang is described as a tall, horse-headed creature with long limbs, fiery eyes, and supernatural speed, capable of confusing travelers and leading them in circles within the dense wilderness. \n \nMany accounts say it appears to those who wander too far into the mountain or show disrespect to the spirits residing in nature. Some versions portray the tikbalang not as purely evil but as a guardian spirit of Madya-as, testing the character and intentions of anyone who enters its domain. Those who treat the forest with humility are left unharmed, while the arrogant or careless are frightened, misled, or chased away. \n \nThe legend also reflects the ancient belief that the mountains of Panay, especially Madya-as, are home to enchanted beings and ancestral spirits. Over time, tales of the tikbalang became warnings for travelers to stay cautious, respect the environment, and avoid wandering alone in unfamiliar terrain. Today, the story remains part of Antique’s rich folklore, symbolizing the mystery and spiritual presence believed to dwell within the island’s oldest mountains.
";
$content = "<p>" . nl2br(htmlspecialchars($paragraph)) . "</p>";

include 'bookTemplate.php';  // load the design
?>