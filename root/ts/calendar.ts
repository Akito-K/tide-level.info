'use strict';
import Config from './config';
import Func from './func';
import $ = require("jquery");
import calJS = require("cal.js");

namespace Calendar {
    export class MyCalendar {

        private el: any;
        private target: string;
        private date: any;
        private year: number;
        private month: number;
        private day: number;

        constructor(
            private calendar = {year: 0, month: 0, day: 0},
            ){
            const today = new Date();
            this.year = Number(today.getFullYear());
            this.month = Number(today.getMonth()) + 1;
            this.day = Number(today.getDate());

            let self = this;
            // 日付枠クリックでカレンダーを開く
            $('.trigShowCalendar').click( function(e){
                self.el = $(this);
                self.showCalendar(e);
            });

            // ×クリックでカレンダーを閉じる
            $('.trigHideCalendar').click( () => {
                this.hideCalendar();
            });

            // カレンダーの日付クリック
            $('#calendar').on("click", "td", function(){
                self.el = $(this);

                const target = $('#calendar').attr("data-target");
                self.calendar.year = Number( $(this).attr("data-year") );
                self.calendar.month = Number( $(this).attr("data-month") );
                self.calendar.day = Number( $(this).attr("data-day") );
                self.enterDate(target);
            });

            // カレンダーの前月
            $('.trigPrevMonth').click( () => {
                this.month--;
                if(this.month === 0){
                    this.month = 12;
                    this.year--;
                }

                this.refreshCalendarBody();
            });

            // カレンダーの次月
            $('.trigNextMonth').click( () => {
                this.month++;
                if(this.month === 13){
                    this.month = 1;
                    this.year++;
                }

                this.refreshCalendarBody();
            });

            // カレンダーの年月変更
            $('.trigSelectYearMonth').change( () => {
                this.year  = Number( $('#calendar_year').val() );
                this.month = Number( $('#calendar_month').val() );

                this.refreshCalendarBody();
            });

            // 日付枠のテキスト変更でカレンダーを更新
            $('.trigShowCalendar input').keyup( function() {
                const vals = self.getDateInputs( $(this) );
                self.year  = vals.year;
                self.month  = vals.month;
                self.day  = vals.day;
                $('#calendar_year').val(self.year);
                $('#calendar_month').val(self.month);
                self.refreshCalendarBody();

                const id = $(this).attr("id");
                $('#hide_' + id).val( $(this).val() );
            });

        }

        public getCurrentDay(): number {
            let day = 0;
            const vals = this.getDateInputs( $('#' + this.target + '_at') );
            if(this.matchDates( vals )){
                day = this.day = vals.day;
            }

            return day;
        }

        // カレンダーと日付入力欄の比較
        public matchDates(args): boolean {
            return (args.year === this.year && args.month === this.month);
        }

        // 日付欄の入力値を取得
        public getDateInputs( el ): any {
            const val = el.val();
            let vals;
            if(val){
                vals = el.val().split('/');
            }else{
                vals = [0, 0, this.day];
            }
            const args = {
                year: Number(vals[0]),
                month: Number(vals[1]),
                day: Number(vals[2]),
            };

            return args;
        }


        // カレンダーを表示・非表示セット
        public showCalendar(e): void {
            let hide: boolean = false;
            if( $('.bulletCalendar').attr("data-show") !== "1" ){
                hide = false;
            }else if( this.target === this.el.data("calendar") ){
                hide = true;
            }else{
                hide = false;
            }

            if( hide ){
                $('.bulletCalendar').hide().attr("data-show", "0");
            }else{
                const input = this.el.children('input');
                const vals = input.val().split('/');
                if(vals[0] !== ""){
                    this.year = Number(vals[0]);
                    this.month = Number(vals[1]);
                    $('#calendar_year').val(this.year);
                    $('#calendar_month').val(this.month);
                }else{
                    this.year  = Number( $('#calendar_year').val() );
                    this.month = Number( $('#calendar_month').val() );
                }
                this.target = this.el.data("calendar");
                this.refreshCalendarBody();

                let x = e.offsetX;
                if( $(window).width() - 310 < x ){
                    x = $(window).width() - 310;
                }

                $('.bulletCalendar').slideDown(100).attr("data-show", "1").attr("data-target", this.target).css({left: x, top: e.pageY});

                console.log(e.offsetY, e.pageY, e.clientY, e.screenY);
            }
        }

        // カレンダーの中身を更新
        public refreshCalendarBody(): void {
            const cals = this.myGetCalArr();
            const body = this.HTML(cals);
            $('#calendar-body').html(body);
            $('#calendar_year').val(this.year);
            $('#calendar_month').val(this.month);
        }

        public hideCalendar(): void {
            $('.bulletCalendar').hide().attr("data-show", "0");
        }

        // カレンダーの日付クリックで日付を入れる
        public enterDate(target): void {
            if(this.el.attr("data-null") !== "1"){
                const date = Number(this.el.html());
                const val = this.calendar.year + "/" + this.calendar.month + "/" + date;
                $('#' + target + '_at').val(val);
                $('#hide_' + target + '_at').val(val);
                $('.bulletCalendar').hide().attr("data-show", "0");
                if($('#creates_day').length > 0){
                    if($('#creates_day').data('flag_onchage_submit') == 1){
                        $('#creates_day').submit();
                    }
                }
            }
        }
/*
            // カレンダーの日付クリックで日付を入れる
        public enterDate(): void {
            if(this.date.attr("data-null") !== "1"){
                const date = Number(this.date.html());
                const val = this.year + "/" + this.month + "/" + date;
                $('#date_' + this.target).val(val);
                $('.bulletCalendar').hide().attr("data-show", "0");
            }
        }
*/
        /**
         @return arr {
            {  0,  0,  1,  2,  3,  4,  5},
            {  6,  7,  8,  9, 10, 11, 12},
            { 13, 14, 15, 16, 17, 18, 19},
            { 20, 21, 22, 23, 24, 25, 26},
            { 27, 28, 29, 30, 31,  0,  0},
            {  0,  0,  0,  0,  0,  0,  0},
         }
        */
        public myGetCalArr(): any {
            const calendar = new calJS( {year: this.year, month: this.month} );
            const cal = calendar.getCalArr();
            let cals = [];
            const self = this;
            Object.keys( cal ).forEach( function(key){
                const newKey = Math.floor(Number(key) / 7);
                if( Number(key)%7 == 0 ){
                    cals[ newKey ] = [];
                }

                if(cal[key].month == self.month - 1){
                    cals[ newKey ].push( cal[key].date );
                }else{
                    cals[ newKey ].push( 0 );
                }
            });

            return cals;
        }

        // カレンダーの日付 HTML 生成
        public HTML(cals): string {
            const day = this.getCurrentDay();
            let body = "";
            const self = this;
            Object.keys( cals ).forEach( function( i ){
                if( Func.sum(cals[i]) > 0){
                    body += '<tr>';
                    Object.keys( cals[i] ).forEach( function( wday ){
                        if( Number(cals[i][wday]) > 0 ){
                            const sel = (day === cals[i][wday])? " selected": "";
                            //console.log(day, self.year, self.month);
                            body += '<td class="wday' + wday + sel + '" data-year="' + self.year + '" data-month="' + self.month + '" data-day="' + cals[i][wday] + '">' + cals[i][wday] + '</td>';
                        }else{
                            body += '<td class="wday' + wday + '" data-null="1"></td>';
                        }
                    });
                    body += '</tr>';
                }
            });

            return body;
        }



    }

}
export default Calendar;