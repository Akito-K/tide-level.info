<li class="navi__list navi__list--child"><a href="{{ url('') }}">日本の潮位</a></li>
<li class="navi__list navi__list--child"><a href="{{ url('') }}/admin/pagemeta">サイトメタ</a></li>
<li class="navi__list navi__list--child"><a href="{{ route('admin.tide.list', ['year' => 2020]) }}">データリスト</a></li>
<li class="navi__list navi__list--logout">{!! \MyHTML::logout('<i class="fa fa-sign-out"></i> ログアウト') !!}</li>
