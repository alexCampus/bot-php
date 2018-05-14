<?php 

$method = $_SERVER['REQUEST_METHOD'];

// Process only when method is POST
if($method == 'POST'){
	$requestBody = file_get_contents('php://input');
	$json = json_decode($requestBody);

	$text = $json->result->parameters->text;

	switch ($text) {
		case 'Salut !':
			$speech = "Hi, Nice to meet you";
			break;

		case 'Bonjour !':
			$speech = "Hi, Nice to meet you";
			break;

		case 'Salutations !':
			$speech = "Hi, Nice to meet you";
			break;

		case 'Bienvenue !':
			$speech = "Hi, Nice to meet you";
			break;
		
		default:
			$speech = "Sorry, I didnt get that. Please ask me something else.";
			break;
	};

	$response = new \stdClass();
	$response->fulfillmentText = $speech;
	//$response->displayText = $speech;
	$response->fulfillmentMessages = ["Que souhaitez vous faire maintenant ?"];
	$response->source = "webhook";
	echo json_encode($response);
}
else
{
	echo "Method not allowed";
}

?>