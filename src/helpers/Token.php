<?php

use Firebase\JWT\JWT;

class Token
{
    private $issuer = "";
    private $secret;
    private $expiration = 60;
    public function __construct($issuer, $secret)
    {
        $this->issuer = $issuer;
        $this->secret = $secret;
    }

    public function setExpirationTime($expiration)
    {
        $this->expiration = $expiration;
    }

    public function generateToken($data)
    {
        $now = new DateTime();
        $future = new DateTime("+{$this->expiration} minutes");

        //ISSUER
        $iss = $this->issuer;

        //ISSUED AT
        $iat = $now->getTimeStamp();

        //NOT BEFORE
        $nbf = $iat;

        //EXPIRATION TIME
        $exp = $future->getTimeStamp();

        //JWT ID
        $jti = sha1($iat);

        $payload = [
            "iss" => $iss,
            "iat" => $iat,
            "nbf" => $nbf,
            "exp" => $exp,
            "jti" => $jti
        ];

        $payload = array_merge($payload, $data);
        $secret = $this->secret;
        $token = JWT::encode($payload, $secret, "HS256");
        return $token;
    }
}