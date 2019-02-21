[33mcommit cc824b19c73569de2d61fb79707edc1f21dd7567[m[33m ([m[1;36mHEAD -> [m[1;32mmaster[m[33m)[m
Author: lost <Mr_liuxl@163.com>
Date:   Thu Feb 21 06:04:01 2019 +0000

    小区接口调试完成

[33mcommit b847f33f7be2f48866c90710c45156f08dba67ad[m[33m ([m[1;31morigin/master[m[33m, [m[1;31morigin/HEAD[m[33m)[m
Merge: d56f8b6 da77543
Author: lost <Mr_liuxl@163.com>
Date:   Wed Feb 20 12:03:53 2019 +0000

    街道管理修改

[33mcommit d56f8b669e0a0ea78593ca1cbd3968746613b913[m
Author: lost <Mr_liuxl@163.com>
Date:   Wed Feb 20 11:18:17 2019 +0000

    修改街道接口

[33mcommit da77543144728ca5a32a14f119fbaed3ca2ead6b[m
Author: likun <joollee@qq.com>
Date:   Wed Feb 20 19:15:52 2019 +0800

    添加了商品，活动

[33mcommit 6d033a1f685e77ea57dc1fe59b2bcff981c0f24b[m
Author: lost <Mr_liuxl@163.com>
Date:   Wed Feb 20 09:39:42 2019 +0000

    添加街道管理、小区管理接口

[33mcommit 383ceb84340ae8cf213e536ff51ac14269885f83[m
Author: lost <Mr_liuxl@163.com>
Date:   Wed Feb 20 09:09:17 2019 +0000

    URL链接 保存至常量

[33mcommit 3b08a2351d4a6df9875b393ddc61b0ab9a8bd8ef[m
Author: lost <Mr_liuxl@163.com>
Date:   Wed Feb 20 08:59:29 2019 +0000

    添加小程序模板发送接口

[33mcommit cf108efe3212f84930a68563cb57e1b487b8bc30[m
Author: lost <Mr_liuxl@163.com>
Date:   Wed Feb 20 08:29:17 2019 +0000

    修改小程序端小区管理代码结构

[33mcommit 31d1965b91e05d7ee323c87b837aa89a9ba76133[m
Author: lost <Mr_liuxl@163.com>
Date:   Wed Feb 20 06:23:56 2019 +0000

    2019/2/20 14:25

[33mcommit 0b139da628fd9eed81079d9c088a9d4086af4586[m
Merge: b4bb874 d927d05
Author: lost <Mr_liuxl@163.com>
Date:   Wed Feb 20 06:16:36 2019 +0000

    Merge branch 'master' of http://10.1.1.18/cgp/api

[33mcommit b4bb874fdca5f4c87ee42c117cc69a8ab4f29ba0[m
Author: lost <Mr_liuxl@163.com>
Date:   Wed Feb 20 02:17:24 2019 +0000

    街道表添加注释

[33mcommit d927d0577e2e9e3a4203395325bd4db2d12792f5[m
Merge: 4bad7b2 e26110f
Author: likun <joollee@qq.com>
Date:   Wed Feb 20 10:04:50 2019 +0800

    Merge branch 'master' of http://gitlab/CGP/api

[33mcommit 4bad7b22647d55970b504bd146f8a2ddfbdb2d53[m
Author: likun <joollee@qq.com>
Date:   Wed Feb 20 10:01:26 2019 +0800

    同步数据表设置

[33mcommit 288f6b40d7b188c341fc1d34c7aa269a63bfac1a[m
Author: lost <Mr_liuxl@163.com>
Date:   Wed Feb 20 01:56:37 2019 +0000

    合并database/migrations customer roads

[33mcommit b699b50790ad436d9765e96928b420032b4e2f0e[m
Author: vagrant <vagrant@homestead>
Date:   Wed Feb 20 01:53:30 2019 +0000

    上传添加 网络异常处理

[33mcommit dc8f1f225398911456b1b4783335fb6f771c23cd[m
Author: vagrant <vagrant@homestead>
Date:   Wed Feb 20 00:23:05 2019 +0000

    modify route/api

[33mcommit e26110f48810f3057324f754482311e2f0b5d83c[m
Merge: 1c51d0a e04c60a
Author: vagrant <vagrant@homestead>
Date:   Tue Feb 19 09:16:17 2019 +0000

    merge

[33mcommit 1c51d0a74b01224a3ef11157df360b9cdfd105a1[m
Author: vagrant <vagrant@homestead>
Date:   Tue Feb 19 09:13:02 2019 +0000

    changed

[33mcommit fcf7ee201db99eeecdb48b1dc738704f78dcebff[m
Author: vagrant <vagrant@homestead>
Date:   Tue Feb 19 08:55:17 2019 +0000

            modified:   app/Exceptions/Handler.php
            new file:   app/Helper/functions.php
            modified:   app/Http/Controllers/Auth/CustomerController.php
            modified:   app/Http/Controllers/Auth/DistributorController.php
            modified:   app/Http/Controllers/Auth/TraderController.php
            new file:   app/Http/Controllers/Common/QiNiuUploadController.php
            new file:   app/Http/Controllers/Common/SmsApiController.php
            new file:   app/Http/Controllers/Customer/CommunityController.php
            new file:   app/Http/Controllers/Customer/RoadController.php
            new file:   app/Http/Controllers/Distributor/LeaderController.php
            new file:   app/Http/Controllers/MiniProgram/errorCode.php
            new file:   app/Http/Controllers/MiniProgram/wxBizDataCrypt.php
            modified:   app/Http/Middleware/AutoGuards.php
            modified:   app/Models/Auth/Customer.php
            new file:   app/Models/Community.php
            new file:   app/Models/Leader.php
            new file:   app/Models/Road.php
            modified:   app/Providers/AppServiceProvider.php
            new file:   app/Utils/NetHelper.php
            modified:   composer.json
            modified:   config/app.php
            modified:   config/filesystems.php
            modified:   config/jwt.php
            new file:   config/wx.php
            new file:   public/images/thumb.png
            modified:   resources/lang/en/validation.php
            modified:   routes/v1/auth.php

[33mcommit e04c60a4c50c5dd0445a195a01caa2af9dc0e76e[m
Author: likun <joollee@qq.com>
Date:   Tue Feb 19 16:28:39 2019 +0800

    添加了商户接口

[33mcommit e224d6d28d79347245fc3778dbd32c3e44bd86c2[m
Author: likun <joollee@qq.com>
Date:   Wed Feb 13 09:10:26 2019 +0800

    控制器基类，增加统一的权限验证

[33mcommit db69b7ccf14bd3ed96f9fbefaa25910e9af3dd09[m
Author: likun <joollee@qq.com>
Date:   Tue Feb 12 20:00:02 2019 +0800

    添加了自动刷新的登陆验证

[33mcommit 71b1d0cf768dfcfa62d0be8dacf5185efdc2462d[m
Author: likun <joollee@qq.com>
Date:   Tue Feb 12 19:34:33 2019 +0800

    添加了多用户表认证

[33mcommit b5a242f98085d0abc7ccd65a1f26c637172ca568[m
Author: likun <joollee@qq.com>
Date:   Tue Feb 12 11:06:41 2019 +0800

    init project
