<?php

namespace Eurom\Routing;

class Router
{
    function scnDir($path)
    {
        $out = [];
        $scan = array_diff(scandir($path), array('.', '..', "BaseController.php"));
        foreach ($scan as $value) {
            $val = $path . "/" . $value;
            if (is_dir($val))
                $out = array_merge($out, $this->scnDir($val));
            else {
                $namespc = explode("/routes/", $val)[1];
                $namespc = str_replace(".php", "", $namespc);
                $namespc = str_replace("/", "\\", $namespc);
                $namespc = str_replace("\default", "", $namespc);
                $namespc = str_replace("{", "", $namespc);
                $namespc = str_replace("}", "", $namespc);
                $namespace = "";
                foreach (explode("\\", $namespc) as $value) {
                    $namespace .= "\\" . ucfirst($value);
                }
                $namespace = "Routes" . $namespace . "\Controller";

                $route = explode("/routes", $val)[1];
                $route = str_replace(".php", "", $route);
                $route = str_replace("/default", "", $route);

                $out[$namespace] = $route;
            }
        }
        return $out;
    }
    public function __construct(public string $path)
    {
    }
    public function applyRoutes(\Slim\App $app)
    {
        foreach ($this->scnDir($this->path) as $namespace => $path) {
            if (str_contains($namespace, ".DS_Store")) continue;
            (new $namespace)->register($path, $app, $namespace);
        }
    }
}
