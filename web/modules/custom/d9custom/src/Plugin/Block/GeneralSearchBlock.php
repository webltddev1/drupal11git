<?php 

namespace Drupal\d9custom\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Url;


/**
 * Provides a 'General search' Block.
 *
 * @Block(
 *   id = "generalsearch_block",
 *   admin_label = @Translation("General Search block"),
 * )
 */

class GeneralSearchBlock extends BlockBase{
  public function build() {
    $form = \Drupal::formBuilder()->getForm('Drupal\d9custom\Form\GeneralSearchForm');
    return $form;
  }
}