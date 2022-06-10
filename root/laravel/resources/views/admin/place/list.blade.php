@section('title',         '')
@section('description',   '')

@extends('layouts.admin')
@section('content')

    <div class="box">
        <div class="box-body">
            <h2 class="page-header">一覧</h2>

            @forelse($datas as $year => $areas)
                <h3>{{ $year }}</h3>
                <button class="btn btn-warning trigGetYearlyTideDatas" type="button" data-year="{{ $year }}">{{ $year }}年全地点の潮位データ取得</button>

                <table class="table table-striped table-bordered">
                    <caption>

                    </caption>
                    <tr>
                        <th colspan="2">No</th>
                        <th>地点コード</th>
                        <th>地点名</th>
                        <th>潮位データ</th>
                        <th>データ取得</th>
                    </tr>

                    @php $k = 0; @endphp
                    @forelse($areas as $area_id => $places)
                        <tr>
                            <td colspan="6">{{ $area_names[$area_id] }} エリア</td>
                        </tr>
                        @forelse($places as $place_id => $place)
                            @php $k++; @endphp
                            <tr>
                                <td></td>
                                <td>{{ $k }}</td>
                                <td>{{ $place->code }}</td>
                                <td>{{ $place->name }}</td>
                                <td class="bulletGetTideData" data-place="{{ $place->code }}" data-year="{{ $year }}">{{ $place->has_file ? '◯': 'x' }}</td>
                                <td>
                                    @if(!$place->has_file)
                                        <button class="btn btn-warning trigGetTideData" type="button" data-place="{{ $place->code }}" data-year="{{ $year }}">取得</button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                        @endforelse
                    @empty
                    @endforelse

                </table>
            @empty
            @endforelse

        </div>
    </div>

@endsection
