# LeaPHP Framework #

　　当前最新版本 `v1.0.0(alpha)`

## Description ##

　　在最近的项目开发中，发现使用框架虽然会牺牲一些性能上的优势，但是其快速开发及规范的统一，完全可以掩盖住性能上的牺牲。

　　于是，LeaPHP诞生了。

　　LeaPHP本身是一个极度精简的轻量级框架，其本身只实现了基本的MVC中的V和C。由于平时所开发项目的多样性及业务复杂性，并未对M层时行封装。

　　框架本身的核心在于其自动加载的特性。开发者可以基于此进行更轻松的二次定制开发。将个性化的功能模块作为模块，以插件的形式将其加载到框架中，实现了多项目间的代码复用。


## Requirement ##

- LeaPHP只支持PHP5.4.0及以上版本，并支持最新的PHP5.5；
- LeaPHP的运行需要APC模块，请自行前往PECL进行下载安装；
- 使用到数据库连接时（MySQL、SQLite、PostgreSQL、Oracle），需要PDO支持。

## TODO ##

- 框架内置插件包的完善。

## See Also ##

- [LeaPHP Framework 项目主页](http://hliang0813.github.io/leaphp/)
- [LeaPHP Framework 开发手册](https://github.com/hliang0813/leaphp/tree/master/wiki)

