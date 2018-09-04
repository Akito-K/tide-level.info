@extends('layouts.admin')
@section('content')

    <div class="box">
        <div class="box-body">
            <h2 class="page-header">ダッシュボード</h2>

            <div class="dashboard__block">
                <h3 class="dashboard__title">更新情報</h3>
                <div class="dashboard__box">

                    @if(!empty($news))
                    @foreach($news as $new)
                    <ul class="dashboard__lists">
                        <li class="dashboard__list dashboard__list--title">{!! \Func::dateFormat($new->date_at, 'Y.m.d') !!}</li>
                        <li class="dashboard__list dashboard__list--value"><a href="{{ url('') }}/admin/news/{{$new->news_id}}/detail">{{ $new->title }}</a></li>
                    </ul>
                    @endforeach
                    @else
                    <p class="dashboard__null">更新情報はありません。</p>
                    @endif

                </div>
            </div>

        </div>
    </div>

@endsection
