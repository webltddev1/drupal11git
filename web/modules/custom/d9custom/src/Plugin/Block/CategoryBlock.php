<?php
namespace Drupal\d9custom\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Url;
use Drupal\Core\Link;

/**
 * Category Block
 *
 * @Block(
 *   id = "cats_block",
 *   admin_label = @Translation("Category Block")
 * )
 */
class CategoryBlock extends BlockBase {
  /**
  * {@inheritdoc}
  */
  public function blockForm($form, FormStateInterface $formState) {
    //get all categories
    $categories_tree = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree('establishment_category',0,NULL,TRUE);
    $categories = ['all'=>'All'];
    foreach($categories_tree as $cat){
      $id = $cat->tid->value;
      $categories[$id] = $cat->name->value;
    }
    
    $def = (isset($this->configuration['d9custom_block_category']))? $this->configuration['d9custom_block_category']:'';
    $form['block_cat'] = [
      '#type' => 'select',
      '#options' => $categories,
      '#title' => t('Category'),
      '#required' => TRUE,
      '#default_value' => $def
    ];
    
    $limit = (isset($this->configuration['d9custom_block_limit']))? $this->configuration['d9custom_block_limit']:'';
    $form['block_limit'] = [
      '#type' => 'number',
      '#min' => 0,
      '#max' => 10,
      '#title' => t('Limit'),
      '#required' => TRUE,
      '#default_value' => $limit
    ];

    return $form;
  }
  
  /**
  * {@inheritdoc}
  */
  public function blockSubmit($form, FormStateInterface $formState) {
    $this->configuration['d9custom_block_category'] = $formState->getValue('block_cat');
    $this->configuration['d9custom_block_limit'] = $formState->getValue('block_limit');
  }
  
  
  public function build() {
		$build = [];
		$term_name = $term_desc = '';
		$bgcolor = '#FFFFFF';
		
    $block_cat_id = $this->configuration['d9custom_block_category'];
    $block_cat_limit = (isset($this->configuration['d9custom_block_limit']))? $this->configuration['d9custom_block_limit']:10;
    
    $curr_langcode = \Drupal::languageManager()->getCurrentLanguage(\Drupal\Core\Language\LanguageInterface::TYPE_CONTENT)->getId();
    
		if( $block_cat_id!='all' ){
				$term = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($block_cat_id);
				if( $term->hasTranslation($curr_langcode) ){
					$term = $term->getTranslation($curr_langcode);
				}
    
				$term_name = $term->name->value;
				$bgcolor = '#FFFFFF';//$term->field_color->color;
				$term_desc['#markup'] = $term->description->value;
		}
    
    if( $block_cat_limit==0 || $block_cat_limit=='' ) $block_cat_limit = 10;
		
		
		
		$category_hotels = [1,29];//hotel deals, rodrigues
		$categories_activity = [17,20,28,31];//31=events = removed
		$bundle = ['accomodation_booking'];
		
		if( $block_cat_id=='all' ){
			$bundle = ['accomodation_booking','activity','single_day_event'];
		}
		elseif( in_array($block_cat_id,$categories_activity) ){//activities, spa, restaurant
			$bundle = ['activity'];
		}
		elseif( $block_cat_id==30 ){//day use
			$bundle = ['single_day_event'];
		}
		
		
    
    $output = $articles = "";
		$nodes = [];

    // Get articles in this category
    $query = \Drupal::entityQuery('commerce_product');
    $query->condition('status', 1);
    $query->condition('type', $bundle, 'IN');
		
		if( $block_cat_id!='all' ){
			$query->condition('field_prod_category.target_id', $block_cat_id);
		}
		
    $query->sort('created', 'DESC');
    // $query->sort('field_position2', 'ASC');
    $query->range(0, $block_cat_limit);

    $entity_ids = $query->execute();
    $entity_type = 'commerce_product';
    $view_mode = 'teaser';
    
    foreach( $entity_ids as $entity_id ){
      $view_builder = \Drupal::entityTypeManager()->getViewBuilder($entity_type);
      $storage = \Drupal::EntityTypeManager()->getStorage($entity_type);
      $prod = $storage->load($entity_id);
			
			// $node_translated = \Drupal::service('entity.repository')->getTranslationFromContext($prod, $curr_langcode);
     
      $nodes[] = $view_builder->view($prod, $view_mode);
    }

		if( !empty($nodes) ){
			// $url = Url::fromRoute('entity.taxonomy_term.canonical', ['taxonomy_term' => $block_cat_id])->toString();
			// $url = Url::fromRoute( 'd9custom.categorylisting', ['tid' => $block_cat_id] )->toString();
			// $url = Url::fromUserInput( '/catalog/'.$block_cat_id )->toString();
			
			$url = $url1 = '';
			
			if( $block_cat_id!='all' ){
				$url = \Drupal::service('path_alias.manager')->getAliasByPath( '/catalog/'.$block_cat_id );
				
				$url1 = Url::fromUserInput( '/catalog/'.$block_cat_id );
				$url1->setOption('query', [
					'sort_by' => 'field_prod_bestseller',
					'sort_order' => 'DESC',
				]);
			}
			
			$build['content'] = [ '#theme' => 'categoryblock', '#nodes' => $nodes, '#url' => $url, '#url1' => $url1, '#term_name' => $term_name, '#term_desc' => $term_desc, '#bgcolor'=>$bgcolor ];
			$build['#attached']['library'][] = 'd9custom/d9custom-slick';
			
			$build['#cache'] = [
				'tags' => ['D9CUSTOM_UPDATED'], // #HERE! need to check again
			];
		}
    
    return $build;
  }

  public function getCacheContexts() {
    //if you depends on \Drupal::routeMatch()
    //you must set context of this block with 'route' context tag.
    //Every new route this block will rebuild
    return Cache::mergeContexts(parent::getCacheContexts(), array('route'));
  }
}