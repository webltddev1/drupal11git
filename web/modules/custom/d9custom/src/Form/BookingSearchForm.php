<?php 

/**
 * @file
 * Contains \Drupal\d9custom\Form\BookingSearchForm.
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


class BookingSearchForm extends FormBase {
  public function getFormId() {
      return 'bookingsearchForm';
  }
	
  public function buildForm(array $form, FormStateInterface $form_state){
		$form = [];
		
		// $current_uri = \Drupal::request()->getRequestUri();
		
		// $form['current_uri'] = [
			// '#markup' => 'cu: '. urldecode($current_uri)
		// ];
		
		// $form['#action'] = urldecode($current_uri);

		$checkin = \Drupal::request()->query->get('c');
		$form['checkin'] = [
			'#type' => 'textfield',
			'#title' => t('Check-in'),
			// '#title_display' 		=> 'invisible',
			'#attributes' 		=> ['placeholder'=>t('- select -'), 'autocomplete'=>'off'],
			'#required' => TRUE,
			// '#min' => '+2 days',
			// '#max' => '+4 months',
			// '#date_format'=> 'd/m/Y'
			'#default_value' => ( $checkin!='' )? $checkin:''
		];
		// $form['checkin1'] = [
			// '#type' => 'textfield',
		// ];
		// $form['checkout1'] = [
			// '#type' => 'textfield',
		// ];
		
		$checkout = \Drupal::request()->query->get('d');
		$form['checkout'] = [
			'#type' => 'textfield',
			'#title' => t('Check-out'),
			// '#title_display' 		=> 'invisible',
			'#attributes' 		=> ['placeholder'=>t('- select -'),'autocomplete'=>'off'],
			'#required' => TRUE,
			'#default_value' => ( $checkout!='' )? $checkout:''
		];
		
		//no of adults
		$adults_def = \Drupal::request()->query->get('g');
		$adults = [
				100=>'1 '. t('Adult'), 
				101=>'1 '. t('Adult') .' + 1 '. t('Child'), 
				102=>'1 '. t('Adult') .' + 2 '. t('Children'), 
				103=>'1 '. t('Adult') .' + 3 '. t('Children'), 
				200=>'2 '. t('Adults'),
				201=>'2 '. t('Adults') .' + 1 '. t('Child'), 
				202=>'2 '. t('Adults') .' + 2 '. t('Children'), 
				203=>'2 '. t('Adults') .' + 3 '. t('Children'), 
				300=>'3 '. t('Adults'),
				301=>'3 '. t('Adults') .' + 1 '. t('Child'), 
				302=>'3 '. t('Adults') .' + 2 '. t('Children'), 
				303=>'3 '. t('Adults') .' + 3 '. t('Children'), 
				400=>'4 '. t('Adults'),
				401=>'4 '. t('Adults') .' + 1 '. t('Child'), 
				402=>'4 '. t('Adults') .' + 2 '. t('Children'), 
				403=>'4 '. t('Adults') .' + 3 '. t('Children'), 
				500=>'5 '. t('Adults'),
				501=>'5 '. t('Adults') .' + 1 '. t('Child'), 
				502=>'5 '. t('Adults') .' + 2 '. t('Children'), 
				503=>'5 '. t('Adults') .' + 3 '. t('Children'), 
				600=>'6 '. t('Adults'),
				601=>'6 '. t('Adults') .' + 1 '. t('Child'), 
				602=>'6 '. t('Adults') .' + 2 '. t('Children'), 
				603=>'6 '. t('Adults') .' + 3 '. t('Children'), 
			];
		// $adults = [100=>t('1 Adult'), 200=>t('2 Adults'), 201=>t('2 Adults + 1 Child'), 202=>t('2 Adults + 2 Children'), ];
		$form['adults'] = [
			'#type' 		=> 'select',
			'#title' 		=> t('Guests'),
			// '#title_display' 		=> 'invisible',
			'#attributes' 	=> ['placeholder'=>t('- select -'),'autocomplete'=>'off'],
			// '#description' 	=> t('Guests'),
			// '#title' 		=> t('Guests'),
			// '#empty_option' => t('Guests'),
			'#options' 	=> $adults,
			'#required' => TRUE,
			'#default_value' => ( $adults_def!='' )? $adults_def:''
		];
		
		// $form['adults'] = [
			// '#type' 		=> 'number',
			// '#title' 		=> t('Guests'),
			// // '#title_display' 		=> 'invisible',
			// '#attributes' 	=> ['placeholder'=>t('- select -')],
			// '#min' 	=> 1,
			// '#max' 		=> 12,
			// '#step' => 1,
			// '#required' => TRUE,
			// '#default_value' => ( $adults_def!='' )? $adults_def:1
		// ];
		
		
		
		
		$form['actions'] = ['#type' => 'actions'];
		$form['actions']['search'] = [
			'#type' => 'submit',
			'#value' => $this->t('Search'),
			'#submit' => ['::submitForm']
		];
		
		// $form['#attached']['library'][] = 'd9offers/d9offers-booking'; // ****************************************************************
		// $form['#after_build'][] = [get_class($this), 'afterBuild'];
		// $form_state->setMethod('GET');

    return $form;
  }
	
  // public static function afterBuild(array $form, FormStateInterface $form_state) {
    // unset($form['form_token']);
    // unset($form['form_build_id']);
    // unset($form['form_id']);
    // unset($form['op']);
    // return $form;
  // }
  
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // #HERE! validate options selected
		
  }
	
  public function submitForm(array &$form, FormStateInterface $form_state) {
		// \Drupal::messenger()->addStatus('submitted');
		// http://mystore.local/hotel-deals?g=201&c=2021-12-09&d=2021-12-10&sort_by=field_prod_stars&sort_order=ASC
		
		
			$product_current = \Drupal::routeMatch()->getParameter('commerce_product');
			$hotel_id = $product_current->Id();
			// $hotel_id = 5;//3;
		
		$checkin = \Drupal::request()->request->get('checkin');
		$checkout = \Drupal::request()->request->get('checkout');
		$adults = \Drupal::request()->request->get('adults');
		
		// \Drupal::messenger()->addStatus('checkin '.$checkin);
		// \Drupal::messenger()->addStatus('checkout '.$checkout);
		
		// $c = date('d-m-Y',strtotime($checkin));
		// \Drupal::messenger()->addStatus('c '.$c);
		
		$url1 = Url::fromUserInput( '/product/'.$hotel_id );
		$url1->setOption('query', [
			'g' => $adults,
			'c' => $checkin,
			'd' => $checkout,
		]);
		// $u = $url1->toString();
		
		
		$form_state->setRedirectUrl($url1);
		return;
  }
}
