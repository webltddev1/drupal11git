<?php 

/**
 * @file
 * Contains \Drupal\d9custom\Form\GeneralSearchForm.
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


class GeneralSearchForm extends FormBase {
  public function getFormId() {
      return 'generalsearchform';
  }
	
  public function buildForm(array $form, FormStateInterface $form_state){
		$form = [];
		
		$checkin = \Drupal::request()->query->get('c');
		$form['venue'] = [
			// '#type' => 'entity_autocomplete',
			// '#target_type' => 'commerce_product',
      '#type' => 'textfield',
			'#required' => true,
      '#attributes' => ['placeholder'=>$this->t('Search the best deals')],
      '#title' => $this->t('search the site'),
      '#title_display' => 'invisible',
      // '#autocomplete_route_name' => 'd9custom.generalsearch',
			// '#autocomplete_route_parameters' => array('field_name' => $field_name),
    ];
		
		$form['actions'] = ['#type' => 'actions'];
		$form['actions']['search'] = [
			'#type' => 'submit',
			'#value' => $this->t('Search'),
			'#submit' => ['::submitForm']
		];
		
		// $form['#attached']['library'][] = 'd9offers/d9offers-booking';

    return $form;
  }
  
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // #HERE! validate options selected
		
  }
	
  public function submitForm(array &$form, FormStateInterface $form_state){
		$venue = \Drupal::request()->request->get('venue');
		// \Drupal::messenger()->addStatus($venue);
		
		
		// $values = $form_state->getValues();
    // foreach( $values as $index => $value ){
			// \Drupal::messenger()->addStatus($value);
      // if( is_numeric($value) ){
				// \Drupal::messenger()->addStatus($value);
				// $url = Url::fromRoute('entity.commerce_product.canonical', ['commerce_product' => $value]);
				// $form_state->setRedirectUrl($url);
				// return;
			// }
    // }
		
		$url = Url::fromUserInput( '/search-deals' );
		$url->setOption('query', [
			'title' => strip_tags(trim($venue)),
		]);
		
		$form_state->setRedirectUrl($url);
		return;
  }
}
