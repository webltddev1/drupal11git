<?php 

/**
 * @file
 * Contains \Drupal\d9custom\Form\BookingLibertySearchForm.
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
use Drupal\Core\Datetime\DrupalDateTime;

use Drupal\file\Entity\File;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;


class BookingLibertySearchForm extends FormBase{
	public function getFormId(){
		return 'bookinglibertysearchform';
	}
	
	public function buildForm(array $form, FormStateInterface $form_state){
		$form = [];
		
		
		$d = \Drupal::request()->query->get('d');
		$ddates = '';
		$d1 = \Drupal::request()->query->get('d1');
		$d2 = \Drupal::request()->query->get('d2');
		if( $d1!='' && $d2!='' ){
			$dd1 = new DrupalDateTime();
			$ddate1 = $dd1->createFromFormat('Y-m-d', $d1);
			$dd2 = new DrupalDateTime();
			$ddate2 = $dd2->createFromFormat('Y-m-d', $d2);
			
			$ddates = $ddate1->format('d/m/Y').' - '.$ddate2->format('d/m/Y');
		}
		// $form['destination'] = [
            // '#type' => 'textfield',
            // '#required' => TRUE,
            // '#title' => $this->t('Destination'),
            // '#placeholder' => $this->t('France, Mauritius, ...'),
			// '#default_value' => ( $d!='' )? $d:''
        // ];
		
		$destinations = [];
		$vid = 'country';
		$terms =\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($vid);
		foreach( $terms as $term ){
			
			$query = \Drupal::entityQuery('commerce_product');
			$query->condition('status', 1);
			$query->condition('field_country', $term->tid);
			$query->accessCheck(FALSE);
			$entity_ids = $query->execute();
			
			if( count($entity_ids) > 0 ){
				$destinations[$term->tid] = $term->name;
			}
		}
		$form['destination'] = [
            '#type' => 'select',
            '#required' => TRUE,
            '#title' => $this->t('Destination'),
			'#options' => $destinations,
            '#placeholder' => $this->t('France, Mauritius, ...'),
			'#default_value' => ( $d!='' )? $d:''
        ];
		
		$checkin = \Drupal::request()->query->get('c');
		$form['periode'] = [
			'#type' => 'textfield',
			'#title' => t('Period and duration'),
			// '#title_display' 		=> 'invisible',
			'#attributes' => ['placeholder'=>t('- select -')],
			// '#required' => TRUE,
			// '#min' => '+2 days',
			// '#max' => '+4 months',
			// '#date_format'=> 'd/m/Y'
			'#default_value' => ( $ddates!='' )? $ddates:''
		];
		
		//no of adults
		$adults_def = \Drupal::request()->query->get('g');
		$adults = [
				''=>'-- --',
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
			'#type' => 'select',
			'#title' => t('Guests'),
			// '#title_display' 		=> 'invisible',
			'#attributes' => ['placeholder'=>t('- select -'),'autocomplete'=>'off'],
			// '#description' 	=> t('Guests'),
			// '#empty_option' => t('Guests'),
			'#options' => $adults,
			// '#required' => TRUE,
			'#default_value' => ( $adults_def!='' )? $adults_def:''
		];
		
		$form['actions'] = ['#type' => 'actions'];
		$form['actions']['search'] = [
			'#type' => 'submit',
			'#value' => $this->t('Search'),
			'#submit' => ['::submitForm']
		];
		
		
		// $form['#attached']['library'][] = 'd9custom/d9custom-custom';
		$form['#attached']['library'][] = 'd9custom/d9custom-daterangepicker';
		// $form['#attributes']['class'][] = 'clearfix';
		// $form['#prefix'] = '<div class="myclass">';
		// $form['#suffix'] = '</div>';
		
		$form['#attributes']['class'][] = 'your-custom-form-class';
		
		return $form;
	}
	
	
	public function validateForm(array &$form, FormStateInterface $form_state){
		// #HERE! validate options selected
	}
	
	public function submitForm(array &$form, FormStateInterface $form_state){
		$term_id = '';
		$product_current = \Drupal::routeMatch()->getParameter('commerce_product');
		$url1 = Url::fromUserInput( '/hotels' );
		if( $product_current ){
			$hotel_id = $product_current->Id();
			$url1 = Url::fromUserInput( '/product/'.$hotel_id );
		}
		
		$destination = \Drupal::request()->request->get('destination');
		// $terms = \Drupal::entityTypeManager()
            // ->getStorage('taxonomy_term')
            // ->loadByProperties([
                    // 'vid' => 'country',
                    // 'name' =>$destination,
                // ]);
		// foreach( $terms as $term ){
			// $term_id = $term->id();
		// }
		
		
		$adults = \Drupal::request()->request->get('adults');
		
		$periode = \Drupal::request()->request->get('periode');
		$dates = explode(' - ',$periode);
		
		
		$query = [];
		// $query['f[0]'] = 'ac:accomodation_booking';
		if( $destination!='' ) $query['d'] = $destination;
		if( $destination!='' ) $query['f'][1] = 'country:'.$destination;
		
		if( isset($dates[0]) && $dates[0]!='' ){
			$d = new DrupalDateTime();
			$date = $d->createFromFormat('d/m/Y', $dates[0]);
			$query['d1'] = $date->format('Y-m-d');
		}
		if( isset($dates[1]) && $dates[1]!='' ){
			$d2 = new DrupalDateTime();
			$date2 = $d2->createFromFormat('d/m/Y', $dates[1]);
			$query['d2'] = $date2->format('Y-m-d');
		}
		
		if( $adults!='' ) $query['g'] = $adults;
		
		
		// print_r($query);die;
		
		$url1->setOption('query', $query);
		
		
		$form_state->setRedirectUrl($url1);
		return;
	}
}
