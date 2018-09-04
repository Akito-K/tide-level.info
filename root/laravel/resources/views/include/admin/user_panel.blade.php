
<div class="user-panel">
    <div class="pull-left image account-icon account-icon--01" style="background-image: url({!! \Func::myIcon() !!});"></div>
    <div class="pull-left info">
        <p>{!! \Auth::user()->name?: \Auth::user()->sei.\Auth::user()->mei.'さん' !!}</p>
        <!-- Status -->
        <a href="/mimamori/admin/staff/{!! \Auth::user()->hashed_id !!}/detail"><i class="fa fa-circle text-success"></i> Online</a>
    </div>
</div>
