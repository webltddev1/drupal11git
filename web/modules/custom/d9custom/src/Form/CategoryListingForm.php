<?php 

/**
 * @file
 * Contains \Drupal\d9custom\Form\CategoryListingForm.
 */

namespace Drupal\d9custom\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Url;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\CssCommand;

use Drupal\file\Entity\File;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;


class CategoryListingForm extends FormBase {
  public function getFormId() {
      return 'categorylistingform';
  }
	
  public function buildForm(array $form, FormStateInterface $form_state){
		$form = [];
		
		$current_path = \Drupal::service('path.current')->getPath();
		$path_args = explode('/', $current_path);
		if( isset($path_args[2]) && is_numeric($path_args[2]) ){
			
			
			
			$form['filter_by'] = [
				'#markup' => '<h3 class="h3"><span>'.t('Filter By').'</span></h3>'
			];
			
			//Category of offers
			$tid = $path_args[2];
			
			
			//
			$filter_options = $this->getEstablishments($tid);
			
			$mp = \Drupal::request()->query->get('mp');
			if( isset($filter_options['mealplans']) ){
				$form['mp'] = [
					'#type' => 'checkboxes',
					'#title' => t('Meal Plan'),
					// '#required' => TRUE,
					'#options' => $filter_options['mealplans'],
					'#default_value' => ( $mp!='' )? $mp:[]
				];
			}
			
			$tp = \Drupal::request()->query->get('tp');
			if( isset($filter_options['types']) ){
				$form['tp'] = [
					'#type' => 'checkboxes',
					'#title' => t('Types'),
					// '#required' => TRUE,
					'#options' => $filter_options['types'],
					'#default_value' => ( $tp!='' )? $tp:[]
				];
			}
			
			$reg = \Drupal::request()->query->get('reg');
			if( isset($filter_options['regions']) ){
				$form['reg'] = [
					'#type' => 'checkboxes',
					'#title' => t('Regions'),
					// '#required' => TRUE,
					'#options' => $filter_options['regions'],
					'#default_value' => ( $reg!='' )? $reg:[]
				];
			}
			
			$loc = \Drupal::request()->query->get('loc');
			if( isset($filter_options['locations']) ){
				$form['loc'] = [
					'#type' => 'checkboxes',
					'#title' => t('Locations'),
					// '#required' => TRUE,
					'#options' => $filter_options['locations'],
					'#default_value' => ( $loc!='' )? $loc:[]
				];
			}
			
			// $form['#after_build'][] = [get_class($this), 'afterBuild'];
			// $form_state->setMethod('GET');
			
			
			$form['#attached']['library'][] = 'd9custom/d9custom-slick';
		}
		
		
		
		
		
		if( empty($form) ){
			$form['noresults'] = [
				'#markup' => '<div class="noresults">'. $this->t('No offers available.') .'</div>'
			];
		}

    return $form;
  }
	
  // public static function afterBuild(array $form, FormStateInterface $form_state) {
    // unset($form['form_token']);
    // unset($form['form_build_id']);
    // unset($form['form_id']);
    // return $form;
  // }
  
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // #HERE! validate options selected
		
  }
	
  public function submitForm(array &$form, FormStateInterface $form_state) {
		
		$form_state->setRedirectUrl($url );
		
		
		\Drupal::messenger()->addStatus('submitted');
		// $form_state->setRebuild(TRUE);
  }
	
	
	
	//get all establishements for this category id
	// array by region. meal plan. type. city. options???
	private function getEstablishments($tid){
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
			
			
			//meal plans
			if( !isset($mealplans[$result->mealplan_id]) ){
				$mealplans[$result->mealplan_id] = 0;
			}
			if( !isset($hotels[$result->hotel_id]['mealplans'][$result->mealplan_id]) ){
				$hotels[$result->hotel_id]['mealplans'][$result->mealplan_id] = $result->mealplan_id;
				$mealplans[$result->mealplan_id] += 1;
			}
		}

		
/*---------------------------------------------------------------------------------------------*/
		
		if( !empty($offers) ){
			$hotel_ids = array_keys($offers);
			
			
			$bundle = 'establishment';
			$output = $articles = "";
			
			$query = \Drupal::entityQuery('node');
			$query->condition('status', 1);
			$query->condition('type', $bundle);
			// $query->condition('field_category1.target_id', $tid);
			$query->condition('nid', $hotel_ids, 'IN');
			$query->sort('created', 'DESC');
			// $query->sort('field_position2', 'ASC');
			
			
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
			
			foreach( $entity_ids as $entity_id ){
				if( isset($offers[$entity_id]) ){
					$node_load = Node::load($entity_id);
					$establishments[$entity_id] = $node_load->title->value;
					
					
					
					
					
					$type = $node_load->field_establishment_type->target_id;
					if( !isset($types[$type]) ){ $types[$type] = 0; }
					if( !isset($hotels[$result->hotel_id]['types'][$type]) ){
						$hotels[$result->hotel_id]['types'][$type] = $type;
						$types[$type] += 1;
					}
					
					$location = $node_load->field_town->target_id;
					if( !isset($locations[$location]) ){ $locations[$location] = 0; }
					if( !isset($hotels[$result->hotel_id]['locations'][$location]) ){
						$hotels[$result->hotel_id]['locations'][$location] = $location;
						$locations[$location] += 1;
					}
					
					$region = $node_load->field_region->target_id;
					if( !isset($regions[$region]) ){ $regions[$region] = 0; }
					if( !isset($hotels[$result->hotel_id]['regions'][$region]) ){
						$hotels[$result->hotel_id]['regions'][$region] = $region;
						$regions[$region] += 1;
					}
					
					// print_r($region);die;
				}
			}
			
			// print_r($establishments);die;
		}
		
/*---------------------------------------------------------------------------------------------*/
		
		
		
		
		//meal plans
		if( !empty($mealplans) ){
			$this->getTerms($mealplans);
		}
		//regions
		if( !empty($regions) ){
			$this->getTerms($regions);
		}
		//locations
		if( !empty($locations) ){
			$this->getTerms($locations);
		}
		//types
		if( !empty($types) ){
			$this->getTerms($types);
		}
		
		
		return [
				'mealplans'=> $mealplans,
				'regions'=> $regions,
				'locations'=> $locations,
				'types'=> $types,
			];
		
	}
	
	
	
	private function getTerms(&$terms){

			$terms_ids = array_keys($terms);
			$terms_loaded = Term::loadMultiple($terms_ids);
			foreach( $terms_loaded as $t ){
				$rid = $t->Id();
				$terms[$rid] = '<span class="opt">'.$t->name->value .'</span> <span class="cnt">'.$terms[$rid].'</span>';
			}

	}
}
