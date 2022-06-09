@section('title',         '')
@section('description',   '')

@extends('layouts.common')
@section('content')

    @include('include.common.search')

    <!-- google map api -->
    <section id="paramGoogleMap" class="map" data-lat="{{ Func::mylatlng_format($place_data->lat) }}" data-lng="{{ Func::mylatlng_format($place_data->lng) }}" data-places='{!! $json_place_datas !!}'>
        <div id="bulletGoogleMap" class="map__box">
        </div>
    </section>

@endsection
