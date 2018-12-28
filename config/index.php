<?php

/**
 * 首页配置
 */
return [
    //顶部banner
    'banner' => [
        [
            'img' => 'http://www.powerfulfin.com/img/banner/banner1.png',
            'url' => 'http://www.baidu.com',
        ],
        [
            'img' => 'http://www.powerfulfin.com/img/banner/banner2.png',
            'url' => 'http://www.baidu.com',
        ],
    ],
    //客服信息
    'customer_service' => [
        'phone' => '4000029691',
        'email' => '123123@powerfulfin.com'
    ],
    //通知信息，初版先配置
    'notice' => [
        'content' => '这是通知，就一条,超长超长，滚动滚动，通知通知通知通知通知通知通知通知',
        'url' => 'powerfulfin://loandetail?lid=123',
    ],
    //IOS正在审核的版本号
    'ios_audit_version' => '1',
];
