<?php 

/**
 * @file
 * Contains \Drupal\d9custom\Form\KoediaResaForm.
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
use Drupal\commerce_product\Entity\ProductVariation;
use Drupal\commerce_product\Entity\Product;
use Drupal\commerce_price\Price;

use Drupal\commerce_cart\CartProviderInterface;
use Drupal\commerce_cart\CartManagerInterface;

use Drupal\commerce_order\Entity\OrderInterface;
use Drupal\commerce_order\Entity\OrderItem;
use Drupal\commerce_order\Entity\Order;

use Drupal\file\Entity\File;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;


class KoediaResaForm extends FormBase{
	public function getFormId(){
		return 'koediaresaform';
	}
	
	public function buildForm(array $form, FormStateInterface $form_state, $data = NULL){
		$form = [];
		
		$get_info = \Drupal::request()->request->get('info');
		
		if( $get_info ){
			$info = json_decode($get_info);
			print_r( $info );
			// die;
			
			$NbAdults = $info->NbAdults;
			$NbChildren = $info->NbChildren;
			
			
			
			$form['content'] = ['#theme' => 'koediabooking', '#info' => $info];

			// $form['info'] = [
				// '#type' => 'hidden',
				// '#value' => $get_info,
			// ];
			
			
			
			
			
			// adults
			for( $i = 1; $i <= $NbAdults; $i++ ){
				
				$form['guests'][$i] = [
					'#type' => 'fieldset',
					'#title' => t('Guest #').' '.$i,
				];
				
				$form['guests'][$i]['title'] = [
					'#type' => 'select',
					'#title' => t('Title'),
					'#options' => ['Mr'=>t('Mr'), 'Mrs'=>t('Mrs'), 'Miss'=>t('Miss'), ],
				];
				
				$form['guests'][$i]['firstname'] = [
					'#type' => 'textfield',
					'#title' => t('Firstname'),
				];
				
				$form['guests'][$i]['lastname'] = [
					'#type' => 'textfield',
					'#title' => t('Lastname'),
				];
			}
			
			// children
			if( $NbChildren>0 ){
				for( $i = 1; $i <= $NbChildren; $i++ ){
					
					$form['guests'][$i] = [
						'#type' => 'fieldset',
						'#title' => t('Child #').' '.$i,
					];
					
					$form['guests'][$i]['child_title'] = [
						'#type' => 'select',
						'#title' => t('Title'),
						'#options' => ['Mr'=>t('Mr'), 'Miss'=>t('Miss'), ],
					];
					
					$form['guests'][$i]['child_firstname'] = [
						'#type' => 'textfield',
						'#title' => t('Firstname'),
					];
					
					$form['guests'][$i]['child_lastname'] = [
						'#type' => 'textfield',
						'#title' => t('Lastname'),
					];
					
					$form['guests'][$i]['child_dob'] = [
						'#type' => 'textfield',
						'#title' => t('Date of Birth'),
					];
				}
			}
			


			$form['actions'] = ['#type' => 'actions'];
			$form['actions']['next'] = [
				'#type' => 'submit',
				'#value' => $this->t('Next'),
			];
		}
		else{
			\Drupal::logger('d9custom')->warning('KoediaResaForm, Error.');
			
			$form['novalue'] = [
				'#markup' => '<div class="novalue">'. t('Error s.') .'</div>'
			];
		}

		return $form;
	}

	public function validateForm(array &$form, FormStateInterface $form_state) {
		// #HERE! validate options selected
		
	}

	public function submitForm(array &$form, FormStateInterface $form_state){
		
		$url1 = Url::fromUserInput( '/hotels' );
		$form_state->setRedirectUrl($url1);
		return;
		
		
		// $url1 = Url::fromRoute('d9custom.koediabook');
		// $form_state->setRedirectUrl($url1);
		
		// $values = $form_state->getValues();
		// print_r($values);
		// die;
		
		\Drupal::messenger()->addStatus( 'messagessss' );
			
		// $variation = ProductVariation::create([
			// 'type' => 'koedia_product',
			// 'title' => $prod_name,
			// 'sku' => $prod_id.'--'.$offer_n_capacity.'--'.time(),
			// 'price' => new Price($pwice2, 'EUR'),//USD
			// 'field_remaining_amount' => new Price($remaining, 'EUR'),//USD
			// 'field_prodvar_unit'  => $qty_unit,
			// 'field_prodvar_adult' => $qty_adult,
			// 'field_prodvar_teen'  => $qty_teen,
			// 'field_prodvar_child' => $qty_child,
			// 'field_prodvar_offer' => $offer,
			// 'field_prodvar_mealplan' => $mealplan,
			// 'field_prodvar_checkin' => $date_checkin,
			// 'field_prodvar_checkout' => $date_checkout,
			// 'field_addons' => $addons,
			// 'field_guestdetails' => $guestdetails,
		// ]);
		
		// if( $variation->save() ){
			
			// $product_current->addVariation($variation);
			// $product_current->save();
		// }
		
		
		
		
		// return ['#markup'=>'kkk'];
	}
}
