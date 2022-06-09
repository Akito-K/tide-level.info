'use strict';
import $ = require("jquery");

export class Tide {

    constructor(
        private ajaxing: boolean = false,
    ){

        let self = this;

        // フェイクのファイル選択ボタン初期値取得
        $(document).on('click', '.trigGetTideData', function(){
            const year = Number($(this).attr('data-year'));
            const placeCode = $(this).attr('data-place');
            self.ajaxGetTideData(year, placeCode);
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
}
export default Tide;


