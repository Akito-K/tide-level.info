  var mymap;
  var infoWin = new InfoBox({maxWidth:300});
  var styles;

  var stylesDark = [
    {
      "stylers": [
        { "gamma": 0.34 },
        { "saturation": -41 },
        { "lightness": -64 }
      ]
    },{
      "elementType": "labels.text.fill",
      "stylers": [
        { "color": "#bac5d3" }
      ]
    },{
      "featureType": "road",
      "elementType": "labels.icon",
      "stylers": [
        { "color": "#ddebf2" }
      ]
    }
  ];
  var stylesLight = [{}];
  var bodySkin = $('body').attr("class");
  if(bodySkin == "skin-dark"){
    styles = stylesDark;
  }else{
    styles = stylesLight;
  }

  // googleMap Style
  // http://gmaps-samples-v3.googlecode.com/svn/trunk/styledmaps/wizard/index.html

  function initialize() {
    // 初期ズーム値・中心位置・地図タイプ指定の配列
    var mopt = {zoom: 9, center: new google.maps.LatLng(centerLat, centerLng), mapTypeId: google.maps.MapTypeId.ROADMAP, styles: styles};
  //  console.log(mopt);
    // 地図表示の実行
    mymap = new google.maps.Map(
      document.getElementById('block-googlemap'),
      mopt
    );
    // クリックイベントの設定
    google.maps.event.addListener(mymap, 'click', function(){
      infoWin.close();
    });
    // マーカークラスターの作成
    var mcOptions = {
      gridSize : 50,
      maxZoom : 5
    };
    clusterer = new MarkerClusterer(mymap, [], mcOptions);
    // 全てのマーカーをカテゴリごとに保持する
    var markers = {};
    // マーカーを作成
    var locLen = locations.length;
    var latLng;
    for (var i = 0; i < locLen; i++) {
      var data = locations[i];
      if(data){
        var loclat = data.lat;
        var loclng = data.lng;
        latLng = new google.maps.LatLng(loclat, loclng);
        var category = data.category;
        if ( category in markers == false) {
          markers[category] = [];
        }
        var html = '<div class="infoCont"><p class="gMapTit"><a href="/tide/?search_place=' + data.id + '">'+data.title+'</a></p></div>';
        // マーカーを地図上に配置
        var marker = createMarker({
          position : latLng,
          title : data.title,
          icon: data.icon,
          description : html,
          optimized:false
        });
        markers[category].push(marker);
      }
    }
    //カテゴリの選択が変更されたとき、クラスターを作り直す
    $(".mapIconArea").click(function(){
      var mia = $(this).attr("id");
      var mapnum = $(this).attr("data-mapnum");
      var view = $(this).attr("data-view");
      if(view == 1){
        $(this).attr("data-view", "2").addClass("check");
      }else if(view == 2){
        $(this).attr("data-view", "1").removeClass("check");
      }
      var viewArray = new Array();
      var miaLen = $(".mapIconArea").length;
      for(var i=0; i<miaLen; i++){
        if($(".mia"+i).attr("data-view") == 1){
          viewArray.push($(".mia"+i).attr("data-mapnum"));
        }
      }
      var select = document.getElementById(mia);
      clusterer.clearMarkers();
      for(var i=0; i<viewArray.length; i++){
        var selected = viewArray[i];
        if(markers[selected]){
          clusterer.addMarkers(markers[selected]);
        }
      }
    });
    google.maps.event.addDomListener(document, "load", function() {
      clusterer.addMarkers(markers[1]);
    });
    google.maps.event.trigger(document, "load");
  }

  function createMarker(options) {
    var marker = new google.maps.Marker(options);
    google.maps.event.addListener(marker, "click", function(){
      infoWin.setContent(options.description);
      infoWin.open(mymap, marker);
    });
    return marker;
  }

  google.maps.event.addDomListener(window, "load", initialize);
