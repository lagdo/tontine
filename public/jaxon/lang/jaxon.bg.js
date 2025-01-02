/**
 * translation for: jaxon v.x.x
 * @version: 1.0.0
 * @author: mic <info@joomx.com>
 * @copyright jaxon project
 * @license GNU/GPL
 * @package jaxon x.x.x
 * @since v.x.x.x
 * save as UTF-8
 */

if ('undefined' != typeof jaxon.debug) {
    /*
        Array: text
    */
    jaxon.debug.messages = {
        warning: 'ПРЕДУПРЕЖДЕНИЕ: ',
        error: 'ГРЕШКА: ',
        heading: 'JAXON ДЕБЪГ СЪОБЩЕНИЕ:\n',
        request: {
            uri: 'Адрес: ',
            init: 'ИНИЦИАЛИЗИРАНЕ НА ЗАЯВКАТА',
            creating: 'ИНИЦИАЛИЗИРАНЕ НА ОБЕКТА НА ЗАЯВКАТА',
            starting: 'СТАРТИРАНЕ НА JAXON ЗАЯВКАТА',
            preparing: 'ПОДГОТВЯВАНЕ НА ЗАЯВКАТА',
            calling: 'ИЗВИКВАНЕ: ',
            sending: 'ИЗПРАЩАНЕ НА ЗАЯВКИ',
            sent: 'ИЗПРАТЕНИ [{length} байта]'
        },
        response: {
            long: '...\n[ДЪЛЪГ ОТГОВОР]\n...',
            success: 'ПОЛУЧЕНИ [статус: {status}, размер: {length} байта, време: {duration}мсек]:\n',
            content: 'Сървъра върна следния HTTP статус: {status}\nПОЛУЧЕНИ:\n{text}',
            redirect: 'Сървъра върна пренасочване към:<br />{location}',
            no_processor: 'Няма регистрирани функции, които да обработят заявката ви на сървъра!\n',
            check_errors: '.\nПровери за съобщения за грешки на сървъра.'
        },
        processing: {
            parameters: 'ОБРАБОТВАНЕ НА ПАРАМЕТРИТЕ [{count}]',
            no_parameters: 'НЯМА ПАРАМЕТРИ ЗА ОБРАБОТВАНЕ',
            calling: 'СТАРТИРАНЕ НА JAXON ПОВИКВАНЕТО (остаряло: вместо това използвай jaxon.request)',
            calling: 'JAXON ПОВИКВАНЕТО ({cmd}, {options})',
            done: 'ГОТОВО [{duration}мсек]'
        }
    };
     
    jaxon.debug.exceptions = [];
    jaxon.debug.exceptions[10001] = 'Невалиден XML отговор: Отговора съдържа непознат таг: {data}.';
    jaxon.debug.exceptions[10002] = 'GetRequestObject: Няма XMLHttpRequest, jaxon е изключен.';
    jaxon.debug.exceptions[10003] = 'Препълване на опашката: Обекта не може да бъде сложен на опашката, защото тя е пълна.';
    jaxon.debug.exceptions[10004] = 'Невалиден XML отговор: Отговора съдържа неочакван таг или текст: {data}.';
    jaxon.debug.exceptions[10005] = 'Невалиден адрес: Невалиден или липсващ адрес; автоматичното откриване неуспешнп; please specify a one explicitly.';
    jaxon.debug.exceptions[10006] = 'Невалидна команда в отговора: Получена беше невалидна команда като отговор.';
    jaxon.debug.exceptions[10007] = 'Невалидна команда в отговора: Командата [{data}] е непозната.';
    jaxon.debug.exceptions[10008] = 'Елемент с ID [{data}] не беше намерен в документа.';
    jaxon.debug.exceptions[10009] = 'Невалидна заявка: Параметъра с името на функцията липсва.';
    jaxon.debug.exceptions[10010] = 'Невалидна заявка: Липсва обекта на функцията.';

    jaxon.debug.lang = {
        isLoaded: true
    };
}
             
if (typeof jaxon.config != 'undefined' && typeof jaxon.config.status != 'undefined') {
    /*
        Object: update
    */
    jaxon.config.status.update = function() {
        return {
            onRequest: function() {
                window.status = 'Изпращане на заявка...';
            },
            onWaiting: function() {
                window.status = 'Изчакване на отговор...';
            },
            onProcessing: function() {
                window.status = 'Изпълнение...';
            },
            onComplete: function() {
                window.status = 'Готово.';
            }
        }
    }
}