/**
 * Encode binary file chunk into base64 to prevent problems with text-based transportation
 */
var EncoderBase64 = function () {

	this.type = function() {
		return 'base64';
	};

	/**
	 * API
	 * @param data
	 * @returns {string|any}
	 */
	this.encode = function (data) {
		return this.base64(data);
	};

	/**
	 * Encode data into Base64
	 * @param {string} data
	 * @return {string}
	 */
	this.base64 = function(data) {
		// phpjs.org
		// http://kevin.vanzonneveld.net
		// *     example 1: base64_encode('Kevin van Zonneveld');
		// *     returns 1: 'S2V2aW4gdmFuIFpvbm5ldmVsZA=='
		// mozilla has this native
		// - but breaks in 2.0.0.12!
		var b64 = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
		var o1,
			o2,
			o3,
			h1,
			h2,
			h3,
			h4,
			bits,
			i = 0,
			ac = 0,
			tmp_arr = [];
		if (!data) {
			return data;
		}
		do {
			// pack three octets into four hexets
			o1 = data.charCodeAt(i++);
			o2 = data.charCodeAt(i++);
			o3 = data.charCodeAt(i++);
			bits = (o1 << 16) | (o2 << 8) | o3;
			h1 = (bits >> 18) & 0x3f;
			h2 = (bits >> 12) & 0x3f;
			h3 = (bits >> 6) & 0x3f;
			h4 = bits & 0x3f;
			// use hexets to index into b64, and append result to encoded string
			tmp_arr[ac++] = b64.charAt(h1) + b64.charAt(h2) + b64.charAt(h3) + b64.charAt(h4);
		} while (i < data.length);
		var enc = tmp_arr.join("");
		var r = data.length % 3;
		return (r ? enc.slice(0, r - 3) : enc) + "===".slice(r || 3);
	};
};
