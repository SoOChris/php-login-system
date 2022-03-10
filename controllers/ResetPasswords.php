<?php

use PHPMailer\PHPMailer\PHPMailer;

require_once '../models/ResetPassword.php';
require_once '../helpers/session_helper.php';
require_once '../models/User.php';

require_once '../phpmailer/src/PHPMailer.php';
require_once '../phpmailer/src/Exception.php';
require_once '../phpmailer/src/SMTP.php';

class ResetPasswords
{
    private $resetModel;
    private $userModel;
    private $mail;

    public function __construct()
    {
        $this->resetModel = new ResetPassword();
        $this->userModel = new User();

        $this->mail = new PHPMailer();
        $this->mail->isSMTP();
        $this->mail->Host = 'smtp.mailtrap.io';
        $this->mail->SMTPAuth = true;
        $this->mail->Port = 2525;
        $this->mail->Username = '4be95f32f73804';
        $this->mail->Password = '05a385802631d3';
    }

    public function sendEmail()
    {
        $_POST =  filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        $usersEmail = trim($_POST['usersEmail']);

        if (empty($usersEmail)) {
            flash("reset", "please input email");
            redirect("../reset-password.php");
        }

        if (!filter_var($usersEmail, FILTER_VALIDATE_EMAIL)) {
            flash("reset", "Invalid Email");
            redirect("../reset-password.php");
        }

        $selector = bin2hex(random_bytes(8));
        $token = random_bytes(32);

        $url = 'http://localhost/php-login-system/create-new-password.php?selector=' . $selector . '&validator=' . bin2hex($token);

        $expires = date("U") + 1800;

        if (!$this->resetModel->deleteEmail($usersEmail)) {
            die("There was an error");
        }

        $hashesToken = password_hash($token, PASSWORD_DEFAULT);
        if (!$this->resetModel->insertToken($usersEmail, $selector, $hashesToken, $expires)) {
            die("There was an error");
        }

        $subject = "Reset your password";
        $message = "<p>We receive a password request</p>";
        $message = "<p>Here is your password reset link</p>";
        $message = "<a href='" . $url . "'>" . $url . "</p>";

        $this->mail->setFrom("");
        $this->mail->isHTML(true);
        $this->mail->Subject = $subject;
        $this->mail->Body = $message;
        $this->mail->addAddress($usersEmail);

        $this->mail->send();

        flash("reset", "Check your email", 'form-message form-message-green');
        redirect('../reset-password.php');
    }

    public function resetPassword()
    {
        $_POST =  filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        $data = [
            'selector' => trim($_POST['selector']),
            'valitor' => trim($_POST['validator']),
            'pwd' => trim($_POST['pwd']),
            'pwd_repeat' => trim($_POST['pwd-repeat'])
        ];

        $url = '../create-new-password.php?selector=' . $data['selector'] . '&validator=' . $data['validator'];


        if (empty($_POST['pwd'] || $_POST['pwd-repeat'])) {
            flash("newReset", "Please fill out all fields");
            redirect($url);
        } else if ($data['pwd'] != $data['pwd-repeat']) {
            flash("newReset", "Passwords do not match");
            redirect($url);
        } else if (strlen($data['pwd']) < 6) {
            flash("newReset", "Invalid password");
            redirect($url);
        }

        $currentDate = date("U");
    }
}

$init = new ResetPasswords;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    switch ($_POST['type']) {
        case 'send':
            $init->sendEmail();
            break;
    }
} else {
    header("Location: ../index.php");
}
