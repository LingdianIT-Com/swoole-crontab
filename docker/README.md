## swoole-crontab修改说明
### 架构

![架构](https://processon.com/view/link/59698feae4b064b2bffca3cd)

### 运行
1. 修改对应环境的数据库配置
2. 修改docker-compose 里面映射目录 为自己本机目录 ,然后docker-compose up -d 


### 改动说明
1. 去掉nginx+fpm 模式,直接使用 swoole 来做 httpserver 提供admin端web服务
2. 使用环境变量来确定运行环境的选择
3. 添加日志记录,方便调试运行状态
4. 添加docker运行环境
5. 添加swoole-framework为子模块