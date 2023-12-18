<?php

// can be app, mysql, mariadb, postgres, mongo or redis
$serviceType = 'app';

// Your API endpoint URL
$apiUrl = "https://shark.lacorte.tech/api/trpc/services.$serviceType.enableService';

// Your authorization token
$authorizationToken = 'clq8tise0000s1etc889wgavm';

// Request headers
$headers = array(
    'Content-Type: application/json',
    'Authorization: ' . $authorizationToken,
);

$projectName = 'project-name';

$serviceName = 'service-name';

// Request data
$jsonData = json_encode(array(
    'json' => array(
        'projectName' => $projectName,
        'serviceName' => $serviceName,
    ),
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

// Display the response
echo $response;
?>
