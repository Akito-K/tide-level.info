<?php

namespace App\Library;

class Qreki {
    //////////////////////////////////////////////////////////////////////////////////////
    //                                                                                  //
    //                ＰＨＰによる旧暦計算プログラム                                    //
    //                                                                                  //
    //   ■六曜 ： $rokuyou = get_rokuyou($year,$month,$day,$hour,$min,$sec);           //
    //   ■月齢 ： $age = get_moon($year,$month,$day,$hour,$min,$sec);                  //
    //   ■旧暦 ： list($q_yaer,$uruu,$q_mon,$q_day,$m_val)                             //
    //             = get_qreki($year,$month,$day,$hour,$min,$sec);                      //
    //   ■潮位の計算 ： $tide = tidename($age); $age…月齢（整数）                  //
    //   ■２４節気 ： $sekki = check_24sekki($year,$month,$day);                       //
    //   ■雑節 ： $zats = get_zatsetu($year,$month,$day);                              //
    //   ■太陽と月の黄経 ： list($sun,$moon) = sun_moon($tm);                          //
    //   ■太陽黄経からユリウス ： $jd_sun = lsun_jd($year,$sun);                       //
    //                                                                                  //
    //////////////////////////////////////////////////////////////////////////////////////

    // グローバル変数
    // 円周率の定義と（角度の）度からラジアンに変換する係数の定義
    private $PI;
    private $pai;
    // 潮名
    private $tide_name_ary;
    function __construct(){
        $this->PI = 3.141592653589793238462;
        $this->pai = $this->PI / 180;
        $this->tide_name_ary = array("大","中","小","長","若","中","大","中","小","長","若","中","大");
    }
    // -----------------------------------------------------------------
    //  六曜算出 $rokuyo = get_rokuyou($year,$mon,$day,$hour,$min,$sec);
    //  引数：新暦年月日
    //  戻値：0:大安 1:赤口 2:先勝 3:友引 4:先負 5:仏滅
    // -----------------------------------------------------------------

    function get_rokuyou($year,$mon,$day,$hour,$min,$sec){
        list($q_yaer,$uruu,$q_mon,$q_day,$m_val) = $this->get_qreki($year,$mon,$day,$hour,$min,$sec);
        return(($q_mon + $q_day) % 6);
    }

    // -----------------------------------------------------------------
    //  月齢計算 $moonval = get_moon($year,$mon,$day,$hour,$min,$sec);
    //  引数：新暦年月日
    //  戻値：月齢
    // -----------------------------------------------------------------

    function get_moon($year,$mon,$day,$hour,$min,$sec){
        list($q_yaer,$uruu,$q_mon,$q_day,$m_val) = $this->get_qreki($year,$mon,$day,$hour,$min,$sec);
        return($m_val);
    }

    // -----------------------------------------------------------------
    //  旧暦計算 $qreki = get_qreki($year,$mon,$day,$hour,$min,$sec);
    //  引数：新暦年月日
    //  戻値：配列
    //        $qreki[0] : 旧暦年
    //        $qreki[1] : 平月:0 閏月:1
    //        $qreki[2] : 旧暦月
    //        $qreki[3] : 旧暦日
    //  閏月…太陰暦による12か月は354日で、太陽暦と比べ年に11日ほど短い
    // -----------------------------------------------------------------

    function get_qreki($year,$mon,$day,$hour,$min,$sec){
        $tm0 = $this->ymdt_jd($year,$mon,$day,$hour,$min,$sec);  //(ユリウス日)
        //計算対象の直前にあたる二分(春分と秋分)二至（夏至と冬至）の時刻
        //$chu[0,0]:二分二至の時刻  $chu[0,1]:その時の太陽黄経
        list($chu[0][0],$chu[0][1]) = $this->call_nibun($tm0);
        //中気(黄径0°を春分点とし、30°ごとに区切った点…
        //…各月の２番目の節気のこと)の時刻を計算（４回計算する）
        //$chu[$i,0]:中気の時刻  $chu[$i,1]:太陽黄経
        //太陽黄経…春分点を0度として東から西へ360度で区切る角度
        for($i=1;$i<4;$i++){
            list($chu[$i][0],$chu[$i][1]) = $this->get_chu($chu[$i-1][0]+32);
        }
        //計算対象の直前にあたる二分二至の直前の朔の時刻
        $saku[0] = $this->get_saku($chu[0][0]);
        //朔の時刻(太陽と月の黄経が等しい時刻…新月)
        for($i=1;$i<5;$i++){
            $tm=$saku[$i-1];
            $tm += 30;
            $saku[$i]=$this->get_saku($tm);
            //前と同じ時刻を計算して両者の差が26日以内なら初期値を+33日にして再実行
            if( abs( $this->int($saku[$i-1])-$this->int($saku[$i]) ) <= 26 ){
                $saku[$i]=$this->get_saku($saku[$i-1]+35);
            }
        }
        //$saku[1]が二分二至の時刻以前になったら朔の時刻を繰り下げ修正
        if($this->int($saku[1]) <= $this->int($chu[0][0]) ){
            for($i=0;$i<5;$i++){
                $saku[$i]=$saku[$i+1];
            }
            $saku[4] = $this->get_saku($saku[3]+35);
        }
        //$saku[0]が二分二至の時刻以後になったら朔の時刻を繰り上げ修正
        elseif( $this->int($saku[0]) > $this->int($chu[0][0]) ){
            for($i=4;$i>0;$i--){
                $saku[$i] = $saku[$i-1];
            }
            $saku[0] = $this->get_saku($saku[0]-27);
        }
        //$lap=0:平月  $lap=1:閏月
        //節月で４か月の間に朔が５回あると、閏月がある可能性がある
        if($this->int($saku[4]) <= $this->int($chu[3][0]) ){
            $lap=1;
        }else{
            $lap=0;
        }
        //朔日行列の作成
        //$m[$i,0] ： 月名（1:正月 2:２月 3:３月 ....）
        //$m[$i,1] ： 閏フラグ（0:平月 1:閏月）
        //$m[$i,2] ： 朔日の$jd
        $m = [
            0 => [NULL, NULL, NULL]
        ];
        $m[0][0]=$this->int($chu[0][1]/30) + 2;
        if( $m[0][1] > 12 ){
            $m[0][0]-=12;
        }
        $m[0][2]=$this->int($saku[0]);
        $m[0][1]=0;

        for($i=1;$i<5;$i++){
            if($lap == 1 && $i !=1 ){
                if( $this->int($chu[$i-1][0]) <= $this->int($saku[$i-1]) || $this->int($chu[$i-1][0]) >= $this->int($saku[$i]) ){
                    $m[$i-1][0] = $m[$i-2][0];
                    $m[$i-1][1] = 1;
                    $m[$i-1][2] = $this->int($saku[$i-1]);
                    $lap=0;
                }
            }
            $m[$i][0] = $m[$i-1][0]+1;
            if( $m[$i][0] > 12 ){
                $m[$i][0]-=12;
            }
            $m[$i][2]=$this->int($saku[$i]);
            $m[$i][1]=0;
        }
        //朔日行列から旧暦
        $state=0;
        for($i=0;$i<5;$i++){
            if($this->int($tm0) < $this->int($m[$i][2])){
                $state=1;
                break;
            }elseif($this->int($tm0) == $this->int($m[$i][2])){
                $state=2;
                break;
            }
        }
        if($state==0||$state==1){
            $i--;
        }

        $qreki[1]=$m[$i][1];
        $qreki[2]=$m[$i][0];
        $qreki[3]=$this->int($tm0)-$this->int($m[$i][2])+1;
        //旧暦年の計算
        //旧暦月が10以上でかつ新暦月より大きい場合はまだ年を越していない
        $a = $this->jd_ymdt($tm0);
        $qreki[0] = $a[0];
        if($qreki[2] > 9 && $qreki[2] > $a[1]){
            $qreki[0]--;
        }
        // 月齢を計算
        $moon_val = $this->moon_value($tm0);
        return array($qreki[0],$qreki[1],$qreki[2],$qreki[3],$moon_val);

    }

    // -----------------------------------------------------------------
    // 潮位の計算 $tide_nm = tidename($age); …$age…月齢（整数）
    // -----------------------------------------------------------------
    function tidename($age){
        $tide_num = $this->tidenm($age);
        return $this->tide_name_ary[$tide_num];
    }
    function tidenm($age){
        $k = 0;
        if($age <= 1.5){ $k = 0;}
        else if($age <= 5.5){ $k = 1;}
        else if($age <= 8.5){ $k = 2;}
        else if($age <= 9.5){ $k = 3;}
        else if($age <= 10.5){ $k = 4;}
        else if($age <= 12.5){ $k = 5;}
        else if($age <= 16.5){ $k = 6;}
        else if($age <= 20.5){ $k = 7;}
        else if($age <= 23.5){ $k = 8;}
        else if($age <= 24.5){ $k = 9;}
        else if($age <= 25.5){ $k = 10;}
        else if($age <= 27.5){ $k = 11;}
        else if($age <= 30.5){ $k = 12;}
        return $k;
    }

    // -----------------------------------------------------------------
    //  今日が24節気かどうか
    //  引数：計算対象となる年月日　$year $mon $day
    //  戻値：24節気の名称(節気と中気が各月に１つずつある)
    //  基点ユリウス日(2451545)：2000/1/2/0/0/0(年/月/日/時/分/秒…世界時)
    // -----------------------------------------------------------------

    function check_24sekki($year,$mon,$day){
        //24節気の定義
        $sekki24 = array("春分","清明","穀雨","立夏","小満","芒種","夏至","小暑","大暑","立秋","処暑","白露","秋分","寒露","霜降","立冬","小雪","大雪","冬至","小寒","大寒","立春","雨水","啓蟄");
        $tm = $this->ymdt_jd($year,$mon,$day,0,0,0); //今日の午前0時(ユリウス日)
        //時刻引数を分解
        $tm1 = $this->int($tm);
        $tm2 = $tm - $tm1;
        $tm2-=9/24; //世界時と日本時の時差補正
        $t=($tm2+0.5) / 36525;  //36525は１年365日と1/4を現す数値
        $t=$t + ($tm1-2451545) / 36525; //2451545は基点までのユリウス日
        //今日の太陽の黄経
        $lsun_today = $this->long_sunq($t);
        //明日のユリウス日
        $tm++;
        $tm1 = $this->int($tm);
        $tm2 = $tm - $tm1;
        $tm2-=9/24;
        $t=($tm2+0.5) / 36525;
        $t=$t + ($tm1-2451545) / 36525;
        //明日の太陽の黄経
        $lsun_tomorow = $this->long_sunq($t);
        $lsun_today0   = 15 * $this->int($lsun_today / 15);
        $lsun_tomorow0 = 15 * $this->int($lsun_tomorow / 15);
        if($lsun_today0 != $lsun_tomorow0){
            return($sekki24[$lsun_tomorow0 / 15]);
        }else{
            return('');
        }
    }

    // -----------------------------------------------------------------
    // 各月の雑節を求める
    // 引数：$zats = get_zatsetu($year,$mon,$day) …年月
    // 戻値：$zatsetu スカラ変数 …雑節(日)のセット
    // -----------------------------------------------------------------

    function get_zatsetu($ut_year,$ut_mon,$ut_day){
        $zatsu = array(0=>'土用',1=>'節分',2=>'彼岸',3=>'八十八夜',4=>'入梅',5=>'半夏生',6=>'二百十日',7=>'社日');
        $zt_mon = $ut_mon - 1;
        // 太陽の黄経を求める--太陽黄道が-75度の時に$tzは0となる
        $tz = $zt_mon * 30;
        // ６月か７月に入梅(太陽黄経80°)、半夏生(黄経100°)がある
        if ($zt_mon == 5 || $zt_mon == 6) {
            $tz = $tz - ($zt_mon % 5) * 15 + ($zt_mon % 4) * 5;
            $jd2 = $this->lsun_jd($ut_year,$tz); // 太陽の黄経から qreki.inc
            list($dmy1,$dmy2,$zday,$dmy3)= $this->jd_ut0($jd2);  // 世界時間に変換 qreki.inc
            $zf = $zt_mon - 1;
        }
        // ２月に節分(立春の前日)がある
        if($zt_mon == 1){
            $zf = 1;
            $tz = 30;
            $jd4 = $this->lsun_jd($ut_year,$tz); // 立春のユリウス日を求める
            $jd4 -= 1;  // その前日が節分（太陽の黄経315°）
            list($dmy1,$xmon,$zday,$dmy3)= $this->jd_ut0($jd4);
        }
        // 節気(中気)を求める
        $tz = $zt_mon * 30 + 15;
        $jd1 = $this->lsun_jd($ut_year,$tz); // qreki.inc
        list($dmy1,$dmy2,$hday1,$dmy3)= $this->jd_ut0($jd1); // qreki.inc
        // １月(黄経279°)４月(27°)７月(117°)１０月(207°)に土用がある
        if($zt_mon % 3 == 0){
            $tz -= 3;
            $jd2 = $this->lsun_jd($ut_year,$tz); // 太陽の黄経から土用のユリウス日を求める qreki.inc
            list($dmy1,$dmy2,$zday0,$dmy3)= $this->jd_ut0($jd2); // 世界時間に変換 qreki.inc
        }
        // ３月と９月に彼岸(春分の3日前～と秋分の3日前～)と社日(春分と秋分に最も近い戊の日)がある
        if ($zt_mon == 2 || $zt_mon == 8) {
            $zf = 2;
            $zday_0 = $hday1 - 3;     # 彼岸は春分、秋分の3日前から
            $zday_1 = $hday1 + 3;     # 3日後まで
            $zday = "{$zday_0}日～{$zday_1}";
            $jd3 = $this->int($jd1 / 10) * 10 + 4; // 社日は春分、秋分に一番近い戊
            list($dmy1,$dmy2,$zday7,$dmy3) = $this->jd_ut0($jd3);  // 社日の日を求める
        }
        // ５月に八十八夜(立春から数えて88日目)がある
        if ($zt_mon == 4){
            $tz = 30;
            $jd4 = $this->lsun_jd($ut_year,$tz); // 立春のユリウス日を求める
            $jd4 += 87; // その88日目
            list($dmy1,$dmy2,$zday,$dmy3)= $this->jd_ut0($jd4);  // ユリウス日を世界時間に変換
            $zf = 3;
        }
        // ８月か９月に二百十日(立春から数えて210日目)がある
        if ($zt_mon == 7 || $zt_mon == 8){
            $tz = 30;
            $jd4 = $this->lsun_jd($ut_year,$tz); // 立春のユリウス日を求める
            $jd4 += 209;  // その210日目
            list($dmy1,$xmon,$xday,$dmy3)= $this->jd_ut0($jd4);  // ユリウス日を世界時間に変換
            if($zt_mon == $xmon) {
                $zday6 = $xday;
            }
        }
        if($zday){$zatsetu .= "$zatsu[$zf]({$zday}日) ";}
        if($zday0){$zatsetu .= "$zatsu[0]({$zday0}日) ";}
        if($zday6){$zatsetu .= "$zatsu[6]({$zday6}日) ";}
        if($zday7){$zatsetu .= "$zatsu[7]({$zday7}日)";}
        if($zatsetu){return ($zatsetu);}else{return ("なし");}
    }

    // -----------------------------------------------------------------
    // 各日の干支を求める（1970/11/6～）
    // 引数：$eto = get_eto($year,$mon,$day,$fg) …年月日 $fg=0なら本日
    // 戻値：$eto スカラ変数 …干支のセット
    // -----------------------------------------------------------------

    function get_eto($year,$mon,$day,$fg){
        $sen = array("庚","辛","壬","癸","甲","乙","丙","丁","戊","己");
        $shi = array("寅","卯","辰","巳","午","未","申","酉","戌","亥","子","丑");
        $core_year = 1970;
        $core_mon= 11;
        $core_day = 6;
        $core_stp = mktime(0,0,0,$core_mon,$core_day,$core_year,0)/86400;
        if($fg){
            $to_stp = mktime(0,0,0,$mon,$day,$year,0)/86400;
        }else{
            $to_stp = time()/86400;
        }
        $cntsen = 0;
        $cntshi = 0;
        for($v=$core_stp; $v<=$to_stp; $v++){
            $sen_name = $sen[$cntsen];
            $shi_name = $shi[$cntshi];
            $cntsen++; $cntshi++;
            if($cntsen==10){$cntsen =0;}
            if($cntshi==12){$cntshi =0;}
        }
        $eto = $sen_name.$shi_name;
        return ($eto);
    }

    // -----------------------------------------------------------------
    // 指定日の干支を求める（1970/11/6～）
    // 引数：$eto = eto_get($year,$mon,$day)
    // 戻値：$eto スカラ変数 …干/支のセット
    // -----------------------------------------------------------------

    function eto_get($year,$mon,$day){
        $sen = array("庚","辛","壬","癸","甲","乙","丙","丁","戊","己");
        $shi = array("寅","卯","辰","巳","午","未","申","酉","戌","亥","子","丑");
        $core_year = 1970;
        $core_mon= 11;
        $core_day = 6;
        $core_stp = mktime(0,0,0,$core_mon,$core_day,$core_year,0)/86400;
        $to_stp = mktime(0,0,0,$mon,$day,$year,0)/86400;
        $cntsen = 0;
        $cntshi = 0;
        for($v=$core_stp; $v<=$to_stp; $v++){
            $sen_name = $sen[$cntsen];
            $shi_name = $shi[$cntshi];
            $cntsen++; $cntshi++;
            if($cntsen==10){$cntsen =0;}
            if($cntshi==12){$cntshi =0;}
        }
        $eto = $sen_name.'/'.$shi_name;
        return ($eto);
    }

    // -----------------------------------------------------------------
    //  中気の時刻(黄径0°を春分点とし30°ごとに区切った点)
    //  呼び出し時にセットする変数
    //  $tm   計算対象となる時刻（ユリウス日）
    //  戻値  中気の時刻、その時の黄経を配列で渡す
    //  基点ユリウス日(2451545)：2000/1/2/0/0/0(年/月/日/時/分/秒…世界時)
    // -----------------------------------------------------------------

    function get_chu($tm){
        //時刻引数を分解
        $tm1 = $this->int( $tm );
        $tm2 = $tm - $tm1;
        //JST => DT （補正時刻=0秒 と仮定して計算）
        $tm2-=9/24;
        //中気の黄経 λsun0
        $t=($tm2+0.5) / 36525;
        $t=$t + ($tm1-2451545) / 36525;
        $lsun = $this->long_sunq( $t );
        $lsun0 = 30*$this->int($lsun/30);
        //繰り返し計算によって中気の時刻を計算
        //誤差が±1秒以内になったら打ち切り
        $delta1 = 0;
        for( $delta2 = 1 ; abs( $delta1 + $delta2 ) > ( 1 / 86400 ) ; ){
            //λsun を計算
            $t =($tm2+0.5) / 36525;
            $t =$t + ($tm1-2451545) / 36525;
            $lsun=$this->long_sunq( $t );
            //黄経差 Δλ＝λsun －λsun0
            $delta_l = $lsun - $lsun0 ;
            //Δλの引き込み範囲（±180°）を逸脱した場合には補正
            if( $delta_l > 180 ){
                $delta_l-=360;
            }elseif( $delta_l < -180 ){
                $delta_l+=360;
            }
            //時刻引数の補正値 Δt
            $delta1 = $this->int($delta_l * 365.2 / 360);
            $delta2 = $delta_l * 365.2 / 360;
            $delta2 -= $delta1;
            //  時刻引数の補正（$tm -= $delta;）
            $tm1 = $tm1 - $delta1;
            $tm2 = $tm2 - $delta2;
            if($tm2 < 0){
                $tm2+=1;
                $tm1-=1;
            }
        }
        //  戻り値の作成
        //  $chu[$i,0]:時刻引数を合成するのと、DT ==> JST 変換を行い戻値とする
        // （補正時刻=0秒 と仮定して計算）
        //  $chu[$i,1]:黄経
        $temp[0] = $tm2+9/24;
        $temp[0] += $tm1;
        $temp[1] = $lsun0;
        return array($temp[0],$temp[1]);
    }

    // -----------------------------------------------------------------
    //  直前の二分二至の時刻
    //  呼び出し時にセットする変数
    //  $tm   計算対象となる時刻（ユリウス日）
    //  戻値  二分二至の時刻、その時の黄経を配列で渡す
    //  基点ユリウス日(2451545)：2000/1/2/0/0/0(年/月/日/時/分/秒…世界時)
    // -----------------------------------------------------------------

    function call_nibun($tm){
        //時刻引数を分解する
        $tm1 = $this->int( $tm );
        $tm2 = $tm - $tm1;

        //JST ==> DT （補正時刻=0秒 と仮定して計算）
        $tm2-=9/24;
        //直前の二分二至の黄経 λsun0
        $t=($tm2+0.5) / 36525;
        $t=$t + ($tm1-2451545) / 36525;
        $lsun=$this->long_sunq($t);
        $lsun0=90*$this->int($lsun/90);
        //繰り返し計算によって直前の二分二至の時刻を計算
        //誤差が±1秒以内になったら打ち切り
        $delta1 = 0;
        for( $delta2 = 1 ; abs( $delta1+$delta2 ) > ( 1 / 86400 ) ; ){
            //λsun を計算
            $t=($tm2+0.5) / 36525 + ($tm1-2451545) / 36525;
            $lsun=$this->long_sunq($t);
            //黄経差 Δλ＝λsun －λsun0
            $delta_l = $lsun - $lsun0 ;
            //Δλの引き込み範囲（±180°）を逸脱した場合には補正
            if( $delta_l > 180 ){
                $delta_l-=360;
            }elseif( $delta_l < -180){
                $delta_l+=360;
            }
            //時刻引数の補正値 Δt
            //$delta = $delta_l * 365.2 / 360;
            $delta1 = $this->int($delta_l * 365.2 / 360);
            $delta2 = $delta_l * 365.2 / 360;
            $delta2 -= $delta1;
            //時刻引数の補正（$tm -= $delta;）
            $tm1 = $tm1 - $delta1;
            $tm2 = $tm2 - $delta2;
            if($tm2 < 0){
                $tm2+=1;
                $tm1-=1;
            }

        }
        //戻値の作成
        //$nibun[0,0]:時刻引数を合成し、DT ==> JST 変換を行い戻値とする
        //（補正時刻=0秒 と仮定し計算）
        //$nibun[0,1]:黄経
        $nibun[0] = $tm2+9/24;
        $nibun[0] += $tm1;
        $nibun[1] = $lsun0;
        return array($nibun[0],$nibun[1]);
    }

    // -----------------------------------------------------------------
    //  月齢の計算
    //  引数：$tm 計算対象となる時刻（ユリウス日）
    //  戻値：月齢
    // -----------------------------------------------------------------

    function  moon_value($tm){
        $moon = $this->int((($tm - $this->get_saku($tm)) * 10)+(1/2))/10;
        //朔望月の最大値は29.8
        //朔望月…月の満ち欠けの一周期のこと
        if ( $moon >= 29.9 ) { $moon -= 29.8; }
        //朔望月の最小値は29.3
        if ( $moon < 0 ) {
            $moon_abs = (-$moon);
            $moon = 29.3 - $moon_abs;
            if ( $moon < 0 ) {
                $moon_abs2 = (-$moon);
                $moon = 29.7 - $moon_abs2;
            }
        }
        $moon = $this->int(( $moon * 10 )+1/2)/ 10;
        return($moon);

    }

    // -----------------------------------------------------------------
    //  朔の時刻（JST）計算
    //  引数：$tm 計算対象となる時刻（ユリウス日）
    //  戻値：朔の時刻（ユリウス日）…時分秒は日の小数
    //  基点ユリウス日(2451545)：2000/1/2/0/0/0(年/月/日/時/分/秒…世界時)
    // -----------------------------------------------------------------

    function get_saku($tm){
        //ループカウンタのセット
        $lc=1;
        //時刻引数を分解
        $tm1 = $this->int( $tm );
        $tm2 = $tm - $tm1;
        //JST ==> DT （補正時刻=0秒 と仮定して計算）
        $tm2-=9/24;
        //繰り返し計算によって朔の時刻を計算する
        //誤差が±1秒以内になったら打ち切り
        $delta1 = 0;
        for( $delta2 = 1 ; abs( $delta1+$delta2 ) > ( 1 / 86400 ) ; $lc++){
            //太陽の黄経λsun ,月の黄経λmoon を計算
            $t=($tm2+0.5) / 36525 + ($tm1-2451545) / 36525;
            $lsun=$this->long_sunq($t);
            $lmoon=$this->long_moon($t);
            //月と太陽の黄経差Δλ（Δλ＝λmoon－λsun）
            $delta_l = $lmoon - $lsun ;
            //ループの１回目（$lc=1）で $delta_l < 0 の場合には引き込み範囲に入るように補正
            if( $lc==1 && $delta_l < 0 ){
                $delta_l = $this->regu_angle($delta_l);
            }
            //春分の近くに朔がある場合（0 ≦λsun≦ 20）で、月の黄経λmoon≧300 の
            //場合には、Δλ＝ 360 － Δλ と計算して補正
            elseif( $lsun >= 0 && $lsun <= 20 && $lmoon >= 300 ){
                $delta_l = $this->regu_angle( $delta_l );
                $delta_l = 360 - $delta_l;
            }
            //Δλの引き込み範囲（±40°）を逸脱した場合には補正
            elseif( abs( $delta_l ) > 40 ) {
                $delta_l = $this->regu_angle( $delta_l );
            }
            //時刻引数の補正値 Δt
            //$delta = $delta_l * 29.530589 / 360;
            $delta1 = $this->int($delta_l * 29.530589 / 360);
            $delta2 = $delta_l * 29.530589 / 360;
            $delta2 -= $delta1;
            //時刻引数の補正（$tm -= $delta;）
            $tm1 = $tm1 - $delta1;
            $tm2 = $tm2 - $delta2;
            if($tm2 < 0){
                $tm2+=1;
                $tm1-=1;
            }
            //  ループ回数が15回になったら初期値 $tm を $tm-26
            if($lc == 15 && abs( $delta1+$delta2 ) > ( 1 / 86400 ) ){
                $tm1 = $this->int( $tm-26 );
                $tm2 = 0;
            }
            //初期値を補正しても振動を続ける場合は初期値を返して
            //強制的にループを抜け出し異常終了させる。
            elseif( $lc > 30 && abs( $delta1+$delta2 ) > ( 1 / 86400 ) ){
                $tm1=$tm;$tm2=0;
                break;
            }
        }
        //時刻引数を合成し、DT ==> JST 変換を行い戻値とする
        //補正時刻=0秒 と仮定して計算
        return($tm2+$tm1+9/24);
    }

    // -----------------------------------------------------------------
    //  年月日、時分秒からユリウス日（JD）を計算
    //  ユリウス暦法による年月日から求められない
    //  ユリウス日…紀元前4713年1月1日から連続した日数
    // -----------------------------------------------------------------

    function ymdt_jd($year,$month,$day,$hour,$min,$sec){
        if( $month < 3 ){
            $year -= 1;
            $month += 12;
        }
        $jd  = $this->int( 365.25 * $year );
        $jd += $this->int( $year / 400 );
        $jd -= $this->int( $year / 100 );
        $jd += $this->int( 30.59 * ( $month-2 ) );
        $jd += 1721088;
        $jd += $day;
        $t  = $sec / 3600;
        $t += $min /60;
        $t += $hour;
        $t  = $t / 24;
        $jd += $t;
        return($jd);  //時間の単位で返す
    }

    // -----------------------------------------------------------------
    //  ユリウス日（$jd）から年月日、時分秒を計算…グレゴリオ暦法
    //  戻値：$ymd[]
    //  $ymd[0] ： 年   $ymd[1] ： 月   $ymd[2] ： 日
    //  $ymd[3] ： 時   $ymd[4] ： 分   $ymd[5] ： 秒   $ymd[6] ： 日曜日 0
    // -----------------------------------------------------------------

    function jd_ymdt($jd){
        $x0 = $this->int( $jd+68570);
        $x1 = $this->int( $x0/36524.25 );
        $x2 = $x0 - $this->int( 36524.25*$x1 + 0.75 );
        $x3 = $this->int( ( $x2+1 )/365.2425 );
        $x4 = $x2 - $this->int( 365.25*$x3 )+31;
        $x5 = $this->int( $this->int($x4) / 30.59 );
        $x6 = $this->int( $this->int($x5) / 11 );
        $ymd[2] = $x4 - $this->int( 30.59*$x5 );
        $ymd[1] = $x5 - 12*$x6 + 2;
        $ymd[0] = 100*( $x1-49 ) + $x3 + $x6;
        //2月30日の補正
        if($ymd[1]==2 && $ymd[2] > 28){
            if($ymd[0] % 100 == 0 && $ymd[0] % 400 == 0){
                $ymd[2]=29;
            }elseif($ymd[0] % 4 ==0){
                $ymd[2]=29;
            }else{
                $ymd[2]=28;
            }
        }
        $tm=86400*( $jd - $this->int( $jd ) );
        $ymd[3] = $this->int( $tm/3600 );
        $ymd[4] = $this->int( ($tm - 3600*$ymd[3])/60 );
        $ymd[5] = $this->int( $tm - 3600*$ymd[3] - 60*$ymd[4] );
        $ymd[6] = ($this->int($jd) + 2) % 7;
        return array($ymd[0],$ymd[1],$ymd[2],$ymd[3],$ymd[4],$ymd[5],$ymd[6]);
    }

    // -----------------------------------------------------------------
    // ユリウス日を世界時間に変換
    // 引数：$jd …ユリウス日
    // 戻値：array($year,$mon,$mday,$wday)
    // -----------------------------------------------------------------

    function jd_ut0($jd){
        $wt = $this->int( $jd );
        $x0 = $wt + 68570;
        $x1 = $this->int( $x0 / 36524.25 );
        $x2 = $x0 - $this->int( 36524.25 * $x1 + 0.75 );
        $x3 = $this->int( ( $x2 + 1 ) / 365.2425 );
        $x4 = $x2 - $this->int( 365.25 * $x3 ) + 31;
        $x5 = $this->int( $x4 / 30.59 );
        $x6 = $this->int( $x5 / 11 );
        $mday = $x4 - $this->int( 30.59 * $x5 );
        $mon = $x5 - 12 * $x6 + 1;
        $year = 100 * ( $x1 - 49 ) + $x3 + $x6;
        if ($mon == 1 && $mday > 28) {
            $mday = (($year % 400 == 0) || ($year % 4 == 0 && $year % 100 != 0)) ? 29 : 28;
        }
        $wday = ($wt + 2) % 7;
        return array($year,$mon,$mday,$wday);
    }

    // -----------------------------------------------------------------
    // 1970.1.1.00:00以後の通算秒(日本時間)からユリウス日に変換
    // 引数：$sec …1970.1.1.00:00からの通算秒 $sec = time() + 9*60*60
    // 戻値：$tm = sc_tm($sec)
    // (2440587…1969.12.31.24:59:59までのユリウス日)
    // -----------------------------------------------------------------

    function sc_tm($times){
        return($times / 86400 + 2440587);
    }

    // -----------------------------------------------------------------
    // 太陽と月の黄経を求める
    // 引数：$tm …ユリウス日
    // 戻値：list($sun,$moon) = sun_moon($tm)
    // -----------------------------------------------------------------

    function sun_moon($tm){
        $tm1 = $this->int($tm);
        $tm2 = $tm-$tm1-9/24;
        $tm3 = ($tm2 + 0.5) / 36525 + ($tm1 - 2451545) / 36525;
        $sun = $this->long_sunq($tm3);
        $moon = $this->long_moon($tm3);
        $sun = $this->int($sun * 10) / 10;
        $moon = $this->int($moon * 10) / 10;
        return array($sun,$moon);
    }

    // -----------------------------------------------------------------
    // 太陽の黄経からユリウス日（$jd）を計算
    // 引数：$year …西暦年  $tk …太陽の黄経°
    // 戻値：ユリウス日 $jd = lsun_jd($year,$tk)
    // -----------------------------------------------------------------

    function lsun_jd($year,$tk){
        $t = ($year - 1975) * .999978626 + $tk * .0027777184 + .016566868;
        $d1 = $this->fd(-.7754 * $t + 4.21) * .0003;
        $d1 += $this->fd(18.849 * $t + 6.102) * .0003;
        $d1 -= $this->fd(12.5667 * $t + 3.457) * .0004;
        $d1 += $this->fd(5.2238 * $t + 4.081) * .0004;
        $d1 += $this->fd(5.5076 * $t + 4.622) * .0004;
        $d1 += $this->fd(-.3981 * $t + 1.73) * .0005;
        $d1 += sin(-.0262 * $t + .176) * .0005;
        $d1 += $this->fd(5.8849 * $t + 4.173) * .0006;
        $d1 += $this->fd(-.5297 * $t + .211) * .0007;
        $d1 += $this->fd(1.5774 * $t + .934) * .0007;
        $d1 += $this->fd(11.5068 * $t + 3.63) * .0008;
        $d1 += $this->fd(3.9301 * $t + 3.684) * .0013;
        $d1 += $this->fd(7.8604 * $t + 4.231) * .0015;
        $d1 += sin(-.0035 * $t + 5.126) * .0018;
        $d1 += $this->fd(-77.7138 * $t + 5.833) * .0018;
        $d1 += $this->fd(5.7533 * $t + 4.974) * .002;
        $d1 -= $this->fd(-.33756 * $t + 4.3396) * .0048;
        $d1 += $this->fd(12.566 * $t + 6.1621) * .02;
        $d1 += $this->fd(6.28302 * $t + 6.22264) * (1.9159 - .000048 * $t);
        $d2 = $this->fd(12.566 * $t + 1.44971) * .000688;
        $d2 += $this->fd(6.28302 * $t + 1.51025) * (.032957 - 8.3E-07 * $t);
        $d2 += .985648;
        $jd = ($t * 365.25 - $d1/$d2 + 42412.0008347) / 1.0000000317;
        $jd += 2400000;
        // DT=>JST
        $jd += 9/24;
        return($jd);
    }
    function fd($a){
        $PI=3.141592653589793238462;
        return(sin($a-$this->int($a/(2*$PI))*2*$PI));
    }

    // -----------------------------------------------------------------
    //  数値の、切捨てfloor($in)、切り上げceil($in) 処理
    // -----------------------------------------------------------------

    function int($in){
        if($in > 0){return floor($in);}else{return ceil($in);}
    }

    // -----------------------------------------------------------------
    //   角度の正規化（引数の範囲 0≦θ＜360）
    // -----------------------------------------------------------------

    function regu_angle($angle){
        if( $angle < 0 ){
            $angle1 = -$angle;
            $angle2 = $this->int( $angle1 / 360 );
            $angle1 -= 360 * $angle2;
            $angle1 = 360 - $angle1;
        }else{
            $angle1 = $this->int( $angle / 360 );
            $angle1 = $angle - 360 * $angle1;
        }
        return($angle1);
    }

    // -----------------------------------------------------------------
    //  太陽の黄経 λsun を計算
    // -----------------------------------------------------------------

    function long_sunq($t){
        //摂動項(太陽黄経の近似解を求める部分)の計算
        $ang = $this->regu_angle(  31557 * $t + 161 );
        $th =                .0004 * cos( $this->pai*$ang );
        $ang = $this->regu_angle(  29930 * $t +  48 );
        $th = $th +          .0004 * cos( $this->pai*$ang );
        $ang = $this->regu_angle(   2281 * $t + 221 );
        $th = $th +          .0005 * cos( $this->pai*$ang );
        $ang = $this->regu_angle(    155 * $t + 118 );
        $th = $th +          .0005 * cos( $this->pai*$ang );
        $ang = $this->regu_angle(  33718 * $t + 316 );
        $th = $th +          .0006 * cos( $this->pai*$ang );
        $ang = $this->regu_angle(   9038 * $t +  64 );
        $th = $th +          .0007 * cos( $this->pai*$ang );
        $ang = $this->regu_angle(   3035 * $t + 110 );
        $th = $th +          .0007 * cos( $this->pai*$ang );
        $ang = $this->regu_angle(  65929 * $t +  45 );
        $th = $th +          .0007 * cos( $this->pai*$ang );
        $ang = $this->regu_angle(  22519 * $t + 352 );
        $th = $th +          .0013 * cos( $this->pai*$ang );
        $ang = $this->regu_angle(  45038 * $t + 254 );
        $th = $th +          .0015 * cos( $this->pai*$ang );
        $ang = $this->regu_angle( 445267 * $t + 208 );
        $th = $th +          .0018 * cos( $this->pai*$ang );
        $ang = $this->regu_angle(     19 * $t + 159 );
        $th = $th +          .0018 * cos( $this->pai*$ang );
        $ang = $this->regu_angle(  32964 * $t + 158 );
        $th = $th +          .0020 * cos( $this->pai*$ang );
        $ang = $this->regu_angle(  71998.1 * $t + 265.1 );
        $th = $th +          .0200 * cos( $this->pai*$ang );
        $ang = $this->regu_angle(  35999.05 * $t + 267.52 );
        $th = $th -     0.0048 * $t * cos( $this->pai*$ang ) ;
        $th = $th +      1.9147     * cos( $this->pai*$ang ) ;
        //比例項の計算
        $ang = $this->regu_angle( 36000.7695 * $t );
        $ang = $this->regu_angle( $ang + 280.4659 );
        $th  = $this->regu_angle( $th + $ang );
        return($th);
    }

    // -----------------------------------------------------------------
    //  月の黄経 λmoon を計算する
    // -----------------------------------------------------------------

    function long_moon($t){
        //摂動項(月黄経の近似解を求める部分)の計算
        $ang = $this->regu_angle( 2322131  * $t + 191  );
        $th =      .0003 * cos( $this->pai*$ang );
        $ang = $this->regu_angle(    4067  * $t +  70  );
        $th = $th + .0003 * cos( $this->pai*$ang );
        $ang = $this->regu_angle(  549197  * $t + 220  );
        $th = $th + .0003 * cos( $this->pai*$ang );
        $ang = $this->regu_angle( 1808933  * $t +  58  );
        $th = $th + .0003 * cos( $this->pai*$ang );
        $ang = $this->regu_angle(  349472  * $t + 337  );
        $th = $th + .0003 * cos( $this->pai*$ang );
        $ang = $this->regu_angle(  381404  * $t + 354  );
        $th = $th + .0003 * cos( $this->pai*$ang );
        $ang = $this->regu_angle(  958465  * $t + 340  );
        $th = $th + .0003 * cos( $this->pai*$ang );
        $ang = $this->regu_angle(   12006  * $t + 187  );
        $th = $th + .0004 * cos( $this->pai*$ang );
        $ang = $this->regu_angle(   39871  * $t + 223  );
        $th = $th + .0004 * cos( $this->pai*$ang );
        $ang = $this->regu_angle(  509131  * $t + 242  );
        $th = $th + .0005 * cos( $this->pai*$ang );
        $ang = $this->regu_angle( 1745069  * $t +  24  );
        $th = $th + .0005 * cos( $this->pai*$ang );
        $ang = $this->regu_angle( 1908795  * $t +  90  );
        $th = $th + .0005 * cos( $this->pai*$ang );
        $ang = $this->regu_angle( 2258267  * $t + 156  );
        $th = $th + .0006 * cos( $this->pai*$ang );
        $ang = $this->regu_angle(  111869  * $t +  38  );
        $th = $th + .0006 * cos( $this->pai*$ang );
        $ang = $this->regu_angle(   27864  * $t + 127  );
        $th = $th + .0007 * cos( $this->pai*$ang );
        $ang = $this->regu_angle(  485333  * $t + 186  );
        $th = $th + .0007 * cos( $this->pai*$ang );
        $ang = $this->regu_angle(  405201  * $t +  50  );
        $th = $th + .0007 * cos( $this->pai*$ang );
        $ang = $this->regu_angle(  790672  * $t + 114  );
        $th = $th + .0007 * cos( $this->pai*$ang );
        $ang = $this->regu_angle( 1403732  * $t +  98  );
        $th = $th + .0008 * cos( $this->pai*$ang );
        $ang = $this->regu_angle(  858602  * $t + 129  );
        $th = $th + .0009 * cos( $this->pai*$ang );
        $ang = $this->regu_angle( 1920802  * $t + 186  );
        $th = $th + .0011 * cos( $this->pai*$ang );
        $ang = $this->regu_angle( 1267871  * $t + 249  );
        $th = $th + .0012 * cos( $this->pai*$ang );
        $ang = $this->regu_angle( 1856938  * $t + 152  );
        $th = $th + .0016 * cos( $this->pai*$ang );
        $ang = $this->regu_angle(  401329  * $t + 274  );
        $th = $th + .0018 * cos( $this->pai*$ang );
        $ang = $this->regu_angle(  341337  * $t +  16  );
        $th = $th + .0021 * cos( $this->pai*$ang );
        $ang = $this->regu_angle(   71998  * $t +  85  );
        $th = $th + .0021 * cos( $this->pai*$ang );
        $ang = $this->regu_angle(  990397  * $t + 357  );
        $th = $th + .0021 * cos( $this->pai*$ang );
        $ang = $this->regu_angle(  818536  * $t + 151  );
        $th = $th + .0022 * cos( $this->pai*$ang );
        $ang = $this->regu_angle(  922466  * $t + 163  );
        $th = $th + .0023 * cos( $this->pai*$ang );
        $ang = $this->regu_angle(   99863  * $t + 122  );
        $th = $th + .0024 * cos( $this->pai*$ang );
        $ang = $this->regu_angle( 1379739  * $t +  17  );
        $th = $th + .0026 * cos( $this->pai*$ang );
        $ang = $this->regu_angle(  918399  * $t + 182  );
        $th = $th + .0027 * cos( $this->pai*$ang );
        $ang = $this->regu_angle(    1934  * $t + 145  );
        $th = $th + .0028 * cos( $this->pai*$ang );
        $ang = $this->regu_angle(  541062  * $t + 259  );
        $th = $th + .0037 * cos( $this->pai*$ang );
        $ang = $this->regu_angle( 1781068  * $t +  21  );
        $th = $th + .0038 * cos( $this->pai*$ang );
        $ang = $this->regu_angle(     133  * $t +  29  );
        $th = $th + .0040 * cos( $this->pai*$ang );
        $ang = $this->regu_angle( 1844932  * $t +  56  );
        $th = $th + .0040 * cos( $this->pai*$ang );
        $ang = $this->regu_angle( 1331734  * $t + 283  );
        $th = $th + .0040 * cos( $this->pai*$ang );
        $ang = $this->regu_angle(  481266  * $t + 205  );
        $th = $th + .0050 * cos( $this->pai*$ang );
        $ang = $this->regu_angle(   31932  * $t + 107  );
        $th = $th + .0052 * cos( $this->pai*$ang );
        $ang = $this->regu_angle(  926533  * $t + 323  );
        $th = $th + .0068 * cos( $this->pai*$ang );
        $ang = $this->regu_angle(  449334  * $t + 188  );
        $th = $th + .0079 * cos( $this->pai*$ang );
        $ang = $this->regu_angle(  826671  * $t + 111  );
        $th = $th + .0085 * cos( $this->pai*$ang );
        $ang = $this->regu_angle( 1431597  * $t + 315  );
        $th = $th + .0100 * cos( $this->pai*$ang );
        $ang = $this->regu_angle( 1303870  * $t + 246  );
        $th = $th + .0107 * cos( $this->pai*$ang );
        $ang = $this->regu_angle(  489205  * $t + 142  );
        $th = $th + .0110 * cos( $this->pai*$ang );
        $ang = $this->regu_angle( 1443603  * $t +  52  );
        $th = $th + .0125 * cos( $this->pai*$ang );
        $ang = $this->regu_angle(   75870  * $t +  41  );
        $th = $th + .0154 * cos( $this->pai*$ang );
        $ang = $this->regu_angle(  513197.9  * $t + 222.5  );
        $th = $th + .0304 * cos( $this->pai*$ang );
        $ang = $this->regu_angle(  445267.1  * $t +  27.9  );
        $th = $th + .0347 * cos( $this->pai*$ang );
        $ang = $this->regu_angle(  441199.8  * $t +  47.4  );
        $th = $th + .0409 * cos( $this->pai*$ang );
        $ang = $this->regu_angle(  854535.2  * $t + 148.2  );
        $th = $th + .0458 * cos( $this->pai*$ang );
        $ang = $this->regu_angle( 1367733.1  * $t + 280.7  );
        $th = $th + .0533 * cos( $this->pai*$ang );
        $ang = $this->regu_angle(  377336.3  * $t +  13.2  );
        $th = $th + .0571 * cos( $this->pai*$ang );
        $ang = $this->regu_angle(   63863.5  * $t + 124.2  );
        $th = $th + .0588 * cos( $this->pai*$ang );
        $ang = $this->regu_angle(  966404  * $t + 276.5  );
        $th = $th + .1144 * cos( $this->pai*$ang );
        $ang = $this->regu_angle(   35999.05 * $t +  87.53 );
        $th = $th + .1851 * cos( $this->pai*$ang );
        $ang = $this->regu_angle(  954397.74 * $t + 179.93 );
        $th = $th + .2136 * cos( $this->pai*$ang );
        $ang = $this->regu_angle(  890534.22 * $t + 145.7  );
        $th = $th + .6583 * cos( $this->pai*$ang );
        $ang = $this->regu_angle(  413335.35 * $t +  10.74 );
        $th = $th + 1.2740 * cos( $this->pai*$ang );
        $ang = $this->regu_angle( 477198.868 * $t + 44.963 );
        $th = $th + 6.2888 * cos( $this->pai*$ang );
        //比例項の計算
        $ang = $this->regu_angle(  481267.8809 * $t );
        $ang = $this->regu_angle(  $ang + 218.3162 );
        $th  = $this->regu_angle(  $th  +  $ang );
        return($th);
    }


}
