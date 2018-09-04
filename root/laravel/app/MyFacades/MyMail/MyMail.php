<?php

namespace MyFacade;

use Illuminate\Support\Facades\DB;

class MyMail {

    // 添付ファイル付メール送信
    /**
     * @param str email
     * @param str
     * @param str
     * @param str email
     * @param str NAME
     * @param object
     *          fullpath string: FilePath
     *          mime     string: MIME TYPE 'application/pdf'
     *          ja       string: 日本語ファイル名
     * @return boolean
     */
    public static function sendAttachmentMail($to, $subject, $plain_message, $from_email, $from_name="", $attachment = null){
        if ($attachment === null) {
        //添付が無ければ添付なしで送信
            \MyMail::sendMail($to, $subject, $plain_message, $from_email, $from_name);
        } else {
            if (!file_exists($attachment->fullpath)){
                return false;
            }else{
                //必要に応じて適宜文字コードを設定
                mb_language('Ja');
                mb_internal_encoding('UTF-8');
                // 送信元情報をエンコード
                $from_name_enc = mb_encode_mimeheader($from_name, "ISO-2022-JP");
                $from = ($from_name)? "$from_name_enc<$from_email>": $from_email;

                $boundary = '__BOUNDARY__'.md5(rand());

                $headers = "Content-Type: multipart/mixed;boundary=\"{$boundary}\"\n";
                $headers .= "From: {$from}";

                $body = "--{$boundary}\n";
                $body .= "Content-Type: text/plain; charset=\"ISO-2022-JP\"\n";
                $body .= "\n{$plain_message}\n";

                $filebase = ($attachment->ja)?: basename($attachment->fullpath);
                $body .= "--{$boundary}\n";
                $body .= "Content-Type: {$attachment->mime}; name=\"{$filebase}\"\n";
                $body .= "Content-Disposition: attachment; filename=\"{$filebase}\"\n";
                $body .= "Content-Transfer-Encoding: base64\n";
                $body .= "\n";
                $body .= chunk_split(base64_encode(file_get_contents($attachment->fullpath)))."\n";

                $body .= "--{$boundary}--";

                $ret = mb_send_mail($to, $subject, $body, $headers);

                return $ret;
            }
        }
    }

    // メール送信
    public static function sendMail($to, $subject, $body, $from_email, $from_name){
        // 言語と文字エンコーディングを正しくセット
        mb_language("Japanese");
        mb_internal_encoding("UTF-8");

        // 送信元情報をエンコード
        $from_name_enc = mb_encode_mimeheader($from_name, "ISO-2022-JP");
        $from = "$from_name_enc<$from_email>";

        // メールヘッダを作成
        $header  = "From: $from\n";
        $header .= "Reply-To: $from";

        // 機種依存文字の変換
        $subject = \MyMail::replaceMachineDependence($subject);
        $body = \MyMail::replaceMachineDependence($body);
        $body = str_replace("\r\n", "\n", $body);

        // 日本語メールの送信
        $result = mb_send_mail($to, $subject, $body, $header);

        return $result;
    }

    // 機種依存文字を置換
    public static function replaceMachineDependence($entry_data="") {
      $arr = array(
        "①" => "(1)",  "②" => "(2)",  "③" => "(3)",  "④" => "(4)",  "⑤" => "(5)",  "⑥" => "(6)",  "⑦" => "(7)",  "⑧" => "(8)",  "⑨" => "(9)",  "⑩" => "(10)",
        "⑪" => "(11)", "⑫" => "(12)", "⑬" => "(13)", "⑭" => "(14)", "⑮" => "(15)", "⑯" => "(16)", "⑰" => "(17)", "⑱" => "(18)", "⑲" => "(19)", "⑳" => "(20)",
        "㊤" => "(上)", "㊥" => "(中)", "㊦" => "(下)", "㊧" => "(左)", "㊨" => "(右)",
        "Ⅰ" => "I", "Ⅱ" => "II", "Ⅲ" => "III", "Ⅳ" => "IV", "Ⅴ" => "V", "Ⅵ" => "VI", "Ⅶ" => "VII", "Ⅷ" => "VIII", "Ⅸ" => "IX", "Ⅹ" => "X",
        "㍉" => "ミリ", "㌔" => "キロ", "㌢" => "センチ", "㍍" => "メートル", "㌘" => "グラム", "㌧" => "トン", "㌃" => "アール", "㌶" => "ヘクタール",
        "㍑" => "リットル", "㍗" => "ワット", "㌍" => "カロリー", "㌦" => "ドル", "㌣" => "セント", "㌫" => "パーセント", "㍊" => "ミリバール", "㌻" => "ページ",
        "㎜" => "mm", "㎝" => "cm", "㎞" => "km", "㎎" => "mg", "㎏" => "kg", "㏄" => "cc", "㎡" => "m2", "№" => "No.", "㏍" => "K.K.", "℡" => "TEL.",
        "㈱" => "(株)", "㈲" => "(有)", "㈹" => "(代)", "㍾" => "明治", "㍽" => "大正", "㍼" => "昭和", "㍻" => "平成" );
      if ( 0 < mb_strlen($entry_data) ) {
          $result = str_replace( array_keys($arr), array_values($arr), $entry_data );
      } else {
          $result = $entry_data;
      }

      return $result;
    }

}
