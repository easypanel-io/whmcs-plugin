<?php
use Ramsey\Uuid\Uuid;

class EasyPanelSDK {

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

    public function listProjects($apiUrl, $authorizationToken) {
        // Your API endpoint URL
        $apiUrl = "$apiUrl/api/trpc/projects.listProjectsAndServices?input={\"json\":null}";

        // Validate url
        $apiUrl = filter_var($apiUrl, FILTER_VALIDATE_URL);
        if ($apiUrl === false) {
            throw new Exception("Invalid API URL", 1);
        }

        $authorizationToken = htmlspecialchars($authorizationToken, ENT_QUOTES, 'UTF-8');
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

    public function listServices($apiUrl, $authorizationToken, $clientId) {
        $projectName = "client.$clientId";

        // Your API endpoint URL
        $apiUrl = "$apiUrl/api/trpc/projects.inspectProject?input={\"json\":{\"projectName\":\"$projectName\"}}";

        // Validate url  
        $apiUrl = filter_var($apiUrl, FILTER_VALIDATE_URL);
        if ($apiUrl === false) {
            throw new Exception("Invalid API URL", 1);
        }

        // Your authorization token
        $authorizationToken = htmlspecialchars($authorizationToken, ENT_QUOTES, 'UTF-8');

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

    public function createProject($apiUrl, $authorizationToken, $clientId) {
        // Your API endpoint URL
        $apiUrl = "https://$apiUrl/api/trpc/projects.createProject";

        // Validate url
        $apiUrl = filter_var($apiUrl, FILTER_VALIDATE_URL);
        if ($apiUrl === false) {
            throw new Exception("Invalid API URL", 1);
        }

        $authorizationToken = htmlspecialchars($authorizationToken, ENT_QUOTES, 'UTF-8');
        if (empty($authorizationToken)) {
            throw new Exception("Invalid API URL", 1);
        }

        $projectName = "client.$clientId";

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

    public function createServiceFromTemplate($apiUrl, $authorizationToken, $clientId, $templateName) {
        // API endpoint URL
        $apiUrl = "$apiUrl/api/trpc/templates.createFromSchema";

        // Validate url
        $apiUrl = filter_var($apiUrl, FILTER_VALIDATE_URL);
        if ($apiUrl === false) {
            throw new Exception("Invalid API URL", 1);
        }

        $authorizationToken = htmlspecialchars($authorizationToken, ENT_QUOTES, 'UTF-8');
        if (empty($authorizationToken)) {
            throw new Exception("Invalid API URL", 1);
        }

        $projectName = join("client", $clientId);

        $projectName = htmlspecialchars($projectName, ENT_QUOTES, 'UTF-8');

        $serviceName = join("client", $clientId, Uuid::uuid4().toString(), array(, $templateName));

        $serviceName = htmlspecialchars($serviceName, ENT_QUOTES, 'UTF-8');

        $passwordSeed = new Uuid::uuid4().toString();

        $password = str_replace('-', '', $passwordSeed);

        // Request headers
        $headers = array(
            'Content-Type: application/json',
            "Authorization: $authorizationToken",
        );
        
        // Request data
        $jsonData = json_encode(array(
            'json' => array(
                'name' => $serviceName,
                'projectName' => $projectName,
                'schema' => array(
                    'services' => array(
                        array(
                            'type' => 'app',
                            'data' => array(
                                'projectName' => $projectName,
                                'serviceName' => $serviceName,
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
                                    database__connection__host=$projectName_" . join('mysql', ) . new Uuid::uuid4().toString() ."
                                    database__connection__user=mysql
                                    database__connection__password=$password
                                    database__connection__database=$projectName
                                ")
                            )
                        ),
                        array(
                            'type' => 'mysql',
                            'data' => array(
                                'projectName' => $projectName,
                                'serviceName' => join('mysql', $serviceName),
                                'password' => $password
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
    }

    public function destroyService() {
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
    }

    public function monitorServiceStatus() {
        // TODO: Implement monitorServiceStatus method
    }
}
