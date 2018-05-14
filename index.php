<?php 

$method = $_SERVER['REQUEST_METHOD'];

// Process only when method is POST
if($method == 'POST'){
	$requestBody = file_get_contents('php://input');
	$json = json_decode($requestBody);
	
	$text      = $json->queryResult->parameters;
	$cityArray = get_object_vars($text);
	var_dump($text);
	$requestCity = file_get_contents("https://geo.api.gouv.fr/communes?nom=" . $cityArray['geo-city-fr'] . "&fields=nom,code,codesPostaux,codeDepartement,codeRegion,population&format=json&geometry=centre");
	$jsonCity = json_decode($requestCity);
	var_dump($jsonCity);
	$resultCity = get_object_vars($jsonCity[0]);

	if ($resultCity['nom'] != null) {
		$speech = "Le code du département est : " . $resultCity['codeDepartement'] . ' . Et il y a ' . number_format($resultCity['population']) . ' habitants.' ;
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