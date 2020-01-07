<tr>
    <th>日付</th>
    <th>潮</th>
    <th><i class="fa fa-sun-o"></i></th>
    <th><i class="fa fa-moon-o"></i></th>
    <th colspan="2">満潮</th>
    <th colspan="2">干潮</th>
</tr>
@if(!empty($datas))
    @foreach($datas as $data)
        <tr>
            <td>{{ $data->date_at->format('n/j') }} <span class="wday wday-{{ $data->date_at->format('w') }}">{{ \Func::getWeekDay($data->date_at) }}</span></td>
            <td>{{ $data->tide_name }}</td>
            <td>{{ $data->sunrise->format('H:i') }}</td>
            <td>{{ $data->sunset->format('H:i') }}</td>
            <td class="max--simple">{{ $data->max1 }}</td>
            <td class="max--simple">{{ $data->max2 }}</td>
            <td class="min--simple">{{ $data->min1 }}</td>
            <td class="min--simple">{{ $data->min2 }}</td>
        </tr>
    @endforeach
@endif
