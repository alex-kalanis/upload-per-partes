class CheckSumMD5
{
    /*
     * JavaScript MD5 implementation. Compatible with server-side environments like Node.js, module loaders like RequireJS, Browserify or webpack and all web browsers.
     * Copyright (C) Sebastian Tschan
     * See https://github.com/blueimp/JavaScript-MD5 for details.
     */
    hexTab = '0123456789abcdef';

    type() {
        return 'md5';
    }

    calculate(data) {
        return this.hexMD5(data);
    }

    /**
     * Add integers, wrapping at 2^32.
     * This uses 16-bit operations internally to work around bugs in interpreters.
     *
     * @param {number} x First integer
     * @param {number} y Second integer
     * @returns {number} Sum
     */
    static safeAdd (x, y) {
        let lsw = (x & 0xffff) + (y & 0xffff);
        let msw = (x >> 16) + (y >> 16) + (lsw >> 16);
        return (msw << 16) | (lsw & 0xffff);
    }

    /**
     * Bitwise rotate a 32-bit number to the left.
     *
     * @param {number} num 32-bit number
     * @param {number} cnt Rotation count
     * @returns {number} Rotated number
     */
    static bitRotateLeft (num, cnt) {
        return (num << cnt) | (num >>> (32 - cnt));
    }

    /**
     * Basic operation the algorithm uses.
     *
     * @param {number} q q
     * @param {number} a a
     * @param {number} b b
     * @param {number} x x
     * @param {number} s s
     * @param {number} t t
     * @returns {number} Result
     */
    static md5cmn (q, a, b, x, s, t) {
        return CheckSumMD5.safeAdd(CheckSumMD5.bitRotateLeft(CheckSumMD5.safeAdd(CheckSumMD5.safeAdd(a, q), CheckSumMD5.safeAdd(x, t)), s), b);
    }

    /**
     * Basic operation the algorithm uses.
     *
     * @param {number} a a
     * @param {number} b b
     * @param {number} c c
     * @param {number} d d
     * @param {number} x x
     * @param {number} s s
     * @param {number} t t
     * @returns {number} Result
     */
    static md5ff (a, b, c, d, x, s, t) {
        return CheckSumMD5.md5cmn((b & c) | (~b & d), a, b, x, s, t);
    }

    /**
     * Basic operation the algorithm uses.
     *
     * @param {number} a a
     * @param {number} b b
     * @param {number} c c
     * @param {number} d d
     * @param {number} x x
     * @param {number} s s
     * @param {number} t t
     * @returns {number} Result
     */
    static md5gg (a, b, c, d, x, s, t) {
        return CheckSumMD5.md5cmn((b & d) | (c & ~d), a, b, x, s, t);
    }

    /**
     * Basic operation the algorithm uses.
     *
     * @param {number} a a
     * @param {number} b b
     * @param {number} c c
     * @param {number} d d
     * @param {number} x x
     * @param {number} s s
     * @param {number} t t
     * @returns {number} Result
     */
    static md5hh (a, b, c, d, x, s, t) {
        return CheckSumMD5.md5cmn(b ^ c ^ d, a, b, x, s, t);
    }

    /**
     * Basic operation the algorithm uses.
     *
     * @param {number} a a
     * @param {number} b b
     * @param {number} c c
     * @param {number} d d
     * @param {number} x x
     * @param {number} s s
     * @param {number} t t
     * @returns {number} Result
     */
    static md5ii (a, b, c, d, x, s, t) {
        return CheckSumMD5.md5cmn(c ^ (b | ~d), a, b, x, s, t);
    }

    /**
     * Calculate the MD5 of an array of little-endian words, and a bit length.
     *
     * @param {Array} x Array of little-endian words
     * @param {number} len Bit length
     * @returns {Array<number>} MD5 Array
     */
    static binlMD5 (x, len) {
        /* append padding */
        x[len >> 5] |= 0x80 << len % 32;
        x[(((len + 64) >>> 9) << 4) + 14] = len;

        let i;
        let olda;
        let oldb;
        let oldc;
        let oldd;
        let a = 1732584193;
        let b = -271733879;
        let c = -1732584194;
        let d = 271733878;

        for (i = 0; i < x.length; i += 16) {
            olda = a;
            oldb = b;
            oldc = c;
            oldd = d;

            a = CheckSumMD5.md5ff(a, b, c, d, x[i], 7, -680876936);
            d = CheckSumMD5.md5ff(d, a, b, c, x[i + 1], 12, -389564586);
            c = CheckSumMD5.md5ff(c, d, a, b, x[i + 2], 17, 606105819);
            b = CheckSumMD5.md5ff(b, c, d, a, x[i + 3], 22, -1044525330);
            a = CheckSumMD5.md5ff(a, b, c, d, x[i + 4], 7, -176418897);
            d = CheckSumMD5.md5ff(d, a, b, c, x[i + 5], 12, 1200080426);
            c = CheckSumMD5.md5ff(c, d, a, b, x[i + 6], 17, -1473231341);
            b = CheckSumMD5.md5ff(b, c, d, a, x[i + 7], 22, -45705983);
            a = CheckSumMD5.md5ff(a, b, c, d, x[i + 8], 7, 1770035416);
            d = CheckSumMD5.md5ff(d, a, b, c, x[i + 9], 12, -1958414417);
            c = CheckSumMD5.md5ff(c, d, a, b, x[i + 10], 17, -42063);
            b = CheckSumMD5.md5ff(b, c, d, a, x[i + 11], 22, -1990404162);
            a = CheckSumMD5.md5ff(a, b, c, d, x[i + 12], 7, 1804603682);
            d = CheckSumMD5.md5ff(d, a, b, c, x[i + 13], 12, -40341101);
            c = CheckSumMD5.md5ff(c, d, a, b, x[i + 14], 17, -1502002290);
            b = CheckSumMD5.md5ff(b, c, d, a, x[i + 15], 22, 1236535329);

            a = CheckSumMD5.md5gg(a, b, c, d, x[i + 1], 5, -165796510);
            d = CheckSumMD5.md5gg(d, a, b, c, x[i + 6], 9, -1069501632);
            c = CheckSumMD5.md5gg(c, d, a, b, x[i + 11], 14, 643717713);
            b = CheckSumMD5.md5gg(b, c, d, a, x[i], 20, -373897302);
            a = CheckSumMD5.md5gg(a, b, c, d, x[i + 5], 5, -701558691);
            d = CheckSumMD5.md5gg(d, a, b, c, x[i + 10], 9, 38016083);
            c = CheckSumMD5.md5gg(c, d, a, b, x[i + 15], 14, -660478335);
            b = CheckSumMD5.md5gg(b, c, d, a, x[i + 4], 20, -405537848);
            a = CheckSumMD5.md5gg(a, b, c, d, x[i + 9], 5, 568446438);
            d = CheckSumMD5.md5gg(d, a, b, c, x[i + 14], 9, -1019803690);
            c = CheckSumMD5.md5gg(c, d, a, b, x[i + 3], 14, -187363961);
            b = CheckSumMD5.md5gg(b, c, d, a, x[i + 8], 20, 1163531501);
            a = CheckSumMD5.md5gg(a, b, c, d, x[i + 13], 5, -1444681467);
            d = CheckSumMD5.md5gg(d, a, b, c, x[i + 2], 9, -51403784);
            c = CheckSumMD5.md5gg(c, d, a, b, x[i + 7], 14, 1735328473);
            b = CheckSumMD5.md5gg(b, c, d, a, x[i + 12], 20, -1926607734);

            a = CheckSumMD5.md5hh(a, b, c, d, x[i + 5], 4, -378558);
            d = CheckSumMD5.md5hh(d, a, b, c, x[i + 8], 11, -2022574463);
            c = CheckSumMD5.md5hh(c, d, a, b, x[i + 11], 16, 1839030562);
            b = CheckSumMD5.md5hh(b, c, d, a, x[i + 14], 23, -35309556);
            a = CheckSumMD5.md5hh(a, b, c, d, x[i + 1], 4, -1530992060);
            d = CheckSumMD5.md5hh(d, a, b, c, x[i + 4], 11, 1272893353);
            c = CheckSumMD5.md5hh(c, d, a, b, x[i + 7], 16, -155497632);
            b = CheckSumMD5.md5hh(b, c, d, a, x[i + 10], 23, -1094730640);
            a = CheckSumMD5.md5hh(a, b, c, d, x[i + 13], 4, 681279174);
            d = CheckSumMD5.md5hh(d, a, b, c, x[i], 11, -358537222);
            c = CheckSumMD5.md5hh(c, d, a, b, x[i + 3], 16, -722521979);
            b = CheckSumMD5.md5hh(b, c, d, a, x[i + 6], 23, 76029189);
            a = CheckSumMD5.md5hh(a, b, c, d, x[i + 9], 4, -640364487);
            d = CheckSumMD5.md5hh(d, a, b, c, x[i + 12], 11, -421815835);
            c = CheckSumMD5.md5hh(c, d, a, b, x[i + 15], 16, 530742520);
            b = CheckSumMD5.md5hh(b, c, d, a, x[i + 2], 23, -995338651);

            a = CheckSumMD5.md5ii(a, b, c, d, x[i], 6, -198630844);
            d = CheckSumMD5.md5ii(d, a, b, c, x[i + 7], 10, 1126891415);
            c = CheckSumMD5.md5ii(c, d, a, b, x[i + 14], 15, -1416354905);
            b = CheckSumMD5.md5ii(b, c, d, a, x[i + 5], 21, -57434055);
            a = CheckSumMD5.md5ii(a, b, c, d, x[i + 12], 6, 1700485571);
            d = CheckSumMD5.md5ii(d, a, b, c, x[i + 3], 10, -1894986606);
            c = CheckSumMD5.md5ii(c, d, a, b, x[i + 10], 15, -1051523);
            b = CheckSumMD5.md5ii(b, c, d, a, x[i + 1], 21, -2054922799);
            a = CheckSumMD5.md5ii(a, b, c, d, x[i + 8], 6, 1873313359);
            d = CheckSumMD5.md5ii(d, a, b, c, x[i + 15], 10, -30611744);
            c = CheckSumMD5.md5ii(c, d, a, b, x[i + 6], 15, -1560198380);
            b = CheckSumMD5.md5ii(b, c, d, a, x[i + 13], 21, 1309151649);
            a = CheckSumMD5.md5ii(a, b, c, d, x[i + 4], 6, -145523070);
            d = CheckSumMD5.md5ii(d, a, b, c, x[i + 11], 10, -1120210379);
            c = CheckSumMD5.md5ii(c, d, a, b, x[i + 2], 15, 718787259);
            b = CheckSumMD5.md5ii(b, c, d, a, x[i + 9], 21, -343485551);

            a = CheckSumMD5.safeAdd(a, olda);
            b = CheckSumMD5.safeAdd(b, oldb);
            c = CheckSumMD5.safeAdd(c, oldc);
            d = CheckSumMD5.safeAdd(d, oldd);
        }
        return [a, b, c, d];
    }

    /**
     * Convert an array of little-endian words to a string
     *
     * @param {Array<number>} input MD5 Array
     * @returns {string} MD5 string
     */
    static binl2rstr (input) {
        let i;
        let output = '';
        let length32 = input.length * 32;
        for (i = 0; i < length32; i += 8) {
            output += String.fromCharCode((input[i >> 5] >>> i % 32) & 0xff);
        }
        return output;
    }

    /**
     * Convert a raw string to an array of little-endian words
     * Characters >255 have their high-byte silently ignored.
     *
     * @param {string} input Raw input string
     * @returns {Array<number>} Array of little-endian words
     */
    static rstr2binl (input) {
        let i;
        let output = [];
        output[(input.length >> 2) - 1] = undefined;
        for (i = 0; i < output.length; i += 1) {
            output[i] = 0
        }
        let length8 = input.length * 8;
        for (i = 0; i < length8; i += 8) {
            output[i >> 5] |= (input.charCodeAt(i / 8) & 0xff) << i % 32
        }
        return output;
    }

    /**
     * Calculate the MD5 of a raw string
     *
     * @param {string} s Input string
     * @returns {string} Raw MD5 string
     */
    static rstrMD5 (s) {
        return CheckSumMD5.binl2rstr(CheckSumMD5.binlMD5(CheckSumMD5.rstr2binl(s), s.length * 8));
    }

    /**
     * Calculates the HMAC-MD5 of a key and some data (raw strings)
     *
     * @param {string} key HMAC key
     * @param {string} data Raw input string
     * @returns {string} Raw MD5 string
     */
    static rstrHMACMD5 (key, data) {
        let i;
        let bkey = CheckSumMD5.rstr2binl(key);
        let ipad = [];
        let opad = [];
        let hash;
        ipad[15] = opad[15] = undefined;
        if (bkey.length > 16) {
            bkey = CheckSumMD5.binlMD5(bkey, key.length * 8)
        }
        for (i = 0; i < 16; i += 1) {
            ipad[i] = bkey[i] ^ 0x36363636;
            opad[i] = bkey[i] ^ 0x5c5c5c5c;
        }
        hash = CheckSumMD5.binlMD5(ipad.concat(CheckSumMD5.rstr2binl(data)), 512 + data.length * 8);
        return CheckSumMD5.binl2rstr(CheckSumMD5.binlMD5(opad.concat(hash), 512 + 128));
    }

    /**
     * Convert a raw string to a hex string
     *
     * @param {string} input Raw input string
     * @returns {string} Hex encoded string
     */
    rstr2hex (input) {
        let output = '';
        let x;
        let i;
        for (i = 0; i < input.length; i += 1) {
            x = input.charCodeAt(i);
            output += this.hexTab.charAt((x >>> 4) & 0x0f) + this.hexTab.charAt(x & 0x0f);
        }
        return output;
    }

    /**
     * Encode a string as UTF-8
     *
     * @param {string} input Input string
     * @returns {string} UTF8 string
     */
    static str2rstrUTF8 (input) {
        return unescape(encodeURIComponent(input));
    }

    /**
     * Encodes input string as raw MD5 string
     *
     * @param {string} s Input string
     * @returns {string} Raw MD5 string
     */
    static rawMD5 (s) {
        return CheckSumMD5.rstrMD5(CheckSumMD5.str2rstrUTF8(s));
    }

    /**
     * Encodes input string as Hex encoded string
     *
     * @param {string} s Input string
     * @returns {string} Hex encoded string
     */
    hexMD5 (s) {
        return this.rstr2hex(CheckSumMD5.rawMD5(s));
    }

    /**
     * Calculates the raw HMAC-MD5 for the given key and data
     *
     * @param {string} k HMAC key
     * @param {string} d Input string
     * @returns {string} Raw MD5 string
     */
    static rawHMACMD5 (k, d) {
        return CheckSumMD5.rstrHMACMD5(CheckSumMD5.str2rstrUTF8(k), CheckSumMD5.str2rstrUTF8(d));
    }

    /**
     * Calculates the Hex encoded HMAC-MD5 for the given key and data
     *
     * @param {string} k HMAC key
     * @param {string} d Input string
     * @returns {string} Raw MD5 string
     */
    hexHMACMD5 (k, d) {
        return this.rstr2hex(CheckSumMD5.rawHMACMD5(k, d));
    }

    /**
     * Calculates MD5 value for a given string.
     * If a key is provided, calculates the HMAC-MD5 value.
     * Returns a Hex encoded string unless the raw argument is given.
     *
     * @param {string} string Input string
     * @param {string} [key] HMAC key
     * @param {boolean} [raw] Raw output switch
     * @returns {string} MD5 output
     */
    md5 (string, key, raw) {
        if (!key) {
            if (!raw) {
                return this.hexMD5(string);
            }
            return CheckSumMD5.rawMD5(string);
        }
        if (!raw) {
            return this.hexHMACMD5(key, string)
        }
        return CheckSumMD5.rawHMACMD5(key, string)
    }
}

