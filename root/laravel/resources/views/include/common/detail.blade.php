<?php
$min = $data->min == $i ? 'min--detail': '';
$max = $data->max == $i ? 'max--detail': '';
$key = 'tide' . sprintf('%02d', $i);
?>
<td class="{{ $min }}{{ $max }}">{{ $data->$key }}</td>
