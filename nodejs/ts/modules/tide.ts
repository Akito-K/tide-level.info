'use strict';
import $ = require("jquery");

export class Tide {

    constructor(
        private ajaxing: boolean = false,
    ){

        let self = this;

        // 特定の地点・年の潮位データを取得
        $(document).on('click', '.trigGetTideData', function(){
            const year = Number($(this).attr('data-year'));
            const placeCode = $(this).attr('data-place');
            self.ajaxGetTideData(year, placeCode);
        });

        // 特定の年の全地点潮位データを取得
        $(document).on('click', '.trigGetYearlyTideDatas', function(){
            const year = Number($(this).attr('data-year'));
            self.ajaxGetYearlyTideDatas(year);
        });

    }

    // 年とエリアの潮位データを国交省サイトから取得
    public ajaxGetTideData(year, placeCode){
        let self = this;
        self.ajaxing = true;
        const token: string = $('meta[name="csrf-token"]').attr('content');
        const D = {year: year, place_code: placeCode};

        $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            type: "POST",
            url: '/ajax/get_tide_data',
            data: D,
            dataType: 'json',
            beforeSend: function(){
                // 実行中画面
                $('#ajaxing-waiting').show();
            },
            success: function( data ){
                if(data.error == ''){
                    $('.bulletGetTideData[data-year="' + year + '"][data-place="' + placeCode + '"]').html(data.view);
                    $('.trigGetTideData[data-year="' + year + '"][data-place="' + placeCode + '"]').remove();
                }else{
                    alert(data.error);
                }
            },
            complete: function(){
                // 実行中画面を消す
                $('#ajaxing-waiting').hide();
                self.ajaxing = false;
            }
        });
    }

    // 年とエリアの潮位データを国交省サイトから取得
    public ajaxGetYearlyTideDatas(year){
        let self = this;
        self.ajaxing = true;
        const token: string = $('meta[name="csrf-token"]').attr('content');
        const D = {year: year};

        $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            type: "POST",
            url: '/ajax/get_yearly_tide_datas',
            data: D,
            dataType: 'json',
            beforeSend: function(){
                // 実行中画面
                $('#ajaxing-waiting').show();
            },
            success: function( data ){
                data.results.forEach(function(result){
                    $('.bulletGetTideData[data-year="' + year + '"][data-place="' + result.place_code + '"]').html(result.view);
                    $('.trigGetTideData[data-year="' + year + '"][data-place="' + result.place_code + '"]').remove();
                })
                $('.trigGetYearlyTideDatas[data-year="' + year + '"]').remove();

                if(data.has_error == 1){
                    alert('一部の処理が完了しませんでした。ページを再読み込みして結果を確認してください');
                }
            },
            complete: function(){
                // 実行中画面を消す
                $('#ajaxing-waiting').hide();
                self.ajaxing = false;
            }
        });
    }
}
export default Tide;


