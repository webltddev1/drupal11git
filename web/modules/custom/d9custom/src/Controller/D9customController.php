<?php
namespace Drupal\d9custom\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\Core\Datetime\DrupalDateTime;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;
use Drupal\commerce_product\Entity\Product;
use Drupal\commerce_product\Entity\ProductVariation;
use Drupal\commerce_order\Entity\Order;

class D9customController{
	public function testor(){
		return ['#markup'=>'below does not work'];
		// Load the original product by its ID.
		$originalProductId = 2;
		$originalProduct = Product::load($originalProductId);

		if ($originalProduct) {
			// Clone the product by creating a new product entity.
			$clonedProduct = Product::create([
				'type' => $originalProduct->bundle(),
			]);

		// Copy field values from the original product to the cloned product.
		foreach ($originalProduct->getFieldDefinitions() as $field_name => $field_definition) {
			if ($field_name != 'variations') { // Exclude variations field.
				$value = $originalProduct->get($field_name)->getValue();
				$clonedProduct->set($field_name, $value);
			}
		}

		// $clonedProduct->set('id', '');
		// Save the cloned product to persist the field values.
		$clonedProduct->save();

		} else {

		}


		
		return ['#markup'=>'done2'];
		// return ['#markup'=>'xxxxxxxxxxxx'];
		$order = Order::load(7);
		$connection = \Drupal::database();
		
		$order_items = $order->getItems();
		foreach( $order_items as $order_item ){
			$product 	= $order_item->getPurchasedEntity();
			echo $product->bundle();die;
			
			$productvar_loaded = ProductVariation::load( $order_item->id() );
			
			$field_prodvar_adult 	= $productvar_loaded->get('field_prodvar_adult')->getValue();
			$adult = $field_prodvar_adult[0]['value'];
			
			$field_prodvar_child 	= $productvar_loaded->get('field_prodvar_child')->getValue();
			$child = $field_prodvar_child[0]['value'];
			
			$field_prodvar_teen 	= $productvar_loaded->get('field_prodvar_teen')->getValue();
			$teen = $field_prodvar_teen[0]['value'];
			
			$field_prodvar_offer 	= $productvar_loaded->get('field_prodvar_offer')->getValue();
			$offer = $field_prodvar_offer[0]['target_id'];
			
			$field_prodvar_checkin 	= $productvar_loaded->get('field_prodvar_checkin')->getValue();
			$checkin = strtotime($field_prodvar_checkin[0]['value']);
			
			$field_prodvar_checkout = $productvar_loaded->get('field_prodvar_checkout')->getValue();
			$checkout = strtotime($field_prodvar_checkout[0]['value']);
			
			// var_dump($field_prodvar_teen);die;
			
			$product 	= $order_item->getPurchasedEntity();
			$hotel_id = $productvar_loaded->getProductId();
			
			
			// get all dates
			for( $dd=$checkin; $dd<$checkout; $dd+=(86400) ){
				$date = date('Y-m-d',$dd);
				$k = $hotel_id.'-'.$offer.'-'.$date;
				
				$sql = "UPDATE {d9offers_availability} SET nb_available = nb_available-1 WHERE kkey='$k'";
				$query = $connection->query($sql);
			}
		}
		return ['#markup'=>'xxxxxxxxxxxx'];
	}
	
	
	
	
	public function homepage($tid=''){
		$build = [];
		
		
		$promos = [];
		
		// ** Show latest 4 hotels having a promo (special rate)
		
    $connection = \Drupal::database();
    $get_transactions = $connection->select('d9offers_promo', 'offr');
    $get_transactions->fields('offr');
    $get_transactions->condition('date_end', time(), '>=');
    $get_transactions->condition('enabled', 1);
    $get_transactions->orderby('date_start', 'ASC');
    $get_transactions->range(0,20);
    $num_results = $get_transactions->countQuery()->execute()->fetchField();
    
    if( $num_results==0 ){
      // $build['empty'] = [ '#markup' => '<div class="info empty">'.t('No results.').'</div>'  ];
      $build['empty'] = [ '#markup' => '<div class="info empty"></div>'  ];
      return $build;
    }
				
    $results = $get_transactions->execute();
    
    $entity_type = 'commerce_product';
    $view_mode = 'teaser';
		$view_builder = \Drupal::entityTypeManager()->getViewBuilder($entity_type);
		$storage = \Drupal::EntityTypeManager()->getStorage($entity_type);
		$x=0;
    foreach( $results as $result ){
			$hotel_id = $result->hotel_id;
      $prod = $storage->load($hotel_id);
			if( $prod && $prod->status->getString() == 1 ){
				$nodes[] = $view_builder->view($prod, $view_mode);
				$x++;
				if($x==4) break;
			}
		}
		
    $block_cat_id = 16;
    $block_cat_limit = 4;
    
    $curr_langcode = \Drupal::languageManager()->getCurrentLanguage(\Drupal\Core\Language\LanguageInterface::TYPE_CONTENT)->getId();
    
    $term = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($block_cat_id);
    if( $term->hasTranslation($curr_langcode) ){
      $term = $term->getTranslation($curr_langcode);
    }
    
    $term_name = $term->name->value;
    $bgcolor = '#FFC845';
    $term_desc['#markup'] = '<h2>KLIK MORIS</h2><h1>Our Best offers</h1><p>The best offers & promotions for Mauritians and residents.</p>';

		if( !empty($nodes) ){
			$build = [];
			
			//list of categories
			$cats = [];
			$categories_tree = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree('establishment_category',0,NULL,TRUE);
			$categories = [];
			foreach($categories_tree as $cat){
				$cid = $cat->tid->value;
				$url = \Drupal::service('path_alias.manager')->getAliasByPath( '/catalog/'.$cid );
				
				$cats[$cid]['name'] = $cat->name->value;
				$cats[$cid]['url'] = $url;
			}
			
			
			// $url = Url::fromRoute('entity.taxonomy_term.canonical', ['taxonomy_term' => $block_cat_id])->toString();
			// $url = Url::fromRoute( 'd9custom.categorylisting', ['tid' => $block_cat_id] )->toString();
			// $url = Url::fromUserInput( '/catalog/'.$block_cat_id )->toString();
			$url = \Drupal::service('path_alias.manager')->getAliasByPath( '/catalog/'.$block_cat_id );
			
			$build['content'] = [ '#theme' => 'bestoffers', '#nodes' => $nodes, '#url' => $url, '#term_name' => $term_name, '#term_desc' => $term_desc, '#bgcolor'=>$bgcolor, '#cats'=>$cats ];
			
			$build['#cache'] = [
				'tags' => ['D9CUSTOM_UPDATED'], // #HERE! need to check again
			];
		}
		else{
			$build = [];
			$build['#markup'] = '';
		}
		
		return $build;
	}
	
	
	// news lists
	public function categorylisting_title($tid=''){
		if( $tid !='' ){
			$term = Term::load($tid);
			
			if( isset($term->name->value) ){
				return $term->name->value;
			}
    }
    
		return t('Category List');
	}
	
	
	
	//news list
	public function categorylisting($tid=''){
		if( $tid !='' ){
			$build = [];
			$term = Term::load($tid);
			
			if( isset($term->name->value) ){
				$nodes = $this->getListing($tid, 12);
				
				if( !empty($nodes) ){
					$build['content'] = [ '#theme' => 'categorylisting', '#nodes' => $nodes ];
					$build['pager'] 	= [ '#type' => 'pager' ];
					
					$build['#cache'] = [
						'tags' => ['D9CUSTOM_UPDATED'], // #HERE! need to check again
					];
				}
				
				return $build;
			}
			
			throw new NotFoundHttpException();
    }
    else{
      throw new NotFoundHttpException();
    }
		
	}
	
	
	private function getListing( $tid, $limit = 8 ){
		$nodes = [];
		$curr_langcode = \Drupal::languageManager()->getCurrentLanguage(\Drupal\Core\Language\LanguageInterface::TYPE_CONTENT)->getId();
		
    if( $limit==0 ) $limit = 8;
		
		$connection = \Drupal::database();
		$output = [];
		$offers = $mealplans = $hotels = $establishments = $regions = $locations = $types = [];
		
		
		//get all available offers
		$get_offers = $connection->select('d9offers_prices', 'offr');
		$get_offers->fields('offr');
		// $get_offers->condition('hotel_id', $hotel_id);
		$get_offers->condition('date_end', time(), '>=');
		$get_offers->orderby('id', 'DESC');
		
		
		// FILTERS
		$filters_mealplans = \Drupal::request()->query->get('mp');
		if( isset($filters_mealplans) && !empty($filters_mealplans) ){
			$get_offers->condition('mealplan_id', $filters_mealplans, 'IN');
		}
		
		$results = $get_offers->execute();
		foreach( $results as $result ){
			$offers[$result->hotel_id][] = $result;
		}
		
		if( !empty($offers) ){
			$hotel_ids = array_keys($offers);
			
			
			$bundle = 'establishment';
			
			$query = \Drupal::entityQuery('node');
			$query->condition('status', 1);
			$query->condition('type', $bundle);
			$query->condition('field_category1.target_id', $tid);
			$query->condition('nid', $hotel_ids, 'IN');
			$query->sort('created', 'DESC');
			// $query->sort('field_position2', 'ASC');
			$query->pager($limit);
			
	// FILTERS
			$filters_types = \Drupal::request()->query->get('tp');
			if( isset($filters_types) && !empty($filters_types) ){
				$query->condition('field_establishment_type.target_id', $filters_types, 'IN');
			}
			$filters_regions = \Drupal::request()->query->get('reg');
			if( isset($filters_regions) && !empty($filters_regions) ){
				$query->condition('field_region.target_id', $filters_regions, 'IN');
			}
			$filters_locations = \Drupal::request()->query->get('loc');
			if( isset($filters_locations) && !empty($filters_locations) ){
				$query->condition('field_town.target_id', $filters_locations, 'IN');
			}
			

			$entity_ids = $query->execute();
			$entity_type = 'node';
			$view_mode = 'teaser';
			foreach( $entity_ids as $entity_id ){
				if( isset($offers[$entity_id]) ){
					$view_builder = \Drupal::entityTypeManager()->getViewBuilder($entity_type);
					$storage = \Drupal::EntityTypeManager()->getStorage($entity_type);
					$node = $storage->load($entity_id);
					
					$node_translated = \Drupal::service('entity.repository')->getTranslationFromContext($node, $curr_langcode);
				 
					$nodes[] = $view_builder->view($node_translated, $view_mode);
				}
			}
		}
		
		return $nodes;
	}
	
	
	
	private function getListing2( $tid, $limit = 8 ){//Deprecated :)
		$curr_langcode = \Drupal::languageManager()->getCurrentLanguage(\Drupal\Core\Language\LanguageInterface::TYPE_CONTENT)->getId();
		
    if( $limit==0 ) $limit = 8;
		
    $bundle = 'establishment';
    $output = $articles = "";
		$nodes = [];

    // Get articles in this category
    $query = \Drupal::entityQuery('node');
    $query->condition('status', 1);
    $query->condition('type', $bundle);
    $query->condition('field_category1.target_id', $tid);
    $query->sort('created', 'DESC');
    // $query->sort('field_position2', 'ASC');
    // $query->range(0, $limit);
		$query->pager($limit);

    $entity_ids = $query->execute();
    $entity_type = 'node';
    $view_mode = 'teaser';
    
    foreach( $entity_ids as $entity_id ){
      $view_builder = \Drupal::entityTypeManager()->getViewBuilder($entity_type);
      $storage = \Drupal::EntityTypeManager()->getStorage($entity_type);
      $node = $storage->load($entity_id);
			
			$node_translated = \Drupal::service('entity.repository')->getTranslationFromContext($node, $curr_langcode);
     
      $nodes[] = $view_builder->view($node_translated, $view_mode);
    }


		return $nodes;
	}
	
	
    public function generalsearch(){
			$curr_langcode = \Drupal::languageManager()->getCurrentLanguage(\Drupal\Core\Language\LanguageInterface::TYPE_CONTENT)->getId();
			
			$matches = [];
			$kword = \Drupal::request()->query->get('q');
			
			$query = \Drupal::entityQuery('commerce_product');
			$query->condition('status', 1);
			// $query->condition('type', $bundle);
			$query->condition('title', '%'.$kword.'%', 'LIKE');
			// $query->condition('nid', $hotel_ids, 'IN');
			$query->sort('created', 'DESC');
			$query->range(0,10);
			
			
			$entity_ids = $query->execute();
		
			foreach( $entity_ids as $entity_id ){
				$storage = \Drupal::EntityTypeManager()->getStorage('commerce_product');
				$venue = $storage->load($entity_id);
				$node_translated = \Drupal::service('entity.repository')->getTranslationFromContext($venue, $curr_langcode);
				
				$room_types[$entity_id] = $node_translated->title->value;
				
				
				$matches[$entity_id] = $node_translated->title->value;
			}
			

			// foreach ($result as $row) {
				// $matches[$row->nid] = check_plain($row->title);
			// }
			// $matches[] = array('value' => $kword, 'label' => 'x');
			return new JsonResponse($matches);
    }
	
	
	
	
	
    public function autocompletecountries(Request $request){
        $matches = [];
        // Get the typed string from the URL, if it exists.
        if( $input = $request->query->get('q') ){
            // $matches[] = ['value' => 'test', 'label' => 'test'];
			
			if( strlen($input) >= 3 ){
				$query = \Drupal::entityQuery('taxonomy_term')
					->condition('status',1)
					->condition('vid', 'country')
					->condition('name', '%'.$input.'%', 'LIKE')
					->accessCheck(FALSE);
					// ->rance(0,10);
				$tids = $query->execute();
				if( !empty($tids) ){
					$terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadMultiple($tids);
					foreach( $terms as $term ){
						$matches[] = [
							'value'=>$term->getName(),
							'label'=>$term->getName(),
						];
					}
				}
			}
			
			
        }
        return new JsonResponse($matches);
        
    }
	
	public function getalldestinations(){
		$build = $terms = $term_count = [];
		$block_cat_limit = 55;
		$bundle = ['country'];
		$titre = '';
		
		
		$url1 = Url::fromUserInput( '/hotels' );
		// $url1->setOption('query', [
			// 't' => '1',
		// ]);
		$url = $url1->toString();
		
		// Get articles in this category
		$query = \Drupal::entityQuery('taxonomy_term');
		$query->condition('status', 1);
		$query->condition('vid', $bundle, 'IN');
		// $query->condition('field_top_destination', 1);
		// $query->range(0, $block_cat_limit);
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
		// print_r($term_count);die;
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