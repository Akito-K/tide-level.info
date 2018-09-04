<?php $unread_count = \Func::getUnreadMessageCount(); ?>

<li class="dropdown messages-menu">
    <!-- Menu toggle button -->
    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
        <i class="fa fa-envelope-o"></i>
        <span class="label label-success bulletUnreadCount">{!! $unread_count !!}</span>
    </a>
    <ul class="dropdown-menu">
        <li class="header elm-push-message"><span class="bulletUnreadCount">{!! $unread_count !!}</span>件の未開封メッセージがあります</li>
        <li class="footer"><a href="/mimamori/admin/boards">コンタクトボードへ</a></li>
    </ul>
</li><!-- /.messages-menu -->