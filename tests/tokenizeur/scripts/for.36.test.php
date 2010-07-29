<?php
			for($i=0;$i<count($regex);++$i)
				$this->a = preg_replace_callback($regex[$i],$replace[$i],$this->b);


?>