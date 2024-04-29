// https://stackoverflow.com/questions/17204912/javascript-need-functions-to-convert-a-string-containing-binary-to-hex-then-co

/**
 * Encode binary file chunk into hex string to prevent problems with text-based transportation
 */
class EncoderHex2 {

    type() {
        return 'hex';
    }

    encode(data) {
        return this.bin2hex(data);
    }

    /**
     * @param {string} source
     * @returns {string}
     */
    bin2hex(source)
    {
        return source.split('').reduce(function(str, glyph) {
            return str + (
                    glyph.charCodeAt().toString(16).length < 2
                    ? `0${glyph.charCodeAt().toString(16)}`
                    : glyph.charCodeAt().toString(16)
                )
            }, '');
    }

    /**
     * @param {string} source
     * @returns {string}
     */
    hex2bin(source)
    {
        return source.match(/.{1,2}/g).reduce(function (str, hex) {
            return str + String.fromCharCode(parseInt(hex, 16))
        }, '');
    }
}
