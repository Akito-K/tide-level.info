<tr>
    <th rowspan="2"></th>
    @for($i = 0; $i <= 11; $i++)
        <th>{{ $i }}</th>
    @endfor
</tr>
<tr>
    @for($i = 12; $i <= 23; $i++)
        <th>{{ $i }}</th>
    @endfor
</tr>

@if(!empty($tide_datas))
    @foreach($tide_datas as $data)
        <tr>
            <td rowspan="2">
                {{ $data->date_at->format('n/j') }} <span class="wday wday-{{ $data->date_at->format('w') }}">{{ \Func::getWeekDay($data->date_at) }}</span>
                <span class="tide__name">{{ $data->tide_name }}</span>
            </td>
            @for($i = 0; $i <= 11; $i++)
                @include('include.common.detail')
            @endfor
        </tr>
        <tr>
            @for($i = 12; $i <= 23; $i++)
                @include('include.common.detail')
            @endfor
        </tr>
    @endforeach
@endif
