<?php


class Router
{
   private $routes;

   public function __construct()
   {                                                 
      $routesPath = ROOT . '/config/routes.php';  //* Указываем путь к роутам от базовой дирректории 
      $this->routes = include($routesPath); //* Присваиваем свойству routes массив,который хранится в файле routes.php 
                                                                                                                                          
   } 

   /**
    * Returns request string 
    */
   private function getURI()
   {
      if(!empty($_SERVER['REQUEST_URI'])){  //* Если строка запроса существует  
         return trim($_SERVER['REQUEST_URI'], '/'); //* Получаем строку запроса удалив пробелы (или другие символы) из начала и конца строки.
     } 
   }

   public function run()
   {      
      $uri = $this->getURI(); //* Присваиваем $uri значение строки запроса   
     foreach($this->routes as $uriPattern => $path){ //* Проверяем наличие такого запроса в routes.php
        
         if(preg_match("~$uriPattern~", $uri)){ //* Сравниваем $uriPattern и $uri

           //* Определяем какой контроллер и экшен будут обрабатывать запрос

            $segments = explode('/', $path); //* Разбиваем строку с помощью разделителя на две части. В первой части контоллер, во второй экшен.
            
            $controllersName = array_shift($segments).'Controller'; //* Извлекаем первый элемент массива с помощью array_shift, и приклеиваем Controller. Получаем productController.
            $controllersName = ucfirst($controllersName); //* Первую букву делаем заглавной.

            $actionName = 'action' . ucfirst(array_shift($segments));//* Делаем заглавной первую букву в оставшемся элементе (экшене) после удаления array_shift первого элемента (им был контоллер). Перед этим приклеиваем спереди слово action. Получаем action(Имяэкшена) 
            
            //* Подключаем класс контроллера
            
            $controllerFile = ROOT . '/controllers/' . $controllersName . '.php'; //* К переменной ROOT (её значение С:\****\****\\****\\****\site), приклеиваем  путь к папке controllers, имя контроллера  и " .php ". В итоге получаем полный путь.
               if(file_exists($controllerFile)){ //* Если такой файл существует,
                   include_once($controllerFile); //* то подключаем его.
               }

               //* Создаём объект класса Контроллер

               $controllersObject = new $controllersName;
               $result = $controllersObject->$actionName(); //* Вызываем у созданного объекта его экшен
               if($result != null){
                  break; //* Если результат не ноль,(найдены Контроллер и экшен, они существуют), то заканчиваем поиск.
               } 
           // echo '<br>Контроллер:&nbsp;&nbsp;' . $$controllerFile;
           // echo '<br>Экшен;&nbsp;&nbsp;' . $actionName;
         }

     }       
   }
}