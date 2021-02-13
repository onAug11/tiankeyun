# 天科云接口
## 天科云一键答题
访问接口并传入登陆用户名、登陆密码即可完成当日答题。挂上每日访问一次接口的定时任务即可每日自动完成答题。
### 所需文件
* class/dati.class.php
* dati.php
### 使用方法
将所需文件上传到服务器即可
### 接口
[域名]/dati.php?username=[用户名]&password=[密码]
* 传参
  * [必传]username：登陆用户名
  * [必传]password：登陆密码
* 例如：onAug11.cn/dati.php?username=onAug11&password=123456
***
## 注
本接口仅用于PHP学习，请勿用于其他用途