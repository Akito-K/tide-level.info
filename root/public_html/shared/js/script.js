var bodyClass;
// jQuery
$(function(){
// body の class を取得しておく
  bodyClass = $('body').attr("class");

  // navicon 開閉
  $('.elm-navicon').click(function(){
    var navOpen = Number( $(this).attr("data-open") );
    if(navOpen){
      $('.block-navi').slideUp();
      $(this).attr("data-open", 0);
    }else{
      $('.block-navi').slideDown();
      $(this).attr("data-open", 1);
    }
  })
// ページ内スムーススクロール
  $('a[href^=#]').click(function(){
    var href = $(this).attr("href");
    if(href != "#"){
      var target = $(href == "#" || href == "" ? 'html' : href);
      var position = target.offset().top;
      console.log(target, position);
      if( $(this).attr("data-no-anime") == "1"){
        var speed = 0;
        $("html, body").animate({scrollTop:position}, speed, "linear");
      }else{
        var speed = 1000;
        $("html, body").animate({scrollTop:position}, speed, "swing");
      }
    }
    return false;
  });
  // カレンダー開閉
  $('#search-date').click(function(){
    var calOpen = Number($(this).attr("data-open"));
    if(calOpen){
      $('#calendar').hide();
      $(this).attr("data-open", 0);
    }else{
      $('#calendar').show();
      $(this).attr("data-open", 1);
    }
  });
  // カレンダー欄外クリックで閉じる
  $('#site-wrapper').click(function(){
    if( ( event.target != $('#search-date')[0]) && !$.contains(event.target, $('.tbl-phpCalendar')[0])){
      // カレンダー消す
      $('#calendar').hide();
      $('#search-date').attr("data-open", 0);
    }
  });
  // 日付を取得・代入
  $(document).on("click", "#calendar td", function(){
    var y = $(this).data("y");
    var m = $(this).data("m");
    var d = $(this).data("d");
    // 値を挿入
    $('#val_search_date_y').val(y);
    $('#val_search_date_m').val(m);
    $('#val_search_date_d').val(d);
//    console.log(mydateFormat(y,m,d));
    $('#search-date').html("20"+mydateFormat(y,m,d));
    // カレンダー消す
    $('#calendar').hide();
    $('#search-date').attr("data-open", 0);
  });
  // カレンダー月切替
  $(document).on("click", ".elm-to-other-month", function(){
    var D = new Object();
    D.script = "change_calendar";
    D.y = $(this).attr("data-y");
    D.m = $(this).attr("data-m");
    $.ajax({
      type: "POST",
      url: "/js/script.js.php",
      data: D,
      cache: false,
      success: function(data){
        $('#calendar').html(data);
      }
    });
  });
  // カラースキンを変更
  $('#btn-change-skin').click(function(){
    var skin = $(this).attr("data-skin");
    if(skin == "dark"){
      $('body').attr("class", "skin-light");
      $(this).attr("data-skin", "light");
      $(this).children('.fa').attr("class", "fa fa-sun-o");
      styles = stylesLight;
      initialize();
    }else{
      $('body').attr("class", "skin-dark");
      $(this).attr("data-skin", "dark");
      $(this).children('.fa').attr("class", "fa fa-moon-o");
      styles = stylesDark;
      initialize();
    }
    saveColorSkin(skin);
    return false;
  });
  // エリア選択で地点選択肢入れ替え
  $('#search_area').change(function(){
    var areaId = $('#search_area option:selected').val();
    changeSelectPlaceOptions(areaId);
  });


  // .showAry
  $('.showAry').draggable();

});


// エリアから地点選択肢入れ替え
function changeSelectPlaceOptions(areaId){
  var D = new Object();
  D.script = "change_select_place_option";
  D.area_id = areaId;
  $.ajax({
    type: "POST",
    url: "/js/script.js.php",
    data: D,
    cache: false,
    success: function(data){
//      console.log(data);
      $('#search_place').html(data);
    }
  });

}
// スキンを保存
function saveColorSkin(skin){
  var D = new Object();
  D.script = "save_skin";
  D.skin = skin;
  $.ajax({
    type: "POST",
    url: "/js/script.js.php",
    data: D,
    cache: false,
    success: function(data){
      console.log(data);
    }
  });
}
// 日付選択用
function mydateFormat(y,m,d){
  return sprintf("%01d", y)+"年"+sprintf("%01d", m)+"月"+sprintf("%01d", d)+"日";
}
// sprintf
var sprintf = function (str) {
  var args = Array.prototype.slice.call(arguments, 1);
  return str.replace(/%0(\d+)d/g, function(m, num) {
    var r = String(args.shift());
    var c = '';
    num = parseInt(num) - r.length;
    while (--num >= 0) c += '0';
    return c + r;
    }).replace(/%[sdf]/g, function(m) { return sprintf._SPRINTF_HASH[m](args.shift()) });
};
sprintf._SPRINTF_HASH = {
  '%s': String,
  '%d': parseInt,
  '%f': parseFloat
};
