<?php

use Ramsey\Uuid\Uuid;
use Hackzilla\PasswordGenerator\Generator\ComputerPasswordGenerator;

class EasyPanelSDK {

    function __construct($apiUrl, $authorizationToken)  {
        $this->apiUrl = $apiUrl;
        $this->authorizationToken = $authorizationToken;
    }

    private function service_type($originalString, $keywords) {
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
        if (!$matchedKeyword) {
            return $matchedKeyword;
        } else {
            return "app";
        }
    }

    public function listProjects() {
        // Your API endpoint URL
        $apiUrl = "$this->apiUrl/api/trpc/projects.listProjectsAndServices?input={\"json\":null}";

        // Validate url
        $apiUrl = filter_var($apiUrl, FILTER_VALIDATE_URL);
        if ($apiUrl === false) {
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
        $apiUrl = filter_var($apiUrl, FILTER_VALIDATE_URL);
        if ($apiUrl === false) {
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
        $apiUrl = "https://$this->apiUrl/api/trpc/projects.createProject";

        // Validate url
        $apiUrl = filter_var($apiUrl, FILTER_VALIDATE_URL);
        if ($apiUrl === false) {
            throw new Exception("Invalid API URL", 1);
        }

        $authorizationToken = htmlspecialchars($this->authorizationToken, ENT_QUOTES, 'UTF-8');
        if (empty($authorizationToken)) {
            throw new Exception("Invalid API URL", 1);
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

    public function createService($clientId, $templateName) {
        // API endpoint URL
        $apiUrl = "$this->apiUrl/api/trpc/templates.createFromSchema";

        // Validate url
        $apiUrl = filter_var($apiUrl, FILTER_VALIDATE_URL);
        if ($apiUrl === false) {
            throw new Exception("Invalid API URL", 1);
        }

        $authorizationToken = htmlspecialchars($this->authorizationToken, ENT_QUOTES, 'UTF-8');
        if (empty($authorizationToken)) {
            throw new Exception("Invalid API URL", 1);
        }

        $projectName = htmlspecialchars($clientId, ENT_QUOTES, 'UTF-8');

        $serviceNamePrefix = Uuid::uuid4()->toString()."_";

        $serviceNamePrefix = htmlspecialchars($serviceNamePrefix, ENT_QUOTES, 'UTF-8');

        $service = $this->$templateName();

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
            return [
                'services' => $service["services"],
                'prefix' => $serviceNamePrefix,
            ];
        } else {
            // HTTP/1.1 409 Conflict
            return false;
        }
    }

    /*public function destroyService() {
        // can be app, mysql, mariadb, postgres, mongo or redis
        $serviceType = this.service_type($serviceName, ['mysql', 'mariadb', 'postgres', 'mongo', 'redis']) ?;

        // Your API endpoint URL
        $apiUrl = "$apiUrl/api/trpc/services.$serviceType.destroyService";

        // Validate url
        $apiUrl = filter_var($apiUrl, FILTER_VALIDATE_URL);
        if ($apiUrl === false) {
            throw new Exception("Invalid API URL", 1);
        }

        $authorizationToken = htmlspecialchars($authorizationToken, ENT_QUOTES, 'UTF-8');
        if (empty($authorizationToken)) {
            throw new Exception("Invalid API URL", 1);
        }

        // Request headers
        $headers = array(
            'Content-Type: application/json',
            'Authorization: ' . $authorizationToken,
        );

        $projectName = htmlspecialchars($projectName, ENT_QUOTES, 'UTF-8');

        $serviceName = htmlspecialchars($serviceName, ENT_QUOTES, 'UTF-8');

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
    }

    public function disableService() {
        // can be app, mysql, mariadb, postgres, mongo or redis
        $serviceType = this.service_type($serviceName, ['mysql', 'mariadb', 'postgres', 'mongo', 'redis']) ?;

        // Your API endpoint URL
        $apiUrl = "$apiUrl/api/trpc/services.$serviceType.disableService";

        // Validate url
        $apiUrl = filter_var($apiUrl, FILTER_VALIDATE_URL);
        if ($apiUrl === false) {
            throw new Exception("Invalid API URL", 1);
        }

        $authorizationToken = htmlspecialchars($authorizationToken, ENT_QUOTES, 'UTF-8');
        if (empty($authorizationToken)) {
            throw new Exception("Invalid API URL", 1);
        }

        // Request headers
        $headers = array(
            'Content-Type: application/json',
            'Authorization: ' . $authorizationToken,
        );

        $projectName = htmlspecialchars($projectName, ENT_QUOTES, 'UTF-8');

        $serviceName = htmlspecialchars($serviceName, ENT_QUOTES, 'UTF-8');

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
    }

    public function startService($apiUrl, $authorizationToken, $clientId, $projectName, $serviceName) {
        // can be app, mysql, mariadb, postgres, mongo or redis
        $serviceType = this.includes_on_array($serviceName, ['mysql', 'mariadb', 'postgres', 'mongo', 'redis']) ?;

        // Your API endpoint URL
        $apiUrl = "$apiUrl/api/trpc/services.$serviceType.enableService";

        // Validate url
        $apiUrl = filter_var($apiUrl, FILTER_VALIDATE_URL);
        if ($apiUrl === false) {
            throw new Exception("Invalid API URL", 1);
        }

        $authorizationToken = htmlspecialchars($authorizationToken, ENT_QUOTES, 'UTF-8');
        if (empty($authorizationToken)) {
            throw new Exception("Invalid API URL", 1);
        }

        // Request headers
        $headers = array(
            'Content-Type: application/json',
            'Authorization: ' . $authorizationToken,
        );

        $projectName = htmlspecialchars($projectName, ENT_QUOTES, 'UTF-8');

        $serviceName = htmlspecialchars($serviceName, ENT_QUOTES, 'UTF-8');

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
    }*/

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
                                "port": 2368
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
        ];
    }
}
