<?php

// API endpoint URL
$apiUrl = 'https://shark.lacorte.tech/api/trpc/templates.createFromSchema';

// Request headers
$headers = array(
    'Content-Type: application/json',
    'Authorization: c22127d69021dadeb1836d49636d93888ce040416479323ca46ca68e0ab1d37b',
);

// Request data
$jsonData = json_encode(array(
    'json' => array(
        'name' => 'Ghost',
        'projectName' => 'projeto-maroto',
        'schema' => array(
            'services' => array(
                array(
                    'type' => 'app',
                    'data' => array(
                        'projectName' => 'projeto-maroto',
                        'serviceName' => 'ghost',
                        'source' => array(
                            'type' => 'image',
                            'image' => 'ghost:5-alpine'
                        ),
                        'domains' => array(
                            array(
                                'host' => '$(EASYPANEL_DOMAIN)',
                                'port' => 2368
                            )
                        ),
                        'mounts' => array(
                            array(
                                'type' => 'volume',
                                'name' => 'content',
                                'mountPath' => '/var/lib/ghost/content'
                            )
                        ),
                        'env' => preg_replace('/^\s+|\s+$/m', '', "
                            url=https://$(PRIMARY_DOMAIN)
                            database__client=mysql
                            database__connection__host=$(PROJECT_NAME)_mysql
                            database__connection__user=mysql
                            database__connection__password=bc322e6dca683640fd2c
                            database__connection__database=$(PROJECT_NAME)
                        ")
                    )
                ),
                array(
                    'type' => 'mysql',
                    'data' => array(
                        'projectName' => 'projeto-maroto',
                        'serviceName' => 'mysql',
                        'password' => 'bc322e6dca683640fd2c'
                    )
                )
            )
        )
    )
));

// Set up cURL
$ch = curl_init($apiUrl);

// Set cURL options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

// Execute cURL session and get the response
$response = curl_exec($ch);

// Check for cURL errors
if (curl_errno($ch)) {
    echo 'Curl error: ' . curl_error($ch);
}

// Close cURL session
curl_close($ch);

$data = json_decode($response, true);

$hasService = $data['error']['json']['code'];

if($hasService !== -32603) {
    header("HTTP/1.1 200 OK");
} else {
    header("HTTP/1.1 409 Conflict");
}
?>
