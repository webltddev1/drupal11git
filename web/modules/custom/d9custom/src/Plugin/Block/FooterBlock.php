<?php
namespace Drupal\d9custom\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Url;

/**
 * Footer Block
 *
 * @Block(
 *   id = "footer_block",
 *   admin_label = @Translation("Footer Block")
 * )
 */
class FooterBlock extends BlockBase {
  public function build(){
		$config = \Drupal::config('d9custom.settings');
		
		$footer_txt = $config->get('footer_txt');
		$text = (isset($footer_txt['value']))? $footer_txt['value']:'';
		
		$build = [];
		
		$build['content'] = [ '#markup' => $text ];
		
		$build['#cache'] = [
			'tags' => ['D9CUSTOM_UPDATED'], // #HERE! need to check again
		];
    
    return $build;
  }
}