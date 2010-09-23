<?php if (isset($x[$y->z()])): ?>
    <?php echo $x[$y->z()]->z()*3 ?>
<?php elseif (true): $x = 0; else: ?>
    0
<?php endif; ?>