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
  // URL中pathinfo与框架中对象方法的对应
  // 左边值为匹配pathinfo的正则表达式，可以使用()来匹配其中的参数
  // 右边值为“控制器类名.方法名”
  '/^\/?$/' => 'Act.mytest',
  '/^\/(\w+)$/' => 'Act.mytest'
);
?>
```

#### 控制器文件 myappname/actions/Act.ctrl.php
```php
<?php
class Act extends Action {    // 从Action类继承，类名同文件名
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
