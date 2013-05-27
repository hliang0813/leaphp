#LeaPHP Framework

一个简单的PHP快速开发框架。

#Descripttion

由于工作及项目需要，开发本框架。


#Requirement
* PHP 5.4+
* PDO driver for your respective database

#Manual
## 第一个程序 Hello world!

#### 入口文件 index.php
```php
<?php
define('APP_NAME', 'myappname');          // 定义应用名称
define('URLS', __DIR__ . '/my.urls.php'); // 定义URL配置文件位置
include 'leaphp/LeaPHP.php';              // 引入框架主文件
App::run(__DIR__);                        // 开始执行框架程序
?>
```

#### 路径配置文件 my.urls.php
```php
<?php
return array(
  '/^\/?$/' => 'Act.mytest'   // URL中pathinfo与框架中对象方法的对应
);
?>
```

#### 控制器文件 Act.ctrl.php
```php
<?php
class Act extends Action {    // 从Action类继承
  public function mytest() {  // 你的方法
    echo 'Hello world !';     // 主程序内容
  }
}
?>
```

#TODO

#Copyright and License

#See Also
* [LeaPHP Framework Homepage](http://leaphp.net)
