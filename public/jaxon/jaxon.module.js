/*
    @package jaxon
    @version $Id: jaxon.core.js 327 2007-02-28 16:55:26Z calltoconstruct $
    @copyright Copyright (c) 2005-2007 by Jared White & J. Max Wilson
    @copyright Copyright (c) 2008-2010 by Joseph Woolley, Steffen Konerow, Jared White  & J. Max Wilson
    @copyright Copyright (c) 2017 by Thierry Feuzeu, Joseph Woolley, Steffen Konerow, Jared White  & J. Max Wilson
    @license https://opensource.org/license/bsd-3-clause/ BSD License
*/

/**
 * Class: jaxon
 */
var jaxon = {
    /**
     * Version number
     */
    version: {
        major: '5',
        minor: '0',
        patch: '0-beta.13',
    },

    debug: {
        /**
         * Class: jaxon.debug.verbose
         *
         * Provide a high level of detail which can be used to debug hard to find problems.
         */
        verbose: {},
    },

    ajax: {
        callback: {},
        command: {},
        parameters: {},
        request: {},
        response: {},
    },

    cmd: {
        node: {},
        script: {},
        event: {},
    },

    parser: {
        attr: {},
        call: {},
        query: {},
    },

    utils: {
        dom: {},
        form: {},
        queue: {},
        types: {},
        string: {},
        upload: {},
    },

    dom: {},

    dialog: {
        cmd: {},
        lib: {},
    },

    config: {},
};

/**
 * This object contains all the default configuration settings.
 * These are application level settings; however, they can be overridden by
 * specifying the appropriate configuration options on a per call basis.
 */
(function(self) {
    /**
     * An array of header entries where the array key is the header option name and
     * the associated value is the value that will set when the request object is initialized.
     *
     * These headers will be set for both POST and GET requests.
     */
    self.commonHeaders = {
        'If-Modified-Since': 'Sat, 1 Jan 2000 00:00:00 GMT'
    };

    /**
     * An array of header entries where the array key is the header option name and the
     * associated value is the value that will set when the request object is initialized.
     */
    self.postHeaders = {};

    /**
     * An array of header entries where the array key is the header option name and the
     * associated value is the value that will set when the request object is initialized.
     */
    self.getHeaders = {};

    /**
     * true if jaxon should display a wait cursor when making a request, false otherwise.
     */
    self.waitCursor = false;

    /**
     * true if jaxon should log the status to the console during a request, false otherwise.
     */
    self.statusMessages = false;

    /**
     * The base document that will be used throughout the code for locating elements by ID.
     */
    self.baseDocument = document;

    /**
     * The URI that requests will be sent to.
     *
     * @var {string}
     */
    self.requestURI = document.URL;

    /**
     * The request mode.
     * - 'asynchronous' - The request will immediately return, the response will be processed
     *   when (and if) it is received.
     * - 'synchronous' - The request will block, waiting for the response.
     *   This option allows the server to return a value directly to the caller.
     */
    self.defaultMode = 'asynchronous';

    /**
     * The Hyper Text Transport Protocol version designated in the header of the request.
     */
    self.defaultHttpVersion = 'HTTP/1.1';

    /**
     * The content type designated in the header of the request.
     */
    self.defaultContentType = 'application/x-www-form-urlencoded';

    /**
     * The delay time, in milliseconds, associated with the <jaxon.callback.onRequestDelay> event.
     */
    self.defaultResponseDelayTime = 1000;

    /**
     * Always convert the reponse content to json.
     */
    self.convertResponseToJson = true;

    /**
     * The amount of time to wait, in milliseconds, before a request is considered expired.
     * This is used to trigger the <jaxon.callback.onExpiration event.
     */
    self.defaultExpirationTime = 10000;

    /**
     * The method used to send requests to the server.
     * - 'POST': Generate a form POST request
     * - 'GET': Generate a GET request; parameters are appended to <jaxon.config.requestURI> to form a URL.
     */
    self.defaultMethod = 'POST'; // W3C = Method is case sensitive

    /**
     * The number of times a request should be retried if it expires.
     */
    self.defaultRetry = 5;

    /**
     * The maximum depth of recursion allowed when serializing objects to be sent to the server in a request.
     */
    self.maxObjectDepth = 20;

    /**
     * The maximum number of members allowed when serializing objects to be sent to the server in a request.
     */
    self.maxObjectSize = 2000;

    /**
     * The maximum number of commands allowed in a single response.
     */
    self.commandQueueSize = 1000;

    /**
     * The maximum number of requests that can be processed simultaneously.
     */
    self.requestQueueSize = 1000;

    /**
     * Common options for all HTTP requests to the server.
     */
    self.httpRequestOptions = {
        mode: "cors", // no-cors, *cors, same-origin
        cache: "no-cache", // *default, no-cache, reload, force-cache, only-if-cached
        credentials: "same-origin", // include, *same-origin, omit
        redirect: "manual", // manual, *follow, error
    };

    /**
     * Set the options in the request object
     *
     * @param {object} oRequest The request context object.
     *
     * @returns {void}
     */
    self.setRequestOptions = (oRequest) => {
        if (self.requestURI === undefined) {
            throw { code: 10005 };
        }

        const aHeaders = ['commonHeaders', 'postHeaders', 'getHeaders'];
        aHeaders.forEach(sHeader => oRequest[sHeader] = { ...self[sHeader], ...oRequest[sHeader] });

        const oDefaultOptions = {
            statusMessages: self.statusMessages,
            waitCursor: self.waitCursor,
            mode: self.defaultMode,
            method: self.defaultMethod,
            URI: self.requestURI,
            httpVersion: self.defaultHttpVersion,
            contentType: self.defaultContentType,
            retry: self.defaultRetry,
            maxObjectDepth: self.maxObjectDepth,
            maxObjectSize: self.maxObjectSize,
            upload: false,
            aborted: false,
            response: {
                convertToJson: self.convertResponseToJson,
            },
        };
        Object.keys(oDefaultOptions).forEach(sOption =>
            oRequest[sOption] = oRequest[sOption] ?? oDefaultOptions[sOption]);

        oRequest.method = oRequest.method.toUpperCase();
        if (oRequest.method !== 'GET') {
            oRequest.method = 'POST'; // W3C: Method is case sensitive
        }
        oRequest.requestRetry = oRequest.retry;
    };

    /**
     * Class: jaxon.config.status
     *
     * Provides support for updating the browser's status bar during the request process.
     * By splitting the status bar functionality into an object, the jaxon developer has the opportunity
     * to customize the status bar messages prior to sending jaxon requests.
     */
    self.status = {
        /**
         * A set of event handlers that will be called by the
         * jaxon framework to set the status bar messages.
         *
         * @type {object}
         */
        update: {
            onRequest: () => console.log('Sending Request...'),
            onWaiting: () => console.log('Waiting for Response...'),
            onProcessing: () => console.log('Processing...'),
            onComplete: () => console.log('Done.'),
        },

        /**
         * A set of event handlers that will be called by the
         * jaxon framework where status bar updates would normally occur.
         *
         * @type {object}
         */
        dontUpdate: {
            onRequest: () => {},
            onWaiting: () => {},
            onProcessing: () => {},
            onComplete: () => {}
        },
    };

    /**
     * Class: jaxon.config.cursor
     *
     * Provides the base functionality for updating the browser's cursor during requests.
     * By splitting this functionality into an object of it's own, jaxon developers can now
     * customize the functionality prior to submitting requests.
     */
    self.cursor = {
        /**
         * Constructs and returns a set of event handlers that will be called by the
         * jaxon framework to effect the status of the cursor during requests.
         *
         * @type {object}
         */
        update: {
            onWaiting: () => {
                if (jaxon.config.baseDocument.body) {
                    jaxon.config.baseDocument.body.style.cursor = 'wait';
                }
            },
            onComplete: () => {
                if (jaxon.config.baseDocument.body) {
                    jaxon.config.baseDocument.body.style.cursor = 'auto';
                }
            }
        },

        /**
         * Constructs and returns a set of event handlers that will be called by the jaxon framework
         * where cursor status changes would typically be made during the handling of requests.
         *
         * @type {object}
         */
        dontUpdate: {
            onWaiting: () => {},
            onComplete: () => {}
        },
    };
})(jaxon.config);

// Make jaxon accessible with the dom.findFunction function.
window.jaxon = jaxon;


/**
 * Class: jaxon.utils.dom
 */

(function(self, types, baseDocument) {
    /**
     * Shorthand for finding a uniquely named element within the document.
     *
     * @param {string} sId - The unique name of the element (specified by the ID attribute)
     *
     * @returns {object} The element found or null.
     *
     * @see <self.$>
     */
    self.$ = (sId) => !sId ? null : (types.isString(sId) ? baseDocument.getElementById(sId) : sId);

    /**
     * Create a div as workspace for the getBrowserHTML() function.
     *
     * @returns {object} The workspace DOM element.
     */
    const _getWorkspace = () => {
        const elWorkspace = self.$('jaxon_temp_workspace');
        if (elWorkspace) {
            return elWorkspace;
        }
        // Workspace not found. Must be created.
        const elNewWorkspace = baseDocument.createElement('div');
        elNewWorkspace.setAttribute('id', 'jaxon_temp_workspace');
        elNewWorkspace.style.display = 'none';
        elNewWorkspace.style.visibility = 'hidden';
        baseDocument.body.appendChild(elNewWorkspace);
        return elNewWorkspace;
    };

    /**
     * Insert the specified string of HTML into the document, then extract it.
     * This gives the browser the ability to validate the code and to apply any transformations it deems appropriate.
     *
     * @param {string} sValue A block of html code or text to be inserted into the browser's document.
     *
     * @returns {string} The (potentially modified) html code or text.
     */
    self.getBrowserHTML = (sValue) => {
        const elWorkspace = _getWorkspace();
        elWorkspace.innerHTML = sValue;
        const browserHTML = elWorkspace.innerHTML;
        elWorkspace.innerHTML = '';
        return browserHTML;
    };

    /**
     * Tests to see if the specified data is the same as the current value of the element's attribute.
     *
     * @param {string|object} element The element or it's unique name (specified by the ID attribute)
     * @param {string} attribute The name of the attribute.
     * @param {string} newData The value to be compared with the current value of the specified element.
     *
     * @returns {true} The specified value differs from the current attribute value.
     * @returns {false} The specified value is the same as the current value.
     */
    self.willChange = (element, attribute, newData) => {
        element = self.$(element);
        return !element ? false : (newData != element[attribute]);
    };

    /**
     * Get the value of an attribute of an object.
     * Can also get the value of a var in an array.
     *
     * @param {object} xElement The object with the attribute.
     * @param {string} sAttrName The attribute name.
     *
     * @returns {mixed}
     */
    self.getAttrValue = (xElement, sAttrName) => {
        if((aMatches = sAttrName.match(/^(.+)\[(\d+)\]$/)) === null)
        {
            return xElement[sAttrName];
        }

        // The attribute is an array in the form "var[indice]".
        sAttrName = aMatches[1];
        const nAttrIndice = parseInt(aMatches[2]);
        return xElement[sAttrName][nAttrIndice];
    }

    /**
     * Find a function using its name as a string.
     *
     * @param {string} sFuncName The name of the function to find.
     * @param {object} context
     *
     * @returns {object|null}
     */
    self.findFunction = (sFuncName, context = window) => {
        if (sFuncName === 'toInt' && context === window) {
            return types.toInt;
        }

        const aNames = sFuncName.split(".");
        const nLength = aNames.length;
        for (let i = 0; i < nLength && (context); i++) {
            context = self.getAttrValue(context, aNames[i]);
        }
        return context ?? null;
    };

    /**
     * Given an element and an attribute with 0 or more dots,
     * get the inner object and the corresponding attribute name.
     *
     * @param {string} sAttrName The attribute name.
     * @param {object=} xElement The outer element.
     *
     * @returns {object|null} The inner object and the attribute name in an object.
     */
    self.getInnerObject = (sAttrName, xElement = window) => {
        const aNames = sAttrName.split('.');
        // Get the last element in the array.
        sAttrName = aNames.pop();
        // Move to the inner object.
        const nLength = aNames.length;
        for (let i = 0; i < nLength && (xElement); i++) {
            // The real name for the "css" object is "style".
            const sRealAttrName = aNames[i] === 'css' ? 'style' : aNames[i];
            xElement = self.getAttrValue(xElement, sRealAttrName);
        }
        return !xElement ? null : { node: xElement, attr: sAttrName };
    };
})(jaxon.utils.dom, jaxon.utils.types, jaxon.config.baseDocument);


/**
 * Class: jaxon.utils.form
 */

(function(self, dom) {
    /**
     * @param {object} xOptions
     * @param {object} child
     * @param {string} child.type
     * @param {string} child.name
     * @param {string} child.tagName
     * @param {boolean} child.checked
     * @param {boolean} child.disabled
     * @param {mixed} child.value
     * @param {array} child.options
     *
     * @returns {void}
     */
    const _getValue = (xOptions, { type, name, tagName, checked, disabled, value, options }) => {
        if (!name || 'PARAM' === tagName)
            return;
        if (!xOptions.disabled && disabled)
            return;
        const { prefix } = xOptions;
        if (prefix.length > 0 && prefix !== name.substring(0, prefix.length))
            return;
        if ((type === 'radio' || type === 'checkbox') && !checked)
            return;
        if (type === 'file')
            return;

        const values = type !== 'select-multiple' ? value :
            Array.from(options).filter(({ selected }) => selected).map(({ value: v }) => v);
        const keyBegin = name.indexOf('[');

        if (keyBegin < 0) {
            xOptions.values[name] = values;
            return;
        }

        // Parse names into brackets
        let k = name.substring(0, keyBegin);
        let a = name.substring(keyBegin);
        if (xOptions.values[k] === undefined) {
            xOptions.values[k] = {};
        }
        let p = xOptions.values; // pointer reset
        while (a.length > 0) {
            const sa = a.substring(0, a.indexOf(']') + 1);
            const lastKey = k; //save last key
            const lastRef = p; //save last pointer

            a = a.substring(a.indexOf(']') + 1);
            p = p[k];
            k = sa.substring(1, sa.length - 1);
            if (k === '') {
                if ('select-multiple' === type) {
                    k = lastKey; //restore last key
                    p = lastRef;
                } else {
                    k = p.length;
                }
            }
            if (k === undefined) {
                /*check against the global xOptions.values Stack wich is the next(last) usable index */
                k = Object.keys(lastRef[lastKey]).length;
            }
            p[k] = p[k] || {};
        }
        p[k] = values;
    };

    /**
     * @param {object} xOptions
     * @param {array} children
     *
     * @returns {void}
     */
    const _getValues = (xOptions, children) => {
        children.forEach(child => {
            const { childNodes, type } = child;
            if (childNodes !== undefined && type !== 'select-one' && type !== 'select-multiple') {
                _getValues(xOptions, childNodes);
            }
           _getValue(xOptions, child);
        });
    };

    /**
     * Build an associative array of form elements and their values from the specified form.
     *
     * @param {string} formId The unique name (id) of the form to be processed.
     * @param {boolean=false} disabled (optional): Include form elements which are currently disabled.
     * @param {string=''} prefix (optional): A prefix used for selecting form elements.
     *
     * @returns {object} An associative array of form element id and value.
     */
    self.getValues = (formId, disabled = false, prefix = '') => {
        const xOptions = {
            // Submit disabled fields
            disabled: (disabled === true),
            // Only submit fields with a prefix
            prefix: prefix ?? '',
            // Form values
            values: {},
        };

        const form = dom.$(formId);
        if (form && form.childNodes) {
            _getValues(xOptions, form.childNodes);
        }
        return xOptions.values;
    };
})(jaxon.utils.form, jaxon.utils.dom);


/**
 * Class: jaxon.utils.queue
 */

(function(self) {
    /**
     * Construct and return a new queue object.
     *
     * @param {integer} size The number of entries the queue will be able to hold.
     *
     * @returns {object}
     */
    self.create = size => ({
        start: 0,
        count: 0,
        size: size,
        end: 0,
        elements: [],
        paused: false,
    });

    /**
     * Check id a queue is empty.
     *
     * @param {object} oQueue The queue to check.
     *
     * @returns {boolean}
     */
    self.empty = oQueue => oQueue.count <= 0;

    /**
     * Check id a queue is empty.
     *
     * @param {object} oQueue The queue to check.
     *
     * @returns {boolean}
     */
    self.full = oQueue => oQueue.count >= oQueue.size;

    /**
     * Push a new object into the tail of the buffer maintained by the specified queue object.
     *
     * @param {object} oQueue The queue in which you would like the object stored.
     * @param {object} obj    The object you would like stored in the queue.
     *
     * @returns {integer} The number of entries in the queue.
     */
    self.push = (oQueue, obj) => {
        // No push if the queue is full.
        if(self.full(oQueue)) {
            throw { code: 10003 };
        }

        oQueue.elements[oQueue.end] = obj;
        if(++oQueue.end >= oQueue.size) {
            oQueue.end = 0;
        }
        return ++oQueue.count;
    };

    /**
     * Push a new object into the head of the buffer maintained by the specified queue object.
     *
     * This effectively pushes an object to the front of the queue... it will be processed first.
     *
     * @param {object} oQueue The queue in which you would like the object stored.
     * @param {object} obj    The object you would like stored in the queue.
     *
     * @returns {integer} The number of entries in the queue.
     */
    self.pushFront = (oQueue, obj) => {
        // No push if the queue is full.
        if(self.full(oQueue)) {
            throw { code: 10003 };
        }

        // Simply push if the queue is empty
        if(self.empty(oQueue)) {
            return self.push(oQueue, obj);
        }

        // Put the object one position back.
        if(--oQueue.start < 0) {
            oQueue.start = oQueue.size - 1;
        }
        oQueue.elements[oQueue.start] = obj;
        return ++oQueue.count;
    };

    /**
     * Attempt to pop an object off the head of the queue.
     *
     * @param {object} oQueue The queue object you would like to modify.
     *
     * @returns {object|null}
     */
    self.pop = (oQueue) => {
        if(self.empty(oQueue)) {
            return null;
        }

        let obj = oQueue.elements[oQueue.start];
        delete oQueue.elements[oQueue.start];
        if(++oQueue.start >= oQueue.size) {
            oQueue.start = 0;
        }
        oQueue.count--;
        return obj;
    };

    /**
     * Attempt to pop an object off the head of the queue.
     *
     * @param {object} oQueue The queue object you would like to modify.
     *
     * @returns {object|null}
     */
    self.peek = (oQueue) => {
        if(self.empty(oQueue)) {
            return null;
        }
        return oQueue.elements[oQueue.start];
    };
})(jaxon.utils.queue);


/**
 * Class: jaxon.utils.dom
 */

/**
 * Plain javascript replacement for jQuery's .ready() function.
 * See https://github.com/jfriend00/docReady for a detailed description, copyright and license information.
 */
(function(self) {
    "use strict";

    let readyList = [];
    let readyFired = false;
    let readyEventHandlersInstalled = false;

    /**
     * Call this when the document is ready.
     * This function protects itself against being called more than once
     */
    const ready = () => {
        if (readyFired) {
            return;
        }
        // this must be set to true before we start calling callbacks
        readyFired = true;
        // if a callback here happens to add new ready handlers,
        // the jaxon.utils.dom.ready() function will see that it already fired
        // and will schedule the callback to run right after
        // this event loop finishes so all handlers will still execute
        // in order and no new ones will be added to the readyList
        // while we are processing the list
        readyList.forEach(cb => cb.fn.call(window, cb.ctx));
        // allow any closures held by these functions to free
        readyList = [];
    }

    // Was used with the document.attachEvent() function.
    // const readyStateChange = () => document.readyState === "complete" && ready();

    /**
     * This is the one public interface
     * jaxon.utils.dom.ready(fn, context);
     * The context argument is optional - if present, it will be passed as an argument to the callback
     */
    self.ready = function(callback, context) {
        // if ready has already fired, then just schedule the callback
        // to fire asynchronously, but right away
        if (readyFired) {
            setTimeout(function() { callback(context); }, 1);
            return;
        }
        // add the function and context to the list
        readyList.push({ fn: callback, ctx: context });
        // if document already ready to go, schedule the ready function to run
        if (document.readyState === "complete" ||
            (!document.attachEvent && document.readyState === "interactive")) {
            setTimeout(ready, 1);
            return;
        }
        if (!readyEventHandlersInstalled) {
            // first choice is DOMContentLoaded event
            document.addEventListener("DOMContentLoaded", ready, false);
            // backup is window load event
            window.addEventListener("load", ready, false);

            readyEventHandlersInstalled = true;
        }
    }
})(jaxon.utils.dom);


/**
 * Class: jaxon.utils.string
 */

(function(self) {
    /**
     * Replace all occurances of the single quote character with a double quote character.
     *
     * @param {string=} haystack The source string to be scanned
     *
     * @returns {string|false} A new string with the modifications applied. False on error.
     */
    self.doubleQuotes = haystack => haystack === undefined ?
        false : haystack.replace(new RegExp("'", 'g'), '"');

    /**
     * Replace all occurances of the double quote character with a single quote character.
     *
     * @param {string=} haystack The source string to be scanned
     *
     * @returns {string|false} A new string with the modification applied
     */
    self.singleQuotes = haystack => haystack === undefined ?
        false : haystack.replace(new RegExp('"', 'g'), "'");

    /**
     * Detect, and if found, remove the prefix 'on' from the specified string.
     * This is used while working with event handlers.
     *
     * @param {string} sEventName The string to be modified
     *
     * @returns {string} The modified string
     */
    self.stripOnPrefix = (sEventName) => {
        sEventName = sEventName.toLowerCase();
        return sEventName.indexOf('on') === 0 ? sEventName.replace(/on/, '') : sEventName;
    };

    /**
     * Detect, and add if not found, the prefix 'on' from the specified string.
     * This is used while working with event handlers.
     *
     * @param {string} sEventName The string to be modified
     *
     * @returns {string} The modified string
     */
    self.addOnPrefix = (sEventName) => {
        sEventName = sEventName.toLowerCase();
        return sEventName.indexOf('on') !== 0 ? 'on' + sEventName : sEventName;
    };

    /**
     * String functions for Jaxon
     * See http://javascript.crockford.com/remedial.html for more explanation
     */
    if (!String.prototype.supplant) {
        /**
         * Substitute variables in the string
         *
         * @param {object} values The substitution values
         *
         * @returns {string}
         */
        String.prototype.supplant = function(values) {
            return this.replace(
                /\{([^{}]*)\}/g,
                (a, b) => {
                    const r = values[b];
                    const t = typeof r;
                    return t === 'string' || t === 'number' ? r : a;
                }
            );
        };
    }
})(jaxon.utils.string);


/**
 * Class: jaxon.utils.types
 */

(function(self) {
    /**
     * Get the type of an object.
     * Unlike typeof, this function distinguishes objects from arrays.
     *
     * @param {mixed} xVar The var to check
     *
     * @returns {string}
     */
    self.of = (xVar) => Object.prototype.toString.call(xVar).slice(8, -1).toLowerCase();

    /**
     * Check if a var is an object.
     *
     * @param {mixed} xVar The var to check
     *
     * @returns {bool}
     */
    self.isObject = (xVar) => self.of(xVar) === 'object';

    /**
     * Check if a var is an array.
     *
     * @param {mixed} xVar The var to check
     *
     * @returns {bool}
     */
    self.isArray = (xVar) => self.of(xVar) === 'array';

    /**
     * Check if a var is a string.
     *
     * @param {mixed} xVar The var to check
     *
     * @returns {bool}
     */
    self.isString = (xVar) => self.of(xVar) === 'string';

    /**
     * Check if a var is a function.
     *
     * @param {mixed} xVar The var to check
     *
     * @returns {bool}
     */
    self.isFunction = (xVar) => self.of(xVar) === 'function';

    /**
     * Convert to int.
     *
     * @param {string} sValue
     *
     * @returns {integer}
     */
    self.toInt = (sValue) => parseInt(sValue);

    if (!Array.prototype.top) {
        /**
         * Get the last element in an array
         *
         * @returns {mixed}
         */
        Array.prototype.top = function() {
            return this.length > 0 ? this[this.length - 1] : undefined;
        };
    };
})(jaxon.utils.types);


/**
 * Class: jaxon.utils.upload
 */

(function(self, dom, console) {
    /**
     * @param {object} oRequest A request object, created initially by a call to <jaxon.ajax.request.initialize>
     * @param {string=} oRequest.upload The HTML file upload field id
     *
     * @returns {boolean}
     */
    const initRequest = (oRequest) => {
        if (!oRequest.upload) {
            return false;
        }

        oRequest.upload = {
            id: oRequest.upload,
            input: null,
            form: null,
        };
        const input = dom.$(oRequest.upload.id);

        if (!input) {
            console.log('Unable to find input field for file upload with id ' + oRequest.upload.id);
            return false;
        }
        if (input.type !== 'file') {
            console.log('The upload input field with id ' + oRequest.upload.id + ' is not of type file');
            return false;
        }
        if (input.files.length === 0) {
            console.log('There is no file selected for upload in input field with id ' + oRequest.upload.id);
            return false;
        }
        if (input.name === undefined) {
            console.log('The upload input field with id ' + oRequest.upload.id + ' has no name attribute');
            return false;
        }
        oRequest.upload.input = input;
        oRequest.upload.form = input.form;
        return true;
    };

    /**
     * Check upload data and initialize the request.
     *
     * @param {object} oRequest A request object, created initially by a call to <jaxon.ajax.request.initialize>
     *
     * @returns {void}
     */
    self.initialize = (oRequest) => {
        // The content type shall not be set when uploading a file with FormData.
        // It will be set by the browser.
        if (!initRequest(oRequest)) {
            oRequest.postHeaders['content-type'] = oRequest.contentType;
        }
    }
})(jaxon.utils.upload, jaxon.utils.dom, console);


/**
 * Class: jaxon.parser.attr
 *
 * Process Jaxon custom HTML attributes
 */

(function(self, event, debug) {
    /**
     * The DOM nodes associated to Jaxon components
     *
     * @var {object}
     */
    const xComponentNodes = {};

    /**
     * The default component item name
     *
     * @var {string}
     */
    const sDefaultComponentItem = 'main';

    /**
     * The commands to check for changes
     *
     * @var {array}
     */
    const aCommands = ['node.assign', 'node.append', 'node.prepend', 'node.replace'];

    /**
     * The attributes to check for changes
     *
     * @var {array}
     */
    const aAttributes = ['innerHTML', 'outerHTML'];

    /**
     * Remove attributes from a DOM node.
     *
     * @param {Element} xNode A DOM node.
     * @param {array} aAttrs An array of attribute names.
     *
     * @returns {void}
     */
    const removeAttributes = (xNode, aAttrs) => !debug.active &&
        aAttrs.forEach(sAttr => xNode.removeAttribute(sAttr));

    /**
     * Remove a child node from a DOM node.
     *
     * @param {Element} xNode A DOM node.
     * @param {Element} xChild A Child node.
     *
     * @returns {void}
     */
    const removeChildNode = (xNode, xChild) => !debug.active && xNode.removeChild(xChild);

    /**
     * Check if a the attributes on a targeted node must be processed after a command is executed.
     *
     * @param {Element} xTarget A DOM node.
     * @param {string} sCommand The command name.
     * @param {string} sAttribute The attribute name.
     *
     * @returns {void}
     */
    self.changed = (xTarget, sCommand, sAttribute) => (xTarget) &&
        aAttributes.some(sVal => sVal === sAttribute) && aCommands.some(sVal => sVal === sCommand);

    /**
     * @param {Element} xContainer A DOM node.
     *
     * @returns {void}
     */
    const setClickHandlers = (xContainer) => {
        xContainer.querySelectorAll(':scope [jxn-click]').forEach(xNode => {
            const oHandler = JSON.parse(xNode.getAttribute('jxn-click'));
            event.setEventHandler({ event: 'click', func: oHandler }, { target: xNode });

            removeAttributes(xNode, ['jxn-click']);
        });
    };

    /**
     * @param {Element} xTarget The event handler target.
     * @param {Element} xNode The DOM node with the attributes.
     * @param {string} sAttr The event attribute name
     *
     * @returns {void}
     */
    const setEventHandler = (xTarget, xNode, sAttr) => {
        if(!xNode.hasAttribute('jxn-call'))
        {
            return;
        }

        const sEvent = xNode.getAttribute(sAttr).trim();
        const oHandler = JSON.parse(xNode.getAttribute('jxn-call'));
        if(!xNode.hasAttribute('jxn-select'))
        {
            // Set the event handler on the node.
            event.setEventHandler({ event: sEvent, func: oHandler }, { target: xTarget });
            return;
        }

        // Set the event handler on the selected child nodes.
        const sSelector = xNode.getAttribute('jxn-select').trim();
        xTarget.querySelectorAll(`:scope ${sSelector}`).forEach(xChild => {
            // Set the event handler on the child node.
            event.setEventHandler({ event: sEvent, func: oHandler }, { target: xChild });
        });
    };

    /**
     * @param {Element} xContainer A DOM node.
     *
     * @returns {void}
     */
    const setEventHandlers = (xContainer) => {
        xContainer.querySelectorAll(':scope [jxn-on]').forEach(xNode => {
            setEventHandler(xNode, xNode, 'jxn-on');

            removeAttributes(xNode, ['jxn-on', 'jxn-call', 'jxn-select']);
        });
    };

    /**
     * @param {Element} xContainer A DOM node.
     *
     * @returns {void}
     */
    const setTargetEventHandlers = (xContainer) => {
        xContainer.querySelectorAll(':scope [jxn-target]').forEach(xTarget => {
            xTarget.querySelectorAll(':scope [jxn-event]').forEach(xNode => {
                // Check event declarations only on direct child.
                if (xNode.parentNode === xTarget) {
                    setEventHandler(xTarget, xNode, 'jxn-event');

                    removeChildNode(xTarget, xNode);
                }
            });

            removeAttributes(xTarget, ['jxn-target']);
        });
    };

    /**
     * @param {Element} xContainer A DOM node.
     *
     * @returns {void}
     */
    const bindNodesToComponents = (xContainer) => {
        xContainer.querySelectorAll(':scope [jxn-bind]').forEach(xNode => {
            const sComponentName = xNode.getAttribute('jxn-bind');
            const sComponentItem = xNode.getAttribute('jxn-item') ?? sDefaultComponentItem;
            xComponentNodes[`${sComponentName}_${sComponentItem}`] = xNode;

            removeAttributes(xNode, ['jxn-bind', 'jxn-item']);
        });
    };

    /**
     * Process the custom attributes in a given DOM node.
     *
     * @param {Element} xContainer A DOM node.
     *
     * @returns {void}
     */
    self.process = (xContainer = document) => {
        // Set event handlers on nodes
        setTargetEventHandlers(xContainer);
        // Set event handlers on nodes
        setEventHandlers(xContainer);
        // Set event handlers on nodes
        setClickHandlers(xContainer);
        // Attach DOM nodes to Jaxon components
        bindNodesToComponents(xContainer);
    };

    /**
     * Get the DOM node of a given component.
     *
     * @param {string} sComponentName The component name.
     * @param {string=} sComponentItem The component item.
     *
     * @returns {Element|null}
     */
    self.node = (sComponentName, sComponentItem = sDefaultComponentItem) =>
        xComponentNodes[`${sComponentName}_${sComponentItem}`] ?? null;
})(jaxon.parser.attr, jaxon.cmd.event, jaxon.debug);


/**
 * Class: jaxon.parser.call
 *
 * Execute calls from json expressions.
 */

(function(self, query, dialog, dom, form, types) {
    /**
     * The comparison operators.
     *
     * @var {object}
     */
    const xComparators = {
        eq: (xLeftArg, xRightArg) => xLeftArg == xRightArg,
        teq: (xLeftArg, xRightArg) => xLeftArg === xRightArg,
        ne: (xLeftArg, xRightArg) => xLeftArg != xRightArg,
        nte: (xLeftArg, xRightArg) => xLeftArg !== xRightArg,
        gt: (xLeftArg, xRightArg) => xLeftArg > xRightArg,
        ge: (xLeftArg, xRightArg) => xLeftArg >= xRightArg,
        lt: (xLeftArg, xRightArg) => xLeftArg < xRightArg,
        le: (xLeftArg, xRightArg) => xLeftArg <= xRightArg,
    };

    /**
     * Get or set an attribute on a parent object.
     *
     * @param {object|null} xParent The parent object
     * @param {string} sName The attribute name
     * @param {mixed} xValue If defined, the value to set
     * @param {object} xOptions The call options.
     *
     * @var {object}
     */
    const processAttr = (xParent, sName, xValue, xOptions) => {
        if (!xParent) {
            return undefined;
        }
        const xElt = dom.getInnerObject(sName, xParent);
        if (!xElt) {
            return undefined;
        }
        if (xValue !== undefined) {
            // Assign an attribute.
            xElt.node[xElt.attr] = getValue(xValue, xOptions);
        }
        return xElt.node[xElt.attr];
    };

    /**
     * The call commands
     *
     * @var {object}
     */
    const xCommands = {
        select: ({ _name: sName, mode, context: xSelectContext = null }, xOptions) => {
            const { context: { target: xTarget, event: xEvent, global: xGlobal } = {} } = xOptions;
            switch(sName) {
                case 'this': // The current event target.
                    return mode === 'jq' ? query.select(xTarget) : (mode === 'js' ? xTarget : null);
                case 'event': // The current event.
                    return xEvent;
                case 'window':
                    return window;
                default: // Call the selector.
                    return query.select(sName, query.context(xSelectContext, xGlobal.target));
            }
        },
        event: ({ _name: sName, func: xExpression }, xOptions) => {
            // Set an event handler.
            // Takes the expression with a different context as argument.
            const { value: xCurrValue, context: xContext } = xOptions;
            xCurrValue.on(sName, (event) => execExpression(xExpression, {
                ...xOptions,
                context: {
                    ...xContext,
                    event,
                    target: event.currentTarget,
                },
            }));
            return xCurrValue;
        },
        func: ({ _name: sName, args: aArgs = [] }, xOptions) => {
            // Call a "global" function with the current context as "this".
            const { context: xContext } = xOptions;
            const func = dom.findFunction(sName);
            return !func ? undefined : func.apply(xContext, getArgs(aArgs, xOptions));
        },
        method: ({ _name: sName, args: aArgs = [] }, { value: xCurrValue }) => {
            // Call a function with the current value as "this".
            const func = dom.findFunction(sName, xCurrValue);
            // toInt() is a peudo-method that converts the current value to int.
            return !func ? (sName === 'toInt' ? types.toInt(xCurrValue) : undefined) :
                func.apply(xCurrValue, getArgs(aArgs, xCurrValue));
        },
        attr: ({ _name: sName, value: xValue }, xOptions) => {
            const { value: xCurrValue, context: { target: xTarget } } = xOptions;
            return processAttr(xCurrValue || xTarget, sName, xValue, xOptions);
        },
        // Global var. The parent is the "window" object.
        gvar: ({ _name: sName, value: xValue }, xOptions) => {
            return processAttr(window, sName, xValue, xOptions);
        },
    };

    /**
     * The function to call if one of the above is not found.
     *
     * @var {object}
     */
    const xErrors = {
        comparator: () => false, // The default comparison operator.
        command: {
            invalid: (xCall) => {
                console.error('Invalid command: ' + JSON.stringify({ call: xCall }));
                return undefined;
            },
            unknown: (xCall) => {
                console.error('Unknown command: ' + JSON.stringify({ call: xCall }));
                return undefined;
            },
        },
    };

    /**
     * Check if an argument is an expression.
     *
     * @param {mixed} xArg
     *
     * @returns {boolean}
     */
    const isValidCall = xArg => types.isObject(xArg) && !!xArg._type;

    /**
     * Get the value of a single argument.
     *
     * @param {mixed} xArg
     * @param {object} xOptions The call options.
     *
     * @returns {mixed}
     */
    const getValue = (xArg, xOptions) => {
        if (!isValidCall(xArg)) {
            return xArg;
        }
        const { _type: sType, _name: sName } = xArg;
        switch(sType) {
            case 'form': return form.getValues(sName);
            case 'html': return dom.$(sName).innerHTML;
            case 'input': return dom.$(sName).value;
            case 'checked': return dom.$(sName).checked;
            case 'expr': return execExpression(xArg, xOptions);
            case '_': return sName === 'this' ? xOptions.value : undefined;
            default: return undefined;
        }
    };

    /**
     * Get the values of an array of arguments.
     *
     * @param {array} aArgs
     * @param {object} xOptions The call options.
     *
     * @returns {array}
     */
    const getArgs = (aArgs, xOptions) => aArgs.map(xArg => getValue(xArg, xOptions));

    /**
     * Get the options for a json call.
     *
     * @param {object} xContext The context to execute calls in.
     *
     * @returns {object}
     */
    const getOptions = (xContext, xDefault = {}) => {
        xContext.global = {
            // Some functions are meant to be executed in the context of the component.
            component: !xContext.component || !xContext.target ? null : xContext.target,
        };
        // Remove the component field from the xContext object.
        const { component: _, ...xNewContext } = xContext;
        return { context: { target: window, ...xNewContext }, ...xDefault };
    }

    /**
     * Execute a single call.
     *
     * @param {object} xCall
     * @param {object} xOptions The call options.
     *
     * @returns {void}
     */
    const execCall = (xCall, xOptions) => {
        const xCommand = !isValidCall(xCall) ? xErrors.command.invalid :
            (xCommands[xCall._type] ?? xErrors.command.unknown);
        xOptions.value = xCommand(xCall, xOptions);
        return xOptions.value;
    };

    /**
     * Execute a single javascript function call.
     *
     * @param {object} xCall An object representing the function call
     * @param {object=} xContext The context to execute calls in.
     *
     * @returns {mixed}
     */
    self.execCall = (xCall, xContext = {}) => types.isObject(xCall) &&
        execCall(xCall, getOptions(xContext));

    /**
     * Execute the javascript code represented by an expression object.
     * If a call returns "undefined", it will be the final return value.
     *
     * @param {array} aCalls The calls to execute
     * @param {object} xOptions The call options.
     *
     * @returns {mixed}
     */
    const execCalls = (aCalls, xOptions) => aCalls.reduce((xValue, xCall) =>
        xValue === undefined ? undefined : execCall(xCall, xOptions), null);

    /**
     * Replace placeholders in a given string with values
     * 
     * @param {object} phrase
     * @param {string} phrase.str The string to be processed
     * @param {array} phrase.args The values for placeholders
     * @param {object=} xOptions The call options.
     *
     * @returns {string}
     */
    const makePhrase = ({ str, args }, xOptions) => str.supplant(args.reduce((oArgs, xArg, nIndex) =>
        ({ ...oArgs, [nIndex + 1]: getValue(xArg, xOptions) }), {}));

    /**
     * Replace placeholders in a given string with values
     * 
     * @param {object} phrase
     *
     * @returns {string}
     */
    self.makePhrase = (phrase) => makePhrase(phrase, { context: { } });

    /**
     * Show an alert message
     *
     * @param {object} message The message content
     * @param {object} xOptions The call options.
     *
     * @returns {void}
     */
    const showAlert = (message, xOptions) => !!message &&
        dialog.alert({ ...message, text: makePhrase(message.phrase, xOptions) });

    /**
     * @param {object} question The confirmation question
     * @param {object} message The message to show if the user anwsers no to the question
     * @param {array} aCalls The calls to execute
     * @param {object} xOptions The call options.
     *
     * @returns {boolean}
     */
    const execWithConfirmation = (question, message, aCalls, xOptions) =>
        dialog.confirm({ ...question, text: makePhrase(question.phrase, xOptions) },
            () => execCalls(aCalls, xOptions), () => showAlert(message, xOptions));

    /**
     * @param {array} aCondition The condition to chek
     * @param {object} oMessage The message to show if the condition is not met
     * @param {array} aCalls The calls to execute
     * @param {object} xOptions The call options.
     *
     * @returns {boolean}
     */
    const execWithCondition = (aCondition, oMessage, aCalls, xOptions) => {
        const [sOperator, xLeftArg, xRightArg] = aCondition;
        const xComparator = xComparators[sOperator] ?? xErrors.comparator;
        xComparator(getValue(xLeftArg, xOptions), getValue(xRightArg, xOptions)) ?
            execCalls(aCalls, xOptions) : showAlert(oMessage, xOptions);
    };

    /**
     * Execute the javascript code represented by an expression object.
     *
     * @param {object} xExpression
     * @param {object} xOptions The call options.
     *
     * @returns {mixed}
     */
    const execExpression = (xExpression, xOptions) => {
        const { calls, question, condition, message } = xExpression;
        if((question)) {
            execWithConfirmation(question, message, calls, xOptions);
            return;
        }
        if((condition)) {
            execWithCondition(condition, message, calls, xOptions);
            return;
        }
        return execCalls(calls, xOptions);
    };

    /**
     * Execute the javascript code represented by an expression object.
     *
     * @param {object} xExpression An object representing a command
     * @param {object=} xContext The context to execute calls in.
     *
     * @returns {void}
     */
    self.execExpr = (xExpression, xContext = {}) => types.isObject(xExpression) &&
        execExpression(xExpression, getOptions(xContext, { value: null }));
})(jaxon.parser.call, jaxon.parser.query, jaxon.dialog.lib, jaxon.utils.dom,
    jaxon.utils.form, jaxon.utils.types);


/**
 * Class: jaxon.parser.query
 */

(function(self, jq) {
    /**
     * The selector function.
     *
     * @var {object}
     */
    self.jq = jq;

    /**
     * Make the context for a DOM selector
     *
     * @param {mixed} xSelectContext
     * @param {object} xTarget
     *
     * @returns {object}
     */
    self.context = (xSelectContext, xTarget) => {
        if (!xSelectContext) {
            return xTarget;
        }
        if (!xTarget) {
            return xSelectContext;
        }
        return self.select(xSelectContext, xTarget).first();
    };

    /**
     * Call the DOM selector
     *
     * @param {string|object} xSelector
     * @param {object} xContext
     *
     * @returns {object}
     */
    self.select = (xSelector, xContext = null) => !xContext ?
        self.jq(xSelector) : self.jq(xSelector, xContext);
})(jaxon.parser.query, window.jQuery ?? window.chibi);
// window.chibi is the ChibiJs (https://github.com/kylebarrow/chibi) selector function.


/**
 * Class: jaxon.dialog.cmd
 */

(function(self, lib, parser) {
    /**
     * Find a library to execute a given function.
     *
     * @param {string} sLibName The dialog library name
     * @param {string} sFunc The dialog library function
     *
     * @returns {object}
     */
    const getLib = (sLibName, sFunc) => {
        !lib.has(sLibName) &&
            console.warn(`Unable to find a Jaxon dialog library with name "${sLibName}".`);

        const xLib = lib.get(sLibName);
        !xLib[sFunc] &&
            console.error(`The chosen Jaxon dialog library doesn't implement the "${sFunc}" function.`);

        return xLib;
    };

    /**
     * Add an event handler to the specified target.
     *
     * @param {object} command The Response command object.
     * @param {string} command.lib The message library name
     * @param {object} command.type The message type
     * @param {string} command.title The message title
     * @param {object} command.phrase The message content
     *
     * @returns {true} The operation completed successfully.
     */
    self.showAlert = ({ lib: sLibName, type: sType, title: sTitle, phrase }) => {
        const xLib = getLib(sLibName, 'alert');
        xLib.alert && xLib.alert(sType, parser.makePhrase(phrase), sTitle);
        return true;
    };

    /**
     * Remove an event handler from an target.
     *
     * @param {object} command The Response command object.
     * @param {string} command.lib The dialog library name
     * @param {object} command.dialog The dialog content
     * @param {string} command.dialog.title The dialog title
     * @param {string} command.dialog.content The dialog HTML content
     * @param {array} command.dialog.buttons The dialog buttons
     * @param {array} command.dialog.options The dialog options
     *
     * @returns {true} The operation completed successfully.
     */
    self.showModal = ({ lib: sLibName, dialog: { title, content, buttons, options } }) => {
        const xLib = getLib(sLibName, 'show');
        xLib.show && xLib.show(title, content, buttons, options);
        return true;
    };

    /**
     * Set an event handler with arguments to the specified target.
     *
     * @param {object} command The Response command object.
     * @param {string} command.lib The dialog library name
     *
     * @returns {true} The operation completed successfully.
     */
    self.hideModal = ({ lib: sLibName }) => {
        const xLib = getLib(sLibName, 'hide');
        xLib.hide && xLib.hide();
        return true;
    };
})(jaxon.dialog.cmd, jaxon.dialog.lib, jaxon.parser.call);


/**
 * Class: jaxon.dialog.lib
 */

(function(self, types, dom, js, query) {
    /**
     * Labels for confirm question.
     *
     * @var {object}
     */
    const labels = {
        yes: 'Yes',
        no: 'No',
    };

    /**
     * Dialog libraries.
     *
     * @var {object}
     */
    const libs = {};

    /**
     * Check if a dialog library is defined.
     *
     * @param {string} sName The library name
     *
     * @returns {bool}
     */
    self.has = (sName) => !!libs[sName];

    /**
     * Get a dialog library.
     *
     * @param {string=default} sName The library name
     *
     * @returns {object|null}
     */
    self.get = (sName) => libs[sName] ?? libs.default;

    /**
     * Show a message using a dialog library.
     *
     * @param {object} oMessage The message in the command
     * @param {string} oMessage.lib The dialog library to use for the message
     * @param {string} oMessage.type The message type
     * @param {string} oMessage.text The message text
     * @param {string=} oMessage.title The message title
     *
     * @returns {void}
     */
    self.alert = ({ lib: sLibName, type: sType, title: sTitle = '', text: sMessage }) =>
        self.get(sLibName).alert(sType, sMessage, sTitle);

    /**
     * Call a function after user confirmation.
     *
     * @param {object} oQuestion The question in the command
     * @param {string} oQuestion.lib The dialog library to use for the question
     * @param {string} oQuestion.text The question text
     * @param {string=} oQuestion.title The question title
     * @param {function} fYesCb The function to call if the question is confirmed
     * @param {function} fNoCb The function to call if the question is not confirmed
     *
     * @returns {void}
     */
    self.confirm = ({ lib: sLibName, title: sTitle = '', text: sQuestion }, fYesCb, fNoCb) =>
        self.get(sLibName).confirm(sQuestion, sTitle, fYesCb, fNoCb);

    /**
     * Register a dialog library.
     *
     * @param {string} sName The library name
     * @param {callback} xCallback The library definition callback
     *
     * @returns {void}
     */
    self.register = (sName, xCallback) => {
        // Create an object for the library
        libs[sName] = {};
        // Define the library functions
        xCallback(libs[sName], { types, dom, js, jq: query.jq, labels });
    };

    /**
     * Default dialog plugin, based on js alert and confirm functions
     */
    self.register('default', (lib) => {
        /**
         * Show an alert message
         *
         * @param {string} type The message type
         * @param {string} text The message text
         * @param {string} title The message title
         *
         * @returns {void}
         */
        lib.alert = (type, text, title) => alert(!title ? text : `<b>${title}</b><br/>${text}`);

        /**
         * Ask a confirm question to the user.
         *
         * @param {string} question The question to ask
         * @param {string} title The question title
         * @param {callback} yesCallback The function to call if the answer is yes
         * @param {callback} noCallback The function to call if the answer is no
         *
         * @returns {void}
         */
        lib.confirm = (question, title, yesCallback, noCallback) => {
            confirm(!title ? question : `<b>${title}</b><br/>${question}`) ?
                yesCallback() : (noCallback && noCallback());
        };
    });
})(jaxon.dialog.lib, jaxon.utils.types, jaxon.dom, jaxon.parser.call, jaxon.parser.query);


/**
 * Class: jaxon.ajax.callback
 */

(function(self, types, config) {
    /**
     * Create a timer to fire an event in the future.
     * This will be used fire the onRequestDelay and onExpiration events.
     *
     * @param {integer} iDelay The amount of time in milliseconds to delay.
     *
     * @returns {object} A callback timer object.
     */
    const setupTimer = (iDelay) => ({ timer: null, delay: iDelay });

    /**
     * The names of the available callbacks.
     *
     * @var {array}
     */
    const aCallbackNames = ['onInitialize', 'onProcessParams', 'onPrepare',
        'onRequest', 'onResponseDelay', 'onExpiration', 'beforeResponseProcessing',
        'onFailure', 'onRedirect', 'onSuccess', 'onComplete'];

    /**
     * Create a blank callback object.
     * Two optional arguments let you set the delay time for the onResponseDelay and onExpiration events.
     *
     * @param {integer=} responseDelayTime
     * @param {integer=} expirationTime
     *
     * @returns {object} The callback object.
     */
    self.create = (responseDelayTime, expirationTime) => {
        const oCallback = {
            timers: {
                onResponseDelay: setupTimer(responseDelayTime ?? config.defaultResponseDelayTime),
                onExpiration: setupTimer(expirationTime ?? config.defaultExpirationTime),
            },
        };
        aCallbackNames.forEach(sName => oCallback[sName] = null);
        return oCallback;
    };

    /**
     * The global callback object which is active for every request.
     *
     * @var {object}
     */
    self.callback = self.create();

    /**
     * Move all the callbacks defined directly in the oRequest object to the
     * oRequest.callback property, which may then be converted to an array.
     *
     * @param {object} oRequest
     *
     * @return {void}
     */
    self.initCallbacks = (oRequest) => {
        if (types.isObject(oRequest.callback)) {
            oRequest.callback = [oRequest.callback];
        }
        if (types.isArray(oRequest.callback)) {
            oRequest.callback.forEach(oCallback => {
                // Add the timers attribute, if it is not defined.
                if (oCallback.timers === undefined) {
                    oCallback.timers = {};
                }
            });
            return;
        }

        let callbackFound = false;
        // Check if any callback is defined in the request object by its own name.
        const callback = self.create();
        aCallbackNames.forEach(sName => {
            if (oRequest[sName] !== undefined) {
                callback[sName] = oRequest[sName];
                callbackFound = true;
                delete oRequest[sName];
            }
        });
        oRequest.callback = callbackFound ? [callback] : [];
    };

    /**
     * Get a flatten array of callbacks
     *
     * @param {object} oRequest The request context object.
     * @param {array=} oRequest.callback The request callback(s).
     *
     * @returns {array}
     */
    const getCallbacks = ({ callback = [] }) => [self.callback, ...callback];

    /**
     * Execute a callback event.
     *
     * @param {object} oCallback The callback object (or objects) which contain the event handlers to be executed.
     * @param {string} sFunction The name of the event to be triggered.
     * @param {object} oRequest The callback argument.
     *
     * @returns {void}
     */
    const execute = (oCallback, sFunction, oRequest) => {
        const func = oCallback[sFunction];
        if (!func || !types.isFunction(func)) {
            return;
        }
        const timer = oCallback.timers[sFunction];
        if (!timer) {
            func(oRequest); // Call the function directly.
            return;
        }
        // Call the function after the timeout.
        timer.timer = setTimeout(() => func(oRequest), timer.delay);
    };

    /**
     * Execute a callback event.
     *
     * @param {object} oRequest The request context object.
     * @param {string} sFunction The name of the event to be triggered.
     *
     * @returns {void}
     */
    self.execute = (oRequest, sFunction) => getCallbacks(oRequest)
        .forEach(oCallback => execute(oCallback, sFunction, oRequest));

    /**
     * Clear a callback timer for the specified function.
     *
     * @param {object} oCallback The callback object (or objects) that contain the specified function timer to be cleared.
     * @param {string} sFunction The name of the function associated with the timer to be cleared.
     *
     * @returns {void}
     */
    const clearTimer = (oCallback, sFunction) => {
        const timer = oCallback.timers[sFunction];
        timer !== undefined && timer.timer !== null && clearTimeout(timer.timer);
    };

    /**
     * Clear a callback timer for the specified function.
     *
     * @param {object} oRequest The request context object.
     * @param {string} sFunction The name of the function associated with the timer to be cleared.
     *
     * @returns {void}
     */
    self.clearTimer = (oRequest, sFunction) => getCallbacks(oRequest)
        .forEach(oCallback => clearTimer(oCallback, sFunction));
})(jaxon.ajax.callback, jaxon.utils.types, jaxon.config);


/**
 * Class: jaxon.ajax.command
 */

(function(self, config, call, attr, queue, dom, types, dialog) {
    /**
     * An array that is used internally in the jaxon.fn.handler object to keep track
     * of command handlers that have been registered.
     *
     * @var {object}
     */
    const handlers = {};

    /**
     * Registers a new command handler.
     *
     * @param {string} name The short name of the command handler.
     * @param {string} func The command handler function.
     * @param {string=''} desc The description of the command handler.
     *
     * @returns {void}
     */
    self.register = (name, func, desc = '') => handlers[name] = { desc, func };

    /**
     * Unregisters and returns a command handler.
     *
     * @param {string} name The name of the command handler.
     *
     * @returns {callable|null} The unregistered function.
     */
    self.unregister = (name) => {
        const handler = handlers[name];
        if (!handler) {
            return null;
        }
        delete handlers[name];
        return handler.func;
    };

    /**
     * @param {object} command The response command to be executed.
     * @param {string} command.name The name of the function.
     *
     * @returns {boolean}
     */
    self.isRegistered = ({ name }) => name !== undefined && handlers[name] !== undefined;

    /**
     * Calls the registered command handler for the specified command
     * (you should always check isRegistered before calling this function)
     *
     * @param {object} name The command name.
     * @param {object} args The command arguments.
     * @param {object} context The command context.
     *
     * @returns {boolean}
     */
    self.call = (name, args, context) => {
        const { func, desc } = handlers[name];
        context.command.desc = desc;
        return func(args, context);
    }

    /**
     * Perform a lookup on the command specified by the response command object passed
     * in the first parameter.  If the command exists, the function checks to see if
     * the command references a DOM object by ID; if so, the object is located within
     * the DOM and added to the command data.  The command handler is then called.
     * 
     * @param {object} context The response command to be executed.
     *
     * @returns {true} The command completed successfully.
     */
    self.execute = (context) => {
        const { command: { name, args = {}, component = {} } } = context;
        if (!self.isRegistered({ name })) {
            console.error('Trying to execute unknown command: ' + JSON.stringify({ name, args }));
            return true;
        }

        // If the command has an "id" attr, find the corresponding dom node.
        if ((component.name)) {
            context.target = attr.node(component.name, component.item);
            if (!context.target) {
                console.error('Unable to find component node: ' + JSON.stringify(component));
            }
        }
        if (!context.target && (args.id)) {
            context.target = dom.$(args.id);
            if (!context.target) {
                console.error('Unable to find node with id : ' + args.id);
            }
        }

        // Process the command
        self.call(name, args, context);
        // Process Jaxon custom attributes in the new node HTML content.
        attr.changed(context.target, name, args.attr) && attr.process(context.target);
        return true;
    };

    /**
     * Process a single command
     * 
     * @param {object} context The response command to process
     *
     * @returns {boolean}
     */
    const processCommand = (context) => {
        try {
            self.execute(context);
            return true;
        } catch (e) {
            console.log(e);
        }
        return false;
    };

    /**
     * While entries exist in the queue, pull and entry out and process it's command.
     * When oQueue.paused is set to true, the processing is halted.
     *
     * Note:
     * - Set oQueue.paused to false and call this function to cause the queue processing to continue.
     * - When an exception is caught, do nothing; if the debug module is installed, it will catch the exception and handle it.
     *
     * @param {object} oQueue A queue containing the commands to execute.
     *
     * @returns {void}
     */
    const processCommandQueue = (oQueue) => {
        // Stop processing the commands if the queue is paused.
        let context = null;
        oQueue.paused = false;
        while (!oQueue.paused && (context = queue.pop(oQueue)) !== null) {
            if (!processCommand(context)) {
                return;
            }
        }
    };

    /**
     * Queue and process the commands in the response.
     *
     * @param {object} oRequest The request context object.
     *
     * @return {true}
     */
    self.processCommands = (oRequest) => {
        const { response: { content } = {}, status } = oRequest;
        if (!types.isObject(content)) {
            return;
        }

        const { debug: { message } = {}, jxn: { commands = [] } = {} } = content;

        status.onProcessing();

        message && console.log(message);

        // Create a queue for the commands in the response.
        let nSequence = 0;
        const oQueue = queue.create(config.commandQueueSize);
        commands.forEach(command => queue.push(oQueue, {
            sequence: nSequence++,
            command: {
                name: '*unknown*',
                ...command,
            },
            request: oRequest,
            queue: oQueue,
        }));
        // Add a last command to clear the queue
        queue.push(oQueue, {
            sequence: nSequence,
            command: {
                name: 'response.complete',
                fullName: 'Response Complete',
            },
            request: oRequest,
            queue: oQueue,
        });

        processCommandQueue(oQueue);
    };

    /**
     * Causes the processing of items in the queue to be delayed for the specified amount of time.
     * This is an asynchronous operation, therefore, other operations will be given an opportunity
     * to execute during this delay.
     *
     * @param {object} args The command arguments.
     * @param {integer} args.duration The number of 10ths of a second to sleep.
     * @param {object} context The Response command object.
     * @param {object} context.queue The command queue.
     *
     * @returns {true}
     */
    self.sleep = ({ duration }, { queue: oQueue }) => {
        // The command queue is paused, and will be restarted after the specified delay.
        oQueue.paused = true;
        setTimeout(() => processCommandQueue(oQueue), duration * 100);
        return true;
    };

    /**
     * The function to run after the confirm question, for the comfirmCommands.
     *
     * @param {object} oQueue The command queue.
     * @param {integer=0} skipCount The number of commands to skip.
     *
     * @returns {void}
     */
    const resumeQueueProcessing = (oQueue, skipCount = 0) => {
        // Skip commands.
        // The last entry in the queue is not a user command, thus it cannot be skipped.
        while (skipCount > 0 && oQueue.count > 1 && queue.pop(oQueue) !== null) {
            --skipCount;
        }
        processCommandQueue(oQueue);
    };

    /**
     * Prompt the user with the specified question, if the user responds by clicking cancel,
     * then skip the specified number of commands in the response command queue.
     * If the user clicks Ok, the command processing resumes normal operation.
     *
     * @param {object} args The command arguments.
     * @param {integer} args.count The number of commands to skip.
     * @param {object} args.question The question to ask.
     * @param {string} args.question.lib The dialog library to use.
     * @param {object} args.question.title The question title.
     * @param {object} args.question.phrase The question content.
     * @param {object} context The Response command object.
     * @param {object} context.queue The command queue.
     *
     * @returns {true} The queue processing is temporarily paused.
     */
    self.confirm = ({
        count: nSkipCount,
        question: { lib: sLibName, title: sTitle, phrase: oPhrase },
    }, { queue: oQueue }) => {
        // The command queue is paused, and will be restarted after the confirm question is answered.
        const xLib = dialog.get(sLibName);
        oQueue.paused = true;
        xLib.confirm(call.makePhrase(oPhrase), sTitle,
            () => resumeQueueProcessing(oQueue),
            () => resumeQueueProcessing(oQueue, nSkipCount));
        return true;
    };
})(jaxon.ajax.command, jaxon.config, jaxon.parser.call, jaxon.parser.attr,
    jaxon.utils.queue, jaxon.utils.dom, jaxon.utils.types, jaxon.dialog.lib);


/**
 * Class: jaxon.ajax.parameters
 */

(function(self, types, version) {
    /**
     * The array of data bags
     *
     * @type {object}
     */
    const databags = {};

    /**
     * Stringify a parameter of an ajax call.
     *
     * @param {mixed} oVal - The value to be stringified
     *
     * @returns {string}
     */
    const stringify = (oVal) => {
        if (oVal === undefined ||  oVal === null) {
            return '*';
        }
        const sType = types.of(oVal);
        if (sType === 'object' || sType === 'array') {
            try {
                return encodeURIComponent(JSON.stringify(oVal));
            } catch (e) {
                oVal = '';
                // do nothing, if the debug module is installed
                // it will catch the exception and handle it
            }
        }
        oVal = encodeURIComponent(oVal);
        if (sType === 'string') {
            return 'S' + oVal;
        }
        if (sType === 'boolean') {
            return 'B' + oVal;
        }
        if (sType === 'number') {
            return 'N' + oVal;
        }
        return oVal;
    };

    /**
     * Save data in the data bag.
     *
     * @param {string} sBag   The data bag name.
     * @param {object} oValues The values to save in the data bag.
     *
     * @return {void}
     */
    self.setBag = (sBag, oValues) => databags[sBag] = oValues;

    /**
     * Save data in the data bag.
     *
     * @param {object} oValues The values to save in the data bag.
     *
     * @return {void}
     */
    self.setBags = (oValues) => Object.keys(oValues).forEach(sBag => self.setBag(sBag, oValues[sBag]));

    /**
     * Clear an entry in the data bag.
     *
     * @param {string} sBag   The data bag name.
     *
     * @return {void}
     */
    self.clearBag = (sBag) => delete databags[sBag];

    /**
     * Make the databag object to send in the HTTP request.
     *
     * @param {array} aBags The data bag names.
     *
     * @return {object}
     */
    const getBagsValues = (aBags) => JSON.stringify(aBags.reduce((oValues, sBag) => ({
        ...oValues,
        [sBag]: databags[sBag] ?? '*' }
    ), {}));

    /**
     * Sets the request parameters in a container.
     *
     * @param {object} oRequest The request object
     * @param {object} oRequest.func The function to call on the server app.
     * @param {object} oRequest.parameters The parameters to pass to the function.
     * @param {array=} oRequest.bags The keys of values to get from the data bag.
     * @param {callable} fSetter A function that sets a single parameter
     *
     * @return {void}
     */
    const setParams = ({ func, parameters, bags = [] }, fSetter) => {
        const dNow = new Date();
        fSetter('jxnr', dNow.getTime());
        fSetter('jxnv', `${version.major}.${version.minor}.${version.patch}`);

        Object.keys(func).forEach(sParam => fSetter(sParam, encodeURIComponent(func[sParam])));

        // The parameters value was assigned from the js "arguments" var in a function. So it
        // is an array-like object, that we need to convert to a real array => [...parameters].
        // See https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Functions/arguments
        [...parameters].forEach(xParam => fSetter('jxnargs[]', stringify(xParam)));

        bags.length > 0 && fSetter('jxnbags', encodeURIComponent(getBagsValues(bags)));
    };

    /**
     * Processes request specific parameters and store them in a FormData object.
     *
     * @param {object} oRequest
     *
     * @return {FormData}
     */
    const getFormDataParams = (oRequest) => {
        const rd = new FormData();
        setParams(oRequest, (sParam, sValue) => rd.append(sParam, sValue));

        // Files to upload
        const { name: field, files } = oRequest.upload.input;
        // The "files" var is an array-like object, that we need to convert to a real array.
        files && [...files].forEach(file => rd.append(field, file));
        return rd;
    };

    /**
     * Processes request specific parameters and store them in an URL encoded string.
     *
     * @param {object} oRequest
     *
     * @return {string}
     */
    const getUrlEncodedParams = (oRequest) => {
        const rd = [];
        setParams(oRequest, (sParam, sValue) => rd.push(sParam + '=' + sValue));

        if (oRequest.method === 'POST') {
            return rd.join('&');
        }
        // Move the parameters to the URL for HTTP GET requests
        oRequest.requestURI += (oRequest.requestURI.indexOf('?') === -1 ? '?' : '&') + rd.join('&');
        return ''; // The request body is empty
    };

    /**
     * Check if the request has files to upload.
     *
     * @param {object} oRequest The request object
     * @param {object} oRequest.upload The upload object
     *
     * @return {boolean}
     */
    const hasUpload = ({ upload: { form, input } = {} }) => form && input;

    /**
     * Processes request specific parameters and generates the temporary
     * variables needed by jaxon to initiate and process the request.
     *
     * Note:
     * This is called once per request; upon a request failure, this will not be called for additional retries.
     *
     * @param {object} oRequest The request object
     *
     * @return {void}
     */
    self.process = (oRequest) => {
        // Make request parameters.
        oRequest.requestURI = oRequest.URI;
        oRequest.requestData = hasUpload(oRequest) ?
            getFormDataParams(oRequest) : getUrlEncodedParams(oRequest);
    };
})(jaxon.ajax.parameters, jaxon.utils.types, jaxon.version);


/**
 * Class: jaxon.ajax.request
 */

(function(self, config, params, rsp, cbk, upload, queue) {
    /**
     * The queues that hold synchronous requests as they are sent and processed.
     *
     * @var {object}
     */
    self.q = {
        send: queue.create(config.requestQueueSize),
        recv: queue.create(config.requestQueueSize * 2),
    };

    /**
     * Copy the value of the csrf meta tag to the request headers.
     *
     * @param {string} sTagName The request context object.
     *
     * @return {void}
     */
    self.setCsrf = (sTagName) => {
        const metaTags = config.baseDocument.getElementsByTagName('meta') || [];
        for (const metaTag of metaTags) {
            if (metaTag.getAttribute('name') === sTagName) {
                const csrfToken = metaTag.getAttribute('content');
                if ((csrfToken)) {
                    config.postHeaders['X-CSRF-TOKEN'] = csrfToken;
                }
                return;
            }
        }
    };

    /**
     * Initialize a request object.
     *
     * @param {object} oRequest An object that specifies call specific settings that will,
     *      in addition, be used to store all request related values.
     *      This includes temporary values used internally by jaxon.
     *
     * @returns {void}
     */
    self.initialize = (oRequest) => {
        config.setRequestOptions(oRequest);
        cbk.initCallbacks(oRequest);
        cbk.execute(oRequest, 'onInitialize');

        oRequest.status = (oRequest.statusMessages) ? config.status.update : config.status.dontUpdate;
        oRequest.cursor = (oRequest.waitCursor) ? config.cursor.update : config.cursor.dontUpdate;

        // Look for upload parameter
        upload.initialize(oRequest);

        // The request is submitted only if there is no pending requests in the outgoing queue.
        oRequest.submit = queue.empty(self.q.send);

        // Synchronous requests are always queued.
        // Asynchronous requests are queued in send queue only if they are not submitted.
        oRequest.queued = false;
        if (!oRequest.submit || oRequest.mode === 'synchronous') {
            queue.push(self.q.send, oRequest);
            oRequest.queued = true;
        }
    };

    /**
     * Prepare a request, by setting the HTTP options, handlers and processor.
     *
     * @param {object} oRequest The request context object.
     *
     * @return {void}
     */
    self.prepare = (oRequest) => {
        cbk.execute(oRequest, 'onPrepare');

        oRequest.httpRequestOptions = {
            ...config.httpRequestOptions,
            method: oRequest.method,
            headers: {
                ...oRequest.commonHeaders,
                ...(oRequest.method === 'POST' ? oRequest.postHeaders : oRequest.getHeaders),
            },
            body: oRequest.requestData,
        };

        oRequest.response = rsp.create(oRequest);
    };

    /**
     * Send a request.
     *
     * @param {object} oRequest The request context object.
     *
     * @returns {void}
     */
    self._send = (oRequest) => {
        fetch(oRequest.requestURI, oRequest.httpRequestOptions)
            .then(oRequest.response.converter)
            .then(oRequest.response.handler)
            .catch(oRequest.response.errorHandler);
    };

    /**
     * Create a request object and submit the request using the specified request type;
     * all request parameters should be finalized by this point.
     * Upon failure of a POST, this function will fall back to a GET request.
     *
     * @param {object} oRequest The request context object.
     *
     * @returns {void}
     */
    const submit = (oRequest) => {
        self.prepare(oRequest);
        oRequest.status.onRequest();

        // The onResponseDelay and onExpiration aren't called immediately, but a timer
        // is set to call them later, using delays that are set in the config.
        cbk.execute(oRequest, 'onResponseDelay');
        cbk.execute(oRequest, 'onExpiration');
        cbk.execute(oRequest, 'onRequest');
        oRequest.cursor.onWaiting();
        oRequest.status.onWaiting();

        self._send(oRequest);
    };

    /**
     * Create a request object and submit the request using the specified request type.
     *
     * @param {object} oRequest The request context object.
     *
     * @returns {void}
     */
    self.submit = (oRequest) => {
        while (oRequest.requestRetry-- > 0) {
            try {
                submit(oRequest);
                return;
            }
            catch (e) {
                cbk.execute(oRequest, 'onFailure');
                if (oRequest.requestRetry <= 0) {
                    throw e;
                }
            }
        }
    };

    /**
     * Abort the request.
     *
     * @param {object} oRequest The request context object.
     *
     * @returns {void}
     */
    self.abort = (oRequest) => {
        oRequest.aborted = true;
        rsp.complete(oRequest);
    };

    /**
     * Initiates a request to the server.
     *
     * @param {object} func An object containing the name of the function to
     *      execute on the server. The standard request is: {jxnfun:'function_name'}
     * @param {object=} funcArgs A request object which may contain call specific parameters.
     *      This object will be used by jaxon to store all the request parameters as well as
     *      temporary variables needed during the processing of the request.
     *
     * @returns {void}
     */
    self.execute = (func, funcArgs) => {
        if (func === undefined) {
            return;
        }

        const oRequest = funcArgs ?? {};
        oRequest.func = func;
        self.initialize(oRequest);

        cbk.execute(oRequest, 'onProcessParams');
        params.process(oRequest);

        oRequest.submit && self.submit(oRequest);
    };
})(jaxon.ajax.request, jaxon.config, jaxon.ajax.parameters, jaxon.ajax.response,
    jaxon.ajax.callback, jaxon.utils.upload, jaxon.utils.queue);


/**
 * Class: jaxon.ajax.response
 */

(function(self, command, req, cbk, queue) {
    /**
     * This array contains a list of codes which will be returned from the server upon
     * successful completion of the server portion of the request.
     *
     * These values should match those specified in the HTTP standard.
     *
     * @var {array}
     */
    const successCodes = [0, 200];

    // 10.4.1 400 Bad Request
    // 10.4.2 401 Unauthorized
    // 10.4.3 402 Payment Required
    // 10.4.4 403 Forbidden
    // 10.4.5 404 Not Found
    // 10.4.6 405 Method Not Allowed
    // 10.4.7 406 Not Acceptable
    // 10.4.8 407 Proxy Authentication Required
    // 10.4.9 408 Request Timeout
    // 10.4.10 409 Conflict
    // 10.4.11 410 Gone
    // 10.4.12 411 Length Required
    // 10.4.13 412 Precondition Failed
    // 10.4.14 413 Request Entity Too Large
    // 10.4.15 414 Request-URI Too Long
    // 10.4.16 415 Unsupported Media Type
    // 10.4.17 416 Requested Range Not Satisfiable
    // 10.4.18 417 Expectation Failed
    // 10.5 Server Error 5xx
    // 10.5.1 500 Internal Server Error
    // 10.5.2 501 Not Implemented
    // 10.5.3 502 Bad Gateway
    // 10.5.4 503 Service Unavailable
    // 10.5.5 504 Gateway Timeout
    // 10.5.6 505 HTTP Version Not Supported

    /**
     * This array contains a list of status codes returned by the server to indicate
     * that the request failed for some reason.
     *
     * @var {array}
     */
    const errorCodes = [400, 401, 402, 403, 404, 500, 501, 502, 503];

    // 10.3.1 300 Multiple Choices
    // 10.3.2 301 Moved Permanently
    // 10.3.3 302 Found
    // 10.3.4 303 See Other
    // 10.3.5 304 Not Modified
    // 10.3.6 305 Use Proxy
    // 10.3.7 306 (Unused)
    // 10.3.8 307 Temporary Redirect

    /**
     * An array of status codes returned from the server to indicate a request for redirect to another URL.
     *
     * Typically, this is used by the server to send the browser to another URL.
     * This does not typically indicate that the jaxon request should be sent to another URL.
     *
     * @var {array}
     */
    const redirectCodes = [301, 302, 307];

    /**
     * Check if a status code indicates a success.
     *
     * @param {int} nStatusCode A status code.
     *
     * @return {bool}
     */
    self.isSuccessCode = nStatusCode => successCodes.indexOf(nStatusCode) >= 0;

    /**
     * Check if a status code indicates a redirect.
     *
     * @param {int} nStatusCode A status code.
     *
     * @return {bool}
     */
    self.isRedirectCode = nStatusCode => redirectCodes.indexOf(nStatusCode) >= 0;

    /**
     * Check if a status code indicates an error.
     *
     * @param {int} nStatusCode A status code.
     *
     * @return {bool}
     */
    self.isErrorCode = nStatusCode => errorCodes.indexOf(nStatusCode) >= 0;

    /**
     * This is the JSON response processor.
     *
     * @param {object} oRequest The request context object.
     * @param {object} oResponse The response context object.
     * @param {object} oResponse.http The response object.
     * @param {integer} oResponse.http.status The response status.
     * @param {object} oResponse.http.headers The response headers.
     *
     * @return {true}
     */
    const jsonProcessor = (oRequest, { http: { status, headers } }) => {
        if (self.isSuccessCode(status)) {
            cbk.execute(oRequest, 'onSuccess');
            // Queue and process the commands in the response.
            command.processCommands(oRequest);
            return true;
        }
        if (self.isRedirectCode(status)) {
            cbk.execute(oRequest, 'onRedirect');
            self.complete(oRequest);
            window.location = headers.get('location');
            return true;
        }
        if (self.isErrorCode(status)) {
            cbk.execute(oRequest, 'onFailure');
            self.complete(oRequest);
            return true;
        }
        return true;
    };

    /**
     * Process the response.
     *
     * @param {object} oRequest The request context object.
     *
     * @return {mixed}
     */
    self.received = (oRequest) => {
        const { aborted, response: oResponse } = oRequest;
        // Sometimes the response.received gets called when the request is aborted
        if (aborted) {
            return null;
        }

        // The response is successfully received, clear the timers for expiration and delay.
        cbk.clearTimer(oRequest, 'onExpiration');
        cbk.clearTimer(oRequest, 'onResponseDelay');
        cbk.execute(oRequest, 'beforeResponseProcessing');

        return oResponse.processor(oRequest, oResponse);
    };

    /**
     * Prepare a request, by setting the handlers and processor.
     *
     * @param {object} oRequest The request context object.
     *
     * @return {void}
     */
    self.create = (oRequest) => ({
        processor: jsonProcessor,
        ...oRequest.response,
        converter: (http) => {
            // Save the reponse object
            oRequest.response.http = http;
            // Get the response content
            return oRequest.response.convertToJson ? http.json() : http.text();
        },
        handler: (content) => {
            oRequest.response.content = content;
            // Synchronous request are processed immediately.
            // Asynchronous request are processed only if the queue is empty.
            if (queue.empty(req.q.send) || oRequest.mode === 'synchronous') {
                self.received(oRequest);
                return;
            }
            queue.push(req.q.recv, oRequest);
        },
        errorHandler: (error) => {
            cbk.execute(oRequest, 'onFailure');
            throw error;
        },
    });

    /**
     * Clean up the request object.
     *
     * @param {object} oRequest The request context object.
     *
     * @returns {void}
     */
    const cleanUp = (oRequest) => {
        // clean up -- these items are restored when the request is initiated
        delete oRequest.func;
        delete oRequest.URI;
        delete oRequest.requestURI;
        delete oRequest.requestData;
        delete oRequest.httpRequestOptions;
        delete oRequest.response;
    };

    /**
     * Attempt to pop the next asynchronous request.
     *
     * @param {object} oQueue The queue object you would like to modify.
     *
     * @returns {object|null}
     */
    const popAsyncRequest = oQueue => {
        if (queue.empty(oQueue) || queue.peek(oQueue).mode === 'synchronous') {
            return null;
        }
        return queue.pop(oQueue);
    }

    /**
     * Called by the response command queue processor when all commands have been processed.
     *
     * @param {object} oRequest The request context object.
     *
     * @return {void}
     */
    self.complete = (oRequest) => {
        cbk.execute(oRequest, 'onComplete');
        oRequest.cursor.onComplete();
        oRequest.status.onComplete();

        cleanUp(oRequest);

        // All the requests and responses queued while waiting must now be processed.
        if(oRequest.mode === 'synchronous') {
            // Remove the current request from the send queues.
            queue.pop(req.q.send);
            // Process the asynchronous responses received while waiting.
            while((recvRequest = popAsyncRequest(req.q.recv)) !== null) {
                self.received(recvRequest);
            }
            // Submit the asynchronous requests sent while waiting.
            while((sendRequest = popAsyncRequest(req.q.send)) !== null) {
                req.submit(sendRequest);
            }
            // Submit the next synchronous request, if there's any.
            if((sendRequest = queue.peek(req.q.send)) !== null) {
                req.submit(sendRequest);
            }
        }
    };
})(jaxon.ajax.response, jaxon.ajax.command, jaxon.ajax.request, jaxon.ajax.callback,
    jaxon.utils.queue);


/**
 * Class: jaxon.cmd.event
 */

(function(self, call, dom, str) {
    /**
     * Add an event handler to the specified target.
     *
     * @param {object} args The command arguments.
     * @param {string} args.event The name of the event.
     * @param {string} args.func The name of the function to be called
     * @param {object} context The command context.
     * @param {Element} context.target The target DOM element.
     *
     * @returns {true} The operation completed successfully.
     */
    self.addHandler = ({ event: sEvent, func: sFuncName }, { target }) => {
        target.addEventListener(str.stripOnPrefix(sEvent), dom.findFunction(sFuncName), false)
        return true;
    };

    /**
     * Remove an event handler from an target.
     *
     * @param {object} args The command arguments.
     * @param {string} args.event The name of the event.
     * @param {string} args.func The name of the function to be removed
     * @param {object} context The command context.
     * @param {Element} context.target The target DOM element.
     *
     * @returns {true} The operation completed successfully.
     */
    self.removeHandler = ({ event: sEvent, func: sFuncName }, { target }) => {
       target.removeEventListener(str.stripOnPrefix(sEvent), dom.findFunction(sFuncName), false);
       return true;
    };

    /**
     * Call an event handler.
     *
     * @param {string} event The name of the event
     * @param {object} func The expression to be executed in the event handler
     * @param {object} target The target element
     *
     * @returns {void}
     */
    const callEventHandler = (event, func, target) =>
        call.execExpr({ _type: 'expr', ...func }, { event, target });

    /**
     * Add an event handler with arguments to the specified target.
     *
     * @param {object} args The command arguments.
     * @param {string} args.event The name of the event
     * @param {object} args.func The event handler
     * @param {object|false} args.options The handler options
     * @param {object} context The command context.
     * @param {Element} context.target The target DOM element.
     *
     * @returns {true} The operation completed successfully.
     */
    self.addEventHandler = ({ event: sEvent, func, options }, { target }) => {
        target.addEventListener(str.stripOnPrefix(sEvent),
            (event) => callEventHandler(event, func, target), options ?? false);
        return true;
    };

    /**
     * Set an event handler with arguments to the specified target.
     *
     * @param {object} args The command arguments.
     * @param {string} args.event The name of the event
     * @param {object} args.func The event handler
     * @param {object} context The command context.
     * @param {Element} context.target The target DOM element.
     *
     * @returns {true} The operation completed successfully.
     */
    self.setEventHandler = ({ event: sEvent, func }, { target }) => {
        target[str.addOnPrefix(sEvent)] = (event) => callEventHandler(event, func, target);
        return true;
    };
})(jaxon.cmd.event, jaxon.parser.call, jaxon.utils.dom, jaxon.utils.string);


/**
 * Class: jaxon.cmd.
 */

(function(self, dom, types, baseDocument) {
    /**
     * Assign an element's attribute to the specified value.
     *
     * @param {object} args The command arguments.
     * @param {string} args.attr The name of the attribute to set.
     * @param {string} args.value The new value to be applied.
     * @param {object} context The command context.
     * @param {Element} context.target The target DOM element.
     *
     * @returns {true} The operation completed successfully.
     */
    self.assign = ({ attr, value }, { target }) => {
        const xElt = dom.getInnerObject(attr, target);
        if (xElt !== null) {
            xElt.node[xElt.attr] = value;
        }
        return true;
    };

    /**
     * Append the specified value to an element's attribute.
     *
     * @param {object} args The command arguments.
     * @param {string} args.attr The name of the attribute to append to.
     * @param {string} args.value The new value to be appended.
     * @param {object} context The command context.
     * @param {Element} context.target The target DOM element.
     *
     * @returns {true} The operation completed successfully.
     */
    self.append = ({ attr, value }, { target }) => {
        const xElt = dom.getInnerObject(attr, target);
        if (xElt !== null) {
            xElt.node[xElt.attr] = xElt.node[xElt.attr] + value;
        }
        return true;
    };

    /**
     * Prepend the specified value to an element's attribute.
     *
     * @param {object} args The command arguments.
     * @param {string} args.attr The name of the attribute.
     * @param {string} args.value The new value to be prepended.
     * @param {object} context The command context.
     * @param {Element} context.target The target DOM element.
     *
     * @returns {true} The operation completed successfully.
     */
    self.prepend = ({ attr, value }, { target }) => {
        const xElt = dom.getInnerObject(attr, target);
        if (xElt !== null) {
            xElt.node[xElt.attr] = value + xElt.node[xElt.attr];
        }
        return true;
    };

    /**
     * Replace a text in the value of a given attribute in an element
     *
     * @param {object} xElt The value returned by the dom.getInnerObject() function
     * @param {string} sSearch The text to search
     * @param {string} sReplace The text to use as replacement
     *
     * @returns {void}
     */
    const replaceText = (xElt, sSearch, sReplace) => {
        const bFunction = types.isFunction(xElt.node[xElt.attr]);
        const sCurText = bFunction ? xElt.node[xElt.attr].join('') : xElt.node[xElt.attr];
        const sNewText = sCurText.replaceAll(sSearch, sReplace);
        if (bFunction || dom.willChange(xElt.node, xElt.attr, sNewText)) {
            xElt.node[xElt.attr] = sNewText;
        }
    };

    /**
     * Search and replace the specified text.
     *
     * @param {object} args The command arguments.
     * @param {string} args.attr The name of the attribute to be set.
     * @param {string} args.search The search text and replacement text.
     * @param {string} args.replace The search text and replacement text.
     * @param {object} context The command context.
     * @param {Element} context.target The target DOM element.
     *
     * @returns {true} The operation completed successfully.
     */
    self.replace = ({ attr, search, replace }, { target }) => {
        const xElt = dom.getInnerObject(attr, target);
        if (xElt !== null) {
            replaceText(xElt, attr === 'innerHTML' ? dom.getBrowserHTML(search) : search, replace);
        }
        return true;
    };

    /**
     * Clear an element.
     *
     * @param {object} args The command arguments.
     * @param {object} context The command context.
     *
     * @returns {true} The operation completed successfully.
     */
    self.clear = (args, context) => {
        self.assign({ ...args, value: '' }, context);
        return true;
    };

    /**
     * Delete an element.
     *
     * @param {object} args The command arguments.
     * @param {object} context The command context.
     * @param {Element} context.target The target DOM element.
     *
     * @returns {true} The operation completed successfully.
     */
    self.remove = (args, { target }) => {
        target.remove();
        return true;
    };

    /**
     * @param {string} sTag The tag name for the new element.
     * @param {string} sId The id attribute of the new element.
     *
     * @returns {object}
     */
    const createNewTag = (sTag, sId) => {
        const newTag = baseDocument.createElement(sTag);
        newTag.setAttribute('id', sId);
        return newTag;
    };

    /**
     * Create a new element and append it to the specified parent element.
     *
     * @param {object} args The command arguments.
     * @param {string} args.tag.name The tag name for the new element.
     * @param {string} args.tag.id The id attribute of the new element.
     * @param {object} context The command context.
     * @param {Element} context.target The target DOM element.
     *
     * @returns {true} The operation completed successfully.
     */
    self.create = ({ tag: { id: sId, name: sTag } }, { target }) => {
        target && target.appendChild(createNewTag(sTag, sId));
        return true;
    };

    /**
     * Insert a new element before the specified element.
     *
     * @param {object} args The command arguments.
     * @param {string} args.tag.name The tag name for the new element.
     * @param {string} args.tag.id The id attribute of the new element.
     * @param {object} context The command context.
     * @param {Element} context.target The target DOM element.
     *
     * @returns {true} The operation completed successfully.
     */
    self.insertBefore = ({ tag: { id: sId, name: sTag } }, { target }) => {
        target && target.parentNode &&
            target.parentNode.insertBefore(createNewTag(sTag, sId), target);
        return true;
    };

    /**
     * Insert a new element after the specified element.
     *
     * @param {object} args The command arguments.
     * @param {string} args.tag.name The tag name for the new element.
     * @param {string} args.tag.id The id attribute of the new element.
     * @param {object} context The command context.
     * @param {Element} context.target The target DOM element.
     *
     * @returns {true} The operation completed successfully.
     */
    self.insertAfter = ({ tag: { id: sId, name: sTag } }, { target }) => {
        target && target.parentNode &&
            target.parentNode.insertBefore(createNewTag(sTag, sId), target.nextSibling);
        return true;
    };
})(jaxon.cmd.node, jaxon.utils.dom, jaxon.utils.types, jaxon.config.baseDocument);


/**
 * Class: jaxon.cmd.script
 */

(function(self, call, parameters, types) {
    /**
     * Call a javascript function with a series of parameters using the current script context.
     *
     * @param {object} args The command arguments.
     * @param {string} args.func The name of the function to call.
     * @param {array} args.args  The parameters to pass to the function.
     * @param {object} args.context The initial context to execute the command.
     * @param {object} context The command context.
     * @param {Element} context.target The target DOM element.
     *
     * @returns {true} The operation completed successfully.
     */
    self.execCall = ({ func, args, context }, { target }) => {
        call.execCall({ _type: 'func', _name: func, args }, { target, ...context });
        return true;
    };

    /**
     * Execute a javascript expression using the current script context.
     *
     * @param {object} args The command arguments.
     * @param {string} args.expr The json formatted expression to execute.
     * @param {object} args.context The initial context to execute the command.
     * @param {object} context The command context.
     * @param {Element} context.target The target DOM element.
     *
     * @returns {true} The operation completed successfully.
     */
    self.execExpr = ({ expr, context }, { target }) => {
        call.execExpr(expr, { target, ...context });
        return true;
    };

    /**
     * Redirects the browser to the specified URL.
     *
     * @param {object} args The command arguments.
     * @param {string} args.url The new URL to redirect to
     * @param {integer} args.delay The time to wait before the redirect.
     *
     * @returns {true} The operation completed successfully.
     */
    self.redirect = ({ url: sUrl, delay: nDelay }) => {
        // In no delay is provided, then use a 5ms delay.
        window.setTimeout(() => window.location = sUrl, nDelay <= 0 ? 5 : nDelay * 1000);
        return true;
    };

    /**
     * Update the databag content.
     *
     * @param {object} args The command arguments.
     * @param {string} args.values The databag values.
     *
     * @returns {true} The operation completed successfully.
     */
    self.setDatabag = ({ values }) => {
        parameters.setBags(values);
        return true;
    };

    /**
     * Replace the page number argument with the current page number value
     *
     * @param {array} aArgs
     * @param {object} oLink
     *
     * @returns {array}
     */
    const getCallArgs = (aArgs, oLink) => aArgs.map(xArg =>
        types.isObject(xArg) && xArg._type === 'page' ?
        parseInt(oLink.parentNode.getAttribute('data-page')) : xArg);

    /**
     * Set event handlers on pagination links.
     *
     * @param {object} args The command arguments.
     * @param {object} args.func The page call expression
     * @param {object} context The command context.
     * @param {Element} context.target The target DOM element.
     *
     * @returns {true} The operation completed successfully.
     */
    self.paginate = ({ func: oCall }, { target }) => {
        const aLinks = target.querySelectorAll(`li.enabled > a`);
        const { args: aArgs } = oCall;
        aLinks.forEach(oLink => oLink.addEventListener('click', () => call.execCall({
            ...oCall,
            _type: 'func',
            args: getCallArgs(aArgs, oLink),
        })));
        return true;
    };
})(jaxon.cmd.script, jaxon.parser.call, jaxon.ajax.parameters, jaxon.utils.types);


/*
    File: jaxon.js

    This file contains the definition of the main jaxon javascript core.

    This is the client side code which runs on the web browser or similar web enabled application.
    Include this in the HEAD of each page for which you wish to use jaxon.
*/

/**
 * Initiates a request to the server.
 */
jaxon.request = jaxon.ajax.request.execute;

/**
 * Registers a new command handler.
 * Shortcut to <jaxon.ajax.command.register>
 */
jaxon.register = jaxon.ajax.command.register;

/**
 * Shortcut to <jaxon.utils.dom.$>.
 */
jaxon.$ = jaxon.utils.dom.$;

/**
 * Shortcut to <jaxon.ajax.request.setCsrf>.
 */
jaxon.setCsrf = jaxon.ajax.request.setCsrf;

/**
 * Shortcut to the JQuery selector function>.
 */
jaxon.jq = jaxon.parser.query.jq;

/**
 * Shortcut to <jaxon.parser.call.execExpr>.
 */
jaxon.exec = jaxon.parser.call.execExpr;

/**
 * Shortcut to <jaxon.dialog.lib.confirm>.
 */
jaxon.confirm = jaxon.dialog.lib.confirm;

/**
 * Shortcut to <jaxon.dialog.lib.alert>.
 */
jaxon.alert = jaxon.dialog.lib.alert;

/**
 * Shortcut to <jaxon.utils.dom.ready>.
 */
jaxon.dom.ready = jaxon.utils.dom.ready;

/**
 * Shortcut to <jaxon.utils.form.getValues>.
 */
jaxon.getFormValues = jaxon.utils.form.getValues;

/**
 * Shortcut to <jaxon.ajax.parameters.setBag>.
 */
jaxon.setBag = jaxon.ajax.parameters.setBag;

/**
 * Shortcut to <jaxon.parser.attr.process>.
 */
jaxon.processCustomAttrs = jaxon.parser.attr.process;

/**
 * Indicates if jaxon module is loaded.
 */
jaxon.isLoaded = true;

/**
 * Register the command handlers provided by the library, and initialize the message object.
 */
(function(register, cmd, ajax, dialog) {
    // Pseudo command needed to complete queued commands processing.
    register('response.complete', (args, { request }) => {
        ajax.response.complete(request);
        return true;
    }, 'Response complete');

    register('node.assign', cmd.node.assign, 'Node::Assign');
    register('node.append', cmd.node.append, 'Node::Append');
    register('node.prepend', cmd.node.prepend, 'Node::Prepend');
    register('node.replace', cmd.node.replace, 'Node::Replace');
    register('node.clear', cmd.node.clear, 'Node::Clear');
    register('node.remove', cmd.node.remove, 'Node::Remove');
    register('node.create', cmd.node.create, 'Node::Create');
    register('node.insert.before', cmd.node.insertBefore, 'Node::InsertBefore');
    register('node.insert.after', cmd.node.insertAfter, 'Node::InsertAfter');

    register('script.exec.call', cmd.script.execCall, 'Script::ExecJsonCall');
    register('script.exec.expr', cmd.script.execExpr, 'Script::ExecJsonExpr');
    register('script.redirect', cmd.script.redirect, 'Script::Redirect');

    register('script.sleep', ajax.command.sleep, 'Handler::Sleep');
    register('script.confirm', ajax.command.confirm, 'Handler::Confirm');

    register('handler.event.set', cmd.event.setEventHandler, 'Script::SetEventHandler');
    register('handler.event.add', cmd.event.addEventHandler, 'Script::AddEventHandler');
    register('handler.add', cmd.event.addHandler, 'Script::AddHandler');
    register('handler.remove', cmd.event.removeHandler, 'Script::RemoveHandler');

    register('script.debug', ({ message }) => {
        console.log(message);
        return true;
    }, 'Debug message');

    // Pagination
    register('pg.paginate', cmd.script.paginate, 'Paginator::Paginate');
    // Data bags
    register('databag.set', cmd.script.setDatabag, 'Databag::SetValues');
    register('databag.clear', cmd.script.clearDatabag, 'Databag::ClearValue');
    // Dialogs
    register('dialog.alert.show', dialog.cmd.showAlert, 'Dialog::ShowAlert');
    register('dialog.modal.show', dialog.cmd.showModal, 'Dialog::ShowModal');
    register('dialog.modal.hide', dialog.cmd.hideModal, 'Dialog::HideModal');
})(jaxon.register, jaxon.cmd, jaxon.ajax, jaxon.dialog);


module.exports = jaxon;
