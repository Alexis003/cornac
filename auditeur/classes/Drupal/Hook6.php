<?php 


class Drupal_Hook6 extends Drupal_Hook7 {
	protected	$title = 'Spot Drupal6 hooks';
	protected	$description = 'Spot function with Drupal6 hook suffixes. The more there are, the more likely the file will be a Drupal 7 module';

	function __construct($mid) {
        parent::__construct($mid);
        $this->hook_regexp = '_('.join('|',modules::getDrupal6Hooks()).')$';
	}
}

?>