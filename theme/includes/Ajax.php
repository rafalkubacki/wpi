<?php

class Ajax
{
    public static function init()
    {
        $ajaxEvents = [
            'actionName' => [false, 'actionName'],
        ];

        foreach ($ajaxEvents as $key => list($priv, $name)) {
            $name = $name ? $name : $key;
            self::addAction($key, [__CLASS__, $name]);
        }
    }

    static function addAction($name, $function, $private = false)
    {
        add_action('wp_ajax_' . $name, $function);
        !$private && add_action('wp_ajax_nopriv_' . $name, $function);
    }

    public static function actionName()
    {
        echo json_encode(['result' => 'result']);
        exit;
    }
}




