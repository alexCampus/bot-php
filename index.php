<?php 

$method     = $_SERVER['REQUEST_METHOD'];
$resultCity = array();
function skip_accents( $str, $charset='utf-8' ) {
 
    $str = htmlentities( $str, ENT_NOQUOTES, $charset );
    
    $str = preg_replace( '#&([A-za-z])(?:acute|cedil|caron|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $str );
    $str = preg_replace( '#&([A-za-z]{2})(?:lig);#', '\1', $str );
    $str = preg_replace( '#&[^;]+;#', '', $str );
    
    return $str;
}
// Process only when method is POST
if($method == 'POST'){
	$requestBody = file_get_contents('php://input');
	$json = json_decode($requestBody);
	
	$text      = $json->queryResult->parameters->ville;
	
	$requestCity = file_get_contents("https://geo.api.gouv.fr/communes?nom=" . skip_accents($text) . "&fields=nom,code,codesPostaux,codeDepartement,codeRegion,population&format=json&geometry=centre");
	$jsonCity = json_decode($requestCity);
	// foreach ($jsonCity as $key => $value) {
	// 	array_push($resultCity, $value);
	// }
	// $resultCity = get_object_vars($jsonCity[0]);

	if (count($jsonCity) === 1) {
		$speech = $text . " est dans le département : " . $jsonCity[0]->codeDepartement . ' . Et il y a ' . number_format($jsonCity[0]->population) . ' habitants.' ;
	} elseif (count($jsonCity) > 1) {
		$i = 0;
		foreach ($jsonCity as $key => $value) {
			$speech[$i] = $value;
			$i++;
		}
	} else {
		$speech = "Désolé je ne connais pas cette ville.";
	};

	$response = new \stdClass();
	if (count($speech) === 1) {
		$response->fulfillmentText = $speech;
		$response->fulfillmentMessages[]['text']['text'] = [$speech];
	} else {
		$i = 0;
		foreach ($speech as $key => $value) {
			// $response->fulfillmentText['messages'] = [$speech[$i]->nom . ' est dans le département ' . $speech[$i]->codeDepartement . ' et il y a ' . number_format($speech[$i]->population) . ' habitants.'];
			// $response->fulfillmentMessages[]['text']['text'] = [$speech[$i]->nom . ' est dans le département ' . $speech[$i]->codeDepartement . ' et il y a ' . number_format($speech[$i]->population) . ' habitants.'];
			$response->fulfillmentMessages[]['text'] += array(
				'text' => [$speech[$i]->nom . ' est dans le département ' . $speech[$i]->codeDepartement . ' et il y a ' . number_format($speech[$i]->population) . ' habitants.']
			);
			$i++;
		}
	}
	
	$response->source = "webhook";
	echo json_encode($response);
}
else
{
	echo "Method not allowed";
}

?>