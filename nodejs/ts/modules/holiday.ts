'use strict';
import $ = require("jquery");

export class Holiday {
    public el: any;

    constructor(){

        const self = this;

        // カレンダーの日付をクリックして休日設定
        $('.calendar__body__table td').click(function(){
            self.el = this;
            self.setHoliday();
        });

        // ○曜日をまとめて休日設定にする
        $('.trigSetHolidayWeekly').click( function(){
            self.el = this;
            self.setWeeks();
        });



    }

    public setHoliday(): void {
        if( $(this.el).attr("data-thismonth") === "1" ){
            const flag = $(this.el).attr("data-holiday");
            let dataHoliday: string = "";
            if(flag === ""){
                dataHoliday = "both";
            }else if(flag === "both"){
                dataHoliday = "desk";
            }else if(flag === "desk"){
                dataHoliday = "factory";
            }else if(flag === "factory"){
                dataHoliday = "";
            }
            $(this.el).attr("data-holiday", dataHoliday);
            $(this.el).children("input.input--holidays").val(dataHoliday);
        }
    }

    public setWeeks(): void {
        const flag = $(this.el).data("flag");
        const wnum = $('#select-summary').val();
        $('td.wday-'+wnum).attr("data-holiday", flag);
        $('td.wday-'+wnum+' input.input--holidays').val(flag);
    }
}
export default Holiday;


