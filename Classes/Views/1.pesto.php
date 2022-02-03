#Extends = [2]
#Block(content1)
<div>A</div>
<?php
for ($i = 0; $i<10;$i++) {
    echo '<p>'.$i.'</p>';
}
?>
#Endblock
