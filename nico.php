<?php

include "Rest.php";

$url = "https://bukisa.com/wp-json/wp/v2/posts/1093674";

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

        //var_dump($notice);die;

//////////// Armado de json//////////////

	foreach ($notice as $key => $value) {
			
		$datos['name'] = ($key == 0) ? "" : $value[0];
		$datos['description'] = ($key == 0) ? $value[0] : $value[1];
		$datos['img'] = ($key == 0) ? extraerSRC($value[1]) : extraerSRC($value[2]);
		$datos['credit'] = ($key == 0) ? "" : $value[3];
		$datos['text'] = ($key == 0) ? "" : $value[4];
		$datos['above_text'] = "";
		$datos['attribution'] = "";

		$json[] = $datos;


	}

    //header('Content-type: application/json; charset=utf-8');

	//$json = str_replace("\u0022","\\\\\"",json_encode( $json,JSON_HEX_QUOT));

	 echo json_encode($json);

	 exit();


?>