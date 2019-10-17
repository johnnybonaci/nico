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

	function extraerArraysVacios($array){

		$array_4 = array_filter(array_map('trim', $array));
        $array_5 = array_values(($array_4));
        return $array_5;

	}

	function parsearBloques($string,$variables){

		$parse_0 = str_replace($variables[0], $variables[1], $string);
        $parse_1 = str_replace($variables[1], "<$>", $parse_0);
        $parse_2 = str_replace($variables[2], "", $parse_1);
        $parse_3 = str_replace($variables[3], "", $parse_2);
        $parse_4 = explode("<$>", $parse_3);
        return $parse_4;

	}

	function unirTextos($texto){

		$texto = array_values($texto);
		$parrafo="";
		foreach ($texto as $key => $value) {
			
			$parrafo .= '<p>'.$value.'</p>';
		}

		return $parrafo;

	}

$data = new RestWs($url, $headers);
        try {

            $data->execute();

        } catch (Exception $e) {

        	var_dump($e->getMessage());
		}
		// Separa el contenido en bloques
        $proData = json_decode($data->getResult(),true);
        $content = $proData['content']["rendered"];
        $noticias = explode("<!--nextpage-->", $content);
        ///Variables////
        $replace_h2 = array('<h2 style="text-align: center;">','</h2>');
        $replace = array('<div id="containerMrec"></div>','</body></html>');
        $replace_blockquote = array('<blockquote>', '</blockquote>');
        $replace_em = array('<em>', '</em>');
        $replace_p = array('<p>', '</p>');
        $variables = array($replace,$replace_p,$replace_blockquote,$replace_h2,$replace_em);
        $total_n = count($noticias);
        $ultimo = $total_n - 1;

        //// Proceso para clasificar el contenido///
        foreach ($noticias as $key => $value) {

	        $bloque = explode("<img class", $value);

        	//Primer bloque Titulo Descripcion///
        	$bloque_1 = parsearBloques($bloque[0], $variables);
        	$bloque_2 = extraerArraysVacios($bloque_1);
        	$datos['name'] = ($key == 0) ? "" : $bloque_2[0];unset($bloque_2[0]);
        	$datos['description'] = unirTextos($bloque_2);
        	///Segundo Bloque img credit
        	$bloque_3 = parsearBloques($bloque[1], $variables);
        	$bloque_4 = extraerArraysVacios($bloque_3);
        	$datos['img'] = extraerSRC($bloque_4[0]);unset($bloque_4[0]);
        	$datos['above_text'] = "";
			$datos['attribution'] = "";
			$datos['credit'] = "";
			$datos['text'] = "";
        	$bloque_4 = array_values($bloque_4);

        	if(count($bloque_4) > 0){

	        	$datos['credit'] = $bloque_4[0];unset($bloque_4[0]);
	        	$bloque_4 = array_values($bloque_4);
	        	$datos['text'] = unirTextos($bloque_4);
	        }

    		$notice[] = $datos;
	    }

    header('Content-type: application/json; charset=utf-8');

    if((isset($_GET['div'])) && ($_GET['div'] == 1)) {
        $index = 1;
        foreach ($notice as $key => $value) {

            $div ='<div class="slide" infinite_index="'.$index.'">';
            $div .='<h2 class="ajax_title"> ' . $value['name']. ' </h2>';
            if ($index >= 3) {
                $div .= '<div id="RTK_7dnR" load-id="RTK_7dnR" style="overflow: hidden;" ></div>';
            }
            $div .= $value['description'];
            $div .= '<div class="gallery-image">';
            $div .= '<img class="img-responsive ajax_img" alt="" infinite_index="'.$index.'" src="'.$value['img'].'">';
            $div .= "</div>";
            if ($index >= 5) {
                $div .= '<div id="RTK_CFgp" load-id="RTK_CFgp" style="overflow: hidden;" ></div> <div id="RTK_rgCe" load-id="RTK_rgCe" style="overflow: hidden;" ></div>';
            }
            $creditos = str_replace($variables[4], "", $value['credit']);
            $div .= '<em class="ajax_attribution">'.$creditos.'</em>';
            $div .= $value['text'];
            if ($index >= 3) {
                $div .= '<div id="RTK_CVmP" load-id="RTK_CVmP" style="overflow: hidden;" ></div>';
            }
            $div .= "</div>";
            $div .= "</div>";
            $index++;
            $array['div'] = $div;
            $array_div[] = $array;
        }
       

       echo json_encode($array_div);

    }
    else{

	   echo json_encode($notice);
    }

	 exit();


?>