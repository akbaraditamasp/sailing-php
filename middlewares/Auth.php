<?php
namespace Middleware;

use App\Response;
use \App\Util;
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

class Auth
{
    public static function valid($request, $app, $next)
    {
        $fail = true;
        $bearer = null;
        $headers = Util::header("Authorization");

        if ($headers) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                $bearer = $matches[1];
            }
        }

        if ($bearer) {
            $decoded = null;
            try {
                $decoded = JWT::decode($bearer, new Key(Util::env("JWT_KEY", "123456"), 'HS256'));
                $decoded = (array) $decoded;
            } catch (\Exception$e) {}

            if ($decoded) {
                $user = ($app->db)->get("users", [
                    "[>]user_tokens" => [
                        "id" => "user_id",
                    ],
                ], [
                    "users.id",
                    "users.username",
                    "user_tokens.token",
                ], [
                    "user_tokens.token" => $decoded["token"],
                ]);

                if ($user) {
                    $request["user"] = $user;
                    $fail = false;
                }
            }
        }

        if ($fail) {
            Response::JSON([
                "error" => "Unauthorized",
            ], 401);
        }

        $next($request);
    }

    public static function optional($request, $app, $next)
    {
        $bearer = null;
        $headers = Util::header("Authorization");

        if ($headers) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                $bearer = $matches[1];
            }
        }

        if ($bearer) {
            $decoded = null;
            try {
                $decoded = JWT::decode($bearer, new Key(Util::env("JWT_KEY"), 'HS256'));
                $decoded = (array) $decoded;
            } catch (\Exception$e) {}

            if ($decoded) {
                $user = ($app->db)->get("users", [
                    "[>]user_tokens" => [
                        "id" => "user_id",
                    ],
                ], [
                    "users.id",
                    "users.username",
                    "user_tokens.token",
                ], [
                    "user_tokens.token" => $decoded["token"],
                ]);

                if ($user) {
                    $request["user"] = $user;
                }
            }
        }

        $next($request);
    }
}
