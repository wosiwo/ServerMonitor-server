ServerMonitor-server
====================

使用php的swoole扩展以及Swoole框架来实现，监控服务器信息，并提供socket访问接口；

## 运行

1. PHP INI文件[这里区别于默认的配置，另外创建了一个配置文件] /etc/php.cli.ini 
    
    ```
   extension=/usr/lib/php/extensions/no-debug-non-zts-20100525/swoole.so
    ```

2. 命令行运行

    ```
    /usr/bin/php -c /etc/php.cli.ini   ./monitor_server.php
    ```
## Requirements

* PHP 5.3+
* PHP Swoole extension
* PHP Swoole框架


