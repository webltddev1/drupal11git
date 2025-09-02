<?php 

namespace Drupal\d9custom\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Url;


/**
 * Provides a 'Koedia search' Block.
 *
 * @Block(
 *   id = "koediasearch_block",
 *   admin_label = @Translation("Koedia Search block"),
 * )
 */

class KoediaSearchBlock extends BlockBase{
	public function build() {
		$form = \Drupal::formBuilder()->getForm('Drupal\d9custom\Form\KoediaSearchForm');
		return $form;
	}
}