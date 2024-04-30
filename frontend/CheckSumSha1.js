var CheckSumSha1 = function () {
    /*
     * A JavaScript implementation of the RSA Data Security, Inc. MD5 Message
     * Digest Algorithm, as defined in RFC 1321.
     * Copyright (C) Paul Johnston 1999 - 2000.
     * Updated by Greg Holt 2000 - 2001.
     * See http://pajhome.org.uk/site/legal.html for details.
     */

    this.type = function() {
        return 'sha1';
    };

    this.calculate = function(data) {
        return this.calcSha1(data);
    };
};
