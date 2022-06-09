<input type="hidden" id="flagUploadable" @if( isset($uploadable) && $uploadable ) value="1" @else value="0" @endif >
<div class="ajaxing ajaxing--uploading bulletAjaxingUploading" id="ajaxing-uploading">
    <div class="ajaxing__block">
        <div class="ajaxing__box">
            <p class="ajaxing__box__body">ただいまファイルをアップロード中です。<br />画面を閉じずにしばらくお待ちください。</p>
            <p class="ajaxing__box__icon"><span class="fa fa-spin"><i class="fa fa-pulse fa-spinner"></i></span></p>
        </div>
        <div class="ajaxing__cover"></div>
    </div>
</div>

<div class="ajaxing ajaxing--drag-enter bulletAjaxingDragEnter" id="ajaxing-drag-enter">
    <div class="ajaxing__block">
        <div class="ajaxing__box">
            <p class="ajaxing__box__body">ここにファイルをドラッグしてください</p>
            <p class="ajaxing__box__icon"><i class="fa fa-arrow-circle-o-down"></i></p>
        </div>
        <div class="ajaxing__cover"></div>
    </div>
</div>


<div class="ajaxing ajaxing--editting" id="ajaxing-editting">
    <div class="ajaxing__block">
        <div class="ajaxing__box">
            <p class="ajaxing__box__body">ただいま処理中です。<br />画面を閉じずにしばらくお待ちください。</p>
            <p class="ajaxing__box__icon"><span class="fa fa-spin"><i class="fa fa-pulse fa-spinner"></i></span></p>
        </div>
        <div class="ajaxing__cover"></div>
    </div>
</div>

<div class="ajaxing ajaxing--waiting" id="ajaxing-waiting">
    <div class="ajaxing__block">
        <div class="ajaxing__box">
            <p class="ajaxing__box__body">画面を閉じずにしばらくお待ちください。</p>
            <p class="ajaxing__box__icon"><span class="fa fa-spin"><i class="fa fa-pulse fa-spinner"></i></span></p>
        </div>
        <div class="ajaxing__cover"></div>
    </div>
</div>


