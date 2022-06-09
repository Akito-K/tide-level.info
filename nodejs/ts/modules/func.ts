'use strict';
import $ = require("jquery");

class Func {

    static sum( numbers ): number {
        let sum: number = 0;
        numbers.forEach( function(num){
            sum += num;
        })

        return sum;
    }

    static getExtention( filename ): string {
        let types = filename.split('.');

        return types[types.length - 1].toLowerCase();
    }

    static numberFormat( number ): string {
        return number.toString().replace(/([0-9]+?)(?=(?:[0-9]{3})+$)/g , '$1,');
    }

    static unitFormat(number, unit = "B"): string {
        let str = number;
        if(number > 1*1000*1000*1000){
            str = Func.numberFormat( (number/1000/1000/1000).toFixed(2) ) + "G";
        }else if(number > 1*1000*1000){
            str = Func.numberFormat( (number/1000/1000).toFixed(2) ) + "M";
        }else if(number > 1*1000){
            str = Func.numberFormat( (number/1000).toFixed(2) ) + "K";
        }

        return str + unit;
    }

    static inArray(needle, haystack): boolean {
        let result = false;
        Object.keys( haystack ).forEach( function(i){
            if( needle == haystack[ Number(i) ]){
                result = true;
            }
        });
        return result;
    }

    static dateFormat(date, format='Y-m-d H:i:s'): string {
        const year    = date.getFullYear();
        const month   = date.getMonth() + 1;
        const day     = date.getDay();
        const hour    = date.getHours();
        const minutes = date.getMinutes();
        const second  = date.getSeconds();



        return format.replace('Y', year)
                    .replace('m', Func.sprintf(month, 2))
                    .replace('d', Func.sprintf(day, 2))
                    .replace('n', month)
                    .replace('j', day)
                    .replace('H', hour)
                    .replace('i', minutes)
                    .replace('s', second);
    }

    /**
     * @param number
     * @param number
     * @return string
     */
    static sprintf(num, len): string{
        if( String(num).length >= len ){
            return String(num);
        }else{
            const zeros = len - String(num).length;
            let str = "";
            for( let i:number = 0; i<zeros; i++){
                str += "0";
            }
            return str + String(num);
        }
    }

    static smoothScroll(target: number, flagAnimation: boolean): boolean {
        if( flagAnimation === true){
            var speed = 500;
            $("html, body").animate({scrollTop:target}, speed, "swing");
        }else{
            var speed = 0;
            $("html, body").animate({scrollTop:target}, speed, "linear");
        }

        return false;
    }

}
export default Func;
