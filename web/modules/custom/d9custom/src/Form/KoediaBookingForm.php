<?php 

/**
 * @file
 * Contains \Drupal\d9custom\Form\KoediaBookingForm.
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


class KoediaBookingForm extends FormBase{
	public function getFormId(){
		return 'koediabookingform';
	}
	
	public function buildForm(array $form, FormStateInterface $form_state, $data = NULL){
		$form = [];
		
		$get_info = \Drupal::request()->request->get('info');
		
		if( $get_info ){
			$info = json_decode($get_info);
			// print_r( $info );
			// die;
			
			$NbAdults = $info->NbAdults;
			$NbChildren = $info->NbChildren;
			
			
			
			$form['content'] = ['#theme' => 'koediabooking', '#info' => $info];

			$form['info'] = [
				'#type' => 'hidden',
				'#value' => $get_info,
			];
			
			
			
			
			
			// adults
			for( $i = 1; $i <= $NbAdults; $i++ ){
				
				$form['guests'.$i] = [
					'#type' => 'fieldset',
					'#title' => t('Guest #').' '.$i,
				];
				
				$form['guests'.$i]['title'.$i] = [
					'#type' => 'select',
					'#title' => t('Title'),
					'#required' => TRUE,
					'#options' => ['Mr'=>t('Mr.'), 'Mrs'=>t('Mrs.'), 'Miss'=>t('Miss'), ],
				];
				
				$form['guests'.$i]['firstname'.$i] = [
					'#type' => 'textfield',
					'#required' => TRUE,
					'#title' => t('Firstname'),
				];
				
				$form['guests'.$i]['lastname'.$i] = [
					'#type' => 'textfield',
					'#required' => TRUE,
					'#title' => t('Lastname'),
				];
			}
			
			// children
			if( $NbChildren>0 ){
				for( $c = 1; $c <= $NbChildren; $c++ ){
					
					$form['guests'.$c] = [
						'#type' => 'fieldset',
						'#title' => t('Child #').' '.$c,
					];
					
					$form['guests'.$c]['child_title'.$c] = [
						'#type' => 'select',
						'#title' => t('Title'),
						'#required' => TRUE,
						'#options' => ['Mr'=>t('Mr.'), 'Miss'=>t('Miss'), ],
					];
					
					$form['guests'.$c]['child_firstname'.$c] = [
						'#type' => 'textfield',
						'#required' => TRUE,
						'#title' => t('Firstname'),
					];
					
					$form['guests'.$c]['child_lastname'.$c] = [
						'#type' => 'textfield',
						'#required' => TRUE,
						'#title' => t('Lastname'),
					];
					
					$form['guests'.$c]['child_dob'.$c] = [
						'#type' => 'textfield',
						'#required' => TRUE,
						'#title' => t('Date of Birth'),
					];
				}
			}
			


			$form['submit'] = [
				'#type' => 'submit',
				'#value' => $this->t('Submit'),
			];
		}
		else{
			\Drupal::logger('d9custom')->warning('KoediaBookingForm, Error.');
			
			$form['novalue'] = [
				'#markup' => '<div class="novalue">'. t('Error.') .'</div>'
			];
		}

		return $form;
	}

	public function validateForm(array &$form, FormStateInterface $form_state) {
		// #HERE! validate options selected
		
	}

	public function submitForm(array &$form, FormStateInterface $form_state){
		
		
		$guests_info = $guests_queryinfo = [];
		
		$get_info = $form_state->getValue('info');
		if( $get_info!= '' ){
			$info = json_decode($get_info);
			// print_r($info);die;
			
			$Refundable = $info->Refundable; 
			$RoomDescription = $info->RoomDescription;
			$NbAdults = $info->NbAdults;
			$NbChildren = $info->NbChildren;
			$NbCot = $info->NbCot;
			$ShortDescription = $info->ShortDescription;
			$currency = $info->currency;
			$TotalPrice = $info->TotalPrice;
			$Board = $info->Board;
			$Total = $info->Total;
			$PerDay = $info->PerDay;
			$SessionId = $info->SessionId;
			$DateRangeStart = $info->DateRangeStart;
			$DateRangeEnd = $info->DateRangeEnd;
			$code = $info->code;
			$type = $info->type;
			$name = $info->name;
			$country = $info->country;
			$city = $info->city;
			$distancevalue = $info->distancevalue;
			$distanceunit = $info->distanceunit;
			$category = $info->category;
			$roomid = $info->roomid;
			
			if( $NbAdults>0 ){
				for( $i = 1; $i <= $NbAdults; $i++ ){
					$guests_name = $form_state->getValue('title'.$i);
					$guests_name .= ' '. $form_state->getValue('firstname'.$i);
					$guests_name .= ' '. $form_state->getValue('lastname'.$i);
					$guests_info[] = t('Adult name:').' '.$guests_name;
					
					$gqinfo = $form_state->getValue('title'.$i);
					$gqinfo .= '|'. $form_state->getValue('firstname'.$i);
					$gqinfo .= '|'. $form_state->getValue('lastname'.$i);
					$guests_queryinfo[] = $gqinfo;
				}
			}
			
			if( $NbChildren>0 ){
				for( $c = 1; $c <= $NbChildren; $c++ ){
					$guests_name = $form_state->getValue('child_title'.$i);
					$guests_name .= ' '. $form_state->getValue('child_firstname'.$i);
					$guests_name .= ' '. $form_state->getValue('child_lastname'.$i);
					$guests_name .= ', '. $form_state->getValue('child_dob'.$i);
					$guests_info[] = t('Child name:').' '.$guests_name;
					
					$gqinfo = $form_state->getValue('child_title'.$i);
					$gqinfo .= '|'. $form_state->getValue('child_firstname'.$i);
					$gqinfo .= '|'. $form_state->getValue('child_lastname'.$i);
					$guests_queryinfo[] = $gqinfo;
					
				}
			}
			$ginfo = implode(' ',$guests_info);
			$gqinfo = implode(';',$guests_queryinfo);
			
			
			
			$det = [];
			$det[] = t('Establishment:').' '.$name;
			$det[] = t('Room:').' '.$RoomDescription;
			$det[] = t('Dates:').' '.$DateRangeStart.' '.t('to').' '.$DateRangeEnd;
			$det[] = t('Total:').' '.$Total.' '.$currency;
			$det[] = t('PerDay:').' '.$PerDay.' '.$currency;
			$det[] = t('Nb. Adults:').' '.$NbAdults;
			$det[] = t('Nb. Children:').' '.$NbChildren;
			$details = implode('\n',$det);
			
			\Drupal::messenger()->addMessage($ginfo);
			
			
			$prod_id = 15;
			$product_current = Product::load($prod_id);
			$variation = ProductVariation::create([
				'type' => 'koedia_product',
				'title' => $name.', '.$RoomDescription,
				'sku' => $prod_id.'--'.time(),
				'price' => new Price($Total, $currency),
				'field_prodvar_adult'  => $NbAdults,
				'field_prodvar_child' => $NbChildren,
				'field_prodvar_teen' => 0,
				'field_prodvar_unit' => 1,
				'field_prodvar_checkin'  => $DateRangeStart,
				'field_prodvar_checkout' => $DateRangeEnd,
				'field_prodvar_details' => $details,
				'field_prodvar__mealplan' => $Board,
				'field_prodvar_priceday' => $PerDay,
				'field_prodvar_refundable' => $Refundable,
				'field_prodvar_sessid' => $SessionId,
				'field_prodvar_guestdetails' => $ginfo,
				'field_prodvar_guestdetails2' => $gqinfo,
				'field_prodvar_accomcode' => $code,
				'field_prodvar_roomid' => $roomid,
			]);
			
			if( $variation->save() ){
				$store_id = 1;
				$order_type = 'default';
				
				$product_current->addVariation($variation);
				$product_current->save();
			
				$entity_manager = \Drupal::EntityTypeManager();
				
				$store = $entity_manager->getStorage('commerce_store')->load($store_id);
				
				$cart_manager = \Drupal::service('commerce_cart.cart_manager');
				$cart_provider = \Drupal::service('commerce_cart.cart_provider');
				$cart = $cart_provider->getCart($order_type, $store);
				if( !$cart ){
					$cart = $cart_provider->createCart($order_type, $store);
				}
				elseif( !empty($cart) ){
					$cart_manager->emptyCart($cart);
				}
				
				
				
				//Create new order item
				$var_id = $variation->Id();
				$order_item = $entity_manager->getStorage('commerce_order_item')->create([
						'title' => 'meproduct',
						'type' => 'default',
						'purchased_entity' => $var_id,
						'quantity' => 1,
						'unit_price' => $variation->getPrice(),
				]);

				$order_item->save();
				$cart_manager->addOrderItem($cart, $order_item);
				
				
				\Drupal::logger('d9offers')->notice( 'Created variation for booking. '.$var_id );
				
				$form_state->setRedirectUrl( Url::fromRoute('commerce_cart.page') );
				return;
			}
			
			
		}
		
		
		\Drupal::messenger()->addStatus( 'end' );
	}
}
