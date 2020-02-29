<?php

namespace App\Handlers;

use GuzzleHttp\Client;
use Overtrue\Pinyin\Pinyin;

class SlugTranslateHandler
{

    public function translate(string $text): string
    {
        $http = new Client;

        $api    = config('services.baidu_translate.api_endpoint');
        $app_id = config('services.baidu_translate.app_id');
        $key    = config('services.baidu_translate.api_key');
        $salt   = time();

        if ($app_id === null || $app_id === '' || $key === null || $key === '') {
            return $this->pinyin($text);
        }

        // 根据文档，生成 sign
        // http://api.fanyi.baidu.com/api/trans/product/apidoc
        // appid+q+salt+密钥 的MD5值
        $sign = md5($app_id . $text . $salt . $key);

        $query = http_build_query([
            'q'     => $text,
            'from'  => 'zh',
            'to'    => 'en',
            'appid' => $app_id,
            'salt'  => $salt,
            'sign'  => $sign,
        ]);

        $response = $http->get($api . $query);

        $result = json_decode($response->getBody(), true);

        return $result['trans_result'][0]['dst'] ?? $this->pinyin($text);
    }

    public function pinyin(string $text): string
    {
        return \Str::slug(app(Pinyin::class)->permalink($text));
    }
}
