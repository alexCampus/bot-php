<?php 

$method = $_SERVER['REQUEST_METHOD'];

// Process only when method is POST
if($method == 'POST'){
	$requestBody = file_get_contents('php://input');
	$json = json_decode($requestBody);
	
	$text = $json->queryResult->parameters;
	var_dump('TEXT', $text[0]->geo-city-fr);
	$requestCity = file_get_contents("https://geo.api.gouv.fr/communes?nom=" . $text . "&fields=nom,code,codesPostaux,codeDepartement,codeRegion,population&format=json&geometry=centre");
	$jsonCity = json_decode($requestCity);
	var_dump('Json City', $jsonCity);
	if ($jsonCity->nom != null) {
		$speech = $jsonCity->code;
	} else {
		$speech = "Désolé je ne connais pas cette ville.";
	};

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