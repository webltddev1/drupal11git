<?php  

namespace Drupal\d9custom\Form;
/**
 * @file
 * Contains \Drupal\d9custom\Form\D9ConfigForm.
 */
 
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Cache\Cache;
use Drupal\file\Entity\File;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\file\FileInterface;

class D9ConfigForm extends ConfigFormBase {  
  /**  
   * {@inheritdoc}  
   */  
  protected function getEditableConfigNames() {  
    return [  
      'd9custom.settings',  
    ];  
  }  

  /**  
   * {@inheritdoc}  
   */  
  public function getFormId() {  
    return 'd9config_form';  
  }
  
  /**  
   * {@inheritdoc}  
   */  
  public function buildForm(array $form, FormStateInterface $form_state) {  
    $config = $this->config('d9custom.settings');
    $config_lang = \Drupal::request()->query->get('lang');

    //header
    $form['newsletter'] = [  
      '#type' => 'fieldset',
      '#title' => $this->t('Newsletter Popup'),
      // '#open' => TRUE,
    ];
    
    $newsletterpopup = $config->get('newsletterpopup');
    $form['newsletter']['newsletterpopup'] = [  
      '#type' => 'checkbox',
      '#title' => $this->t('Enabled'),
      '#default_value' => $newsletterpopup,
    ];
	
	
	
    
    //header
    $form['header'] = [  
      '#type' => 'fieldset',
      '#title' => $this->t('Header'),
      // '#open' => TRUE,
    ];
    
    $header_txt = $config->get('header_txt'.$config_lang);
    $form['header']['header_txt'] = [  
      '#type' => 'text_format',
      '#title' => $this->t('Header Text'),
      '#format' => 'full_html',
      '#required' => TRUE,
      '#default_value' => (isset($header_txt['value']))? $header_txt['value']:'',
    ];
    
    $insurance_txt = $config->get('insurance_txt'.$config_lang);
    $form['header']['insurance_txt'] = [  
      '#type' => 'text_format',
      '#title' => $this->t('Insurance Text'),
      '#format' => 'full_html',
      '#required' => TRUE,
      '#default_value' => (isset($insurance_txt['value']))? $insurance_txt['value']:'',
    ];

    //Footer
    $form['footer'] = [  
      '#type' => 'fieldset',
      '#title' => $this->t('Footer'),
      // '#open' => TRUE,
    ];
		
		
		
    $form['footer']['phone'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Phone'),
      '#default_value' => $config->get('phone')
    ];
    $form['footer']['openinghours'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Opening hours'),
      '#default_value' => $config->get('openinghours')
    ];
    $form['footer']['email'] = [
      '#type' => 'email',
      '#title' => $this->t('Email'),
      '#default_value' => $config->get('email')
    ];
    $form['footer']['instagram'] = [
      '#type' => 'url',
      '#title' => $this->t('Instagram'),
      '#default_value' => $config->get('instagram')
    ];
    $form['footer']['facebook'] = [
      '#type' => 'url',
      '#title' => $this->t('Facebook'),
      '#default_value' => $config->get('facebook')
    ];
    $form['footer']['youtube'] = [
      '#type' => 'url',
      '#title' => $this->t('Youtube'),
      '#default_value' => $config->get('youtube')
    ];
    
    $footer_txt = $config->get('footer_txt'.$config_lang);
    $form['footer']['footer_txt'] = [  
      '#type' => 'text_format',
      '#title' => $this->t('Footer Text'),
      '#format' => 'full_html',
      '#required' => TRUE,
      '#default_value' => (isset($footer_txt['value']))? $footer_txt['value']:'',
    ];
    
    $footer_txt = $config->get('footer_txt'.$config_lang);
    $form['footer']['footer_txt'] = [  
      '#type' => 'text_format',
      '#title' => $this->t('Footer Text'),
      '#format' => 'full_html',
      '#required' => TRUE,
      '#default_value' => (isset($footer_txt['value']))? $footer_txt['value']:'',
    ];
    
    // $footer_txt2 = $config->get('footer_txt2'.$config_lang);
    // $form['footer']['footer_txt2'] = [  
      // '#type' => 'text_format',
      // '#title' => $this->t('Block 2'),
      // '#format' => 'full_html',
      // '#required' => TRUE,
      // '#default_value' => (isset($footer_txt2['value']))? $footer_txt2['value']:'',
    // ];
    
    // $footer_txt3 = $config->get('footer_txt3'.$config_lang);
    // $form['footer']['footer_txt3'] = [  
      // '#type' => 'text_format',
      // '#title' => $this->t('Block 3'),
      // '#format' => 'full_html',
      // '#required' => TRUE,
      // '#default_value' => (isset($footer_txt3['value']))? $footer_txt3['value']:'',
    // ];
    
		
		
		
		
    $form['footer']['copyr_txt'] = [  
      '#type' => 'textarea',
      '#title' => $this->t('Copytright text'),
      '#default_value' => $config->get('copyr_txt'.$config_lang),
      '#required' => TRUE,
      '#description' => ':sitename=Sitename.<br/>:copy=Copyright symbol.<br/>:year=Current year.<br/>@conditionslink=Nos conditions générales.'
    ];
	




    //Footer
    $form['payments'] = [  
      '#type' => 'fieldset',
      '#title' => $this->t('Footer logos'),
      // '#open' => TRUE,
    ];
	for($i=1; $i<=15; $i++ ){
		$home_image = [];
		$img = $config->get('paymentimage'.$i);
		if( $img != '' ) $home_image = [ $img ];
		$form['payments']['paymentimage'.$i] = [
			'#type' => 'managed_file',
			'#title' => $i,
			'#default_value' => $home_image,
			// '#required' => TRUE,
			'#upload_validators' => [
				'file_validate_extensions' => ['png jpg jpeg'],
				'file_validate_size' => [25600000],
			],
			'#upload_location' => 'public://',
		];
	}
    
    
    return parent::buildForm($form, $form_state);  
  }
  
  /**  
   * {@inheritdoc}  
   */  
  public function submitForm( array &$form, FormStateInterface $form_state ){
    parent::submitForm($form, $form_state);

    $config_lang = \Drupal::request()->query->get('lang');
	
	for($i=1; $i<=15; $i++ ){
		$home_image = $form_state->getValue('paymentimage'.$i);
		if( isset($home_image[0]) ){
			$file = File::load($home_image[0]);
			$file->status = FileInterface::STATUS_PERMANENT;
			if( $file->save() ){
				$this->config('d9custom.settings')->set('paymentimage'.$i, $file->id() )->save();
			}
		}
		else{
			$this->config('d9custom.settings')->set('paymentimage'.$i, '' )->save();
		}
	}

    $this->config('d9custom.settings')  
      ->set('newsletterpopup', $form_state->getValue('newsletterpopup'))
      ->set('phone', $form_state->getValue('phone'))
      ->set('openinghours', $form_state->getValue('openinghours'))
      ->set('email', $form_state->getValue('email'))
      ->set('instagram', $form_state->getValue('instagram'))
      ->set('facebook', $form_state->getValue('facebook'))
      ->set('youtube', $form_state->getValue('youtube'))
      ->set('header_txt'.$config_lang, $form_state->getValue('header_txt'))
      ->set('insurance_txt'.$config_lang, $form_state->getValue('insurance_txt'))
      ->set('footer_txt'.$config_lang, $form_state->getValue('footer_txt'))
      // ->set('footer_txt2'.$config_lang, $form_state->getValue('footer_txt2'))
      // ->set('footer_txt3'.$config_lang, $form_state->getValue('footer_txt3'))
      ->set('copyr_txt'.$config_lang, $form_state->getValue('copyr_txt'))
      ->save();
      
			
			Cache::invalidateTags(['NEWS_UPDATED']);
			Cache::invalidateTags(['D9CUSTOM_UPDATED']);
  }
}