<?php

class Router
{
    private $routes;

    public function __construct()
    {
        $routesPath = ROOT.'/config/routes.php';        //путь к роутам
        $this->routes = include ($routesPath);          //присваеваем свойству $routes массив, содержащийся в файле routes.php
    }

    /**
     * returns request string
     * @return string
     *
     * получить строку запроса
     *
     */
    private function getURI()
    {
        if(!empty($_SERVER['REQUEST_URI'])) {
            return trim($_SERVER['REQUEST_URI'],'/');
        }
    }

    public function run()
    {

        //*получить строку запроса
        $uri = $this->getURI();

        //проверить наличие такого запроса в routes.php
        foreach ($this->routes as $uriPattern => $path) {

            //сравниваем $uri and $uriPattern

            if(preg_match("~$uriPattern~", $uri)){

                //получаем внутренний путь из внешнего согласно правилу

                $internalRoute = preg_replace("~$uriPattern~", $path, $uri);

                //если есть совпадение, определить какой контроллер и action обрабатывает запрос, параметры

                $segments = explode('/', $internalRoute);
                $controllerName = ucfirst(array_shift($segments)).'Controller';
                $actionName = 'action'.ucfirst(array_shift($segments));
                $parameters = $segments;
                //print_r($segments);die;

                //подключить файл класса-контроллера

                $controllerFile = ROOT.'/controllers/'.$controllerName.'.php';

                if(file_exists($controllerFile)) {
                    include_once ($controllerFile);
                }
                $controllerObject = new $controllerName;
                $result = call_user_func_array(array($controllerObject, $actionName),$parameters);

                if($result != null){
                    break;
                }
            }
        }


        //создать объект, вызвать метод, т е action









/*        echo '<pre>';
        print_r($this->routes);
        echo "Class Router, method run";
        echo '</pre>';*/
    }
}