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
        warning: 'IKAZ: ',
        error: 'HATA: ',
        heading: 'JAXON DEBUG (HATA AYIKLAMASI) MESAJI:\n',
        request: {
            uri: 'URI: ',
            init: 'ISTEK BASLATILIYOR',
            creating: 'ISTEK NESNESI BASLATILIYOR',
            starting: 'JAXON ISTEGI BASLATILIYOR',
            preparing: 'ISTEK HAZIRLANIYOR',
            calling: 'ÇAGIRILIYOR: ',
            sending: 'ISTEK GÖNDERILIYOR',
            sent: 'GÖNDERILDI [{length} byte]'
        },
        response: {
            long: '...\n[UZUN YANIT]\n...',
            success: 'ALINDI [durum: {status}, boyut: {length} byte, süre: {duration}ms]:\n',
            content: 'Sunucu asagidaki HTTP durumunu gönderdi: {status}\nALINDI:\n{text}',
            redirect: 'Sunucu su adrese yönlendirme istegi gönderdi :<br />{location}',
            no_processor: 'Sunucudan gelen cevabi isleyecek cevap islemcisi yok.\n',
            check_errors: '.\nSunucudan gelen hata mesajlarini kontrol edin.'
        },
        processing: {
            parameters: 'PARAMETRELER ISLENIYOR [{count}]',
            no_parameters: 'ISLENECEK PARAMETRE YOK',
            calling: 'JAXON ÇAGRISI BASLATILIYOR (kullanimi tavsiye edilmiyor: yerine jaxon.request kullanin)',
            calling: 'JAXON BASLATILIYOR ({cmd}, {options})',
            done: 'TAMAMLANDI [{duration}ms]'
        }
    };
    
    /*
        Array: exceptions
    */
    jaxon.debug.exceptions = [];
    jaxon.debug.exceptions[10001] = 'Geçersiz XML cevabi: Cevap bilinmeyen bir etiket tasiyor: {data}.';
    jaxon.debug.exceptions[10002] = 'GetRequestObject: XMLHttpRequest hazir degil, jaxon nesnesi etkisizlestirildi.';
    jaxon.debug.exceptions[10003] = 'Islem kuyrugu fazla yüklendi: Kuyruk dolu oldugu için nesne kuyruga eklenemiyor.';
    jaxon.debug.exceptions[10004] = 'Geçersiz XML cevabi: Cevap bilinmeyen bir etiket veya metin tasiyor: {data}.';
    jaxon.debug.exceptions[10005] = 'Geçersiz istek URI: Geçersiz veya kayip URI; otomatik tespit yapilamadi; lütfen açikça bir tane belirleyiniz.';
    jaxon.debug.exceptions[10006] = 'Geçersiz cevap komutu: Bozulmus cevap komutu alindi.';
    jaxon.debug.exceptions[10007] = 'Geçersiz cevap komutu: [{data}] komutu bilinmiyor.';
    jaxon.debug.exceptions[10008] = '[{data}] ID li element dosya içinde bulunamadi.';
    jaxon.debug.exceptions[10009] = 'Geçersiz istek: Fonksiyon isim parametresi eksik.';
    jaxon.debug.exceptions[10010] = 'Geçersiz istek: Fonksiyon nesne parametresi eksik.';

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
                window.status = 'İstek Gönderiliyor...';
            },
            onWaiting: function() {
                window.status = 'Cevap Bekleniyor...';
            },
            onProcessing: function() {
                window.status = 'İşlem Devam Ediyor...';
            },
            onComplete: function() {
                window.status = 'Tamamlandı.';
            }
        }
    }
}
