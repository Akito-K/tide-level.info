'use strict';
import $ = require("jquery");
import Holiday from './modules/holiday';
import Calendar from './modules/calendar';
import GoogleMap from './modules/googlemap';
import Search from './modules/search';


$(() => {
    // 休日カレンダー
    const HOLIDAY = new Holiday();
    // カレンダー
    const CALENDAR = new Calendar();

    const GOOGLEMAP = new GoogleMap();
    const SEARCH = new Search(GOOGLEMAP);
});