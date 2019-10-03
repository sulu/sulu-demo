import $ from 'jquery';

export default class CookieBox {
    initialize($el) {
        this.$el = $el;
        this.acceptButton = $('#' + this.$el.attr('id') + '-button');
        this.cookieName = 'cookies-accepted';

        let cookie = this.getCookie(this.cookieName);
        if (!cookie) {
            this.$el.addClass('cookie--enable');
        }

        this.acceptButton.click(this.accept.bind(this));
    }

    accept() {
        this.setCookie(this.cookieName, 'yes', 365);
        this.$el.removeClass('cookie--enable');
    }

    setCookie(name, value, expireDays) {
        const date = new Date();
        date.setTime(date.getTime() + expireDays * 1000 * 60 * 60 * 24);

        let expires = 'expires=' + date.toUTCString();
        document.cookie = name + '=' + value + ';' + expires + ';path=/';
    }

    getCookie(name) {
        name = name + '=';
        const decodedCookie = decodeURIComponent(document.cookie);
        const cookies = decodedCookie.split(';');

        for (let i = 0; i < cookies.length; i++) {
            let cookie = cookies[i];
            while (cookie.charAt(0) === ' ') {
                cookie = cookie.substring(1);
            }
            if (cookie.indexOf(name) === 0) {
                return cookie.substring(name.length, cookie.length);
            }
        }

        return '';
    }
}
