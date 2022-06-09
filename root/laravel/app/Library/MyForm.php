<?php

namespace App\Library;

class MyForm
{
    // ラジオボタンのセット
    public static function radio($name, $values, $checked_value=NULL, $options=[], $delimitter=NULL){
        $body = "";

        $bodies = [];
        if(!empty($values)){
            foreach($values as $key => $val){
                $option = $options;
                $option['id'] = isset($options['id'])? $options['id'].'-'.$key: 'select-'.$name.'-'.$key;
                $str = Form::radio($name, $key, $key === $checked_value, $option);
                $str .= ' <label for="'.$option['id'].'">'.$val.'</label>';
                $bodies[] = $str;
            }

            switch($delimitter){
                case 'flex':
                    $body .= '<ul class="radiobox radiobox--flex">';
                    foreach($bodies as $str){
                        $body .= '<li>'.$str.'</li>';
                    }
                    $body .= '</ul>';
                    break;

                case 'span':
                    $body .= '<div class="radiobox radiobox--span">';
                    foreach($bodies as $str){
                        $body .= '<span>'.$str.'</span>';
                    }
                    $body .= '</div>';
                    break;

                default:
                    $body = implode($delimitter, $bodies);
                    break;
            }
        }

        return $body;
    }


    // 年選択肢
    public static function selectYear($selected){
        $body = '';
        for($i=1900; $i<=date("Y"); $i++){
            $sel = ($i == $selected)? " selected": "";
//            $body .= '<option value="'.$i.'"'.$sel.'>'.$i.\Func::jpnYear($i).'</option>';
            $body .= '<option value="'.$i.'"'.$sel.'>'.$i.'</option>';
        }

        return $body;
    }

    // 月選択肢
    public static function selectMonth($selected){
        $body = '';
        for($i=1; $i<=12; $i++){
            $sel = ($i == $selected)? " selected": "";
            $body .= '<option value="'.$i.'"'.$sel.'>'.$i.'</option>';
        }

        return $body;
    }

    // 日選択肢
    public static function selectDay($selected){
        $body = '';
        for($i=1; $i<=31; $i++){
            $sel = ($i == $selected)? " selected": "";
            $body .= '<option value="'.$i.'"'.$sel.'>'.$i.'</option>';
        }

        return $body;
    }

    public static function a ($options){
        $body = '';
        $body .= Form::open(['url' => $options['url']]);
        if(!empty($options['values'])){
            foreach($options['values'] as $key => $value){
                $body .= Form::hidden($key, $value);
            }
        }
        $body .= '<button type="submit" class="'.$options['button']['class'].'">'.$options['button']['text'].'</button>';
        $body .= Form::close();

        return $body;
    }

    public static function selectWeeks($start, $end){
        $body = "";

        for($i=2; $i<=9; $i++){
            $body .= '<li class="search__week__param list trigSelectWeek" data-week="'.$i.'">'.$i.'</li>';
        }

        return $body;
    }

}
