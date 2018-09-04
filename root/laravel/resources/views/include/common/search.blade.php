<section class="search">
    {!! \Form::open(['url' => 'tide', 'class' => 'search__boxes', 'id' => 'bulletSearchForm']) !!}

        <div class="search__box">
            <div class="search__param date-input trigShowCalendar" data-calendar="date">
                {!! \Form::text('date_at', old('date_at', \Func::dateFormat( new \Datetime(), 'Y/n/j(wday)')), ['id' => 'date_at', 'placeholder' => '日付', 'class' => '', 'readonly' => 'readonly']) !!}
                {!! \Form::hidden('hide_date_at', old('hide_date_at', \Func::dateFormat( new \Datetime(), 'Y/n/j')), ['id' => 'hide_date_at']) !!}
                <i class="fa fa-calendar"></i>から
            </div>
            <div class="search__param search__weeks lists">
                <div class="list list-80 list-mr-sm">
                    <ul class="lists">
                        {!! \MyForm::selectWeeks(2, 9) !!}
                    </ul>
                </div>
                <p class="list">週間の</p>
            </div>
        </div>

        <div class="search__box">
            <div class="search__param">
                {!! \Form::select('area_id', $area_names, old('area_id'), ['id' => 'trigChangeArea']) !!}
                エリアから
            </div>
            <div class="search__param search__param--place" id="bulletChangeArea">
                {!! \Form::select('place_id', $place_names, old('place_id', 'AK'), ['id' => 'paramPlaceId']) !!}
                の潮位を
            </div>
        </div>

        {!! \Form::hidden('week', old('week', 2), ['id' => 'bulletSelectWeek']) !!}
        <button type="submit" class="btn btn-block btn-warning btn-submit">調べる</button>

    {!! \Form::close() !!}
</section>
