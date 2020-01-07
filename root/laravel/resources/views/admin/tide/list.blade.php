@extends('layouts.admin')
@section('content')

    <div class="box">
        <div class="box-body">
            <h2 class="page-header">一覧</h2>

{{--            <p><a href="{{ url('') }}/admin/pagemeta/create" class="btn btn-block btn-primary">route のエクセルから再登録する</a></p>--}}

            @if( !empty($datas))
                <table class="pagemeta__table--list">
                    <tr>
                        <th class="pagemeta__cell">No</th>
                        <th class="pagemeta__cell">地点ID</th>
                        <th class="pagemeta__cell">地点名</th>
                        <th class="pagemeta__cell">件数</th>
                        <th class="pagemeta__cell"></th>
                    </tr>

                    @php $i = 0; @endphp
                    @foreach($datas as $place_id => $data)
                        @php $i++; @endphp
                        <tr>
                            <td class="pagemeta__cell">{{ $i }}</td>
                            <td class="pagemeta__cell">{{ $place_id }}</td>
                            <td class="pagemeta__cell">{{ $place_names[$place_id] }}</td>
                            <td class="pagemeta__cell">{{ $data->count }}</td>
                            <td class="pagemeta__cell"></td>
                        </tr>
                    @endforeach
                </table>
            @endif

        </div>
    </div>

@endsection
