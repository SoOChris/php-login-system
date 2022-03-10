<?php

class Config
{
    const SITE_URL = "http://localhost";
    const SITE_FOLDER = "php-login-system";
    const SITE_NOME = "Login_System - PHP & MVC";
    const SITE_EMAIL_ADM = "example@example.com"; //yourEmail

    //DB INFO

    const BD_HOST = "localhost",
        BD_USER = "root",
        BD_PWD = "",
        BD_DB = "login-system-php",
        BD_PREFIX = "";

    //PHPMAILER INFO

    const EMAIL_HOST = "smtp.gmail.com";
    const EMAIL_USER = "soochris16@gmail.com";
    const EMAIL_NAME = "Contato Chris";
    const EMAIL_SENHA = "Cris1102@@";
    const EMAIL_PORT = 587;
    const EMAIL_SMTP = true;
    const EMAIL_SMTPSECURE = true;
    const EMAIL_COPY = "soochris16@gmail.com";
}
