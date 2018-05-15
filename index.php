<?php 

$method     = $_SERVER['REQUEST_METHOD'];
$resultCity = array();
// Process only when method is POST
if($method == 'POST'){
	$requestBody = file_get_contents('php://input');
	$json = json_decode($requestBody);
	
	$text      = $json->queryResult->parameters->ville;
	
	$requestCity = file_get_contents("https://geo.api.gouv.fr/communes?nom=" . $text . "&fields=nom,code,codesPostaux,codeDepartement,codeRegion,population&format=json&geometry=centre");
	$jsonCity = json_decode($requestCity);
	// foreach ($jsonCity as $key => $value) {
	// 	array_push($resultCity, $value);
	// }
	var_dump(count($jsonCity));
	// $resultCity = get_object_vars($jsonCity[0]);

	if (count($jsonCity) === 1) {
		$speech = "Le code du département est : " . $jsonCity[0]['codeDepartement'] . ' . Et il y a ' . number_format($jsonCity[0]['population']) . ' habitants.' ;
	} elseif (count($jsonCity) > 1) {
		$i = 0;
		foreach ($jsonCity as $key => $value) {
			$speech[$i] = $value;
			$i++;
		}
	} else {
		$speech = "Désolé je ne connais pas cette ville.";
	};
	var_dump($speech);
	$response = new \stdClass();
	$response->fulfillmentText = $speech;
	$response->fulfillmentMessages[]['text']['text'] = [$speech];
	$response->source = "webhook";
	echo json_encode($response);
}
else
{
	echo "Method not allowed";
}

?>