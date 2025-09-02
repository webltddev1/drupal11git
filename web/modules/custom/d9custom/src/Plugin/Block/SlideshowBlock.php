<?php
namespace Drupal\d9custom\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Url;
use Drupal\Core\Link;

/**
 * Slideshow Block
 *
 * @Block(
 *   id = "slideshow_block",
 *   admin_label = @Translation("Slideshow Block")
 * )
 */
class SlideshowBlock extends BlockBase {
  
  
  public function build() {
		$build = $diaporama = [];
		
		$curr_langcode = \Drupal::languageManager()->getCurrentLanguage(\Drupal\Core\Language\LanguageInterface::TYPE_CONTENT)->getId();
		
		$limit = 10;
		$bundle = 'homepage_slide';
	
		$query = \Drupal::entityQuery('node');
		$query->condition('status', 1);
		$query->condition('type', $bundle);
		$query->sort('created', 'DESC');
		$query->accessCheck(FALSE);
		// $query->sort('field_position', 'ASC');
		$query->range(0, $limit);

		$entity_ids = $query->execute();
		$entity_type = 'node';
		
		foreach( $entity_ids as $entity_id ){
			$view_builder = \Drupal::entityTypeManager()->getViewBuilder($entity_type);
			$storage = \Drupal::entityTypeManager()->getStorage($entity_type);
			$node_loaded = $storage->load($entity_id);
			
			$node_translated = \Drupal::service('entity.repository')->getTranslationFromContext($node_loaded, $curr_langcode);
			
			$view_mode = 'full';
			$diaporama[] = $view_builder->view($node_translated, $view_mode);
			
		}
		
		
		if( !empty($diaporama) ){
			$build['content'] = [ '#theme' => 'slideshow', '#slides' => $diaporama ];
			$build['#attached']['library'][] = 'd9custom/d9custom-slick';
			
			$build['#cache'] = [
				'tags' => ['D9CUSTOM_UPDATED'], // #HERE! need to check again
			];
		}
		

    
    
    return $build;
  }

  public function getCacheTags() {
    //With this when your node change your block will rebuild
    if ($node = \Drupal::routeMatch()->getParameter('node')) {
      //if there is node add its cachetag
      return Cache::mergeTags(parent::getCacheTags(), array('node:' . $node->id()));
    } else {
      //Return default tags instead.
      return parent::getCacheTags();
    }
  }

  public function getCacheContexts() {
    //if you depends on \Drupal::routeMatch()
    //you must set context of this block with 'route' context tag.
    //Every new route this block will rebuild
    return Cache::mergeContexts(parent::getCacheContexts(), array('route'));
  }
}