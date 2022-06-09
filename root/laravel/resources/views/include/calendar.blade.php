<div class="calendar bulletCalendar draggable" id="calendar">
    <div class="calendar__block" id="calendar-box">
        <span class="calendar__close trigHideCalendar"><i class="fa fa-close"></i></span>
        <div class="calendar__head">
            <span class="fake-link calendar__head__cell calendar__head__cell--arrow trigPrevMonth"><i class="fa fa-chevron-left"></i></span>
            <div class="calendar__head__cell calendar__head__cell--year">
                <select name="calendar_year" class="trigSelectYearMonth" id="calendar_year">
                    {!! MyForm::selectYear( date("Y") ) !!}
                </select> 年
            </div>
            <div class="calendar__head__cell calendar__head__cell--month">
                <select name="calendar_month" class="trigSelectYearMonth" id="calendar_month">
                    {!! MyForm::selectMonth( date("n") ) !!}
                </select> 月
            </div>
            <span class="fake-link calendar__head__cell calendar__head__cell--arrow trigNextMonth"><i class="fa fa-chevron-right"></i></span>
        </div>
        <div class="calendar__body">
            <table class="calendar__table">
                <thead>
                    <tr>
                        <th class="wday0">日</th>
                        <th class="wday1">月</th>
                        <th class="wday2">火</th>
                        <th class="wday3">水</th>
                        <th class="wday4">木</th>
                        <th class="wday5">金</th>
                        <th class="wday6">土</th>
                    </tr>
                </thead>
                <tbody id="calendar-body"></tbody>
            </table>
        </div>
    </div>
</div>
