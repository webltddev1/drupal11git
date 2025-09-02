<?php
namespace Drupal\d9custom\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Url;
use Drupal\Core\Link;

/**
 * Category Bonplans Block
 *
 * @Block(
 *   id = "catsbonplans_block",
 *   admin_label = @Translation("Category Bonplans Block")
 * )
 */
class CategoryBonplansBlock extends BlockBase{
	/**
	* {@inheritdoc}
	*/
	public function build(){
		$build = $nodes = [];
		$block_cat_limit = 4;
		$bundle = ['accomodation_booking','package'];
		$titre = t('Your Exceptional Stays');
		$urltxt = t('More exceptional stays');
		
		
		$url1 = Url::fromUserInput( '/hotels' );
		$url1->setOption('query', [
			'bestseller' => '1',
		]);
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
			$build['content'] = [ '#theme' => 'homelisting', '#nodes' => $nodes, '#titre' => $titre, '#url' => $url, '#urltxt' => $urltxt, ];
			$build['#attached']['library'][] = 'd9custom/d9custom-slick';
			
			$build['#cache'] = [
				'tags' => ['D9CUSTOM_UPDATED'], // #HERE! need to check again
			];
		}
    
		return $build;
	}
}