<?php
namespace Drupal\d9custom\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\user\Entity\User;

/**
 * User Block
 *
 * @Block(
 *   id = "utilisateur_block",
 *   admin_label = @Translation("User Block")
 * )
 */
class UtilisateurBlock extends BlockBase{
	
  public function build() {
		$links = $build = [];
		
		$current_user = \Drupal::currentUser();
		$uid = $current_user->id();
		
		$u = User::load( \Drupal::currentUser()->id() );
		
		if( $uid<= 0 ){//login | register
			$text = t('Log In');
			$url = Url::fromRoute('user.login');
      $link = Link::fromTextAndUrl($text, $url);
      $link = $link->toRenderable();			
			$links['login'] = render($link);
			
			$text = t('Register');
			$url = Url::fromRoute('user.register');
      $link = Link::fromTextAndUrl($text, $url);
      $link = $link->toRenderable();			
			$links['register'] = render($link);
		}
		else{//username | logout
			$text = t('My Account');
			$username = $current_user->getAccountName();
			if( $username!='' ) $text = $username;
			
			$url = Url::fromRoute('entity.user.canonical', ['user' => $uid]);
      $link = Link::fromTextAndUrl($text, $url);
      $link = $link->toRenderable();
			$links['myaccount'] = render($link);
			
			$text = t('Log Out');
			$url = Url::fromRoute('user.logout');
      $link = Link::fromTextAndUrl($text, $url);
      $link = $link->toRenderable();
			$links['logout'] = render($link);
		}
    

		if( !empty($links) ){
			
			$build['content'] = [ '#theme' => 'userblock', '#links' => $links ];
			
			$build['#cache'] = [
				'max-age' => 0,
				'tags' => ['D9CUSTOM_UPDATED'], // #HERE! need to check again
			];
		}
    
    return $build;
  }

  public function getCacheContexts() {
    //if you depends on \Drupal::routeMatch()
    //you must set context of this block with 'route' context tag.
    //Every new route this block will rebuild
    return Cache::mergeContexts(parent::getCacheContexts(), array('route'));
  }
}