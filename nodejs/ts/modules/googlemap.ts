'use strict';
import $ = require("jquery");
import Func from './func';
import GoogleMapsLoader = require('google-maps'); // only for common js environments
GoogleMapsLoader.KEY = 'AIzaSyAMNoR8w0_H9j5IW6qXwM7K53WmEPKt-pk';

export class GoogleMap {

    public mapPosition = {lat: 34.65, lng: 134.98333333333332 };
    public lightStyle = [];
    public darkStyle = [
        {
            "elementType": "geometry",
            "stylers": [
                {
                    "color": "#242f3e"
                }
            ]
        },
        {
            "elementType": "labels.text.fill",
            "stylers": [
                {
                    "color": "#746855"
                }
            ]
        },
        {
            "elementType": "labels.text.stroke",
            "stylers": [
                {
                    "color": "#242f3e"
                }
            ]
        },
        {
            "featureType": "administrative.locality",
            "elementType": "labels.text.fill",
            "stylers": [
                {
                    "color": "#d59563"
                }
            ]
        },
        {
            "featureType": "poi",
            "elementType": "labels.text.fill",
            "stylers": [
                {
                    "color": "#d59563"
                }
            ]
        },
        {
            "featureType": "poi.park",
            "elementType": "geometry",
            "stylers": [
                {
                    "color": "#263c3f"
                }
            ]
        },
        {
            "featureType": "poi.park",
            "elementType": "labels.text.fill",
            "stylers": [
                {
                    "color": "#6b9a76"
                }
            ]
        },
        {
            "featureType": "road",
            "elementType": "geometry",
            "stylers": [
                {
                    "color": "#38414e"
                }
            ]
        },
        {
            "featureType": "road",
            "elementType": "geometry.stroke",
            "stylers": [
                {
                    "color": "#212a37"
                }
            ]
        },
        {
            "featureType": "road",
            "elementType": "labels.text.fill",
            "stylers": [
                {
                    "color": "#9ca5b3"
                }
            ]
        },
        {
            "featureType": "road.highway",
            "elementType": "geometry",
            "stylers": [
                {
                    "color": "#746855"
                }
            ]
        },
        {
            "featureType": "road.highway",
            "elementType": "geometry.stroke",
            "stylers": [
                {
                    "color": "#1f2835"
                }
            ]
        },
        {
            "featureType": "road.highway",
            "elementType": "labels.text.fill",
            "stylers": [
                {
                    "color": "#f3d19c"
                }
            ]
        },
        {
            "featureType": "transit",
            "elementType": "geometry",
            "stylers": [
                {
                    "color": "#2f3948"
                }
            ]
        },
        {
            "featureType": "transit.station",
            "elementType": "labels.text.fill",
            "stylers": [
                {
                    "color": "#d59563"
                }
            ]
        },
        {
            "featureType": "water",
            "elementType": "geometry",
            "stylers": [
                {
                    "color": "#17263c"
                }
            ]
        },
        {
            "featureType": "water",
            "elementType": "labels.text.fill",
            "stylers": [
                {
                    "color": "#515c6d"
                }
            ]
        },
        {
            "featureType": "water",
            "elementType": "labels.text.stroke",
            "stylers": [
                {
                    "color": "#17263c"
                }
            ]
        }
    ];

    public google;

    constructor(
        private skin: string = 'dark',
        private places = {},
        private markers = [],
        private infoWindows = [],
    ){
        let self = this;

        this.getPlaceDatas();
        GoogleMapsLoader.load(function(google) {
            self.skin = $('#trigChangeSkin').data('skin');
            self.google = google;
            self.getLatLng();
            self.initMap();
        });
    }

    public setSkin(skin){
        this.skin = skin;
    }

    public getPlaceDatas(){
        const places = $('#paramGoogleMap').data('places');
        for( let key in places){
            const place = places[key];
            this.places[ place.id ] = place;
        }
    }

    public getLatLng(){
        this.mapPosition = {
            lat: $('#paramGoogleMap').data('lat'),
            lng: $('#paramGoogleMap').data('lng'),
        };
    }

    public initMap() {
        let self = this;
        const mapArea = document.getElementById('bulletGoogleMap');

        let style = self.darkStyle;
        if(self.skin == 'light'){
            style = self.lightStyle;
        }
        const mapOptions = {
            center: self.mapPosition,
            zoom: 9,
            styles: style,
        };
        const map = new self.google.maps.Map(mapArea, mapOptions);

        for(let key in this.places){
            const place = this.places[key];
            this.addMarker(place, key, self.google, map);
            this.addInfoWindow(place, key, self.google);

            this.markers[key].addListener('click', function() {
                self.infoWindows[key].open(map, self.markers[key]);
            });
        }

    }

    public addMarker(place, key, google, map) {
        const markerOptions = {
            map: map,
            position: {lat: place.lat, lng: place.lng},
            label: {
                text: place.name,
                color: '#ffffff',
                fontFamily: 'sans-serif',
                fontSize: '9px'
            },
        };
        this.markers[key] = new google.maps.Marker(markerOptions);
    }

    public addInfoWindow(place, key, google) {
        const contentString = '<div class="info-window">'+
            '<div class="info-window__btn trigSubmitFromMap" data-place_id="'+ key +'">'+ place.name + '</div>'+
            '</div>';

        this.infoWindows[key] = new google.maps.InfoWindow({
            content: contentString
        });
    }

}
export default GoogleMap;


