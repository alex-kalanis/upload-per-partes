var CheckSumMD5 = function () {
    /*
     * A JavaScript implementation of the RSA Data Security, Inc. MD5 Message
     * Digest Algorithm, as defined in RFC 1321.
     * Copyright (C) Paul Johnston 1999 - 2000.
     * Updated by Greg Holt 2000 - 2001.
     * See http://pajhome.org.uk/site/legal.html for details.
     */

    /*
     * Convert a 32-bit number to a hex string with ls-byte first
     */
    this.hex_chr = "0123456789abcdef";

    this.rhex = function(num)
    {
        var str = "";
        for(var j = 0; j <= 3; j++)
            str += this.hex_chr.charAt((num >> (j * 8 + 4)) & 0x0F) +
                this.hex_chr.charAt((num >> (j * 8)) & 0x0F);
        return str;
    };

    /*
     * Convert a string to a sequence of 16-word blocks, stored as an array.
     * Append padding bits and the length, as described in the MD5 standard.
     */
    this.str2blks_MD5 = function(str)
    {
        var nblk = ((str.length + 8) >> 6) + 1;
        var blks = new Array(nblk * 16);
        for (var k = 0; k < nblk * 16; k++) blks[k] = 0;
        for (var i = 0; i < str.length; i++) {
            blks[i >> 2] |= str.charCodeAt(i) << ((i % 4) * 8);
            blks[i >> 2] |= 0x80 << ((i % 4) * 8);
        }
        blks[nblk * 16 - 2] = str.length * 8;
        return blks;
    };

    /*
     * Add integers, wrapping at 2^32. This uses 16-bit operations internally
     * to work around bugs in some JS interpreters.
     */
    this.add = function(x, y)
    {
        var lsw = (x & 0xFFFF) + (y & 0xFFFF);
        var msw = (x >> 16) + (y >> 16) + (lsw >> 16);
        return (msw << 16) | (lsw & 0xFFFF);
    };

    /*
     * Bitwise rotate a 32-bit number to the left
     */
    this.rol = function(num, cnt)
    {
        return (num << cnt) | (num >>> (32 - cnt));
    };

    /*
     * These functions implement the basic operation for each round of the
     * algorithm.
     */
    this.cmn = function(q, a, b, x, s, t)
    {
        return checkSumMD5.add(checkSumMD5.rol(checkSumMD5.add(checkSumMD5.add(a, q), checkSumMD5.add(x, t)), s), b);
    };
    this.ff = function(a, b, c, d, x, s, t)
    {
        return checkSumMD5.cmn((b & c) | ((~b) & d), a, b, x, s, t);
    };
    this.gg = function(a, b, c, d, x, s, t)
    {
        return checkSumMD5.cmn((b & d) | (c & (~d)), a, b, x, s, t);
    };
    this.hh = function(a, b, c, d, x, s, t)
    {
        return checkSumMD5.cmn(b ^ c ^ d, a, b, x, s, t);
    };
    this.ii = function(a, b, c, d, x, s, t)
    {
        return checkSumMD5.cmn(c ^ (b | (~d)), a, b, x, s, t);
    };

    /*
     * Take a string and return the hex representation of its MD5.
     */
    this.calcMD5 = function(str)
    {
        var x = checkSumMD5.str2blks_MD5(str);
        var a =  1732584193;
        var b = -271733879;
        var c = -1732584194;
        var d =  271733878;

        for(var i = 0; i < x.length; i += 16)
        {
            var olda = a;
            var oldb = b;
            var oldc = c;
            var oldd = d;

            a = checkSumMD5.ff(a, b, c, d, x[i   ], 7 , -680876936);
            d = checkSumMD5.ff(d, a, b, c, x[i+ 1], 12, -389564586);
            c = checkSumMD5.ff(c, d, a, b, x[i+ 2], 17,  606105819);
            b = checkSumMD5.ff(b, c, d, a, x[i+ 3], 22, -1044525330);
            a = checkSumMD5.ff(a, b, c, d, x[i+ 4], 7 , -176418897);
            d = checkSumMD5.ff(d, a, b, c, x[i+ 5], 12,  1200080426);
            c = checkSumMD5.ff(c, d, a, b, x[i+ 6], 17, -1473231341);
            b = checkSumMD5.ff(b, c, d, a, x[i+ 7], 22, -45705983);
            a = checkSumMD5.ff(a, b, c, d, x[i+ 8], 7 ,  1770035416);
            d = checkSumMD5.ff(d, a, b, c, x[i+ 9], 12, -1958414417);
            c = checkSumMD5.ff(c, d, a, b, x[i+10], 17, -42063);
            b = checkSumMD5.ff(b, c, d, a, x[i+11], 22, -1990404162);
            a = checkSumMD5.ff(a, b, c, d, x[i+12], 7 ,  1804603682);
            d = checkSumMD5.ff(d, a, b, c, x[i+13], 12, -40341101);
            c = checkSumMD5.ff(c, d, a, b, x[i+14], 17, -1502002290);
            b = checkSumMD5.ff(b, c, d, a, x[i+15], 22,  1236535329);

            a = checkSumMD5.gg(a, b, c, d, x[i+ 1], 5 , -165796510);
            d = checkSumMD5.gg(d, a, b, c, x[i+ 6], 9 , -1069501632);
            c = checkSumMD5.gg(c, d, a, b, x[i+11], 14,  643717713);
            b = checkSumMD5.gg(b, c, d, a, x[i   ], 20, -373897302);
            a = checkSumMD5.gg(a, b, c, d, x[i+ 5], 5 , -701558691);
            d = checkSumMD5.gg(d, a, b, c, x[i+10], 9 ,  38016083);
            c = checkSumMD5.gg(c, d, a, b, x[i+15], 14, -660478335);
            b = checkSumMD5.gg(b, c, d, a, x[i+ 4], 20, -405537848);
            a = checkSumMD5.gg(a, b, c, d, x[i+ 9], 5 ,  568446438);
            d = checkSumMD5.gg(d, a, b, c, x[i+14], 9 , -1019803690);
            c = checkSumMD5.gg(c, d, a, b, x[i+ 3], 14, -187363961);
            b = checkSumMD5.gg(b, c, d, a, x[i+ 8], 20,  1163531501);
            a = checkSumMD5.gg(a, b, c, d, x[i+13], 5 , -1444681467);
            d = checkSumMD5.gg(d, a, b, c, x[i+ 2], 9 , -51403784);
            c = checkSumMD5.gg(c, d, a, b, x[i+ 7], 14,  1735328473);
            b = checkSumMD5.gg(b, c, d, a, x[i+12], 20, -1926607734);

            a = checkSumMD5.hh(a, b, c, d, x[i+ 5], 4 , -378558);
            d = checkSumMD5.hh(d, a, b, c, x[i+ 8], 11, -2022574463);
            c = checkSumMD5.hh(c, d, a, b, x[i+11], 16,  1839030562);
            b = checkSumMD5.hh(b, c, d, a, x[i+14], 23, -35309556);
            a = checkSumMD5.hh(a, b, c, d, x[i+ 1], 4 , -1530992060);
            d = checkSumMD5.hh(d, a, b, c, x[i+ 4], 11,  1272893353);
            c = checkSumMD5.hh(c, d, a, b, x[i+ 7], 16, -155497632);
            b = checkSumMD5.hh(b, c, d, a, x[i+10], 23, -1094730640);
            a = checkSumMD5.hh(a, b, c, d, x[i+13], 4 ,  681279174);
            d = checkSumMD5.hh(d, a, b, c, x[i   ], 11, -358537222);
            c = checkSumMD5.hh(c, d, a, b, x[i+ 3], 16, -722521979);
            b = checkSumMD5.hh(b, c, d, a, x[i+ 6], 23,  76029189);
            a = checkSumMD5.hh(a, b, c, d, x[i+ 9], 4 , -640364487);
            d = checkSumMD5.hh(d, a, b, c, x[i+12], 11, -421815835);
            c = checkSumMD5.hh(c, d, a, b, x[i+15], 16,  530742520);
            b = checkSumMD5.hh(b, c, d, a, x[i+ 2], 23, -995338651);

            a = checkSumMD5.ii(a, b, c, d, x[i   ], 6 , -198630844);
            d = checkSumMD5.ii(d, a, b, c, x[i+ 7], 10,  1126891415);
            c = checkSumMD5.ii(c, d, a, b, x[i+14], 15, -1416354905);
            b = checkSumMD5.ii(b, c, d, a, x[i+ 5], 21, -57434055);
            a = checkSumMD5.ii(a, b, c, d, x[i+12], 6 ,  1700485571);
            d = checkSumMD5.ii(d, a, b, c, x[i+ 3], 10, -1894986606);
            c = checkSumMD5.ii(c, d, a, b, x[i+10], 15, -1051523);
            b = checkSumMD5.ii(b, c, d, a, x[i+ 1], 21, -2054922799);
            a = checkSumMD5.ii(a, b, c, d, x[i+ 8], 6 ,  1873313359);
            d = checkSumMD5.ii(d, a, b, c, x[i+15], 10, -30611744);
            c = checkSumMD5.ii(c, d, a, b, x[i+ 6], 15, -1560198380);
            b = checkSumMD5.ii(b, c, d, a, x[i+13], 21,  1309151649);
            a = checkSumMD5.ii(a, b, c, d, x[i+ 4], 6 , -145523070);
            d = checkSumMD5.ii(d, a, b, c, x[i+11], 10, -1120210379);
            c = checkSumMD5.ii(c, d, a, b, x[i+ 2], 15,  718787259);
            b = checkSumMD5.ii(b, c, d, a, x[i+ 9], 21, -343485551);

            a = checkSumMD5.add(a, olda);
            b = checkSumMD5.add(b, oldb);
            c = checkSumMD5.add(c, oldc);
            d = checkSumMD5.add(d, oldd);
        }
        return checkSumMD5.rhex(a) + checkSumMD5.rhex(b) + checkSumMD5.rhex(c) + checkSumMD5.rhex(d);
    };
};

var checkSumMD5 = new CheckSumMD5();
