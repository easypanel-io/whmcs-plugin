<?php

require_once __DIR__ . '/vendor/autoload.php';

use Ramsey\Uuid\Uuid;
use WHMCS\Database\Capsule;
use Hackzilla\PasswordGenerator\Generator\ComputerPasswordGenerator;

class EasyPanelSDK {

    function __construct($apiUrl, $authorizationToken)  {
        $this->apiUrl = $apiUrl;
        $this->authorizationToken = $authorizationToken;
    }

    private function service_type($originalString) {
        // can be app, mysql, mariadb, postgres, mongo or redis
        $keywords = ["mysql", "mariadb", "postgres", "mongo", "redis", "wordpress"];

        // Variable to store the matched keyword
        $matchedKeyword = null;

        // Check if the original string includes any keyword from the array
        foreach ($keywords as $keyword) {
            if (!!strpos($originalString, $keyword)) {
                $matchedKeyword = $keyword;
                break; // Break out of the loop once a match is found
            }
        }

        // Output the result
        if (!is_null($matchedKeyword)) {
            return $matchedKeyword;
        } else {
            return "app";
        }
    }

    public function getStatus() {
        // Your API endpoint URL
        $apiUrl = "$this->apiUrl/api/trpc/update.getStatus?input={\"json\":null}";

        // Validate url
        $apiUrlTest = filter_var($apiUrl, FILTER_VALIDATE_URL);
        if ($apiUrlTest === false) {
            throw new Exception("Invalid API URL", 1);
        }

        $authorizationToken = htmlspecialchars($this->authorizationToken, ENT_QUOTES, 'UTF-8');
        if (empty($authorizationToken)) {
            throw new Exception("Invalid API URL", 1);
        }

        // Set up cURL
        $ch = curl_init($apiUrl);

        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            "Authorization: $authorizationToken",
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

        $metadata = $data['result']['data']['json'];

        return $metadata;
    }

    public function listProjects() {
        // Your API endpoint URL
        $apiUrl = "$this->apiUrl/api/trpc/projects.listProjectsAndServices?input={\"json\":null}";

        // Validate url
        $apiUrlTest = filter_var($apiUrl, FILTER_VALIDATE_URL);
        if ($apiUrlTest === false) {
            throw new Exception("Invalid API URL", 1);
        }

        $authorizationToken = htmlspecialchars($this->authorizationToken, ENT_QUOTES, 'UTF-8');
        if (empty($authorizationToken)) {
            throw new Exception("Invalid API URL", 1);
        }

        // Set up cURL
        $ch = curl_init($apiUrl);

        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            "Authorization: $authorizationToken",
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

        $projects = $data['result']['data']['json']['projects'];

        return $projects;
    }

    public function listServices($clientId) {
        $projectName = "$clientId";

        // Your API endpoint URL
        $apiUrl = "$this->apiUrl/api/trpc/projects.inspectProject?input={\"json\":{\"projectName\":\"$projectName\"}}";

        // Validate url
        $apiUrlTest = filter_var($apiUrl, FILTER_VALIDATE_URL);
        if ($apiUrlTest === false) {
            throw new Exception("Invalid API URL", 1);
        }

        // Your authorization token
        $authorizationToken = htmlspecialchars($this->authorizationToken, ENT_QUOTES, 'UTF-8');

        // Set up cURL
        $ch = curl_init($apiUrl);

        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            "Authorization: $authorizationToken",
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
            return $data;
        }
    }

    public function createProject($clientId) {
        // Your API endpoint URL
        $apiUrl = "$this->apiUrl/api/trpc/projects.createProject";

        // Validate url
        $apiUrlTest = filter_var($apiUrl, FILTER_VALIDATE_URL);
        if (!$apiUrlTest) {
            throw new Exception("Invalid API URL: $apiUrl", 1);
        }

        $authorizationToken = htmlspecialchars($this->authorizationToken, ENT_QUOTES, 'UTF-8');
        if (empty($authorizationToken)) {
            throw new Exception("Invalid API Token: $authorizationToken", 1);
        }

        $projectName = "$clientId";

        // Your JSON data
        $jsonData = json_encode(['json' => ['name' => $projectName]]);

        // Set up cURL
        $ch = curl_init($apiUrl);

        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            "Authorization: $authorizationToken",
        ]);

        // Execute cURL session and get the response
        $response = curl_exec($ch);

        // Check for cURL errors
        if (curl_errno($ch)) {
            throw new Exception("Error Processing Request", 1);
        }

        // Close cURL session
        curl_close($ch);

        // Display the response
        if ($response) {
            return true;
        } else {
            throw new Exception("Error Processing Request", 1);
        }
    }

    public function createServiceFromSchema($clientId, $templateName, $serviceNamePrefix, $domain) {
        // API endpoint URL
        $apiUrl = "$this->apiUrl/api/trpc/templates.createFromSchema";
        // Validate url
        $apiUrlTest = filter_var($apiUrl, FILTER_VALIDATE_URL);
        if (!$apiUrlTest) {
            throw new Exception("Invalid API URL: $apiUrl", 1);
        }

        $authorizationToken = htmlspecialchars($this->authorizationToken, ENT_QUOTES, 'UTF-8');
        if (empty($authorizationToken)) {
            throw new Exception("Invalid API Token: $authorizationToken", 1);
        }

        $serviceNamePrefix = $serviceNamePrefix . "_";

        $serviceNamePrefix = htmlspecialchars($serviceNamePrefix, ENT_QUOTES, 'UTF-8');

        $service = $this->$templateName($clientId, $serviceNamePrefix);

        $template = $service["template"];

        $headers = array(
            'Content-Type: application/json',
            "Authorization: $authorizationToken",
        );

        // Set up cURL
        $ch = curl_init($apiUrl);

        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $template);
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
            $domain = $this->setDomain($clientId, $serviceNamePrefix.$templateName, $service['port'], $domain);
            $headers = array(
                'Content-Type: application/json',
                "Authorization: $authorizationToken",
            );
            $apiUrl = "$this->apiUrl/api/trpc/services.app.updateDomains";
            // Set up cURL
            $ch = curl_init($apiUrl);

            // Set cURL options
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $domain);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            // Execute cURL session and get the response
            curl_exec($ch);

            // Check for cURL errors
            if (curl_errno($ch)) {
                echo 'Curl error: ' . curl_error($ch);
            }

            // Close cURL session
            curl_close($ch);

            return [
                'services' => $service["services"],
                'prefix' => $serviceNamePrefix,
            ];
        } else {
            // HTTP/1.1 409 Conflict
            return false;
        }
    }

    public function destroyService($clientId, $service) {
        // Type of service
        $serviceType = $this->service_type($service);

        // Your API endpoint URL
        $apiUrl = "$this->apiUrl/api/trpc/services.$serviceType.destroyService";

        // Validate url
        $apiUrl = filter_var($apiUrl, FILTER_VALIDATE_URL);
        if ($apiUrl === false) {
            throw new Exception("Invalid API URL", 1);
        }

        $authorizationToken = htmlspecialchars($this->authorizationToken, ENT_QUOTES, 'UTF-8');
        if (empty($authorizationToken)) {
            throw new Exception("Invalid API URL", 1);
        }

        // Request headers
        $headers = array(
            'Content-Type: application/json',
            'Authorization: ' . $authorizationToken,
        );

        $project = htmlspecialchars($clientId, ENT_QUOTES, 'UTF-8');

        $service = htmlspecialchars($service, ENT_QUOTES, 'UTF-8');

        // Request data
        $body = json_encode(array(
            'json' => array(
                'projectName' => $project,
                'serviceName' => $service,
            ),
        ));

        // Set up cURL
        $ch = curl_init($apiUrl);

        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
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
    }

    public function disableService($clientId, $service) {
        // Type of service
        $serviceType = $this->service_type($service);

        // Version adaptations
        $current_version = $this->getStatus()["version"];
        if($serviceType == "app") {
            if (version_compare($current_version, "1.43.0") >= 0) {
                echo "Version is higher or equal to v1.43.0";
                // Your API endpoint URL
                $apiUrl = "$this->apiUrl/api/trpc/services.$serviceType.stopService";
            } else {
                echo "Version is lower than v1.43.0";
                // Your API endpoint URL
                $apiUrl = "$this->apiUrl/api/trpc/services.$serviceType.disableService";
            }
        } else {
            // Your API endpoint URL
            $apiUrl = "$this->apiUrl/api/trpc/services.$serviceType.disableService";
        }

        // Validate url
        $apiUrl = filter_var($apiUrl, FILTER_VALIDATE_URL);
        if ($apiUrl === false) {
            throw new Exception("Invalid API URL", 1);
        }

        $authorizationToken = htmlspecialchars($this->authorizationToken, ENT_QUOTES, 'UTF-8');
        if (empty($authorizationToken)) {
            throw new Exception("Invalid API URL", 1);
        }

        // Request headers
        $headers = array(
            'Content-Type: application/json',
            'Authorization: ' . $authorizationToken,
        );

        $project = htmlspecialchars($clientId, ENT_QUOTES, 'UTF-8');

        $service = htmlspecialchars($service, ENT_QUOTES, 'UTF-8');

        // Request data
        $body = json_encode(array(
            'json' => array(
                'projectName' => $project,
                'serviceName' => $service,
            ),
        ));

        // Set up cURL
        $ch = curl_init($apiUrl);

        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
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
    }

    public function enableService($clientId, $service) {
        // Type of service
        $serviceType = $this->service_type($service);

        // Version adaptations
        $current_version = $this->getStatus()["version"];
        if($serviceType == "app") {
            if (version_compare($current_version, "1.43.0", "ge")) {
                echo "Version is higher or equal to v1.43.0";
                // Your API endpoint URL
                $apiUrl = "$this->apiUrl/api/trpc/services.$serviceType.startService";
            } else {
                echo "Version is lower than v1.43.0";
                // Your API endpoint URL
                $apiUrl = "$this->apiUrl/api/trpc/services.$serviceType.enableService";
            }
        } else {
            // Your API endpoint URL
            $apiUrl = "$this->apiUrl/api/trpc/services.$serviceType.enableService";
        }

        // Validate url
        $apiUrl = filter_var($apiUrl, FILTER_VALIDATE_URL);
        if ($apiUrl === false) {
            throw new Exception("Invalid API URL", 1);
        }

        $authorizationToken = htmlspecialchars($this->authorizationToken, ENT_QUOTES, 'UTF-8');
        if (empty($authorizationToken)) {
            throw new Exception("Invalid API URL", 1);
        }

        // Request headers
        $headers = array(
            'Content-Type: application/json',
            'Authorization: ' . $authorizationToken,
        );

        $project = htmlspecialchars($clientId, ENT_QUOTES, 'UTF-8');

        $service = htmlspecialchars($service, ENT_QUOTES, 'UTF-8');

        // Request data
        $body = json_encode(array(
            'json' => array(
                'projectName' => $project,
                'serviceName' => $service,
            ),
        ));

        // Set up cURL
        $ch = curl_init($apiUrl);

        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
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
    }

    public function restartService($clientId, $service) {
        // Type of service
        $serviceType = $this->service_type($service);

        // Your API endpoint URL
        $apiUrl = "$this->apiUrl/api/trpc/services.$serviceType.restartService";

        // Validate url
        $apiUrl = filter_var($apiUrl, FILTER_VALIDATE_URL);
        if ($apiUrl === false) {
            throw new Exception("Invalid API URL", 1);
        }

        $authorizationToken = htmlspecialchars($this->authorizationToken, ENT_QUOTES, 'UTF-8');
        if (empty($authorizationToken)) {
            throw new Exception("Invalid API URL", 1);
        }

        // Request headers
        $headers = array(
            'Content-Type: application/json',
            'Authorization: ' . $authorizationToken,
        );

        $project = htmlspecialchars($clientId, ENT_QUOTES, 'UTF-8');

        $service = htmlspecialchars($service, ENT_QUOTES, 'UTF-8');

        // Request data
        $body = json_encode(array(
            'json' => array(
                'projectName' => $project,
                'serviceName' => $service,
            ),
        ));

        // Set up cURL
        $ch = curl_init($apiUrl);

        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
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
    }

    public function updateResources($project, $service, $cpu_limit, $ram_limit) {
        // Type of service
        $serviceType = $this->service_type($service);

        // Your API endpoint URL
        $apiUrl = "$this->apiUrl/api/trpc/services.$serviceType.updateResources";

        // Validate url
        $apiUrl = filter_var($apiUrl, FILTER_VALIDATE_URL);
        if ($apiUrl === false) {
            throw new Exception("Invalid API URL", 1);
        }

        $authorizationToken = htmlspecialchars($this->authorizationToken, ENT_QUOTES, 'UTF-8');
        if (empty($authorizationToken)) {
            throw new Exception("Invalid API URL", 1);
        }

        // Request headers
        $headers = array(
            'Content-Type: application/json',
            'Authorization: ' . $authorizationToken,
        );

        $project = htmlspecialchars($clientId, ENT_QUOTES, 'UTF-8');

        $service = htmlspecialchars($service, ENT_QUOTES, 'UTF-8');

        // Request data
        $body = json_decode('
        {
            "json": {
                "projectName": "'.$project.'",
                "serviceName": "'.$service.'",
                "resources": {
                    "memoryReservation": 0,
                    "memoryLimit": '.$ram_limit.',
                    "cpuReservation": 0,
                    "cpuLimit": '.$cpu_limit.'
                }
            }
        }
        ');

        // Set up cURL
        $ch = curl_init($apiUrl);

        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
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
    }

    public function monitorServiceStatus() {
        // TODO: Implement monitorServiceStatus method
    }

    private function ghost($projectName, $serviceNamePrefix) {
        $generator = new ComputerPasswordGenerator();

        $generator
            ->setOptionValue(ComputerPasswordGenerator::OPTION_UPPER_CASE, true)
            ->setOptionValue(ComputerPasswordGenerator::OPTION_LOWER_CASE, true)
            ->setOptionValue(ComputerPasswordGenerator::OPTION_NUMBERS, true)
            ->setOptionValue(ComputerPasswordGenerator::OPTION_SYMBOLS, false)
        ;

        $mysql_password = $generator->generatePassword();
        $template = '
{
    "json": {
        "name": "Ghost",
        "projectName": "'.$projectName.'",
        "schema": {
            "services": [
                {
                    "type": "app",
                    "data": {
                        "projectName": "'.$projectName.'",
                        "serviceName": "'.$serviceNamePrefix.'ghost",
                        "source": {
                            "type": "image",
                            "image": "ghost:5.82.12-alpine"
                        },
                        "domains": [
                            {
                                "host": "$(EASYPANEL_DOMAIN)",
                                "port": 2368,
                                "https": true,
                                "path": "/"
                            }
                        ],
                        "mounts": [
                            {
                                "type": "volume",
                                "name": "content",
                                "mountPath": "/var/lib/ghost/content"
                            }
                        ],
                        "env": "url=https://$(PRIMARY_DOMAIN)\ndatabase__client=mysql\ndatabase__connection__host=$(PROJECT_NAME)_'.$serviceNamePrefix.'mysql\ndatabase__connection__user=mysql\ndatabase__connection__password='.$mysql_password.'\ndatabase__connection__database=$(PROJECT_NAME)"
                    }
                },
                {
                    "type": "mysql",
                    "data": {
                        "projectName": "'.$projectName.'",
                        "serviceName": "'.$serviceNamePrefix.'mysql",
                        "password": "'.$mysql_password.'"
                    }
                }
            ]
        }
    }
}
        ';
        return [
            'template' => $template,
            'services' => [
                $serviceNamePrefix . "ghost",
                $serviceNamePrefix . "mysql",
            ],
            'port' => 2368
        ];
    }

    private function wordpress($projectName, $serviceNamePrefix) {
        $generator = new ComputerPasswordGenerator();

        $generator
            ->setOptionValue(ComputerPasswordGenerator::OPTION_UPPER_CASE, true)
            ->setOptionValue(ComputerPasswordGenerator::OPTION_LOWER_CASE, true)
            ->setOptionValue(ComputerPasswordGenerator::OPTION_NUMBERS, true)
            ->setOptionValue(ComputerPasswordGenerator::OPTION_SYMBOLS, false)
        ;

        $mysql_password = $generator->generatePassword();

        $template = '
        {
            "json": {
                "name": "Wordpress",
                "projectName": "'.$projectName.'",
                "schema": {
                    "services": [
                        {
                            "type": "app",
                            "data": {
                                "serviceName": "'.$serviceNamePrefix.'wordpress",
                                "env": "WORDPRESS_DB_HOST=$(PROJECT_NAME)_'.$serviceNamePrefix.'mysql\nWORDPRESS_DB_USER=mysql\nWORDPRESS_DB_PASSWORD='.$mysql_password.'\nWORDPRESS_DB_NAME=$(PROJECT_NAME)",
                                "source": {
                                    "type": "image",
                                    "image": "wordpress:latest"
                                },
                                "domains": [
                                    {
                                        "host": "$(EASYPANEL_DOMAIN)",
                                        "https": true,
                                        "port": 80,
                                        "path": "/"
                                    }
                                ],
                                "mounts": [
                                    {
                                        "type": "volume",
                                        "name": "data",
                                        "mountPath": "/var/www/html"
                                    },
                                    {
                                        "type": "file",
                                        "content": "upload_max_filesize = 100M\npost_max_size = 100M\n",
                                        "mountPath": "/usr/local/etc/php/conf.d/custom.ini"
                                    }
                                ]
                            }
                        },
                        {
                            "type": "mysql",
                            "data": {
                                "serviceName": "'.$serviceNamePrefix.'mysql",
                                "password": "'.$mysql_password.'"
                            }
                        }
                    ]
                }
            }
        }
        ';
        return [
            'template' => $template,
            'services' => [
                $serviceNamePrefix . "wordpress",
                $serviceNamePrefix . "mysql",
            ],
            'port' => 80
        ];
    }

    public function setDomain($projectName, $serviceName, $servicePort, $domain) {
        $request = '
{
    "json": {
        "projectName": "'.$projectName.'",
        "serviceName": "'.$serviceName.'",
        "domains": [
            {
                "host": "'.$domain.'",
                "https": true,
                "port": '.$servicePort.',
                "path": "/",
                "middlewares": [],
                "certificateResolver": "",
                "wildcard": false
            }
        ]
    }
}
        ';
        return $request;
    }
}