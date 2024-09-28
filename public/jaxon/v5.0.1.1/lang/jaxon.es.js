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
        warning: 'ALERTA: ',
        error: 'ERROR: ',
        heading: 'MENSAJE DE DEPURACION JAXON:\n',
        request: {
            uri: 'URI: ',
            init: 'INICIALIZANDO PETICION',
            creating: 'INICIALIZANDO PETICION DEL OBJETO',
            starting: 'INICIANDO PETICION JAXON',
            preparing: 'PREPARANDO PETICION',
            calling: 'LLAMADA: ',
            sending: 'ENVIANDO PETICION',
            sent: 'ENVIADO [{length} bytes]'
        },
        response: {
            long: '...\n[RESPUESTA LARGA]\n...',
            success: 'RECIBIDO [status: {status}, tamaño: {length} bytes, tiempo: {duration}ms]:\n',
            content: 'El servidor retorno el siguiente estado HTTP: {status}\nRECIBIDO:\n{text}',
            redirect: 'El servidor retorno una redireccion a:<br />{location}',
            no_processor: 'Ningun procesador de respuesta esta disponible para tratar la respuesta del servidor.\n',
            check_errors: '.\nRevisa mensajes de error del servidor.'
        },
        processing: {
            parameters: 'PROCESANDO PARAMETROS [{count}]',
            no_parameters: 'NO HAY PARAMETROS QUE PROCESAR',
            calling: 'INICIANDO LLAMADA JAXON (En desuso: use jaxon.request)',
            calling: 'LLAMADA JAXON ({cmd}, {options})',
            done: 'HECHO [{duration}ms]'
        }
    };
     
    jaxon.debug.exceptions = [];
    jaxon.debug.exceptions[10001] = 'Respuesta XML invalida: La respuesta contiene una etiqueta desconocida: {data}.';
    jaxon.debug.exceptions[10002] = 'GetRequestObject: XMLHttpRequest no disponible, jaxon esta deshabilitado.';
    jaxon.debug.exceptions[10003] = 'Queue overflow: No se puede colocar objeto en cola porque esta llena.';
    jaxon.debug.exceptions[10004] = 'Respuesta XML invalida: La respuesta contiene una etiqueta o texto inesperado: {data}.';
    jaxon.debug.exceptions[10005] = 'Solicitud URI invalida: URI invalida o perdida; autodeteccion fallida; por favor especifica una explicitamente.';
    jaxon.debug.exceptions[10006] = 'Comando de respuesta invalido: Orden de respuesta mal formado recibido.';
    jaxon.debug.exceptions[10007] = 'Comando de respuesta invalido: Comando [{data}] no es un comando conocido.';
    jaxon.debug.exceptions[10008] = 'Elemento con ID [{data}] no encontrado en el documento.';
    jaxon.debug.exceptions[10009] = 'Respuesta invalida: Nombre parametro de funcion perdido.';
    jaxon.debug.exceptions[10010] = 'Respuesta invalida: Objeto parametro de funcion perdido.';

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
                window.status = 'Enviando Peticion...';
            },
            onWaiting: function() {
                window.status = 'Esperando Respuesta...';
            },
            onProcessing: function() {
                window.status = 'Procesando...';
            },
            onComplete: function() {
                window.status = 'Hecho.';
            }
        }
    }
}
