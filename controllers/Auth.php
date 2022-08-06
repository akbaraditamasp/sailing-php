<?php
namespace Controller;

use App\Response;
use App\Util;
use App\Validation;
use \Firebase\JWT\JWT;

class Auth
{
    public static function login($req, $app)
    {
        Validation::validate($_GET, [
            "username" => "required",
            "password" => "required",
        ]);

        $fail = true;
        $user = ($app->db)->get("users", "*", ["username" => Util::get("username")]);

        if ($user) {
            if (password_verify(Util::get("password"), $user["password"])) {
                $fail = false;
                unset($user["password"]);
            }
        }

        if ($fail) {
            Response::JSON([
                "error" => "Unauthorized",
            ], 401);
        }

        $token = Util::unique(($app->db), "user_tokens", "token");

        $jwt = JWT::encode([
            "token" => $token,
        ], Util::env("JWT_KEY", "123456"), 'HS256');

        ($app->db)->insert("user_tokens", [
            "user_id" => $user["id"],
            "token" => $token,
        ]);

        Response::JSON($user + ["token" => $jwt]);
    }

    public static function logout($req, $app)
    {
        ($app->db)->delete("user_tokens", [
            "token" => $req["user"]["token"],
        ]);

        Response::JSON([
            "success" => "Your has been logout",
        ]);
    }
}
