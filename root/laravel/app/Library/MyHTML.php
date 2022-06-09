<?php

namespace App\Library;;

class MyHTML
{
    // ログアウトの HTML セット
    public static function logout($text = "ログアウト", $class = "", $id = "logout-form"){
        $body = '';
        $body .= \Form::open(['url' => route('logout'), 'id' => $id, 'style' => 'display: none;']);
        $body .= \Form::close();
        $body .= '<a href="#" class="' . $class . '" onclick="event.preventDefault(); document.getElementById(\'logout-form\').submit();">';
        $body .= $text;
        $body .= '</a>';

        return $body;
    }

    /**
     * エラーメッセージ
     *
     * @param array $errors
     * @return String HTML
     */
    public static function errorMessage($errors=[]){
        $body = "";
        if( count($errors) > 0){
            $body .= '
                <div class="alert alert-danger">
                    <strong>【入力エラー】</strong> 入力に誤りがあります。ご確認の上もう一度送信してください。<br><br>
                    <ul>';
            foreach ($errors->all() as $error){
                $body .= '<li>'.$error.'</li>';
            }
            $body .= '
                    </ul>
                </div>';
        }
        return $body;
    }

    /**
     * フラッシュメッセージ
     *
     * @return String HTML
     */
    public static function flashMessage(){
        $body = "";
        if (\Session::has('flash_message')){
            $body .= '<div class="alert alert-success">'.\Session::get('flash_message').'</div>';
        }
        return $body;
    }


}
