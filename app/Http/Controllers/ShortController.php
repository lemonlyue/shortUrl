<?php

namespace App\Http\Controllers;

use App\Model\ShortUrl;
use Illuminate\Http\Request;

class ShortController extends Controller
{
    /**
     * @param $url
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function getShortUrl ($url)
    {
        $result = ShortUrl::where('short_url', $url)->first();
        if (!empty($result)) {
            return redirect(data_get($result, 'url'), 302);
        }
        return response([
            'code' => '0',
            'message' => '短链接无效',
            'data' => []
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function setShortUrl (Request $request)
    {
        $request->validate([
            'url' => 'required|url'
        ], [
            'url.required' => '参数为空',
            'url.url' => '非法链接'
        ]);
        $url = $request->get('url');
        $model = new ShortUrl();
        $model->url = $url;
        $save = $model->save();
        $short_url = $this->dec_to(data_get($model, 'id'));
        $model->short_url = $short_url;
        $res = $model->save();
        if ($save && $res) {
            return response([
                'code' => '200',
                'message' => '',
                'data' => [
                    'short_url' => 'http://127.0.0.1/api/short/'.$short_url
                ]
            ]);
        }
        return response([
            'code' => '0',
            'message' => '创建短链接失败，请重试',
            'data' => []
        ]);
    }

    /**
     * 十进制数转换成其它进制
     * 可以转换成2-62任何进制
     *
     * @param integer $num
     * @param integer $to
     * @return string
     */
    private function dec_to ($num, $to = 62) {
        if ($to == 10 || $to > 62 || $to < 2) {
            return $num;
        }
        $dict = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $ret = '';
        do {
            $ret = $dict[bcmod($num, $to)] . $ret;
            $num = bcdiv($num, $to);
        } while ($num > 0);
        return $ret;
    }
}
