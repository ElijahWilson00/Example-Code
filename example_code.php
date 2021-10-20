<?php
    class SERVICE_CLASS
    {
        private $host = 'localhost';
        private $login = 'example';
        private $password = 'VerySecretPassword';
        private $base = 'example';

        public function connectBD($query, $type)
        {
            $mysqli = new mysqli($this->host, $this->login, $this->password, $this->base);
            $mysqli->set_charset('utf8');
            $data = $mysqli->query($query);

            switch ($type)
            {
                case 'get':
                    $data = $data->fetch_object();
                    $mysqli->close();
                    return $data;
                    break;
                case 'write':
                    $mysqli->close();
                    return $data;
                    break;
            }
        }

        public function message($type, $data)
        {
            return json_encode(array('type' => $type, 'title' => $this->errorTitle[$this->lang][$type], 'data' => $data));
        }
    }

    $SERVICE_CLASS = new SERVICE_CLASS;

    if ($_POST['create'])
        {
            if ($_POST['create']['first_name'] and $_POST['create']['last_name'] and $_POST['create']['email'] and $_POST['create']['password'])
            {
                if (ctype_alpha($_POST['create']['first_name']) and ctype_alpha($_POST['create']['last_name']))
                {
                    if (filter_var($_POST['create']['email'], FILTER_VALIDATE_EMAIL))
                    {
                        $first_name = $_POST['create']['first_name'];
                        $last_name = $_POST['create']['last_name'];
                        $email = $_POST['create']['email'];
                        $password = password_hash($_POST['create']['password'], PASSWORD_DEFAULT, ['cost' => 12]);

                        $emailCheck = $SERVICE_CLASS->connectBD("SELECT id FROM users WHERE email_adress = '$email'", 'get');

                        if (!$emailCheck->id)
                        {
                            $SERVICE_CLASS->connectBD("INSERT INTO user (first_name, last_name, email, password) VALUES ('$first_name', '$last_name', '$email', '$password')", 'write');
                            header('Location: https://example.com');
                            exit;
                        } else
                        {
                            echo '<script>showAlert('.$SERVICE_CLASS->message('error', 'Sorry, email registered').');reloadCaptcha();</script>';
                            exit;
                        }
                    } else
                    {
                        echo '<script>showAlert('.$SERVICE_CLASS->message('error', 'Incorrect Email').');reloadCaptcha();</script>';
                        exit;
                    }
                } else
                {
                    echo '<script>showAlert('.$SERVICE_CLASS->message('error', 'Incorrect First Name or Last Name').');reloadCaptcha();</script>';
                    exit;
                }
            } else
            {
                echo '<script>showAlert('.$SERVICE_CLASS->message('error', 'Fill all fields').');reloadCaptcha();</script>';
                exit;
            }
            exit;
        }
