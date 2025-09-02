<?php
namespace Drupal\d9custom\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Url;

/**
 * Social Media Block
 *
 * @Block(
 *   id = "social_block",
 *   admin_label = @Translation("Social Media Block")
 * )
 */
class SocialBlock extends BlockBase {
  public function build() {
    $output = '';
    $path = \Drupal::service('extension.list.module')->getPath('d9custom');
    
    $config = \Drupal::config('d9custom.settings');
	
	
	
	
    
    // $email = $config->get('email');
    // if( $email!= '' ){
      // $em = [
        // '#type' => 'link',
        // '#attributes' => [ 'target'=>'_blank' ],
        // '#title' => ['#theme' => 'image', '#uri' => $path.'/img/mt.png' ],
        // '#url' => Url::fromUri('mailto:' . $email)
      // ];
      
      // $output .= '<li class="social social-em">'. \Drupal::service('renderer')->render($em) .'</li>';
    // }
	
    
    $facebook = $config->get('facebook');
    if( $facebook!= '' ){
      $fb = [
        '#type' => 'link',
        '#attributes' => [ 'target'=>'_blank' ],
        '#title' => ['#theme' => 'image', '#uri' => $path.'/img/fbk-icon.png' ],
        '#url' => Url::fromUri( $facebook )
      ];
      
      $output .= '<li class="social social-fb">'. \Drupal::service('renderer')->render($fb) .'</li>';
    }
    
    $youtube = $config->get('youtube');
    if( $youtube!= '' ){
      $yt = [
        '#type' => 'link',
        '#attributes' => [ 'target'=>'_blank' ],
        '#title' => ['#theme' => 'image', '#uri' => $path.'/img/social-yt.png' ],
        '#url' => Url::fromUri( $youtube )
      ];
      
      $output .= '<li class="social social-yt">'. \Drupal::service('renderer')->render($yt) .'</li>';
    }
    
    $instagram = $config->get('instagram');
    if( $instagram!= '' ){
      $tp = [
        '#type' => 'link',
        '#attributes' => [ 'target'=>'_blank' ],
        '#title' => ['#theme' => 'image', '#uri' => $path.'/img/insta-icon.png' ],
        '#url' => Url::fromUri( $instagram )
      ];
      
      $output .= '<li class="social social-tp">'. \Drupal::service('renderer')->render($tp) .'</li>';
    }


    
    
    if( $output!= '' ) $output = '<div class="titre">'. t('Follow us') .'</div><ul id="social-links" class="clearfix">'. $output .'</ul>';
    return [ '#markup' => $output ];
  }
}