<?php

include "Rest.php";

$id="";

if(isset($_GET['id'])) {

	$id = $_GET['id'];

}

$url = "https://bukisa.com/wp-json/wp/v2/posts/".$id."";

$headers = array(
        'Accept:application/json',
        'Content -Type:application/json'
    );

	function extraerSRC($cadena) {

	    preg_match('@src="([^"]+)"@', $cadena, $array);
	    $src = array_pop($array);
	    return $src;
	}

$data = new RestWs($url, $headers);
        try {

            $data->execute();

        } catch (Exception $e) {

        	var_dump($e->getMessage());
		}

        $proData = json_decode($data->getResult(),true);

        $content = $proData['content']["rendered"];

        ///Variables////
        $noticias = explode("<!--nextpage-->", $content);
        $replace_h2 = array('<h2 style="text-align: center;">', '</h2>');
        $replace = array('<em>', '<div id="containerMrec"></div>');
        $total_n = count($noticias);
        $ultimo = $total_n - 1;
        //// Proceso para clasificar el contenido///
        foreach ($noticias as $key => $value) {

	        $parse_1 = str_replace("</p>", "<$>", $value);
	        $parse_2 = str_replace("<p>", "<$>", $parse_1);
	        $parse_3 = explode("<$>", $parse_2);
	        if($key === 0){

	        	unset($parse_3[0]);
		        $parse_3 = explode("<img", $parse_3[1]);
		        $parse_3[0]= str_replace($replace, '', $parse_3[0]);
		        $parse_3[1] = $parse_3[1];
		        $parse_3[1] = str_replace('</em>', '', $parse_3[1]);
	        }
	        if ($key === $ultimo) {
	        	unset($parse_3[count($parse_3)-1]);
	        }

	        $parse_4 = array_filter(array_map('trim', $parse_3));
	        $parse_5 = array_values(($parse_4));
        	$notice[] = $parse_5;
        }


//////////// Armado de json//////////////

	foreach ($notice as $key => $value) {
			
		$datos['name'] = ($key == 0) ? "" : str_replace($replace_h2, "", $value[0]);
		$datos['description'] = ($key == 0) ? str_replace($replace_h2, "", $value[0]) : str_replace($replace_h2, "", $value[1]);
		$datos['img'] = ($key == 0) ? extraerSRC($value[1]) : extraerSRC($value[2]);
		$datos['credit'] = ($key == 0) ? "" : $value[3];
		$datos['text'] = ($key == 0) ? "" : $value[4];
		$datos['above_text'] = "";
		$datos['attribution'] = "";

		$json[] = $datos;


	}

	//$json = str_replace("\u0022","\\\\\"",json_encode( $json,JSON_HEX_QUOT));

    header('Content-type: application/json; charset=utf-8');
	echo json_encode($json);

	 exit();


?>