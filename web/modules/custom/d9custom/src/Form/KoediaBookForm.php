<?php 

/**
 * @file
 * Contains \Drupal\d9custom\Form\KoediaBookForm.
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


class KoediaBookForm extends FormBase{
	public function getFormId(){
		return 'koediabookform';
	}
	
	public function buildForm(array $form, FormStateInterface $form_state, $data = NULL){
		$form = [];
		
		
		// print_r($data);die;
		
		
		$info = json_encode($data);
		
		
		$form['#action'] = Url::fromRoute('d9custom.koediabook')->toString();

		$form['info'] = [
			'#type' => 'hidden',
			'#value' => $info,
		];

		// $form['daterangestart'] = [
			// '#type' => 'hidden',
			// '#value' => ( isset($data['DateRangeStart']) )? $data['DateRangeStart']:'',
		// ];

		// $form['daterangeend'] = [
			// '#type' => 'hidden',
			// '#value' => ( isset($data['DateRangeEnd']) )? $data['DateRangeEnd']:'',
		// ];

		// $form['sessionid'] = [
			// '#type' => 'hidden',
			// '#value' => ( isset($data['SessionId']) )? $data['SessionId']:'',
		// ];

		// $form['accomcode'] = [
			// '#type' => 'hidden',
			// '#value' => ( isset($data['accomcode']) )? $data['accomcode']:'',
		// ];

		// $form['roomid'] = [
			// '#type' => 'hidden',
			// '#value' => ( isset($data['roomid']) )? $data['roomid']:'',
		// ];
		
		
		// // if( $data['showfields']== 1 ){
		
		
			// // $form['guests'] = [
				// // '#type' => 'fieldset',
				// // '#title' => t('Guest #'),
			// // ];
		
		
			// // $info = "Date: ".$data['DateRangeStart'];
			// // $info .= "<br/>Date: ".$data['DateRangeEnd'];
			// // $form['guests']['info'] = [
				// // '#markup' => $info
			// // ];
		
		
		
		
		
			// // $nb_of_guests = $data['guests'];
			// // for( $i = 1; $i <= $nb_of_guests; $i++ ){
				
				// // $form['guests'][$i] = [
					// // '#type' => 'fieldset',
					// // '#title' => t('Guest #').$i,
				// // ];
				
				// // $form['guests'][$i]['title'] = [
					// // '#type' => 'select',
					// // '#title' => t('Title'),
					// // '#options' => ['Mr'=>t('Mr'), 'Mrs'=>t('Mrs'), 'Miss'=>t('Miss'), ],
				// // ];
				
				// // $form['guests'][$i]['firstname'] = [
					// // '#type' => 'textfield',
					// // '#title' => t('Firstname'),
				// // ];
				
				// // $form['guests'][$i]['lastname'] = [
					// // '#type' => 'textfield',
					// // '#title' => t('Lastname'),
				// // ];
			// // }
		// // }


		$form['actions'] = ['#type' => 'actions'];
		$form['actions']['search'] = [
			'#type' => 'submit',
			'#value' => $this->t('Book'),
			'#submit' => ['::submitForm']
		];

		return $form;
	}

	public function validateForm(array &$form, FormStateInterface $form_state) {
		// #HERE! validate options selected
		
	}

	public function submitForm(array &$form, FormStateInterface $form_state){
		// $url1 = Url::fromRoute('d9custom.koediabook');
		// $form_state->setRedirectUrl($url1);
		return;
	}
}
