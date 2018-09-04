
<li class="dropdown user user-menu">
    <!-- Menu Toggle Button -->
    <a href="#" class="dropdown-toggle header__account__wrap" data-toggle="dropdown">
        <!-- The user image in the navbar-->
        <span class="account-icon account-icon--02 header__account__child" style="background-image: url({!! \Func::myIcon() !!});"></span>
        <!-- hidden-xs hides the username on small devices so only the image appears. -->
        <span class="hidden-xs header__account__child">{!! \Auth::user()->name?: \Auth::user()->sei.\Auth::user()->mei.'さん' !!}</span>
    </a>
    <ul class="dropdown-menu">
        <!-- The user image in the menu -->
        <li class="user-header">
            <span class="account-icon account-icon--03" style="background-image: url({!! \Func::myIcon() !!});"></span>
            <p>
                {!! \Auth::user()->name?: \Auth::user()->sei.\Auth::user()->mei.'さん' !!}
            </p>
        </li>
        <!-- Menu Footer-->
        <li class="user-footer">
            <div class="pull-left">
                <a href="/mimamori/admin/staff/{!! \Auth::user()->hashed_id !!}/detail" class="btn btn-default btn-flat">プロフィール</a>
            </div>
            <div class="pull-right">
                {!! \MyHTML::logout('ログアウト', 'btn btn-default btn-flat', 'logout-form2') !!}
            </div>
        </li>
    </ul>
</li>
