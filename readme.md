!['logo'](logo.png)

# 精弘毅行

## 安装

使用了Docker技术，让部署变得非常简单

默认情况下设计，如下两个端口

   - nginx :8080
   - mysql :3306
   
使用如下命令启动：

    docker-compose up
    
使用如下命令初始化：
   
    docker-compose run --rm composer update
    docker-compose run --rm npm run dev
    docker-compose run --rm artisan migrate

## 开发

```/src```   是项目的后端，使用laravel技术

