<?php
namespace Drupal\d9custom\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Drupal\user\Entity\User;
use Symfony\Component\Routing\RouteCollection;

class RouteSubscriber extends RouteSubscriberBase{
  protected function alterRoutes(RouteCollection $collection){
    
		if( $route = $collection->get('entity.taxonomy_term.delete_form') ){
			$route->setRequirement('_role', 'administrator');
    }
    if( $route = $collection->get('entity.commerce_product_variation.collection') ){
			$route->setRequirement('_role', 'administrator');
    }
		
  }
}
