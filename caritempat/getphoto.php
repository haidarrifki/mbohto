<?php
error_reporting(0);

require_once('util.php');

$id = $_GET['id'] ? $_GET['id'] : '';
$output = $_GET['output'] ? $_GET['output'] : '';

$clientId = 'paste_your_clientid_here';
$clientSecret = 'paste_your_clientsecret_here';
$today = date('Ymd');

$url = 'https://api.foursquare.com/v2/venues/'.$id.'/photos?client_id='.$clientId.'&client_secret='.$clientSecret.'&v='.$today;

$ch = curl_init($url);

curl_setopt_array($ch, array(
    CURLOPT_RETURNTRANSFER => true
));

$respon = curl_exec($ch);

$data = json_decode($respon, true);

$item = $data['response']['photos']['items'];

$photo = $item[0]['prefix'].'original'.$item[0]['suffix'];

curl_close($ch);

$result = array();

switch ($output) {
    case 'array':
        $result['urlPhoto'] = $photo;

        print_r($result);
        break;
    case 'xml':
        $xml_data = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><data></data>');

        $xml_data->addChild('urlPhoto', $photo);

        header("Content-type: text/xml; charset=utf-8");

        echo $xml_data->asXML();
        break;
    default:
        header('Content-type:application/json;charset=utf-8');
            
        $json_output = array(
            'urlPhoto' => $photo,
        );

        echo json_encode($json_output);
        break;
}