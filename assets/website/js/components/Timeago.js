import $ from 'jquery';
import * as timeagoJS from 'timeago.js';

export default class Timeago {
    initialize = (el, {
        date,
    }) => {
        $(el).html(timeagoJS.format(date, document.documentElement.lang || 'en'));
    };

    static localeEnglish = (number, index) => {
        return [
            ['just now', 'right now'],
            ['%s seconds ago', 'in %s seconds'],
            ['a minute ago', 'in a minute'],
            ['%s minutes ago', 'in %s minutes'],
            ['an hour ago', 'in an hour'],
            ['%s hours ago', 'in %s hours'],
            ['yesterday', 'tomorrow'],
            ['%s days ago', 'in %s days'],
            ['a week ago', 'in a week'],
            ['%s weeks ago', 'in %s weeks'],
            ['a month ago', 'in a month'],
            ['%s months ago', 'in %s months'],
            ['a year ago', 'in a year'],
            ['%s years ago', 'in %s years'],
        ][index];
    };

    static localeGerman = (number, index) => {
        return [
            ['gerade eben', 'genau jetzt'],
            ['vor %s Sekunden', 'in %s Sekunden'],
            ['vor einer Minute', 'in einer Minute'],
            ['vor %s Minuten', 'in %s Minuten'],
            ['vor einer Stunde', 'in einer Stunde'],
            ['vor %s Stunden', 'in %s Stunden'],
            ['gestern', 'morgen'],
            ['vor %s Tagen', 'in %s Tagen'],
            ['vor einer Woche', 'in einer Woche'],
            ['vor %s Wochen', 'in %s Wochen'],
            ['vor einem Monat', 'in einem Monat'],
            ['vor %s Monaten', 'in %s Monaten'],
            ['vor einem Jahr', 'in einem Jahr'],
            ['vor %s Jahren', 'in %s Jahren'],
        ][index];
    };
}

timeagoJS.register('en', Timeago.localeEnglish);
timeagoJS.register('de', Timeago.localeGerman);
