<?php 

namespace Drupal\d9custom\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Url;


/**
 * Provides a 'Booking search' Block.
 *
 * @Block(
 *   id = "bookingsearch_block",
 *   admin_label = @Translation("Booking Search block"),
 * )
 */

class BookingSearchBlock extends BlockBase{
  public function build() {
    $form = \Drupal::formBuilder()->getForm('Drupal\d9custom\Form\BookingSearchForm');
    return $form;
  }
}