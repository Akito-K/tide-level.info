'use strict';
import $ = require("jquery");

export class Search {

    constructor(
        private GoogleMap,
        private ajaxing: boolean = false,
    ){

        let self = this;

        // フェイクのファイル選択ボタン初期値取得
        if($('#bulletSelectWeek').length > 0){
            const val = $('#bulletSelectWeek').val();
            $('.trigSelectWeek[data-week="'+val+'"]').addClass("selected");
        }

        // フェイクのファイル選択ボタン
        $('.trigSelectWeek').click( function(){
            self.selectWeek( $(this) );
        });

        // エリア選択で地点選択肢入れ替え
        $('#trigChangeArea').change(function(){
            const areaId = $(this).val();
            self.changeSelectPlaceOptions(areaId);
        });

        // Skin 変更
        $('#trigChangeSkin').click(function(){
            const currentSkin = $(this).attr('data-skin');
            self.changeSkin(currentSkin);
        });

        // Map から検索
        $(document).on('click', '.trigSubmitFromMap', function(){
//            $('.trigSubmitFromMap').click(function(){
            const placeId = $(this).data('place_id');
            $('#paramPlaceId').val(placeId);
            $('#bulletSearchForm').submit();
        });


    }

    public getNextSkin(currentSkin){
        let skin = '';
        if(currentSkin == 'dark'){
            skin = 'light';
        }else{
            skin = 'dark';
        }

        return skin;
    }

    public changeSkin(currentSkin){
        const skin = this.getNextSkin(currentSkin);
        $('#trigChangeSkin').attr("data-skin", skin);

        let stylesheet = $('#bulletChangeSkin').attr('href');
        stylesheet = stylesheet.replace(currentSkin, skin);
        $('#bulletChangeSkin').attr('href', stylesheet);

        this.GoogleMap.setSkin(skin);
        this.GoogleMap.initMap();
        this.ajaxChangeSkin(skin);
    }

    /**
     * @param obj {name, size, type, lastModified, lastModifiedDate}
     * @return boolean
     */
    public selectWeek(elm): void {
        $('.trigSelectWeek').removeClass('selected');
        elm.addClass('selected');
        const week = elm.data('week');
        $('#bulletSelectWeek').val(week);
    }

    // エリアから地点選択肢入れ替え
    public changeSelectPlaceOptions(areaId){
        let self = this;
        self.ajaxing = true;
        const token: string = $('meta[name="csrf-token"]').attr('content');
        const D = {area_id: areaId};

        $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            type: "POST",
            url: '/ajax/change_places',
            data: D,
            dataType: 'json',
            beforeSend: function(){
                // 実行中画面
                $('#ajaxing-waiting').show();
            },
            success: function( data ){
                //console.log(data);
                $('#bulletChangeArea').html(data.view);
            },
            complete: function(){
                // 実行中画面を消す
                $('#ajaxing-waiting').hide();
                self.ajaxing = false;
            }
        });
    }

    // Skin の Session を更新
    public ajaxChangeSkin(skin){
        let self = this;
        self.ajaxing = true;
        const token: string = $('meta[name="csrf-token"]').attr('content');
        const D = {skin: skin};

        $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            type: "POST",
            url: '/ajax/change_skin',
            data: D,
            dataType: 'json',
            beforeSend: function(){
                // 実行中画面
                $('#ajaxing-waiting').show();
            },
            success: function( data ){
                //console.log(data);
                $('#trigChangeSkin').html(data.view).blur();
            },
            complete: function(){
                // 実行中画面を消す
                $('#ajaxing-waiting').hide();
                self.ajaxing = false;
            }
        });
    }
}
export default Search;


