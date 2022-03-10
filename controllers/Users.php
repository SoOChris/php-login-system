<?php

require_once '../models/User.php';
require_once '../helpers/session_helper.php';

class Users
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }
    public function Register()
    {
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        $data = [
            'usersName' => trim($_POST['usersName']),
            'usersEmail' => trim($_POST['usersEmail']),
            'usersUid' => trim($_POST['usersUid']),
            'usersPwd' => trim($_POST['usersPwd']),
            'pwdRepeat' => trim($_POST['pwdRepeat']),
        ];

        //Validating inputs
        if (empty($data['usersName']) || empty($data['usersEmail']) || empty($data['usersUid']) || empty($data['usersPwd']) || empty($data['pwdRepeat'])) {
            flash("register", "please fill out all inputs");
            redirect("../signup.php");
        }

        if (!preg_match("/^[a-zA-Z0-9]*$/", $data['usersUid'])) {
            flash("register", "Invalid username");
            redirect("../signup.php");
        }

        if (!filter_var($data['usersEmail'], FILTER_VALIDATE_EMAIL)) {
            flash("register", "Invalid email");
            redirect("../signup.php");
        }

        if (strlen($data['usersPwd']) < 6) {
            flash("register", "Invalid Pwd");
            redirect("../signup.php");
        } else if ($data['usersPwd'] !== $data['pwdRepeat']) {
            flash("register", "Passwords don't match");
            redirect("../signup.php");
        }

        if ($this->userModel->findUserByEmailOrUsername($data['usersEmail'], $data['usersName'])) {
            flash("register", "Email or username already taken");
            redirect("../signup.php");
        }

        $data['usersPwd'] = password_hash($data['usersPwd'], PASSWORD_DEFAULT);

        if ($this->userModel->register($data)) {
            redirect("../login.php");
        } else {
            die("Something went wrong");
        }
    }

    public function login()
    {
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        $data = [
            'name/email' => trim($_POST['name/email']),
            'usersPwd' => trim($_POST['usersPwd']),
        ];

        if (empty($data['name/email']) || empty($data['usersPwd'])) {
            flash("login", "Please fill out all inputs");
            header("location:../login.php");
            exit();
        }

        if ($this->userModel->findUserByEmailOrUsername($data['name/email'], $data['name/email'])) {
            $loggedInUser = $this->userModel->login($data['name/email'], $data['usersPwd']);
            if ($loggedInUser) {
                $this->createUserSession($loggedInUser);
            } else {
                flash("Login", "Incorrect password");
                redirect("../login.php");
            }
        } else {
            flash("login", "no user found");
            redirect("../login.php");
        }
    }

    public function createUserSession($user)
    {
        $_SESSION['usersId'] = $user->usersId;
        $_SESSION['usersName'] = $user->usersName;
        $_SESSION['usersEmail'] = $user->usersEmail;
        redirect("../index.php");
    }

    public function logout()
    {
        unset($_SESSION['usersId']);
        unset($_SESSION['usersName']);
        unset($_SESSION['usersEmail']);
        session_destroy();
        redirect("../index.php");
    }
}

$init = new Users();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    switch ($_POST['type']) {
        case 'register':
            $init->register();
            break;
        case 'login':
            $init->login();
    }
} else {
    switch ($_GET['q']) {
        case 'logout':
            $init->logout();
            break;
        default:
            redirect("../index.php");
    }
}
