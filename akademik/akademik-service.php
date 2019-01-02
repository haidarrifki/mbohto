<?php

require_once('lib/nusoap.php');

$ns = "http://".$_SERVER['HTTP_HOST']."/akademik/akademik-service.php";

$server = new nusoap_server();

$server->configureWSDL("akademik-service", $ns);

$server->wsdl->addComplexType(
    'Mahasiswa',
    'complexType',
    'struct',
    'sequence',
    '',
    array(
        'nim' => array(
            'name' => 'nim',
            'type' => 'xsd:string'
        ),
        'nama' => array(
            'name' => 'nama',
            'type' => 'xsd:string'
        ),
        'prodi' => array(
            'name' => 'prodi',
            'type' => 'xsd:string'
        )
    )
);

$server->wsdl->addComplexType(
    'ArrayMahasiswa',
    'complexType',
    'array',
    '',
    'SOAP-ENC:Array',
    array(),
    array(
        array(
            'ref'=>'SOAP-ENC:arrayType',
            'wsdl:arrayType'=>'tns:Mahasiswa[]'
        )
    ),
    'tns:Mahasiswa'
);

$server->register(
    'getMhs',
    array('input' => 'xsd:Array'), // input parameters
    array('mhs' => 'tns:ArrayMahasiswa'),
    $ns, // namespace
    "urn:".$ns.'#getMhs', // soapaction
    "rpc", // style
    "encoded", // use
    "Mengambil semua data mahasiswa"
);

$server->register(
    'getMhsByNim',
    array('nomhs' => 'xsd:string'), // input parameters
    array('mhs' => 'tns:Mahasiswa'),
    $ns, // namespace
    "urn:".$ns.'#getMhsByNim', // soapaction
    "rpc", // style
    "encoded", // use
    "Mengambil data mahasiswa dari nim"
);

$server->register(
    'inputMhs',
    array(
        'nomhs' => 'xsd:string',
        'nmmhs' => 'xsd:string',
        'psmhs' => 'xsd:string'
    ),
    array('msg' => 'xsd:string'),
    $ns,
    "urn:".$ns.'#inputMhs',
    "rpc",
    "encoded",
    "Input data mahasiswa"
);

$server->register(
    'updateMhs',
    array(
        'nomhs' => 'xsd:string',
        'nmmhs' => 'xsd:string',
        'psmhs' => 'xsd:string'
    ),
    array('msg' => 'xsd:string'),
    $ns, // namespace
    "urn:".$ns.'#updateMhs', // soapaction
    "rpc", // style
    "encoded", // use
    "Update data mahasiswa berdasarkan nim"
);

$server->register(
    'deleteMhs',
    array('nomhs' => 'xsd:string'),
    array('msg' => 'xsd:string'),
    $ns, // namespace
    "urn:".$ns.'#deleteMhs', // soapaction
    "rpc", // style
    "encoded", // use
    "Hapus data mahasiswa berdasarkan nim"
);

function getMhs() {

    $cn = new mysqli('localhost','root','','akademik');

    $query = $cn->query("SELECT * FROM mahasiswa");
    
    while ($value = $query->fetch_assoc()) {

        $mhs[] = array(
            'nim' => $value['nim'],
            'nama' => $value['nama'],
            'prodi' => $value['prodi']
        );

    }

    return $mhs;

}

function getMhsByNim($nomhs) {

    $cn = new mysqli('localhost','root','','akademik');

    $mhs = array();

    if (!empty($nomhs)) {

        $query = $cn->query("SELECT * FROM mahasiswa WHERE nim = '".$nomhs."'");

        $data = $query->fetch_assoc();

        $mhs = array(
            'nim' => $data['nim'],
            'nama' => $data['nama'],
            'prodi' => $data['prodi']
        );

    }

    return $mhs;

}

function inputMhs($nomhs, $nmmhs, $psmhs) {

    $cn = new mysqli('localhost','root','','akademik');

    $msg = '';
    
    if (!empty($nomhs) && !empty($nmmhs) && !empty($psmhs)) {

        $sql = "INSERT INTO mahasiswa (nim, nama, prodi) VALUES ('$nomhs','$nmmhs','$psmhs') ";

        $hasil = $cn->query($sql);

        if ($hasil > 0) {

            $msg = "Simpan data mahasiswa berhasil.";

        }
        else {

            $msg = "Simpan data mahasiswa gagal, silakan ulangi!";

        }

    }
    else {

        $msg = "Input data tidak valid!";

    }

    return $msg;
    
}

function updateMhs($nomhs, $nmmhs, $psmhs) {

    $cn = new mysqli('localhost','root','','akademik');

    $msg = "";

    if (!empty($nomhs) && !empty($nmmhs) && !empty($psmhs)) {

        $hasil = $cn->query("UPDATE mahasiswa SET nama = '$nmmhs', prodi = '$psmhs' WHERE nim = '$nomhs'");
        
        if ($hasil > 0) {

            $msg = "Update data mahasiswa berhasil.";

        }
        else {

            $msg = "Update data mahasiswa gagal, silakan ulangi!";

        }
    }
    else {

        $msg = "Input data tidak valid!";

    }

    return $msg;

}

function deleteMhs($nomhs) {

    $cn = new mysqli('localhost','root','','akademik');

    $msg = "";

    if (!empty($nomhs)) {

        $hasil = $cn->query("DELETE FROM mahasiswa WHERE nim = '$nomhs'");
        
        if ($hasil > 0) {

            $msg = "Hapus data mahasiswa berhasil.";

        }
        else {

            $msg = "Hapus data mahasiswa gagal, silakan ulangi!";

        }
    }
    else {

        $msg = "Nim tidak ditemukan!";

    }

    return $msg;

}

$server->service(file_get_contents('php://input'));