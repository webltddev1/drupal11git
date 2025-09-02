<?php 

namespace Drupal\d9custom\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Url;


/**
 * Provides a 'Booking Liberty search' Block.
 *
 * @Block(
 *   id = "bookinglibertysearch_block",
 *   admin_label = @Translation("Booking Liberty Search block"),
 * )
 */

class BookingLibertySearchBlock extends BlockBase{
	public function build() {
		$form = \Drupal::formBuilder()->getForm('Drupal\d9custom\Form\BookingLibertySearchForm');
		return $form;
	}
}