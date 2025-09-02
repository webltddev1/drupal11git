<?php
namespace Drupal\d9custom\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Url;
use Drupal\Core\Link;

/**
 * Hotel Sort Block
 *
 * @Block(
 *   id = "hotelsort_block",
 *   admin_label = @Translation("Hotel Sort Block")
 * )
 */
class HotelSortBlock extends BlockBase {
  
  
  public function build() {
		$build = [];
		$sort_direction = 'ASC';
		$subtitle = t('Hotel');

		
		$current_path = \Drupal::service('path.current')->getPath();
		// print $current_path;
		$result = \Drupal::service('path_alias.manager')->getAliasByPath($current_path);
		// print $result;
		$current_route_match = \Drupal::service('current_route_match')->getRouteName();
		if( $current_route_match=='view.catalog.page_3' ){
			$subtitle = t('Cruises');
			$op = \Drupal::request()->query->get('op');
			if( $op==2 ) $subtitle = t('Packages');
		}
		
		$params = $params1 = $params2 = $params3 = \Drupal::request()->query->all();
		// print_r($params);
		
		$classes1 = $classes2 = $classes3 = ['asc'];
		
		$links['stars'] = $links['bestseller'] = $links['price'] = '';

		
		//star rating field_prod_stars
		$params1['sort_by'] = 'st';
		$params1['sort_order'] = 'ASC';
		//best sellers bs
		$params2['sort_by'] = 'bs';
		$params2['sort_order'] = 'DESC';
		//price field_prod_price
		$params3['sort_by'] = 'pr';
		$params3['sort_order'] = 'ASC';
		
		if( isset($params['sort_by']) ){
			if( isset($params['sort_order'])  ){
				if( $params['sort_order'] == 'ASC' ){
					if( $params['sort_by']=='st' ){
						$params1['sort_order'] = 'DESC';
						$classes1 = ['active','desc'];
					}
					elseif( $params['sort_by']=='bs' ){
						$params2['sort_order'] = 'DESC';
						$classes2 = ['active','desc'];
					}
					elseif( $params['sort_by']=='pr' ){
						$params3['sort_order'] = 'DESC';
						$classes3 = ['active','desc'];
					}
				}
				elseif( $params['sort_order'] == 'DESC' ){
					if( $params['sort_by']=='st' ){
						$params1['sort_order'] = 'ASC';
						$classes1 = ['active','desc'];
					}
					elseif( $params['sort_by']=='bs' ){
						$params2['sort_order'] = 'ASC';
						$classes2 = ['active','desc'];
					}
					elseif( $params['sort_by']=='pr' ){
						$params3['sort_order'] = 'ASC';
						$classes3 = ['active','desc'];
					}
				}
			}
		}
		
		
		$url1 = Url::fromUserInput( $current_path, ['attributes' => ['class'=>$classes1]] );
		$url1->setOption('query', [
			$params1
		]);
		$u1 = $url1->toString();
		
		$link1 = Link::fromTextAndUrl(t('Star rating'), $url1);
		$links['stars'] = $link1->toRenderable();
		
		

		$url2 = Url::fromUserInput( $current_path, ['attributes' => ['class'=>$classes2]] );
		$url2->setOption('query', [
			$params2
		]);
		$u2 = $url2->toString();
		
		$link2 = Link::fromTextAndUrl(t('Best Seller'), $url2);
		$links['bestseller'] = $link2->toRenderable();

		

		$url3 = Url::fromUserInput( $current_path, ['attributes' => ['class'=>$classes3]] );
		$url3->setOption('query', [
			$params3
		]);
		$u13 = $url3->toString();
		
		$link3 = Link::fromTextAndUrl( t('Price'), $url3 );
		$links['price'] = $link3->toRenderable();
		
    // $build['sorting'] = [ $linked1, $linked2, $linked3 ];
		
		$build['content'] = [ '#theme' => 'hotelsort', '#links' => $links, '#subtitle' => $subtitle ];
		
		$build['#cache'] = [
			'tags' => ['D9CUSTOM_UPDATED'], // #HERE! need to check again
		];
    
    return $build;
  }

  public function getCacheContexts() {
    //if you depends on \Drupal::routeMatch()
    //you must set context of this block with 'route' context tag.
    //Every new route this block will rebuild
    return Cache::mergeContexts(parent::getCacheContexts(), array('route'));
  }
}