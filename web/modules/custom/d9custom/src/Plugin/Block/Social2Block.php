<?php
namespace Drupal\d9custom\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Url;

/**
 * Social2 Media Block
 *
 * @Block(
 *   id = "social2_block",
 *   admin_label = @Translation("Social2 Media Block")
 * )
 */
class Social2Block extends BlockBase {
  public function build() {
    $output = '';
    $path = \Drupal::service('extension.list.module')->getPath('d9custom');
    
    $config = \Drupal::config('d9custom.settings');
	
	
	
	
    
    $phone = $config->get('phone');
    if( $phone!= '' ){
      $ph = [
        '#type' => 'link',
        '#attributes' => [ 'target'=>'_blank' ],
        '#title' => t('Internet - Shop'),
        '#url' => Url::fromUri('tel:' . $phone)
      ];
      
      $output .= '<li class="contact contact-ph">'. \Drupal::service('renderer')->render($ph) .'</li>';
    }
    
    $email = $config->get('email');
    if( $email!= '' ){
      $em = [
        '#type' => 'link',
        '#attributes' => [ 'target'=>'_blank' ],
        '#title' => t('Contact'),
        '#url' => Url::fromUri('mailto:' . $email)
      ];
      
      $output .= '<li class="contact contact-em">'. \Drupal::service('renderer')->render($em) .'</li>';
    }
	
	
    $openinghours = $config->get('openinghours');

    
    $build = [];
    if( $output!= '' ){
		$build['content'] = [ '#theme' => 'social2', '#output' => ['#markup'=>$output], '#openinghours' => $openinghours ];
	}
	
	$build['#cache'] = [
		'tags' => ['D9CUSTOM_UPDATED'], // #HERE! need to check again
	];
	return $build;
  }
}