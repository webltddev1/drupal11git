<?php
namespace Drupal\d9custom\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\Core\Datetime\DrupalDateTime;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;
use Drupal\commerce_product\Entity\Product;
use Drupal\commerce_product\Entity\ProductVariation;
use Drupal\commerce_order\Entity\Order;
// use Symfony\Component\HttpFoundation\Cookie;


class KoediaController{
	public function koediasearch(){
		$nodes = [];
		
		$request = \Drupal::request();
		$session = $request->getSession();
		// $session->set('d9custom.koediasess', 'hihi');
		// $cookie = new Cookie('koediasessid', 'xcxcxcxc');
		// \Drupal::response->headers->setCookie($cookie);
		// \Drupal::service('response.cookies')->set('my_cookie', 'cookie_value', time() + 3600, '/');
		
		$destination = \Drupal::request()->query->get('d');
		$checkin = \Drupal::request()->query->get('d1');//2024-08-13
		$checkout = \Drupal::request()->query->get('d2');//2024-08-20
		$guests = \Drupal::request()->query->get('g');// 2 1
		$adults = $child = '';
		if( $guests!='' ){
			$adults = $guests[0];
			$child = $guests[2];
		}
		
		if( $destination=='' ){
			return [ '#markup' => t('Please select your preferred Destination') ];
		}
		
		/*
		// XML request
		$xmlRequest = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:web="http://webservice.ospita.koedia.com/document/v0.9/schemas" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
			<soapenv:Header/>
			<soapenv:Body>
			<searchAccomAvailability>
				<ReqAccomAvailability xsi:schemaLocation="http://webservice.ospita.koedia.com/document/v0.8/schemas/global.xsd" includeonrequest="false" currency="EUR" lang="fre" accomtype="HOTEL" xmlns="http://webservice.ospita.koedia.com/document/v0.8/schemas" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
				   <ClientIdentification agencyid="tstYourTravel" password="pwdTs_YourTr4vel" channelid="YOURTRAVEL_CANAL" networkid="YOURTRAVEL" customeragentid="karen"/>
				   <SessionId/>
				   <TimeCriterias>
					  <Start>'.$checkin.'</Start>
					  <End>'.$checkout.'</End>
				   </TimeCriterias>
				   <DestinationCriterias>
					  <City code="'.$destination.'" standard="HOBBES">
						 <SearchWithin unit="KM" value="10"/>
					  </City>
				   </DestinationCriterias>
				   <RoomCriterias>
					  <RoomPlans>
						 <RoomPlan roomcount="1">
							<PaxPlan>
							   <NbAdult>'.$adults.'</NbAdult>
							   <!--ListChildAge nbchildren="1">
								  <Age>6</Age>
							   </ListChildAge>-->
							   <NbInfant>'.$child.'</NbInfant
							</PaxPlan>
						 </RoomPlan>
					  </RoomPlans>
				   </RoomCriterias>
				   <AdvancedCriterias>
					  <MinimumStars>2</MinimumStars>
					  <BoardType>BB</BoardType>
					  <!--Supplier code="ALP"/>
					  <Supplier code="HUN">
						 <RateAccessCode>ABC</RateAccessCode>
					  </Supplier-->
				   </AdvancedCriterias>
				</ReqAccomAvailability>
				</searchAccomAvailability>
			</soapenv:Body>
		</soapenv:Envelope>';
		*/
		
		
		// echo "$destination, $checkin, $checkout, $adults, $child";die;
		$xmlRequest = $this->getxml($destination, $checkin, $checkout, $adults, $child);
		// echo $xmlRequest;die;
		

		// SOAP endpoint URL
		$soapEndpoint = 'https://webservice-ospita.koedia.com/v2.3.2/accomservice';

		// HTTP headers
		$headers = [
			'Content-type: text/xml',
			'Accept-Encoding: gzip',
		];

		// cURL initialization
		$ch = curl_init();

		// cURL options
		curl_setopt($ch, CURLOPT_URL, $soapEndpoint);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlRequest);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_VERBOSE, true);

		// Execute cURL session
		$response = curl_exec($ch);
		

		// Check for cURL errors
		if (curl_errno($ch)) {
			echo 'Error during cURL request: ' . curl_error($ch);
		}

		// Close cURL session
		curl_close($ch);

		// Display SOAP response
		// echo $response;

		$decodedResponse = gzdecode($response);
		
		// print '<pre>';
		// print htmlentities($decodedResponse);
		// print '</pre>';
		// die;
		// // $output = htmlspecialchars($decodedResponse);
		
		
		
		$dom = new \DOMDocument();
		$dom->loadXML($decodedResponse);
		
		
		$SessionId = $dom->getElementsByTagName('SessionId')->item(0)->nodeValue;
		$session->set('d9custom.koediasess', $SessionId);
		$DateRangeStart = $dom->getElementsByTagName('DateRange')->item(0)->getAttribute('start');
		$DateRangeEnd = $dom->getElementsByTagName('DateRange')->item(0)->getAttribute('end');
		
		// $array = $this->xmlread($dom);
		// $x = print_r($array, true);
		
		// $accomNames = $dom->getElementsByTagName('Name');
		// $accomName = $accomNames->item(0)->nodeValue;
		$Name = '';
		$AccomResponses = $dom->getElementsByTagName('AccomResponse');
		foreach( $AccomResponses as $AccomResponse ){
			$code = $AccomResponse->getAttribute('code');
			$nodes[$code]['code'] = $code;
			$nodes[$code]['type'] = $AccomResponse->getAttribute('accomtype');
			$nodes[$code]['name'] = $AccomResponse->getElementsByTagName('Name')->item(0)->nodeValue;
			$nodes[$code]['country'] = $AccomResponse->getElementsByTagName('City')->item(0)->getAttribute('code');
			$nodes[$code]['city'] = $AccomResponse->getElementsByTagName('City')->item(0)->nodeValue;
			$nodes[$code]['distancevalue'] = $AccomResponse->getElementsByTagName('Distance')->item(0)->getAttribute('value');
			$nodes[$code]['distanceunit'] = $AccomResponse->getElementsByTagName('Distance')->item(0)->getAttribute('unit');
			$nodes[$code]['category'] = $AccomResponse->getElementsByTagName('Category')->item(0)->getAttribute('code');
			$nodes[$code]['url'] = '';
			
			$nodes[$code]['txt'] = '';
			
			
			$img = $AccomResponse->getElementsByTagName('ThumbnailUrl')->item(0)->nodeValue;
			if( $img=='' ){
				$nodes[$code]['img'] = base_path() . \Drupal::service('extension.list.module')->getPath('d9custom') .'/img/liberty-default.jpg';
			}
			
			// $GeoLocalization = $dom->getElementsByTagName('GeoLocalization');
			$nodes[$code]['latitude'] = number_format($AccomResponse->getElementsByTagName('Latitude')->item(0)->nodeValue,6);
			$nodes[$code]['longitude'] = number_format($AccomResponse->getElementsByTagName('Longitude')->item(0)->nodeValue,6);
			
			$facilities = $dom->getElementsByTagName('Facility');
			foreach( $facilities as $facility ){
				$facilityCode = $facility->getAttribute('code');
				$facilityDescription = $facility->nodeValue;
				$nodes[$code]['facilities'][$facilityCode] = $facilityDescription;
			}
			
			$PossibilitiesLists = $dom->getElementsByTagName('PossibilitiesList');
			foreach( $PossibilitiesLists as $PossibilitiesList ){
				$Possibilities = $dom->getElementsByTagName('Possibility');
				foreach( $Possibilities as $Posibility ){
					$currency = $Posibility->getAttribute('currency');
					$TotalPrice = $Posibility->getElementsByTagName('TotalPrice')->item(0)->getAttribute('net');
					$RoomPlans = $Posibility->getElementsByTagName('RoomPlans')->item(0);
					$RoomPlan = $RoomPlans->getElementsByTagName('RoomPlan')->item(0);
					
					$roomid = $RoomPlan->getAttribute('roomid');
					$nodes[$code]['posibilities'][$roomid]['Refundable'] = $RoomPlan->getElementsByTagName('Refundable')->item(0)->nodeValue;
					$nodes[$code]['posibilities'][$roomid]['RoomDescription'] = $RoomPlan->getElementsByTagName('RoomDescription')->item(0)->nodeValue;
					$nodes[$code]['posibilities'][$roomid]['NbAdults'] = $NbAdults = $RoomPlan->getElementsByTagName('NbAdults')->item(0)->nodeValue;
					$nodes[$code]['posibilities'][$roomid]['NbChildren'] = $NbChildren = $RoomPlan->getElementsByTagName('NbChildren')->item(0)->nodeValue;
					$nodes[$code]['posibilities'][$roomid]['NbCot'] = $RoomPlan->getElementsByTagName('NbCot')->item(0)->nodeValue;
					
					$nodes[$code]['posibilities'][$roomid]['ShortDescription'] = $ShortDescription = $Posibility->getElementsByTagName('ShortDescription')->item(0)->nodeValue;
					// $nodes[$code]['posibilities'][$roomid]['ContractRemark'] = $Posibility->getElementsByTagName('ContractRemark')->item(0)->nodeValue;
					
					$nodes[$code]['posibilities'][$roomid]['currency'] = $currency;
					$nodes[$code]['posibilities'][$roomid]['TotalPrice'] = $TotalPrice;
					
					$Board = $RoomPlan->getElementsByTagName('Board')->item(0);
					$Included = $Board->getElementsByTagName('Included');
					$nodes[$code]['posibilities'][$roomid]['Board'] = $mealpln = $Included->item(0)->getElementsByTagName('Adult')->item(0)->nodeValue;
					
					$RoomPrice = $RoomPlan->getElementsByTagName('RoomPrice')->item(0);
					$Total = $RoomPrice->getElementsByTagName('Total');
					$nodes[$code]['posibilities'][$roomid]['Total'] = $Total->item(0)->getAttribute('net');
					$PerDay = $RoomPrice->getElementsByTagName('PerDay');
					$nodes[$code]['posibilities'][$roomid]['PerDay'] = $PerDay->item(0)->getAttribute('net');
					
					$data = [];
					$nodes[$code]['posibilities'][$roomid]['SessionId'] = $SessionId;
					$nodes[$code]['posibilities'][$roomid]['DateRangeStart'] = $DateRangeStart;
					$nodes[$code]['posibilities'][$roomid]['DateRangeEnd'] = $DateRangeEnd;
					$nodes[$code]['posibilities'][$roomid]['code'] = $code;
					$nodes[$code]['posibilities'][$roomid]['type'] = $AccomResponse->getAttribute('accomtype');
					$nodes[$code]['posibilities'][$roomid]['name'] = $AccomResponse->getElementsByTagName('Name')->item(0)->nodeValue;
					$nodes[$code]['posibilities'][$roomid]['country'] = $AccomResponse->getElementsByTagName('City')->item(0)->getAttribute('code');
					$nodes[$code]['posibilities'][$roomid]['city'] = $AccomResponse->getElementsByTagName('City')->item(0)->nodeValue;
					$nodes[$code]['posibilities'][$roomid]['distancevalue'] = $AccomResponse->getElementsByTagName('Distance')->item(0)->getAttribute('value');
					$nodes[$code]['posibilities'][$roomid]['distanceunit'] = $AccomResponse->getElementsByTagName('Distance')->item(0)->getAttribute('unit');
					$nodes[$code]['posibilities'][$roomid]['category'] = $AccomResponse->getElementsByTagName('Category')->item(0)->getAttribute('code');
					$nodes[$code]['posibilities'][$roomid]['roomid'] = $roomid;
					// $data['SessionId'] = $SessionId;
					// $data['DateRangeEnd'] = $DateRangeEnd;
					// $data['roomid'] = $roomid;
					// $data['accomcode'] = $code;
					
					// $data['NbAdults'] = $NbAdults;
					// $data['NbChildren'] = $NbChildren;
					// $data['ShortDescription'] = $ShortDescription;
					// $data['mealpln'] = $mealpln;
					
					
					$data = $nodes[$code]['posibilities'][$roomid];
					$data['facilities'] = $nodes[$code]['facilities'];
					
					
					$bookform = \Drupal::formBuilder()->getForm('Drupal\d9custom\Form\KoediaBookForm', $data);
					$nodes[$code]['posibilities'][$roomid]['bookform'] = \Drupal::service('renderer')->render($bookform);
				}
			}
		}
		
		// $myCookieValue = \Drupal::request()->cookies->get('koediasessid');
		// echo "myCookieValue: $myCookieValue";die;
		
		// $koediasess = $session->get('d9custom.koediasess', '');
		// echo "koediasess: $koediasess";die;
		
		
		$build = [];
		$build['content'] = [ '#theme' => 'koedialisting', '#nodes' => $nodes ];
		$build['#attached']['library'][] = 'd9custom/d9custom-gmap';
		$build['#attached']['library'][] = 'd9custom/d9custom-custom';
		// print_r($nodes);die;
		// return ['#markup' => $Name];
		return $build;
	}
	
	private function xmlread($node){
		$output = [];
		foreach ($node->childNodes as $child) {
			if ($child->nodeType === XML_ELEMENT_NODE) {
				$output[$child->nodeName] = $child->hasChildNodes() ? $this->xmlread($child) : $child->nodeValue;
			}
		}
		return $output;
	}
	
	private function getxml($country, $checkin, $checkout, $adults, $children){
		$xml = new \DOMDocument('1.0', 'UTF-8');

		// Create the Envelope element
		$envelope = $xml->createElement('soapenv:Envelope');
		$envelope->setAttribute('xmlns:soapenv', 'http://schemas.xmlsoap.org/soap/envelope/');
		$envelope->setAttribute('xmlns:web', 'http://webservice.ospita.koedia.com/document/v0.9/schemas');
		$envelope->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
		$xml->appendChild($envelope);

		// Create the Header element
		$header = $xml->createElement('soapenv:Header');
		$envelope->appendChild($header);

		// Create the Body element
		$body = $xml->createElement('soapenv:Body');
		$envelope->appendChild($body);

		// Create the searchAccomAvailability element
		$searchAccomAvailability = $xml->createElement('searchAccomAvailability');
		$body->appendChild($searchAccomAvailability);

		// Create the ReqAccomAvailability element
		$reqAccomAvailability = $xml->createElementNS('http://webservice.ospita.koedia.com/document/v0.8/schemas', 'ReqAccomAvailability');
		$reqAccomAvailability->setAttribute('xsi:schemaLocation', 'http://webservice.ospita.koedia.com/document/v0.8/schemas/global.xsd');
		$reqAccomAvailability->setAttribute('includeonrequest', 'false');
		$reqAccomAvailability->setAttribute('currency', 'EUR');
		$reqAccomAvailability->setAttribute('lang', 'fre');
		$reqAccomAvailability->setAttribute('accomtype', 'HOTEL');
		$searchAccomAvailability->appendChild($reqAccomAvailability);

		// Create the ClientIdentification element
		$clientIdentification = $xml->createElement('ClientIdentification');
		$clientIdentification->setAttribute('agencyid', 'tstYourTravel');
		$clientIdentification->setAttribute('password', 'pwdTs_YourTr4vel');
		$clientIdentification->setAttribute('channelid', 'YOURTRAVEL_CANAL');
		$clientIdentification->setAttribute('networkid', 'YOURTRAVEL');
		$clientIdentification->setAttribute('customeragentid', 'karen');
		$reqAccomAvailability->appendChild($clientIdentification);

		// Create the SessionId element
		$sessionId = $xml->createElement('SessionId');
		$reqAccomAvailability->appendChild($sessionId);

		// Create the TimeCriterias element
		$timeCriterias = $xml->createElement('TimeCriterias');
		$reqAccomAvailability->appendChild($timeCriterias);

		// Create the Start and End elements
		$start = $xml->createElement('Start', $checkin);
		$end = $xml->createElement('End', $checkout);
		$timeCriterias->appendChild($start);
		$timeCriterias->appendChild($end);

		// Create the DestinationCriterias element
		$destinationCriterias = $xml->createElement('DestinationCriterias');
		$reqAccomAvailability->appendChild($destinationCriterias);

		// Create the City element
		$city = $xml->createElement('City');
		$city->setAttribute('code', $country);
		$city->setAttribute('standard', 'HOBBES');
		$destinationCriterias->appendChild($city);

		// Create the SearchWithin element
		$searchWithin = $xml->createElement('SearchWithin');
		$searchWithin->setAttribute('unit', 'KM');
		$searchWithin->setAttribute('value', '10');
		$city->appendChild($searchWithin);

		// Create the RoomCriterias element
		$roomCriterias = $xml->createElement('RoomCriterias');
		$reqAccomAvailability->appendChild($roomCriterias);

		// Create the RoomPlans element
		$roomPlans = $xml->createElement('RoomPlans');
		$roomCriterias->appendChild($roomPlans);

		// Create the RoomPlan element
		$roomPlan = $xml->createElement('RoomPlan');
		$roomPlan->setAttribute('roomcount', '1');
		$roomPlans->appendChild($roomPlan);

		// Create the PaxPlan element
		$paxPlan = $xml->createElement('PaxPlan');
		$roomPlan->appendChild($paxPlan);

		// Create the NbAdult and NbInfant elements
		$nbAdult = $xml->createElement('NbAdult', $adults);
		$nbInfant = $xml->createElement('NbInfant', $children);
		$paxPlan->appendChild($nbAdult);
		$paxPlan->appendChild($nbInfant);

		// Create the AdvancedCriterias element
		$advancedCriterias = $xml->createElement('AdvancedCriterias');
		$reqAccomAvailability->appendChild($advancedCriterias);

		// Create the MinimumStars and BoardType elements
		$minimumStars = $xml->createElement('MinimumStars', '2');
		$boardType = $xml->createElement('BoardType', 'BB');
		$advancedCriterias->appendChild($minimumStars);
		$advancedCriterias->appendChild($boardType);

		// Format the XML
		$xml->formatOutput = true;

		// Output the XML
		return $xml->saveXML(null, LIBXML_NOXMLDECL);
	}
	
	
	// public function koediabook(){
		// $daterangestart = \Drupal::request()->request->get('daterangestart');
		// $daterangeend = \Drupal::request()->request->get('daterangeend');
		// $sessionid = \Drupal::request()->request->get('sessionid');
		// $accomcode = \Drupal::request()->request->get('accomcode');
		// $roomid = \Drupal::request()->request->get('roomid');
		
		
		
		// $out = "daterangestart: $daterangestart - daterangeend: $daterangeend - sessionid: $sessionid - accomcode: $accomcode - roomid: $roomid";
		
		// $data = [];
		// $data['guests'] = 1;
		// $data['SessionId'] = $sessionid;
		// $data['DateRangeStart'] = $daterangestart;
		// $data['DateRangeEnd'] = $daterangeend;
		// $data['roomid'] = $roomid;
		// $data['accomcode'] = $accomcode;
		// $data['showfields'] = 1;
		// $bookform = \Drupal::formBuilder()->getForm('Drupal\d9custom\Form\KoediaBookForm', $data);
		// $out = \Drupal::service('renderer')->render($bookform);
		
		// return ['#markup'=>$out];
	// }
}