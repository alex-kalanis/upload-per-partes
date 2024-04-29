// https://stackoverflow.com/questions/17204912/javascript-need-functions-to-convert-a-string-containing-binary-to-hex-then-co

/**
 * Encode binary file chunk into hex string to prevent problems with text-based transportation
 */
var EncoderHex1 = function () {

	this.type = function() {
		return 'hex';
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
	 * @param {string} source
	 * @returns {string}
	 */
	this.bin2hex = function(source)
	{
		var z = -1;
		var number = 0;
		for(var i = source.length; i > -1; i--)
		{
			//Every 1 in binary string is converted to decimal and added to number
			if(source.charAt(i) == "1"){
				number += Math.pow(2, z);
			}
			z+=1;
		}
		// Return is converting decimal to hexadecimal
		return number.toString(16);
	};

	/**
	 * @param {string} source
	 * @returns {string}
	 */
	this.hex2bin = function(source)
	{
		var mybin = "";
		/// Converting to decimal value and getting ceil of decimal sqrt
		var mydec = parseInt(source, 16);
		var i = Math.ceil( Math.sqrt(mydec) );
		while(i >= 0)
		{
			if(Math.pow(2, i) <= mydec){
				mydec = mydec-Math.pow(2, i);
				mybin += "1";
			}else if(mybin != "")
				mybin = mybin + "0";
			i = i-1;
		}
		return mybin;
	};
}
