<?php
namespace Drupal\d9custom\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Url;
use Drupal\Core\Link;

/**
 * Top Destinations Block
 *
 * @Block(
 *   id = "topdestinations_block",
 *   admin_label = @Translation("Top Destinations Block")
 * )
 */
class TopDestinationsBlock extends BlockBase{
	/**
	* {@inheritdoc}
	*/
	public function build(){
		$build = $terms = $term_count = [];
		$block_cat_limit = 3;
		$bundle = ['country'];
		$titre = t('Top Destinations');
		
		
		$url1 = Url::fromUserInput( '/hotels' );
		// $url1->setOption('query', [
			// 't' => '1',
		// ]);
		$url = $url1->toString();
		
		// Get articles in this category
		$query = \Drupal::entityQuery('taxonomy_term');
		$query->condition('status', 1);
		$query->condition('vid', $bundle, 'IN');
		$query->condition('field_top_destination', 1);
		$query->range(0, $block_cat_limit);
		$query->accessCheck(FALSE);

		$entity_ids = $query->execute();
		$entity_type = 'taxonomy_term';
		
		foreach( $entity_ids as $entity_id ){
			$view_builder = \Drupal::entityTypeManager()->getViewBuilder($entity_type);
			$storage = \Drupal::EntityTypeManager()->getStorage($entity_type);
			$terms[$entity_id] = $storage->load($entity_id);
			$term_count[$entity_id] = 0;
			
			$query = \Drupal::entityQuery('commerce_product')
				->accessCheck(FALSE)
				->condition('status', 1) // Only active products.
				->condition('field_country', $entity_id); // Adjust field_country to your actual field name
			$count = $query->count()->execute();
			$term_count[$entity_id] = $count;
		}

		if( !empty($terms) ){
			$build['content'] = [ '#theme' => 'topdestinations', '#terms' => $terms, '#titre' => $titre, '#url' => $url, '#term_count' => $term_count, ];
			
			$build['#cache'] = [
				'max-age' => 0,
				// 'tags' => ['D9CUSTOM_UPDATED'], // #HERE! need to check again
			];
		}
    
		return $build;
	}
}