'use strict';
import $ = require("jquery");
import Func from './func';

import Holiday from './holiday';
import Calendar from './calendar';
import Upload from './upload';
//import Model from './model';

$(() => {
//    Func.hoge();

    // 休日カレンダー
    const HOLIDAY = new Holiday.calendar();
    // カレンダー
    const CALENDAR = new Calendar.MyCalendar();
    // ドラッグでアップロード
    const UPLOAD = new Upload.MyUpload();
});