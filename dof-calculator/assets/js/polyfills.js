// Polyfills for older browsers

// Fetch polyfill for Internet Explorer and older browsers
if (!window.fetch) {
    window.fetch = function(url, options) {
        return new Promise(function(resolve, reject) {
            var xhr = new XMLHttpRequest();
            var method = (options && options.method) || 'GET';
            var headers = (options && options.headers) || {};
            var body = (options && options.body) || null;
            
            xhr.open(method, url, true);
            
            // Set headers
            for (var header in headers) {
                if (headers.hasOwnProperty(header)) {
                    xhr.setRequestHeader(header, headers[header]);
                }
            }
            
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    var response = {
                        ok: xhr.status >= 200 && xhr.status < 300,
                        status: xhr.status,
                        statusText: xhr.statusText,
                        text: function() {
                            return Promise.resolve(xhr.responseText);
                        },
                        json: function() {
                            return Promise.resolve(JSON.parse(xhr.responseText));
                        }
                    };
                    
                    if (response.ok) {
                        resolve(response);
                    } else {
                        reject(new Error('Network response was not ok'));
                    }
                }
            };
            
            xhr.onerror = function() {
                reject(new Error('Network error'));
            };
            
            xhr.send(body);
        });
    };
}

// Promise polyfill for IE
if (!window.Promise) {
    window.Promise = function(executor) {
        var self = this;
        self.state = 'pending';
        self.value = undefined;
        self.onResolvedCallbacks = [];
        self.onRejectedCallbacks = [];
        
        function resolve(value) {
            if (self.state === 'pending') {
                self.state = 'resolved';
                self.value = value;
                self.onResolvedCallbacks.forEach(function(callback) {
                    callback(value);
                });
            }
        }
        
        function reject(reason) {
            if (self.state === 'pending') {
                self.state = 'rejected';
                self.value = reason;
                self.onRejectedCallbacks.forEach(function(callback) {
                    callback(reason);
                });
            }
        }
        
        try {
            executor(resolve, reject);
        } catch (e) {
            reject(e);
        }
    };
    
    Promise.prototype.then = function(onResolved, onRejected) {
        var self = this;
        return new Promise(function(resolve, reject) {
            if (self.state === 'resolved') {
                if (onResolved) {
                    try {
                        var result = onResolved(self.value);
                        resolve(result);
                    } catch (e) {
                        reject(e);
                    }
                } else {
                    resolve(self.value);
                }
            } else if (self.state === 'rejected') {
                if (onRejected) {
                    try {
                        var result = onRejected(self.value);
                        resolve(result);
                    } catch (e) {
                        reject(e);
                    }
                } else {
                    reject(self.value);
                }
            } else {
                self.onResolvedCallbacks.push(function(value) {
                    if (onResolved) {
                        try {
                            var result = onResolved(value);
                            resolve(result);
                        } catch (e) {
                            reject(e);
                        }
                    } else {
                        resolve(value);
                    }
                });
                
                self.onRejectedCallbacks.push(function(reason) {
                    if (onRejected) {
                        try {
                            var result = onRejected(reason);
                            resolve(result);
                        } catch (e) {
                            reject(e);
                        }
                    } else {
                        reject(reason);
                    }
                });
            }
        });
    };
    
    Promise.prototype.catch = function(onRejected) {
        return this.then(null, onRejected);
    };
    
    Promise.resolve = function(value) {
        return new Promise(function(resolve) {
            resolve(value);
        });
    };
    
    Promise.reject = function(reason) {
        return new Promise(function(resolve, reject) {
            reject(reason);
        });
    };
}

// Array.from polyfill
if (!Array.from) {
    Array.from = function(arrayLike, mapFn, thisArg) {
        var arr = [];
        for (var i = 0; i < arrayLike.length; i++) {
            if (mapFn) {
                arr.push(mapFn.call(thisArg, arrayLike[i], i));
            } else {
                arr.push(arrayLike[i]);
            }
        }
        return arr;
    };
}

// Object.assign polyfill
if (!Object.assign) {
    Object.assign = function(target) {
        for (var i = 1; i < arguments.length; i++) {
            var source = arguments[i];
            for (var key in source) {
                if (source.hasOwnProperty(key)) {
                    target[key] = source[key];
                }
            }
        }
        return target;
    };
}

// String.includes polyfill
if (!String.prototype.includes) {
    String.prototype.includes = function(search, start) {
        if (typeof start !== 'number') {
            start = 0;
        }
        
        if (start + search.length > this.length) {
            return false;
        } else {
            return this.indexOf(search, start) !== -1;
        }
    };
}

// classList polyfill for older browsers
if (!('classList' in document.createElement('_'))) {
    (function(view) {
        if (!('Element' in view)) return;
        
        var classListProp = 'classList',
            protoProp = 'prototype',
            elemCtrProto = view.Element[protoProp],
            objCtr = Object,
            strTrim = String[protoProp].trim || function() {
                return this.replace(/^\s+|\s+$/g, '');
            },
            arrIndexOf = Array[protoProp].indexOf || function(item) {
                var i = 0, len = this.length;
                for (; i < len; i++) {
                    if (i in this && this[i] === item) {
                        return i;
                    }
                }
                return -1;
            };
        
        var DOMTokenList = function(el) {
            this.el = el;
            var classes = el.className.replace(/^\s+|\s+$/g, '').split(/\s+/);
            for (var i = 0; i < classes.length; i++) {
                this.push(classes[i]);
            }
            this._updateClassName = function() {
                el.className = this.toString();
            };
        };
        
        DOMTokenList[protoProp] = [];
        DOMTokenList[protoProp].item = function(i) {
            return this[i] || null;
        };
        DOMTokenList[protoProp].contains = function(token) {
            token += '';
            return arrIndexOf.call(this, token) !== -1;
        };
        DOMTokenList[protoProp].add = function() {
            var tokens = arguments;
            for (var i = 0, l = tokens.length; i < l; i++) {
                var token = tokens[i] + '';
                if (arrIndexOf.call(this, token) === -1) {
                    this.push(token);
                }
            }
            this._updateClassName();
        };
        DOMTokenList[protoProp].remove = function() {
            var tokens = arguments;
            for (var i = 0, l = tokens.length; i < l; i++) {
                var token = tokens[i] + '';
                var index = arrIndexOf.call(this, token);
                if (index !== -1) {
                    this.splice(index, 1);
                }
            }
            this._updateClassName();
        };
        DOMTokenList[protoProp].toggle = function(token, force) {
            token += '';
            var result = this.contains(token),
                method = result ? force !== true && 'remove' : force !== false && 'add';
            if (method) {
                this[method](token);
            }
            if (force === true || force === false) {
                return force;
            } else {
                return !result;
            }
        };
        DOMTokenList[protoProp].toString = function() {
            return this.join(' ');
        };
        
        if (objCtr.defineProperty) {
            var defineProperty = function(object, name, definition) {
                if (definition.value) {
                    object[name] = definition.value;
                } else {
                    object.__defineGetter__(name, definition.get);
                }
            };
            try {
                defineProperty(elemCtrProto, classListProp, {
                    get: function() {
                        return new DOMTokenList(this);
                    }
                });
            } catch (ex) {
                if (ex.number === -0x7FF5EC54) {
                    defineProperty(elemCtrProto, classListProp, {
                        value: function() {
                            return new DOMTokenList(this);
                        }
                    });
                }
            }
        } else if (objCtr[protoProp].__defineGetter__) {
            elemCtrProto.__defineGetter__(classListProp, function() {
                return new DOMTokenList(this);
            });
        }
    }(self));
}