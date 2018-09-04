@extends('layouts.admin')
@section('content')

    {!! MyHTML::errorMessage($errors) !!}
    {!! MyHTML::flashMessage() !!}

    <div class="box">
        <div class="box-body">
            <h2 class="page-header">一覧</h2>

            {!! Form::open(['url' => 'admin/pagemeta/confirm']) !!}
            {!! Form::hidden('filepath', '', ['id' => 'filepath']) !!}

            <div class="staff__edit__icon">
                <div class="staff__edit__icon__thumbnail" id="bulletUploadedImage"></div>

                <div class="staff__edit__icon__upload ajaxing__upload-box">
                    <p class="ajaxing__upload-box__drag-area trigAjaxingUploadingArea">ここにファイルをドラッグしてください。</p>
                    <button type="button" class="btn ajaxing__upload-box__btn-input trigAjaxingUploadingBtn">ファイル</button>
                    <input type="file" multiple="multiple" class="ajaxing__upload-box__fake-input" id="bulletFakeInput" data-type="spreadsheet" data-target="pagemeta" data-ajaxing="0">
                    {!! Form::hidden('upload_id', '', ['id' => 'uploaded_id']) !!}
                </div>
            </div>

            {!! Form::submit('エクセルの内容を確認する', ['class' => 'btn btn-block btn-warning btn-submit'] ) !!}

            {!! Form::close() !!}

            <p>
                <a href="{{ url('') }}/admin/pagemeta" class="btn btn-block btn-primary">ページメタ一覧へ</a>
            </p>
        </div>
    </div>

@endsection
