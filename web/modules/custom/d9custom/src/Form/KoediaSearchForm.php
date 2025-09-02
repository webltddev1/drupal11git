<?php 

/**
 * @file
 * Contains \Drupal\d9custom\Form\KoediaSearchForm.
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


class KoediaSearchForm extends FormBase{
	public function getFormId(){
		return 'koediasearchform';
	}
	
	public function buildForm(array $form, FormStateInterface $form_state){
		$form = [];
		
		// $url1 = Url::fromRoute('d9custom.koediasearch')->toString();
		// $form['#action'] = $url1;
		
		$d = \Drupal::request()->query->get('d');
		$form['destination'] = [
            '#type' => 'select',
            '#required' => TRUE,
            '#title' => $this->t('Destination'),
            '#options' => ['FRROI'=>'France: Roissy-En-France','FRPAR'=>'France: Port of Paris','GBLON'=>'UK: Port of London'],
            // '#placeholder' => $this->t('France, Mauritius, ...'),
			'#default_value' => ( $d!='' )? $d:''
        ];
		
		$def = '';
		$checkin = \Drupal::request()->query->get('d1');
		$checkout = \Drupal::request()->query->get('d2');
		if( $checkin!='' && $checkout!='' ) $def = "$checkin - $checkout";
		$form['periode'] = [
			'#type' => 'textfield',
			'#title' => t('Check-in'),
			// '#title_display' 		=> 'invisible',
			'#attributes' => ['placeholder'=>t('- select -')],
			'#required' => TRUE,
			// '#min' => '+2 days',
			// '#max' => '+4 months',
			// '#date_format'=> 'd/m/Y'
			'#default_value' => ( $def!='' )? $def:'',
		];
		
		//no of adults
		$adults_def = \Drupal::request()->query->get('g');
		$adults = [
				100=>t('1 Adult'), 
				101=>t('1 Adult + 1 Child'), 
				102=>t('1 Adult + 2 Children'), 
				103=>t('1 Adult + 3 Children'), 
				200=>t('2 Adults'), 
				201=>t('2 Adults + 1 Child'), 
				202=>t('2 Adults + 2 Children'), 
				203=>t('2 Adults + 3 Children'),
				300=>t('3 Adults'), 
				301=>t('3 Adults + 1 Child'), 
				302=>t('3 Adults + 2 Children'), 
				303=>t('3 Adults + 3 Children'),
				400=>t('4 Adults'), 
				401=>t('4 Adults + 1 Child'), 
				402=>t('4 Adults + 2 Children'),
				403=>t('4 Adults + 3 Children'),
				500=>t('5 Adults'), 
				501=>t('5 Adults + 1 Child'),
				502=>t('5 Adults + 2 Children'),
				503=>t('5 Adults + 3 Children'),
				600=>t('6 Adults'), 
				601=>t('6 Adults + 1 Child'), 
				602=>t('6 Adults + 2 Children'),
				603=>t('6 Adults + 3 Children'),
				700=>t('7 Adults'), 
				701=>t('7 Adults + 1 Child'),
				702=>t('7 Adults + 2 Children'),
				703=>t('7 Adults + 3 Children'),
				800=>t('8 Adults'), 
				801=>t('8 Adults + 1 Child'), 
				802=>t('8 Adults + 2 Children'), 
				803=>t('8 Adults + 3 Children'),
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
			'#required' => TRUE,
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
		$url1 = Url::fromRoute( 'd9custom.koediasearch' );
		
		$destination = \Drupal::request()->request->get('destination');
		$adults = \Drupal::request()->request->get('adults');
		$periode = \Drupal::request()->request->get('periode');
		$dates = explode(' - ',$periode);
		
		$url1->setOption('query', [
			'd' => $destination,
			'd1' => $dates[0],
			'd2' => $dates[1],
			'g' => $adults,
		]);
		
		
		$form_state->setRedirectUrl($url1);
		return;
	}
}
