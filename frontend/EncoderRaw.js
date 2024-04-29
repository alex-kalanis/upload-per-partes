var EncoderRaw = function () {

	this.type = function() {
		return 'raw';
	};

	this.encode = function(data) {
		return data;
	};
}
