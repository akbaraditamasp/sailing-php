<?php
namespace App;

class Router
{
    private $routes = [];
    private $request = [];
    private $app = [];

    public function setRoute($method, $pattern, ...$handlers)
    {
        array_push($this->routes, [
            "route" => "[$method]$pattern",
            "handlers" => $handlers,
        ]);
    }

    public function addApp($name, $value)
    {
        $this->app[$name] = $value;
    }

    public function run()
    {
        foreach ($this->routes as $route) {
            $handlers = [];
            if (preg_match("/\[([A-Z]*)\](.*)/", $route["route"], $match)) {
                $uri = explode("?", $_SERVER["REQUEST_URI"])[0];
                $method_allowed = explode("|", $match[1]);
                $paths = explode("/", trim($match[2], "/"));
                $requests = explode("/", trim($uri, "/"));
                $method = isset($_POST["_method"]) ? $_POST["_method"] : $_SERVER['REQUEST_METHOD'];

                $correct = true;
                $params_temp = [];
                if (in_array($method, $method_allowed)) {
                    foreach ($requests as $i => $request) {
                        if (isset($paths[$i])) {
                            if ($request === $paths[$i] || preg_match("/\:([A-Za-z]+)/", $paths[$i], $match)) {
                                if ($match && $request !== $paths[$i]) {
                                    $key = $match[1];
                                    $value = $request;

                                    $params_temp[$key] = $value;
                                }
                            } else if ($paths[$i] === "*") {
                                break;
                            } else {
                                $correct = false;
                                break;
                            }
                        } else {
                            $correct = false;
                            break;
                        }
                    }
                } else {
                    $correct = false;
                }

                if ($correct) {
                    $this->request["params"] = $params_temp;
                    $handlers = $route["handlers"];
                    break;
                }
            }
        }

        if (count($handlers)) {
            Router::hit($this->request, (object) $this->app, 0, $handlers);
        } else {
            Response::JSON([
                "error" => "Page not found",
            ], 404);
        }
    }

    public static function hit($request, $app, $position, $handlers)
    {
        $handler = $handlers[$position];
        if (isset($handlers[$position + 1])) {
            $position++;
            return $handler($request, $app, function ($pass_req = null) use ($request, $app, $position, $handlers) {
                $next = "\\App\\Router::hit";
                $next($pass_req ? $pass_req : $request, $app, $position, $handlers);
            });
        } else {
            return $handler($request, $app);
        }
    }

}
