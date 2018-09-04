@extends('layouts.admin')
@section('content')

    <div class="box">
        <div class="box-body">
            <h2 class="page-header">一覧</h2>

            @if( !empty($datas))

            {!! Form::open(['url' => 'admin/pagemeta/update']) !!}
                {!! Form::hidden('upload_id', $upload_id) !!}

                <p>この内容でよろしいですか？</p>
                <p>{!! Form::submit('実行', ['class' => 'btn btn-block btn-warning btn-submit'] ) !!}</p>

                <table class="pagemeta__table--list">
                    <tr>
                        <th class="pagemeta__cell">ページID</th>
                        <th class="pagemeta__cell">タイトル</th>
                        <th class="pagemeta__cell">説明</th>
                        <th class="pagemeta__cell">body付加class</th>
                        <th class="pagemeta__cell">wrapper class</th>
                    </tr>

                    @foreach($datas as $data)
                    <tr>
                        <td class="pagemeta__cell">{!! $data->page_id !!}</td>
                        <td class="pagemeta__cell">{!! $data->title !!}</td>
                        <td class="pagemeta__cell">{!! $data->description !!}</td>
                        <td class="pagemeta__cell">{!! $data->body_class !!}</td>
                        <td class="pagemeta__cell">{!! $data->wrapper_class !!}</td>
                    </tr>
                    @endforeach
                </table>

                <p>この内容でよろしいですか？</p>
                <p>{!! Form::submit('実行', ['class' => 'btn btn-block btn-warning btn-submit'] ) !!}</p>
            {!! Form::close() !!}

            @endif

            <a href="{{ url('') }}/admin/pagemeta/create" class="btn btn-block btn-primary">route のエクセルから再登録する</a>
        </div>
    </div>

@endsection
