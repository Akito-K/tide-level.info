@extends('layouts.common')
@section('content')

    <h2>{{ $place_data->name }}の潮位情報</h2>

    <section class="tide">
        <article class="tide__simple">
            <section class="tide__simple__box">
                <table>
                    @include('include.common.simple')
                </table>
            </section>
        </article>

        <article class="tide__detail">
            <section class="tide__detail__box">
                <table>
                    @include('include.common.details')
                </table>
            </section>
        </article>
    </section>

    @include('include.common.search')

    <!-- google map api -->
    <section id="paramGoogleMap" class="map" data-lat="{{ \Func::mylatlng_format($place_data->lat) }}" data-lng="{{ \Func::mylatlng_format($place_data->lng) }}" data-places='{!! $json_place_datas !!}'>
        <div id="bulletGoogleMap" class="map__box">
        </div>
    </section>

@endsection
