<?php

$projectName = 'projeto-maroto';

// Your API endpoint URL
$apiUrl = "https://shark.lacorte.tech/api/trpc/projects.inspectProject?input={\"json\":{\"projectName\":\"$projectName\"}}";

// Your authorization token
$authorizationToken = 'c22127d69021dadeb1836d49636d93888ce040416479323ca46ca68e0ab1d37b';

// Set up cURL
$ch = curl_init($apiUrl);

// Set cURL options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPGET, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Authorization: ' . $authorizationToken,
));

// Execute cURL session and get the response
$response = curl_exec($ch);

// Check for cURL errors
if (curl_errno($ch)) {
    echo 'Curl error: ' . curl_error($ch);
}

// Close cURL session
curl_close($ch);

$data = json_decode($response, true);
    
$hasProject = $data['error']['json']['code'];

if($hasProject !== -32603) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
}

?>
