<?php
include '../booksData.php';

$book = $books['Alunsina']; // select the book
$paragraph = "“The Legend of Tungkung Langit and Alunsina” is a well-known Visayan creation myth that explains how the world, the seas, and the skies came into existence. The story tells of Tungkung Langit, a calm, hardworking god devoted to order, and Alunsina, a beautiful yet carefree goddess who loved adornment and leisure. As husband and wife, they lived harmoniously in the heavens until Alunsina’s jealousy led her to send the wind to spy on Tungkung Langit, believing he was being unfaithful. Feeling deeply hurt and angered, Tungkung Langit scolded Alunsina, prompting her to leave their heavenly home and vanish without a trace. Overwhelmed with sorrow, Tungkung Langit searched endlessly for her, and when he could not find her, he turned his grief into creation by arranging the world below—forming the sky, the sea, the land, and the natural order of the universe. \n \nSome versions say that the gentle drizzles and soft winds are Tungkung Langit’s continuing cries and sighs as he longs for his lost wife. The myth reflects themes of love, separation, and the longing for harmony between opposing forces. Today, it remains one of the most enduring Filipino origin stories, offering a poetic explanation of natural phenomena and the beginnings of the world.


";
$content = "<p>" . nl2br(htmlspecialchars($paragraph)) . "</p>";

include 'bookTemplate.php';  // load the design
?>