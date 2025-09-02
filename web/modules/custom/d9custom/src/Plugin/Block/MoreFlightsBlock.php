<?php
namespace Drupal\d9custom\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Url;
use Drupal\Core\Link;

/**
 * More Flights Block
 *
 * @Block(
 *   id = "moreflights_block",
 *   admin_label = @Translation("More Flights Block")
 * )
 */
class MoreFlightsBlock extends BlockBase{
	/**
	* {@inheritdoc}
	*/
	public function build(){
		$build = $nodes = [];
		$block_cat_limit = 3;
		$bundle = ['package'];
		$titre = t('More travel ideas');
		
		
		$url1 = Url::fromUserInput( '/packages' );
		// $url1->setOption('query', [
			// 't' => '1',
		// ]);
		$url = $url1->toString();
		
		// Get articles in this category
		$query = \Drupal::entityQuery('commerce_product');
		$query->condition('status', 1);
		$query->condition('type', $bundle, 'IN');
		$query->condition('field_prod_bestseller', 1);
		$query->sort('created', 'DESC');
		$query->range(0, $block_cat_limit);
		$query->accessCheck(FALSE);

		$entity_ids = $query->execute();
		$entity_type = 'commerce_product';
		$view_mode = 'home';
		
		foreach( $entity_ids as $entity_id ){
			$view_builder = \Drupal::entityTypeManager()->getViewBuilder($entity_type);
			$storage = \Drupal::EntityTypeManager()->getStorage($entity_type);
			$prod = $storage->load($entity_id);
			$nodes[] = $view_builder->view($prod, $view_mode);
		}

		if( !empty($nodes) ){
			$build['content'] = [ '#theme' => 'homelisting2', '#nodes' => $nodes, '#titre' => $titre, '#url' => $url, ];
			// $build['#attached']['library'][] = 'd9custom/d9custom-slick';
			
			$build['#cache'] = [
				'max-age' => 0,
				// 'tags' => ['D9CUSTOM_UPDATED'], // #HERE! need to check again
			];
		}
    
		return $build;
	}
}