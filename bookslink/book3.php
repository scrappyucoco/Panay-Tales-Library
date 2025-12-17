<?php
include '../booksData.php';

$book = $books['Bakunawa'];
$paragraph = "'The Bakunawa and the Seven Moons' is a Visayan myth that explains how the world once had seven radiant moons lighting up the night sky. According to the story, a gigantic serpent-like dragon called the Bakunawa became mesmerized by the beauty of the moons and leapt into the sky to swallow them one by one. Each time the Bakunawa devoured a moon, darkness spread across the earth, terrifying the people who depended on the moons’ light. \n \nTo stop the creature, villagers would bang pots, shout loudly, and create noise to frighten the Bakunawa into releasing the moon it had swallowed. The myth portrays the Bakunawa as both a destructive and awe-inspiring being, connected to ancient beliefs about balance, nature, and celestial events. Over time, the disappearance of the moons was used to explain lunar eclipses, with people believing the Bakunawa was trying to consume the last remaining moon. The tale also reflects pre-colonial Filipino cosmology, where spirits, nature, and the heavens were closely linked to daily life. \n \nToday, the story remains one of the most famous Philippine myths, symbolizing the struggle between darkness and light.";
$content = "<p>" . nl2br(htmlspecialchars($paragraph)) . "</p><br>".
'<iframe width="560" height="315" src="https://www.youtube.com/embed/xfd4Mk3eMBs?si=Hd-0a0S_Qy-EbjsW" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>'
;

include 'bookTemplate.php';
?>



