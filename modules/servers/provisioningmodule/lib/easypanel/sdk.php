<?php
use Ramsey\Uuid\Uuid;

class EasyPanelSDK {

    public function listProjects() {
        // TODO: Implement listProjects method
    }

    public function listServices() {
        // TODO: Implement listServices method
    }

    public function createProject($apiUrl, $authorizationToken, $clientId) {
        // Validate input parameters
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
       
        
        // Request headers
        $headers = array(
            'Content-Type: application/json',
            "Authorization: $authorizationToken",
        );
        
        // Request data
        $jsonData = json_encode(array(
            'json' => array(
                'name' => join(Uuid::uuid4().toString(), array(, $templateName))),
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
        
    }

    public function destroyService() {
        // TODO: Implement destroyService method
    }

    public function disableService() {
        // TODO: Implement disableService method
    }

    public function startService() {
        // TODO: Implement startService method
    }

    public function monitorServiceStatus() {
        // TODO: Implement monitorServiceStatus method
    }
}
