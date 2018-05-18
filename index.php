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
	$music     = $json->queryResult->parameters->music;

	if ($text != null) {
		$requestCity = file_get_contents("https://geo.api.gouv.fr/communes?nom=" . skip_accents($text) . "&fields=nom,code,codesPostaux,codeDepartement,codeRegion,population&format=json&geometry=centre");
		$jsonCity = json_decode($requestCity);

		if (count($jsonCity) === 1) {
			$speech = $text . " est dans le département : " . $jsonCity[0]->codeDepartement . ' . Et il y a ' . number_format($jsonCity[0]->population) . ' habitants.Quel est ton groupe de musique préféré?' ;
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
				if ($speech[$i]->nom === $text) {
					$response->fulfillmentMessages[]['text']['text'] = [$speech[$i]->nom . ' est dans le département ' . $speech[$i]->codeDepartement . ' et il y a ' . number_format($speech[$i]->population) . ' habitants. Quel est ton groupe de musique préféré?' ];
				}
				$i++;
			}
		}
	} elseif($music != null) {
		$requestMusic = file_get_contents("http://ws.audioscrobbler.com/2.0/?method=artist.gettopalbums&artist=". $music ."&api_key=7d9337a7356751d48d0b791b87379ce7&format=json");
		$jsonMusic    = json_decode($requestMusic);
		$array = [];
		for ($i=0; $i <= 3; $i++) { 
			array_push($array, $jsonMusic->topalbums->album[$i]->name);
		}
$response->fulfillmentMessages = array(
							array(
								'text' => array(
									'text' => array(
										array(
											'test1'
										)
									),
									1 => array(
										array(
											'test2'
										)
									),
								)
							)
						);
		// $response->fulfillmentText[]['text']['text'][0] = "Super j'adore " . $music . " moi aussi.  \n  \n Mes titres préférés sont  \n  \n : " . $array[0] . " et   \n  \n" . $array[1];
		// $response->fulfillmentText['text']['text'][1] = "Super j'adore " . $music . " moi aussi.  \n  \n Mes titres préférés sont  \n  \n : " . $array[0] . " et   \n  \n" . $array[1];
		// $response->fulfillmentMessages[]['text']['text'][0] = "Super j'adore " . $music . " moi aussi.  \n  \n Mes albums préférés sont  \n  \n : " . $array[0] . " et  \n  \n " . $array[1];
		// $response->fulfillmentMessages[]['text']['text'][1] = "Super j'adore " . $music . " moi aussi.  \n  \n Mes albums préférés sont  \n  \n : " . $array[0] . " et  \n  \n " . $array[1];
	}
	
	
	$response->source = "webhook";
	echo json_encode($response);
}
else
{
	echo "Method not allowed";
}
$response->fulfillmentMessages = array(
							'text' => array(
								'text' => array(
									array(
										'test1'
									)
								),
								'text' => array(
									array(
										'test2'
									)
								),
							)
						);
?>