'use strict';
import $ = require("jquery");
import Holiday from './modules/holiday';
import Calendar from './modules/calendar';
import Tide from './modules/tide';

$(() => {
    // 休日カレンダー
    const HOLIDAY = new Holiday();
    // カレンダー
    const CALENDAR = new Calendar();
    // 潮位データ
    const TIDE = new Tide();
});