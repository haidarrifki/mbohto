<?php

require_once('util.php');

$loc = $_GET['lokasi'] ? $_GET['lokasi'] : ''; // parameter lokasi
$output = $_GET['output'] ? $_GET['output'] : ''; // parameter output
$type = $_GET['tipe'] ? $_GET['tipe'] : ''; // patameter tipe

$clientId = 'paste_your_clientid_here'; // API Access Four Square
$clientSecret = 'paste_your_clientsecret_here'; // API Access Four Square
$today = date('Ymd'); // ambil tanggal hari ini
// url request api foursquare
$url = 'https://api.foursquare.com/v2/venues/search?client_id='.$clientId.'&client_secret='.$clientSecret.'&v='.$today;

$ch = NULL; // variabel kosong

if ($type == 'coordinates') {
    // inisialisasi curl dan menambahkan param ll pada $url
    $ch = curl_init($url.'&ll='.$loc);
} else {
    // inisialisasi curl dan menambahkan param near pada $url
    $ch = curl_init($url.'&near='.$loc);
}

curl_setopt_array($ch, array(
    CURLOPT_RETURNTRANSFER => true // ambil data dari respon body
));

$respon = curl_exec($ch); // jalankan curl dan simpan data ke variabel respon

$data = json_decode($respon, true); // ubah json ke dalam bentuk array

$venues = $data['response']['venues']; // ambil data venues dari foursquare

$totalData = count($venues); // hitung total array yang ada

curl_close($ch);

switch ($output) {
    case 'array':
        $venues['rows'] = $totalData;

        print_r($venues);
        break;
    case 'xml':
        $xml_data = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><data></data>');

        $xml_data->addChild('rows', $totalData);

        array_to_xml($venues, $xml_data);

        header("Content-type: text/xml; charset=utf-8");

        echo $xml_data->asXML();
        break;
    default:
        header('Content-type:application/json;charset=utf-8');
            
        $json_output = array(
            'rows' => $totalData,
            'data' => array_values($venues)
        );

        echo json_encode($json_output);
        break;
}