<?php foreach ($this->data as $size => $source) {
    echo $this->escape($source); ?>|<?php echo $size; 
}
?>