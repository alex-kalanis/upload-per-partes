// https://stackoverflow.com/questions/17204912/javascript-need-functions-to-convert-a-string-containing-binary-to-hex-then-co

/**
 * Encode binary file chunk into hex string to prevent problems with text-based transportation
 */
var EncoderHex2 = function () {

	this.type = function() {
		return 'hex';
	};

	this.encode = function(data) {
		return this.bin2hex(data);
	};

	/**
	 * @param {string} source
	 * @returns {string}
	 */
	this.bin2hex = function(source)
	{
		return source.split('').reduce(function(str, glyph) {
			return str + (
				glyph.charCodeAt().toString(16).length < 2
					? `0${glyph.charCodeAt().toString(16)}`
					: glyph.charCodeAt().toString(16)
			)
		}, '');
	};

	/**
	 * @param {string} source
	 * @returns {string}
	 */
	this.hex2bin = function(source)
	{
		return source.match(/.{1,2}/g).reduce(function (str, hex) {
			return str + String.fromCharCode(parseInt(hex, 16))
		}, '');
	};
}
