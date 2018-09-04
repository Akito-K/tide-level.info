@extends('layouts.admin')
@section('content')

    {!! MyHTML::errorMessage($errors) !!}
    {!! MyHTML::flashMessage() !!}

    <div class="box">
        <div class="box-body">
            <h2 class="page-header">編集</h2>

            {!! Form::open(['url' => 'admin/pagemeta/update']) !!}
                {!! Form::hidden('page_id', $data->page_id) !!}
                <input type="hidden" id="ajax-upload-target" value="ogp">
                <input type="hidden" id="icon_path" name="icon_path" value="">

                <table class="pagemeta__table pagemeta__table--edit">
                    <tr>
                        <th>タイトル</th>
                        <td>{!! Form::text('title', $data->title, ['class' => 'form-control form-control--mini form-control--50']) !!}</td>
                    </tr>
                    <tr>
                        <th>説明</th>
                        <td>{!! Form::textarea('description', $data->description, ['class' => 'form-control']) !!}</td>
                    </tr>

                    <tr>
                        <th>body 付加 class</th>
                        <td>{!! Form::text('body_class', $data->body_class, ['class' => 'form-control']) !!}</td>
                    </tr>
                </table>

                {!! Form::submit('この内容で登録する', ['class' => 'btn btn-block btn-warning btn-submit'] ) !!}
            {!! Form::close() !!}

        </div>
    </div>

    <a href="{{ url('') }}/admin/pagemeta" class="btn btn-block btn-primary">一覧へ戻る</a>

@endsection
