!['logo'](logo.png)

# 精弘毅行

精弘毅行，全称为浙江工业大学精弘毅行，始于2012年，是浙江工业大学精弘网络参照“毅行者”发起，并和杭州北风户外俱乐部共同组织的一项师生远足活动，由北风户外俱乐部护航队全程引路护航、应急救助，及其他志愿者组成工作队伍，是一项考验耐力与毅力的户外运动。精弘毅行线路选择上结合西湖景区、学校和周边山区的地理位置，包含了北高峰、屏峰山、小和山以及午潮山国家森林公园等，让参与者在登山过程中同时一览杭城景色。


## 部署

使用了Docker技术，让部署变得非常简单

默认情况下设计，如下两个端口

   - nginx :80
   - mysql :3306
   
使用如下命令启动：

    docker-compose up
    
使用如下命令初始化：
   
    docker-compose run --rm composer update
    docker-compose run --rm npm run dev
    docker-compose run --rm artisan migrate

## 开发

```/src```   是项目的后端，使用laravel技术


## Security Vulnerabilities

If you discover a security vulnerability within the project, please send an e-mail  via [idevlab@outlook.com](mailto:idevlab@outlook.com). All security vulnerabilities will be promptly addressed.

## License

The JH Walk serve is open-source software licensed under the [MIT license](https://opensource.org/licenses/MIT).
