<?php

class FileBrowserClient {
    private $baseUri;
    private $username;
    private $password;

    public function __construct($baseUri, $username, $password) {
        $this->baseUri = $baseUri;
        $this->username = $username;
        $this->password = $password;
        $this->token = $this->getToken();
    }

    public function getToken() {
        $url = $this->baseUri . '/api/login';
        $data = json_encode([
            'username' => $this->username,
            'password' => $this->password
        ]);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        $response = curl_exec($ch);
        if ($response === false) {
            curl_close($ch);
            return false;
        }

        curl_close($ch);
        $responseData = json_decode($response, true);

        return $responseData;
    }

    public function mkdir($clientId) {
        $url = $this->baseUri . '/api/resources/' . $clientId . '/?override=false';

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

        $response = curl_exec($ch);
        if ($response === false) {
            curl_close($ch);
            return false;
        }

        curl_close($ch);

        return true;
    }

    public function adduser($clientId) {
        $generator = new ComputerPasswordGenerator();

        $generator
            ->setOptionValue(ComputerPasswordGenerator::OPTION_UPPER_CASE, true)
            ->setOptionValue(ComputerPasswordGenerator::OPTION_LOWER_CASE, true)
            ->setOptionValue(ComputerPasswordGenerator::OPTION_NUMBERS, true)
            ->setOptionValue(ComputerPasswordGenerator::OPTION_SYMBOLS, false)
        ;

        $passwd = $generator->generatePassword();

        $template = '
{
    "what": "user",
    "which": [],
    "data": {
        "scope": "/'.$$clientId.'",
        "locale": "en",
        "viewMode": "mosaic",
        "singleClick": false,
        "sorting": {
            "by": "",
            "asc": false
        },
        "perm": {
            "admin": false,
            "execute": true,
            "create": true,
            "rename": true,
            "modify": true,
            "delete": true,
            "share": false,
            "download": true
        },
        "commands": [],
        "hideDotfiles": false,
        "dateFormat": false,
        "username": "' . $clientId . '",
        "password": "' . $passwd . '",
        "rules": [],
        "lockPassword": false,
        "id": 0
    }
}
        ';

        $url = $this->baseUri . '/api/users';
        $data = json_encode([
            'username' => $this->username,
            'password' => $this->password
        ]);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'X-Auth: '. this->$token . ''
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        $response = curl_exec($ch);
        if ($response === false) {
            curl_close($ch);
            return false;
        }

        curl_close($ch);

        return $passwd;
    }
}