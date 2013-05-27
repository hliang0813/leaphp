#LeaPHP Framework

一个简单的PHP快速开发框架。

#Descripttion

由于工作及项目需要，开发本框架。


#Requirement
* PHP 5.4+
* PDO driver for your respective database

#Manual
## hello world!

#### 入口文件 index.php
```php
<?php
define('APP_NAME', 'myappname');
define('URLS', __DIR__ . '/my.urls.php');
include 'leaphp/LeaPHP.php';
App::run(__DIR__);
?>
```

#### 路径配置文件 my.urls.php
```php
<?php
return array(
  '/^\/?$/' => 'Act.mytest'
);
?>
```

#### 控制器文件 Act.ctrl.php
```php
<?php
class Act extends Action {
  public function mytest() {
    echo 'hello world !';
  }
}
?>
```

#TODO

#Copyright and License

#See Also
* [LeaPHP Framework Homepage](http://leaphp.net)
