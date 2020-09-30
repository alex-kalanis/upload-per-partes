class CheckSumMD5
{
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
    hex_chr = "0123456789abcdef";

    rhex(num)
    {
        let str = "";
        for(let j = 0; j <= 3; j++)
            str += this.hex_chr.charAt((num >> (j * 8 + 4)) & 0x0F) +
                this.hex_chr.charAt((num >> (j * 8)) & 0x0F);
        return str;
    }

    /*
     * Convert a string to a sequence of 16-word blocks, stored as an array.
     * Append padding bits and the length, as described in the MD5 standard.
     */
    static str2blks_MD5 (str)
    {
        let nblk = ((str.length + 8) >> 6) + 1;
        let blks = new Array(nblk * 16);
        for (let i = 0; i < nblk * 16; i++) blks[i] = 0;
        for (let i = 0; i < str.length; i++) {
            blks[i >> 2] |= str.charCodeAt(i) << ((i % 4) * 8);
            blks[i >> 2] |= 0x80 << ((i % 4) * 8);
        }
        blks[nblk * 16 - 2] = str.length * 8;
        return blks;
    }

    /*
     * Add integers, wrapping at 2^32. This uses 16-bit operations internally
     * to work around bugs in some JS interpreters.
     */
    static add (x, y)
    {
        let lsw = (x & 0xFFFF) + (y & 0xFFFF);
        let msw = (x >> 16) + (y >> 16) + (lsw >> 16);
        return (msw << 16) | (lsw & 0xFFFF);
    }

    /*
     * Bitwise rotate a 32-bit number to the left
     */
    static rol (num, cnt)
    {
        return (num << cnt) | (num >>> (32 - cnt));
    }

    /*
     * These functions implement the basic operation for each round of the
     * algorithm.
     */
    static cmn (q, a, b, x, s, t)
    {
        return CheckSumMD5.add(CheckSumMD5.rol(CheckSumMD5.add(CheckSumMD5.add(a, q), CheckSumMD5.add(x, t)), s), b);
    }
    static ff (a, b, c, d, x, s, t)
    {
        return CheckSumMD5.cmn((b & c) | ((~b) & d), a, b, x, s, t);
    }
    static gg (a, b, c, d, x, s, t)
    {
        return CheckSumMD5.cmn((b & d) | (c & (~d)), a, b, x, s, t);
    }
    static hh (a, b, c, d, x, s, t)
    {
        return CheckSumMD5.cmn(b ^ c ^ d, a, b, x, s, t);
    }
    static ii (a, b, c, d, x, s, t)
    {
        return CheckSumMD5.cmn(c ^ (b | (~d)), a, b, x, s, t);
    }

    /*
     * Take a string and return the hex representation of its MD5.
     */
    calcMD5 (str)
    {
        let x = CheckSumMD5.str2blks_MD5(str);
        let a =  1732584193;
        let b = -271733879;
        let c = -1732584194;
        let d =  271733878;

        for(let i = 0; i < x.length; i += 16)
        {
            let olda = a;
            let oldb = b;
            let oldc = c;
            let oldd = d;

            a = CheckSumMD5.ff(a, b, c, d, x[i   ], 7 , -680876936);
            d = CheckSumMD5.ff(d, a, b, c, x[i+ 1], 12, -389564586);
            c = CheckSumMD5.ff(c, d, a, b, x[i+ 2], 17,  606105819);
            b = CheckSumMD5.ff(b, c, d, a, x[i+ 3], 22, -1044525330);
            a = CheckSumMD5.ff(a, b, c, d, x[i+ 4], 7 , -176418897);
            d = CheckSumMD5.ff(d, a, b, c, x[i+ 5], 12,  1200080426);
            c = CheckSumMD5.ff(c, d, a, b, x[i+ 6], 17, -1473231341);
            b = CheckSumMD5.ff(b, c, d, a, x[i+ 7], 22, -45705983);
            a = CheckSumMD5.ff(a, b, c, d, x[i+ 8], 7 ,  1770035416);
            d = CheckSumMD5.ff(d, a, b, c, x[i+ 9], 12, -1958414417);
            c = CheckSumMD5.ff(c, d, a, b, x[i+10], 17, -42063);
            b = CheckSumMD5.ff(b, c, d, a, x[i+11], 22, -1990404162);
            a = CheckSumMD5.ff(a, b, c, d, x[i+12], 7 ,  1804603682);
            d = CheckSumMD5.ff(d, a, b, c, x[i+13], 12, -40341101);
            c = CheckSumMD5.ff(c, d, a, b, x[i+14], 17, -1502002290);
            b = CheckSumMD5.ff(b, c, d, a, x[i+15], 22,  1236535329);

            a = CheckSumMD5.gg(a, b, c, d, x[i+ 1], 5 , -165796510);
            d = CheckSumMD5.gg(d, a, b, c, x[i+ 6], 9 , -1069501632);
            c = CheckSumMD5.gg(c, d, a, b, x[i+11], 14,  643717713);
            b = CheckSumMD5.gg(b, c, d, a, x[i   ], 20, -373897302);
            a = CheckSumMD5.gg(a, b, c, d, x[i+ 5], 5 , -701558691);
            d = CheckSumMD5.gg(d, a, b, c, x[i+10], 9 ,  38016083);
            c = CheckSumMD5.gg(c, d, a, b, x[i+15], 14, -660478335);
            b = CheckSumMD5.gg(b, c, d, a, x[i+ 4], 20, -405537848);
            a = CheckSumMD5.gg(a, b, c, d, x[i+ 9], 5 ,  568446438);
            d = CheckSumMD5.gg(d, a, b, c, x[i+14], 9 , -1019803690);
            c = CheckSumMD5.gg(c, d, a, b, x[i+ 3], 14, -187363961);
            b = CheckSumMD5.gg(b, c, d, a, x[i+ 8], 20,  1163531501);
            a = CheckSumMD5.gg(a, b, c, d, x[i+13], 5 , -1444681467);
            d = CheckSumMD5.gg(d, a, b, c, x[i+ 2], 9 , -51403784);
            c = CheckSumMD5.gg(c, d, a, b, x[i+ 7], 14,  1735328473);
            b = CheckSumMD5.gg(b, c, d, a, x[i+12], 20, -1926607734);

            a = CheckSumMD5.hh(a, b, c, d, x[i+ 5], 4 , -378558);
            d = CheckSumMD5.hh(d, a, b, c, x[i+ 8], 11, -2022574463);
            c = CheckSumMD5.hh(c, d, a, b, x[i+11], 16,  1839030562);
            b = CheckSumMD5.hh(b, c, d, a, x[i+14], 23, -35309556);
            a = CheckSumMD5.hh(a, b, c, d, x[i+ 1], 4 , -1530992060);
            d = CheckSumMD5.hh(d, a, b, c, x[i+ 4], 11,  1272893353);
            c = CheckSumMD5.hh(c, d, a, b, x[i+ 7], 16, -155497632);
            b = CheckSumMD5.hh(b, c, d, a, x[i+10], 23, -1094730640);
            a = CheckSumMD5.hh(a, b, c, d, x[i+13], 4 ,  681279174);
            d = CheckSumMD5.hh(d, a, b, c, x[i   ], 11, -358537222);
            c = CheckSumMD5.hh(c, d, a, b, x[i+ 3], 16, -722521979);
            b = CheckSumMD5.hh(b, c, d, a, x[i+ 6], 23,  76029189);
            a = CheckSumMD5.hh(a, b, c, d, x[i+ 9], 4 , -640364487);
            d = CheckSumMD5.hh(d, a, b, c, x[i+12], 11, -421815835);
            c = CheckSumMD5.hh(c, d, a, b, x[i+15], 16,  530742520);
            b = CheckSumMD5.hh(b, c, d, a, x[i+ 2], 23, -995338651);

            a = CheckSumMD5.ii(a, b, c, d, x[i   ], 6 , -198630844);
            d = CheckSumMD5.ii(d, a, b, c, x[i+ 7], 10,  1126891415);
            c = CheckSumMD5.ii(c, d, a, b, x[i+14], 15, -1416354905);
            b = CheckSumMD5.ii(b, c, d, a, x[i+ 5], 21, -57434055);
            a = CheckSumMD5.ii(a, b, c, d, x[i+12], 6 ,  1700485571);
            d = CheckSumMD5.ii(d, a, b, c, x[i+ 3], 10, -1894986606);
            c = CheckSumMD5.ii(c, d, a, b, x[i+10], 15, -1051523);
            b = CheckSumMD5.ii(b, c, d, a, x[i+ 1], 21, -2054922799);
            a = CheckSumMD5.ii(a, b, c, d, x[i+ 8], 6 ,  1873313359);
            d = CheckSumMD5.ii(d, a, b, c, x[i+15], 10, -30611744);
            c = CheckSumMD5.ii(c, d, a, b, x[i+ 6], 15, -1560198380);
            b = CheckSumMD5.ii(b, c, d, a, x[i+13], 21,  1309151649);
            a = CheckSumMD5.ii(a, b, c, d, x[i+ 4], 6 , -145523070);
            d = CheckSumMD5.ii(d, a, b, c, x[i+11], 10, -1120210379);
            c = CheckSumMD5.ii(c, d, a, b, x[i+ 2], 15,  718787259);
            b = CheckSumMD5.ii(b, c, d, a, x[i+ 9], 21, -343485551);

            a = CheckSumMD5.add(a, olda);
            b = CheckSumMD5.add(b, oldb);
            c = CheckSumMD5.add(c, oldc);
            d = CheckSumMD5.add(d, oldd);
        }
        return this.rhex(a) + this.rhex(b) + this.rhex(c) + this.rhex(d);
    }
}
