<?php
			foreach($escape as $probe)
				$pattern=str_replace($probe,"\\$probe",$pattern);
			$pattern=str_replace('?*','*',$x);

	?>
