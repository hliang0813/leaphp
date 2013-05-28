# LeaPHP Framework

一个简单的PHP快速开发框架。

当前版本`v1.0.0(alpha)`

## Description

基于工作及项目需要，开发本框架。

作为初级PHP框架，只简单实现了控制器部分。模版及数据库抽象部分分别使用了Smarty和ActiveRecoard实现。

## Requirement
* PHP 5.4+
* PDO driver for your respective database

## TODO
* 函数自动加载

## Copyright and License

## See Also
* [LeaPHP Framework 项目主页](http://leaphp.net)
* [LeaPHP Framework Wiki](https://github.com/hliang0813/leaphp1/wiki)

# Manual

## 第一个程序 Hello world!

### 文件目录结构
```php
/
  |- index.php          // 入口文件
  |- my.urls.php        // 路径配置文件
  |- myappname          // 应用目录
    |- actions
      |- Act.ctrl.php   // 控制器文件
```

### 入口文件

作为浏览器访问的入口文件，通常我们使用`index.php`作为入口文件。

在入口文件中定义了应用的基本配置项目，包括应用名称、调试开关、路由配置文件等基本应用配置。

```php
<?php
define('APP_NAME', 'myappname');          // 定义应用名称
define('URLS', __DIR__ . '/my.urls.php'); // 定义URL配置文件位置
include __DIR__ . '/leaphp/LeaPHP.php';   // 引入框架主文件
App::run(__DIR__);                        // 开始执行框架程序
?>
```

### 路径配置文件

使用应用入口文件中配置的`URLS`项指定的文件作为路径的配置文件。

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

### 控制器文件

控制器文件保存在控制器目录下，默认的控制器目录是应用入口文件中配置的`APP_NAME`目录中的`actions`目录下。

控制器文件使用`控制器类名.ctrl.php`作为文件名。

控制器类需要从`Action`类继承而来。因为在使用控制器类时，框架会自动加载`Action`类，所以不需要再使用include引入文件。

```php
<?php
class Act extends Action {    // 从Action类继承，类名同文件名
  public function mytest() {  // 你的方法，声明为public方法
    echo 'Hello world !';     // 主程序内容
  }
}
?>
```

### 访问您的应用

默认的情况下，如果您是用Apache作为服务器中间件，可直接通过`PATHINFO`的方式来访问您的应用。

如果您的服务器中间件是Nginx，由于默认情况下Nginx是不支持`PATHINFO`的，所以还需要您在Nginx上配置`PATHINFO`的支持。

```php
http://localhost/index.php/
http://localhost/index.php/test
```
