<?php
namespace Drupal\d9custom\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Url;
use Drupal\file\Entity\File;

/**
 * Footer2 Block
 *
 * @Block(
 *   id = "footer2_block",
 *   admin_label = @Translation("Footer2 Block")
 * )
 */
class Footer2Block extends BlockBase {
  public function build(){
		$config = \Drupal::config('d9custom.settings');
		
		$footer_txt2 = $config->get('footer_txt2');
		$text = (isset($footer_txt2['value']))? $footer_txt2['value']:'';
		$paymentimages = "<span class='pms'>". t('Our Partners:') ."</span>";
		
		for($i=1; $i<=15; $i++ ){
			$paymentimage = $config->get('paymentimage'.$i, '');
			if( $paymentimage!= '' ){
				$file1 = File::load($paymentimage);
				$paymentimage_uri = $file1->getFileUri();
				
				$u = \Drupal::service('file_url_generator')->generateAbsoluteString($paymentimage_uri);
				$paymentimage_url = Url::fromUri( $u )->toString();
				
				$paymentimages .= "<span class='pm'><img src='$paymentimage_url' /></span>";
			}
		}
		
		$build = [];
		
		$build['content'] = [ '#markup' => $paymentimages ];
		
		$build['#cache'] = [
			'tags' => ['D9CUSTOM_UPDATED'], // #HERE! need to check again
		];
    
    return $build;
  }
}