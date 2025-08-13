<?php

namespace App\Http\Controllers;

abstract class Controller
{
    protected function responseJson($data, int $status = 200, array $headers = [], int $options = JSON_UNESCAPED_UNICODE)
    {
        // もしヘッダーにContent-Typeがなければセットする例
        if (!array_key_exists('Content-Type', array_change_key_case($headers, CASE_LOWER))) {
            $headers['Content-Type'] = 'application/json; charset=utf-8';
        }

        return response()->json($data, $status, $headers, $options);
    }
}
