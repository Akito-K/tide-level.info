'use strict';
import Config from './config';
import $ = require("jquery");

namespace Page {

    export class Info {
        private bodyClass: string = 'dummy';
        private window: {width: number, height: number} = {width: 0, height: 0};

        constructor() {
            this.set();
        }

        public set(): void {
            this.bodyClass = $('body').attr("class");
            this.window.width = $(window).width();
            this.window.height = $(window).height();
        }
    }

    export class aTag {
        public el: any;
        public href: string = '';
        public disabled: boolean = false;

        constructor() {
        }

        public set(el): void {
            this.el = el;
            this.href = this.el.attr("href");
            this.disabled = this.el.attr("disabled") == "disabled";
        }

        public smoothScroll(): boolean {
            if(this.href != '#'){
                if(this.href != '#' && this.href.match(/^#/)){
                    var target = $(this.href == "#" || this.href == "" ? 'html' : this.href);
                    var position = target.offset().top;
                    if( this.el.attr("data-no-anime") == "1"){
                        var speed = 0;
                        $("html, body").animate({scrollTop:position}, speed, "linear");
                    }else{
                        var speed = 500;
                        $("html, body").animate({scrollTop:position}, speed, "swing");
                    }

                    console.log("smoothScroll");
                    return false;
                }else {
                    return true;
                }
            }else{
                return false;
            }
        }

    }

}
export default Page;