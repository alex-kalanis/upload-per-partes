/**
 * Targets paths on actual site
 */
var UploadTargetConfig = function () {
    this.targetInitPath = "//upload-file/init/";
    this.targetCheckPath = "//upload-file/check/";
    this.targetCancelPath = "//upload-file/cancel/";
    this.targetTrimPath = "//upload-file/trim/";
    this.targetFilePath = "//upload-file/file/";
    this.targetDonePath = "//upload-file/done/";
};

/**
 * Translations for uploader
 */
var UploadTranslations = function () {
    this.readFileCannotSlice = "Cannot slice file";
    this.initReturnsFollowingError = "Init returns following error: ";
    this.initReturnsSomethingFailed = "Init does not return a JSON data. More at console.";
    this.checkerReturnsSomethingFailed = "Data check does not return a JSON data. More at console.";
    this.dataUploadReturnsSomethingFailed = "Data upload does not return a JSON. More at console.";
    this.doneReturnsSomethingFailed = "Done does not return a JSON data. More at console.";
};

/**
 * Identify any target in selection box
 * Overwrite with values in your own selection box
 */
var UploadIdentification = function () {
    this.baseProgress = 'base_progress';
    this.knownBulk = 'list';
    this.elapsedTime = 'elapsed_time'; // time passed
    this.estimatedTimeLeft = 'est_time_left'; // time left
    this.currentPosition = 'current_position'; // last position
    this.totalSize = 'total_kbytes'; // total
    this.estimatedSpeed = 'est_speed'; // speed
    this.percentsComplete = 'percent_complete'; // percents
    this.progressCounter = 'single';
    this.localId = 'id';
    this.dataKey = 'data-key'; // attribute to pass key between displayed and stored data
    this.errorLog = 'errorlog';
};

/**
 * Client-side info about file to upload
 */
var UploadedFile = function () {
    // constants
    this.STATUS_STOP = 0;
    this.STATUS_INIT = 1;
    this.STATUS_RUN = 2;
    this.STATUS_FINISH = 3;
    this.STATUS_RETRY = 4;
    this.STATUS_DESTROY = 5;

    this.RESULT_OK = "OK";
    this.RESULT_FAIL = "FAIL";

    // initial
    /** @var {string} */
    this.localId = "";
    /** @var {string} */
    this.fileName = "";
    /** @var {number} */
    this.fileSize = 0;
    /** @var {File} */
    this.fileHandler = null;

    // processing
    /** @var {number} upload status */
    this.readStatus = this.STATUS_STOP;
    /** @var {string} what will be passed back to the server - need to remember during init and then sent repeatedly!!! */
    this.serverData = "";
    /** @var {number} total parts number */
    this.totalParts = 0;
    /** @var {number} last known part on both sides is... */
    this.lastKnownPart = 0;
    /** @var {number} last checked part on both sides is... */
    this.lastCheckedPart = 0;
    /** @var {number} max part size in bytes */
    this.partSize = 0;
    /** @var {string} when it dies... */
    this.errorMessage = "";
    /** @var {number} when the upload starts */
    this.startTime = 0;
    /** @var {string} what method will be used to encode data */
    this.encode = "";
    /** @var {string} what method will be used to check segments */
    this.check = "";
    /** @var {string} what passed back to this client */
    this.clientData = "";

    // setters
    /**
     * @param {File} fileHandler
     */
    this.setInitialData = function(fileHandler) {
        uploadedFile.localId = uploadedFile.parseLocalId(fileHandler);
        uploadedFile.fileName = fileHandler.name;
        uploadedFile.fileSize = parseInt(fileHandler.size);
        uploadedFile.fileHandler = fileHandler;
        return uploadedFile;
    };

    /**
     * @param {File} fileHandler
     * @return {string}
     */
    this.parseLocalId = function(fileHandler) {
        return "file_" + uploadedFile.parseBase(fileHandler.name).replace( /\W/g , '');
    };

    /**
     * @param {string} fileName
     * @return {string}
     */
    this.parseBase = function(fileName) {
        var lastIndex = fileName.lastIndexOf('.');
        return (0 > lastIndex) ? fileName : fileName.substr(0, lastIndex);
    };

    /**
     * @param {*} serverResponse
     * @returns {UploadedFile}
     */
    this.setInitialInfoFromServer = function(serverResponse) {
        uploadedFile.readStatus = this.STATUS_RUN;
        uploadedFile.serverData = serverResponse.serverData;
        uploadedFile.totalParts = parseInt(serverResponse.totalParts);
        uploadedFile.lastKnownPart = parseInt(serverResponse.lastKnownPart);
        uploadedFile.partSize = parseInt(serverResponse.partSize);
        uploadedFile.errorMessage = serverResponse.errorMessage;
        uploadedFile.clientData = serverResponse.clientData;
        uploadedFile.encode = serverResponse.method;
        uploadedFile.check = serverResponse.checksum;
        return uploadedFile;
    };

    /**
     * @param {*} serverResponse
     * @returns {UploadedFile}
     */
    this.setRunnerInfoFromServer = function(serverResponse) {
        uploadedFile.readStatus = this.STATUS_RUN;
        uploadedFile.serverData = serverResponse.serverData;
        uploadedFile.lastKnownPart = parseInt(serverResponse.lastKnownPart);
        uploadedFile.errorMessage = serverResponse.errorMessage;
        uploadedFile.clientData = serverResponse.clientData;
        return uploadedFile;
    };

    /**
     * @param {*} serverResponse
     * @returns {UploadedFile}
     */
    this.setDoneInfoFromServer = function(serverResponse) {
        uploadedFile.readStatus = this.STATUS_FINISH;
        uploadedFile.fileName = serverResponse.fileName;
        uploadedFile.serverData = serverResponse.serverData;
        uploadedFile.errorMessage = serverResponse.errorMessage;
        uploadedFile.clientData = serverResponse.clientData;
        return uploadedFile;
    };

    /**
     * @param {*} serverResponse
     * @returns {UploadedFile}
     */
    this.setCancelInfoFromServer = function(serverResponse) {
        uploadedFile.readStatus = this.STATUS_FINISH;
        uploadedFile.serverData = serverResponse.serverData;
        uploadedFile.errorMessage = serverResponse.errorMessage;
        uploadedFile.clientData = serverResponse.clientData;
        return uploadedFile;
    };

    /**
     * @returns {UploadedFile}
     */
    this.setInfoFromClearer = function() {
        uploadedFile.readStatus = this.STATUS_RUN;
        uploadedFile.lastCheckedPart = 0;
        uploadedFile.errorMessage = '';
        return uploadedFile;
    };

    /**
     * @param {string} message
     * @param {number} status
     * @returns {UploadedFile}
     */
    this.setError = function(message, status) {
        if (status === undefined) {
            status = this.STATUS_STOP;
        }
        uploadedFile.readStatus = status;
        uploadedFile.errorMessage = message;
        return uploadedFile;
    };

    /**
     * @returns {UploadedFile}
     */
    this.nextFilePart = function() {
        uploadedFile.lastKnownPart++;
        return uploadedFile;
    };

    /**
     * @returns {UploadedFile}
     */
    this.nextCheckedPart = function() {
        uploadedFile.lastCheckedPart++;
        return uploadedFile;
    };

    /**
     * @param {*} serverResponse
     * @returns {UploadedFile}
     */
    this.setTruncatedFromServer = function(serverResponse) {
        uploadedFile.lastCheckedPart = uploadedFile.lastKnownPart = parseInt(serverResponse.lastKnownPart);
        return uploadedFile;
    }
};

/**
 * Main processing class
 * Used for passing info between steps, so they do not need to know about each other
 */
var UploaderProcessor = function () {

    /** @var {UploaderInit} */
    this.upInit = null;
    /** @var {UploaderChecker} */
    this.upCheck = null;
    /** @var {UploaderRunner} */
    this.upRunner = null;
    /** @var {UploaderFailure} */
    this.upFailure = null;
    /** @var {UploaderHandler} */
    this.upHandler = null;

    /**
     * @param {UploaderQuery} upQuery
     * @param {UploadTranslations} translations
     * @param {UploadTargetConfig} targetConfig
     */
    this.init = function(upQuery, translations, targetConfig) {
        var remoteQuery = uploaderRemoteQuery.init(upQuery, targetConfig);
        var upRenderer = uploaderRenderer.init(upQuery, uploadIdentification);
        var upReader = uploaderReader.init(translations);
        uploaderProcessor.upInit = uploadInit.init(uploaderProcessor, upRenderer, remoteQuery, translations);
        uploaderProcessor.upCheck = uploaderChecker.init(uploaderProcessor, uploaderChecksum, upReader, upRenderer, remoteQuery, translations);
        uploaderProcessor.upRunner = uploaderRunner.init(uploaderProcessor, upReader, uploaderEncoder, upRenderer, remoteQuery, translations);
        uploaderProcessor.upFailure = uploaderFailure.init(uploaderProcessor, upRenderer, remoteQuery, translations);
        uploaderProcessor.upHandler = uploaderHandler.init(uploaderProcessor, upRenderer);
        return uploaderProcessor;
    };

    /**
     * Sent initial request and get driving instructions
     * @param {UploadedFile} uploadedFile
     */
    this.initRead = function(uploadedFile) {
        uploaderProcessor.upInit.process(uploadedFile);
    };

    /**
     * Call checking of file
     * @param {UploadedFile} uploadedFile
     */
    this.checkParts = function(uploadedFile) {
        uploaderProcessor.upCheck.process(uploadedFile);
    };

    /**
     * Call uploading of file
     * @param {UploadedFile} uploadedFile
     */
    this.uploadParts = function(uploadedFile) {
        uploaderProcessor.upRunner.process(uploadedFile);
    };

    /**
     * Call dumpster render when something fails
     * @param {UploadedFile} uploadedFile
     * @param {*} event
     */
    this.failProcess = function(uploadedFile, event) {
        if (event === undefined) {
            event = null;
        }
        uploaderProcessor.upFailure.process(uploadedFile, event);
    };

    /**
     * Call dumpster response when user decide what to do
     * @param {UploadedFile} uploadedFile
     */
    this.failContinue = function(uploadedFile) {
        uploaderProcessor.upFailure.continue(uploadedFile);
    };

    /**
     * Call dumpster when something fails
     * @param {UploadedFile} uploadedFile
     * @param {*} event
     */
    this.failEnd = function(uploadedFile, event) {
        if (event === undefined) {
            event = null;
        }
        uploaderProcessor.upFailure.end(uploadedFile, event);
    };

    /**
     * @returns {UploaderHandler}
     */
    this.getHandler = function() {
        return uploaderProcessor.upHandler;
    }
};

/**
 * Initial step - prepare file object, ask server about upload details and let him to prepare himself
 */
var UploadInit = function () {
    /** @var {UploaderProcessor} */
    this.upProcessor = null;
    /** @var {UploaderRenderer} */
    this.upRenderer = null;
    /** @var {UploaderRemoteQuery} */
    this.upQuery = null;
    /** @var {UploadTranslations} */
    this.upLang = null;

    /**
     * @param {UploaderProcessor} processor
     * @param {UploaderRenderer} renderer
     * @param {UploaderRemoteQuery} query
     * @param {UploadTranslations} lang
     */
    this.init = function(processor, renderer, query, lang) {
        uploadInit.upProcessor = processor;
        uploadInit.upRenderer = renderer;
        uploadInit.upQuery = query;
        uploadInit.upLang = lang;
        return uploadInit;
    };

    /**
     * Sent initial request and get driving instructions
     * @param {UploadedFile} uploadedFile
     */
    this.process = function(uploadedFile) {
        uploadedFile.startTime = uploadInit.upRenderer.getCurrentTime();
        uploadInit.upRenderer.updateBar(uploadedFile);
        uploadInit.upQuery.begin(
            {
                fileName: uploadedFile.fileName,
                fileSize: uploadedFile.fileSize,
                clientData: uploadedFile.clientData
            },
            function(responseData) {
                if (typeof responseData == "object") {
                    uploadedFile.setInitialInfoFromServer(responseData);
                    if (uploadedFile.RESULT_OK === responseData.status) {
                        // start checking content
                        uploadInit.upRenderer.renderReaded(uploadedFile);
                        uploadInit.upProcessor.checkParts(uploadedFile);
                    } else {
                        // uploadedFile.RESULT_FAIL
                        // File is dead, sent user info
                        uploadInit.upRenderer.consoleError(uploadedFile, responseData);
                        uploadedFile.setError(uploadInit.upLang.initReturnsFollowingError + responseData.errorMessage, responseData.status);
                    }
                } else {
                    // Query dead, sent user info
                    uploadInit.upRenderer.consoleError(uploadedFile, responseData);
                    uploadedFile.setError(uploadInit.upLang.initReturnsSomethingFailed, uploadedFile.RESULT_FAIL);
                }
            },
            function(err) {
                uploadInit.upRenderer.consoleError(uploadedFile, err);
            }
        );
    }
};

/**
 * Check already uploaded parts, trim failed ones
 */
var UploaderChecker = function () {
    /** @var {UploaderProcessor} */
    this.upProcessor = null;
    /** @var {UploaderChecksum} */
    this.upChecksum = null;
    /** @var {UploaderReader} */
    this.upReader = null;
    /** @var {UploaderRenderer} */
    this.upRenderer = null;
    /** @var {UploaderRemoteQuery} */
    this.upQuery = null;
    /** @var {UploadTranslations} */
    this.upLang = null;

    /**
     * @param {UploaderProcessor} processor
     * @param {UploaderChecksum} checksum
     * @param {UploaderReader} reader
     * @param {UploaderRenderer} renderer
     * @param {UploaderRemoteQuery} query
     * @param {UploadTranslations} lang
     */
    this.init = function(processor, checksum, reader, renderer, query, lang) {
        uploaderChecker.upProcessor = processor;
        uploaderChecker.upChecksum = checksum;
        uploaderChecker.upReader = reader;
        uploaderChecker.upRenderer = renderer;
        uploaderChecker.upQuery = query;
        uploaderChecker.upLang = lang;
        return uploaderChecker;
    };

    /**
     * Sent initial request and get driving instructions
     * @param {UploadedFile} uploadedFile
     */
    this.process = function(uploadedFile) {
        uploaderChecker.stillCheck(uploadedFile);
    };

    this.continueChecking = function(uploadedFile) {
        if (uploadedFile.STATUS_RUN === uploadedFile.readStatus) {
            uploaderChecker.stillCheck(uploadedFile);
        } else {
            uploaderChecker.upProcessor.failProcess(uploadedFile, null);
        }
    };

    this.stillCheck = function(uploadedFile) {
        if (uploadedFile.lastCheckedPart < uploadedFile.lastKnownPart) {
            uploaderChecker.checkPart(uploadedFile);
        } else {
            uploaderChecker.nextStep(uploadedFile);
        }
    };

    this.nextStep = function(uploadedFile) {
        if (uploadedFile.STATUS_RUN === uploadedFile.readStatus) {
            uploaderChecker.upProcessor.uploadParts(uploadedFile);
        } else {
            uploaderChecker.upProcessor.failProcess(uploadedFile, null);
        }
    };

    /**
     * Check data on server - for each already uploaded part
     * @param {UploadedFile} uploadedFile
     */
    this.checkPart = function(uploadedFile) {
        var encoder = uploaderChecker.upChecksum.getChecksum(uploadedFile.check);
        if (uploaderChecker.upChecksum.can(encoder)) {
            uploaderChecker.upQuery.check(
                {
                    serverData: uploadedFile.serverData,
                    segment: uploadedFile.lastCheckedPart,
                    method: uploaderChecker.upChecksum.method(),
                    clientData: uploadedFile.clientData
                },
                function(responseData) {
                    if (typeof responseData == "object") {
                        uploadedFile.setCancelInfoFromServer(responseData);
                        if (uploadedFile.RESULT_OK === responseData.status) {
                            // got known checksum on remote - check it against local file
                            uploaderChecker.upReader.processFileRead(uploadedFile, uploadedFile.lastCheckedPart, function (result) {
                                if (responseData.checksum === uploaderChecker.upChecksum.calculate(result)) {
                                    // this part is OK, move to the next one
                                    uploaderChecker.processNext(uploadedFile);
                                } else {
                                    // Check failed, time to truncate
                                    uploaderChecker.truncatePart(uploadedFile);
                                }
                            }, function (event) {
                                uploaderChecker.upRenderer.updateStatus(uploadedFile, event.error);
                            });
                        } else {
                            // failed query
                            uploaderChecker.upProcessor.failProcess(uploadedFile, null);
                        }
                    } else {
                        // Query dead, sent user info
                        uploaderChecker.upRenderer.consoleError(uploadedFile, responseData);
                        uploadedFile.setError(uploaderChecker.upLang.initReturnsSomethingFailed, uploadedFile.RESULT_FAIL);
                    }
                },
                function(err) {
                    uploaderChecker.upProcessor.failProcess(uploadedFile, err);
                }
            );
        } else {
            uploaderChecker.truncateRest(uploadedFile);
        }
    };

    /**
     * callback for processing next part
     * @param uploadedFile
     */
    this.processNext = function(uploadedFile) {
        // this part is OK, move to the next one
        uploaderChecker.upRenderer.updateBar(uploadedFile.nextCheckedPart());
        uploaderChecker.continueChecking(uploadedFile);
    };

    /**
     * callback for truncating the rest
     * @param uploadedFile
     */
    this.truncateRest = function(uploadedFile) {
        // from this part it's shitty - remove it
        uploaderChecker.truncatePart(uploadedFile);
    };

    /**
     * Truncate failed data on server, to upload them correctly
     * @param {UploadedFile} uploadedFile
     */
    this.truncatePart = function(uploadedFile) {
        uploaderChecker.upQuery.trim(
            {
                serverData: uploadedFile.serverData,
                segment: uploadedFile.lastCheckedPart,
                clientData: uploadedFile.clientData
            },
            function(responseData) {
                if (typeof responseData == "object") {
                    uploadedFile.setRunnerInfoFromServer(responseData);
                    if (uploadedFile.RESULT_OK === responseData.status) {
                        // Truncate came OK, time to upload the rest
                        uploaderChecker.upRenderer.updateBar(uploadedFile.setTruncatedFromServer(responseData));
                        uploaderChecker.nextStep(uploadedFile);
                    } else {
                        // Truncate failed, time say something
                        uploaderChecker.upProcessor.failProcess(uploadedFile, null);
                    }
                } else {
                    // Query dead, sent user info
                    uploaderChecker.upRenderer.consoleError(uploadedFile, responseData);
                    uploadedFile.setError(uploaderChecker.upLang.checkerReturnsSomethingFailed, uploadedFile.RESULT_FAIL);
                }
            },
            function(err) {
                uploaderChecker.upRenderer.consoleError(uploadedFile, err);
            }
        );
    }
};

/**
 * Uploading file from client and ask for processing on server side
 */
var UploaderRunner = function () {
    /** @var {UploaderProcessor} */
    this.upProcessor = null;
    /** @var {UploaderReader} */
    this.upReader = null;
    /** @var {UploaderEncoder} */
    this.upEncoder = null;
    /** @var {UploaderRenderer} */
    this.upRenderer = null;
    /** @var {UploaderRemoteQuery} */
    this.upQuery = null;
    /** @var {UploadTranslations} */
    this.upLang = null;

    /**
     * @param {UploaderProcessor} processor
     * @param {UploaderReader} reader
     * @param {UploaderEncoder} encoder
     * @param {UploaderRenderer} renderer
     * @param {UploaderRemoteQuery} query
     * @param {UploadTranslations} lang
     */
    this.init = function(processor, reader, encoder, renderer, query, lang) {
        uploaderRunner.upProcessor = processor;
        uploaderRunner.upReader = reader;
        uploaderRunner.upEncoder = encoder;
        uploaderRunner.upRenderer = renderer;
        uploaderRunner.upQuery = query;
        uploaderRunner.upLang = lang;
        return uploaderRunner;
    };

    /**
     * Sent initial request and get driving instructions
     * @param {UploadedFile} uploadedFile
     */
    this.process = function(uploadedFile) {
        uploaderRunner.stillRunning(uploadedFile);
    };

    this.stillRunning = function(uploadedFile) {
        if (uploadedFile.lastKnownPart < uploadedFile.totalParts) {
            uploaderRunner.uploadPart(uploadedFile);
        } else {
            uploaderRunner.closePart(uploadedFile);
        }
    };

    this.continueRunning = function(uploadedFile) {
        if (uploadedFile.STATUS_RUN === uploadedFile.readStatus) {
            uploaderRunner.stillRunning(uploadedFile);
        } else {
            uploaderRunner.upProcessor.failProcess(uploadedFile, null);
        }
    };

    /**
     * Sent request which contains part of uploaded file
     * @param {UploadedFile} uploadedFile
     */
    this.uploadPart = function(uploadedFile) {
        uploaderRunner.upRenderer.updateBar(uploadedFile);
        uploaderRunner.upReader.processFileRead(uploadedFile, uploadedFile.lastKnownPart, function (result) {
            var encoder = uploaderRunner.upEncoder.getEncoder(uploadedFile.encode);
            if (uploaderRunner.upEncoder.can(encoder)) {
                uploaderRunner.upQuery.upload(
                    {
                        serverData: uploadedFile.serverData,
                        content: encoder.encode(result),
                        method: encoder.method(),
                        clientData: uploadedFile.clientData
                    },
                    function(responseData) {
                        if (typeof responseData == "object") {
                            uploadedFile.setRunnerInfoFromServer(responseData);
                            if (uploadedFile.RESULT_OK === responseData.status) {
                                // everything ok
                                uploadedFile.nextFilePart();
                                uploaderRunner.upRenderer.updateBar(uploadedFile.nextCheckedPart());
                                uploaderRunner.continueRunning(uploadedFile);
                            } else {
                                // dead file, user info
                                uploaderRunner.upProcessor.failProcess(uploadedFile, null);
                            }
                        } else {
                            uploaderRunner.upRenderer.consoleError(uploadedFile, responseData);
                            uploadedFile.setError(uploaderRunner.upLang.dataUploadReturnsSomethingFailed, uploadedFile.RESULT_FAIL);
                        }
                    },
                    function(err) {
                        uploaderRunner.upRenderer.consoleError(uploadedFile, err);
                    }
                );
            } else {
                self.upRenderer.consoleError(uploadedFile, encoder);
                uploadedFile.setError(self.upLang.dataUploadEncoderFailed, uploadedFile.RESULT_FAIL);
            }
        }, function (event) {
            uploaderRunner.upProcessor.failProcess(uploadedFile, event);
        });
    };

    /**
     * Send request about upload closure
     * @param {UploadedFile} uploadedFile
     */
    this.closePart = function(uploadedFile) {
        uploaderRunner.upQuery.done(
            {
                serverData: uploadedFile.serverData,
                clientData: uploadedFile.clientData
            },
            function(responseData) {
                if (typeof responseData == "object") {
                    uploadedFile.setDoneInfoFromServer(responseData);
                    if (uploadedFile.RESULT_OK === responseData.status) {
                        // everything ok
                        uploadedFile.readStatus = uploadedFile.STATUS_FINISH;
                        uploaderRunner.upRenderer.renderFinished(uploadedFile);
                    } else {
                        // dead file, user info
                        uploaderRunner.upProcessor.failProcess(uploadedFile, null);
                    }
                } else {
                    // dead query, user info
                    uploaderRunner.upRenderer.consoleError(uploadedFile, responseData);
                    uploadedFile.setError(uploaderRunner.upLang.doneReturnsSomethingFailed, uploadedFile.RESULT_FAIL);
                }
            },
            function(err) {
                uploaderRunner.upRenderer.consoleError(uploadedFile, err);
            }
        );
    }
};

/**
 * When upload fails then it's necessary to have user-specific action. So here call that.
 */
var UploaderFailure = function () {
    /** @var {UploaderProcessor} */
    this.upProcessor = null;
    /** @var {UploaderRenderer} */
    this.upRenderer = null;
    /** @var {UploaderRemoteQuery} */
    this.upQuery = null;
    /** @var {UploadTranslations} */
    this.upLang = null;

    /**
     * @param {UploaderProcessor} processor
     * @param {UploaderRenderer} renderer
     * @param {UploaderRemoteQuery} query
     * @param {UploadTranslations} lang
     */
    this.init = function(processor, renderer, query, lang) {
        uploaderFailure.upProcessor = processor;
        uploaderFailure.upRenderer = renderer;
        uploaderFailure.upQuery = query;
        uploaderFailure.upLang = lang;
        return uploaderFailure;
    };

    /**
     * Show instructions for user
     * @param {UploadedFile} uploadedFile
     * @param {*} event
     */
    this.process = function(uploadedFile, event) {
        if (event === undefined) {
            event = null;
        }
        // render output, want user input
        // we need info if is necessary to use total clear and if continue
        // this.stillRunning(uploadedFile);
    };

    this.continue = function(uploadedFile) {
        // init, finish, retry, destroy
        // cr-cont; end; cont; cr-end
        if ((uploadedFile.STATUS_INIT === uploadedFile.status) || (uploadedFile.STATUS_DESTROY === uploadedFile.status)) {
            uploaderFailure.contentRemoval(uploadedFile);
        } else {
            uploaderFailure.checkContinue(uploadedFile);
        }
    };

    this.checkContinue = function(uploadedFile) {
        if (uploadedFile.STATUS_INIT === uploadedFile.status) {
            uploaderFailure.upProcessor.initRead(uploadedFile);
        } else if (uploadedFile.STATUS_RETRY === uploadedFile.status) {
            uploaderFailure.upProcessor.checkPart(uploadedFile.setInfoFromClearer());
        } else {
            uploaderFailure.upProcessor.failEnd(uploadedFile);
        }
    };

    /**
     * Send request about upload closure
     * @param {UploadedFile} uploadedFile
     */
    this.contentRemoval = function(uploadedFile) {
        uploaderFailure.upQuery.cancel(
            {
                serverData: uploadedFile.serverData,
                clientData: uploadedFile.clientData
            },
            function(responseData) {
                if (typeof responseData == "object") {
                    uploadedFile.setCancelInfoFromServer(responseData);
                    if (uploadedFile.RESULT_OK === responseData.status) {
                        // everything done
                        uploaderFailure.checkContinue(uploadedFile);
                    } else {
                        // dead file, user info
                        uploaderFailure.end(uploadedFile);
                    }
                } else {
                    // dead query, user info
                    uploaderFailure.upRenderer.consoleError(uploadedFile, responseData);
                    uploadedFile.setError(uploaderFailure.upLang.doneReturnsSomethingFailed, uploadedFile.RESULT_FAIL);
                }
            },
            function(err) {
                uploaderFailure.upRenderer.consoleError(uploadedFile, err);
            }
        );
    };

    /**
     * Show instructions, cannot be done more
     * @param {UploadedFile} uploadedFile
     * @param {*} event
     */
    this.end = function(uploadedFile, event) {
        if (event === undefined) {
            event = null;
        }
        // render output, no user input need
    }
};

/**
 * Class for reading selected file on client's storage
 */
var UploaderReader = function () {
    /** @var {UploadTranslations} */
    this.upLang = null;

    /**
     * @param {UploadTranslations} upLang
     */
    this.init = function(upLang) {
        uploaderReader.upLang = upLang;
        return uploaderReader;
    };

    /**
     * Processing check response that came from server
     * @param {UploadedFile} uploadedFile
     * @param {number} segment
     * @param {*} onSuccess
     * @param {*} onFailure
     */
    this.processFileRead = function(uploadedFile, segment, onSuccess, onFailure) {
        var reader = new FileReader();
        reader.onload = function(event) {
            if (event.target.readyState === reader.DONE) {
                onSuccess(event.target.result);
            }
        };
        reader.onabort = function(event) {
            onFailure(event);
        };
        reader.onerror = function(event) {
            onFailure(event);
        };
        var blob = uploaderReader.fileSlice(
            uploadedFile,
            uploadedFile.partSize * segment,
            (segment + 1 === uploadedFile.totalParts) ? uploadedFile.fileSize : uploadedFile.partSize * (segment + 1)
        );
        if (null == blob) {
            uploadedFile.setError(uploaderReader.upLang.readFileCannotSlice, uploadedFile.RESULT_FAIL);
        } else {
            reader.readAsBinaryString(blob);
        }
    };

    /**
     * @param {UploadedFile} uploadedFile
     * @param {number} begin seek at beginning of segment
     * @param {number} end   seek of ending of segment
     * @returns {null|Blob}
     */
    this.fileSlice = function(uploadedFile, begin, end) {
        if (uploadedFile.fileHandler.slice) {
            return uploadedFile.fileHandler.slice(begin, end);
        } else if (uploadedFile.fileHandler.mozSlice) {
            return uploadedFile.fileHandler.mozSlice(begin, end);
        } else if (uploadedFile.fileHandler.webkitSlice) {
            return uploadedFile.fileHandler.webkitSlice(begin, end);
        } else {
            return null;
        }
    };

    /**
     * @param {*} window
     */
    this.canRead = function(window) {
        return window.File && window.FileReader && window.FileList && window.Blob
    }
};

/**
 * Encode binary file chunk into something else to prevent problems with text-based transportation
 */
var UploaderEncoder = function () {
    /**
     * @param {string} encoder
     * @return {null|EncoderBase64|EncoderRaw|EncoderHex2}
     */
    this.getEncoder = function(encoder) {
        switch (encoder) {
            case 'raw':
                return new EncoderRaw();
            case 'hex':
                return new EncoderHex2();
            case 'base64':
                return new EncoderBase64();
            default:
                return null;
        }
    };

    this.can = function(encoder) {
        return (null != encoder);
    };
};

/**
 * Class for making checksum of segments, usually use MD5
 */
var UploaderChecksum = function () {

    /**
     * @param {string} checksum
     * @return {null|CheckSumMD5|CheckSumSha1}
     */
    this.getChecksum = function(checksum) {
        switch (checksum) {
            case 'md5':
                return new CheckSumMD5();
            // case 'sha1':
            //     return new CheckSumSha1();
            default:
                return null;
        }
    };

    this.can = function(checksum) {
        return (null != checksum);
    };
};

/**
 * Abstract class for enabling different engines for Ajax call and content selectors, not just jQuery
 */
var UploaderQuery = function () {
    /** @var {jQuery} */
    this.queryEngine = null;

    /**
     * @param {jQuery} queryEngine
     */
    this.init = function(queryEngine) {
        uploaderQuery.queryEngine = queryEngine;
        return uploaderQuery;
    };

    /**
     * Post query on remote system
     * @param {string} target
     * @param {Object} params
     * @param {function} onSuccess
     * @param {function} onError
     */
    this.post = function(target, params, onSuccess, onError) {
        uploaderQuery.queryEngine.post(target, params).done(onSuccess).fail(onError);
    };

    /**
     * @param {string} ident
     * @returns {jQuery|*}
     */
    this.getObjectById = function(ident) {
        return uploaderQuery.queryEngine('#' + ident);
    };

    /**
     * @param {string} ident
     * @param {string} content
     */
    this.setObjectContent = function(ident, content) {
        uploaderQuery.queryEngine('#' + ident).text(content);
    }
};

/**
 * Queries to remote machine
 * Should be extended in tests for mocking the querying to behave likewise with the remote machine
 */
var UploaderRemoteQuery = function () {
    /** @var {UploaderQuery} */
    this.queryLib = null;
    /** @var {UploadTargetConfig} */
    this.targetLinks = null;

    /**
     * @param {UploaderQuery} queryLib
     * @param {UploadTargetConfig} targetLinks
     */
    this.init = function(queryLib, targetLinks) {
        uploaderRemoteQuery.queryLib = queryLib;
        uploaderRemoteQuery.targetLinks = targetLinks;
        return uploaderRemoteQuery;
    };

    /**
     * @param {object} queryParams
     * @param {function} onSuccess
     * @param {function} onError
     */
    this.begin = function(queryParams, onSuccess, onError) {
        uploaderRemoteQuery.queryLib.post(
            uploaderRemoteQuery.targetLinks.targetInitPath,
            queryParams,
            onSuccess,
            onError
        );
    };

    /**
     * @param {object} queryParams
     * @param {function} onSuccess
     * @param {function} onError
     */
    this.check = function(queryParams, onSuccess, onError) {
        uploaderRemoteQuery.queryLib.post(
            uploaderRemoteQuery.targetLinks.targetCheckPath,
            queryParams,
            onSuccess,
            onError
        );
    };

    /**
     * @param {object} queryParams
     * @param {function} onSuccess
     * @param {function} onError
     */
    this.trim = function(queryParams, onSuccess, onError) {
        uploaderRemoteQuery.queryLib.post(
            uploaderRemoteQuery.targetLinks.targetTrimPath,
            queryParams,
            onSuccess,
            onError
        );
    };

    /**
     * @param {object} queryParams
     * @param {function} onSuccess
     * @param {function} onError
     */
    this.upload = function(queryParams, onSuccess, onError) {
        uploaderRemoteQuery.queryLib.post(
            uploaderRemoteQuery.targetLinks.targetFilePath,
            queryParams,
            onSuccess,
            onError
        );
    };

    /**
     * @param {object} queryParams
     * @param {function} onSuccess
     * @param {function} onError
     */
    this.done = function(queryParams, onSuccess, onError) {
        uploaderRemoteQuery.queryLib.post(
            uploaderRemoteQuery.targetLinks.targetDonePath,
            queryParams,
            onSuccess,
            onError
        );
    };

    /**
     * @param {object} queryParams
     * @param {function} onSuccess
     * @param {function} onError
     */
    this.cancel = function(queryParams, onSuccess, onError) {
        uploaderRemoteQuery.queryLib.post(
            uploaderRemoteQuery.targetLinks.targetCancelPath,
            queryParams,
            onSuccess,
            onError
        );
    }
};

/**
 * Handle input from browser
 */
var UploaderHandler = function () {
    /** @type {UploadedFile[]} */
    this.uploadingFiles = []; // All uploaded files in JS
    /** @var {UploaderProcessor} */
    this.upProcessor = null;
    /** @var {UploaderRenderer} */
    this.upRenderer = null;

    /**
     * @param {UploaderProcessor} processor
     * @param {UploaderRenderer} renderer
     */
    this.init = function(processor, renderer) {
        uploaderHandler.upProcessor = processor;
        uploaderHandler.upRenderer = renderer;
        return uploaderHandler;
    };

    this.handleFileSelect = function(evt) {
        // files is a FileList of File objects. List some properties.
        uploaderHandler.addFileInput(evt.dataTransfer.files);
    };

    this.handleFileInput = function(evt) {
        uploaderHandler.addFileInput(evt.target.files);
    };

    /**
     * @param {FileList} files
     */
    this.addFileInput = function(files) {
        for (var i = 0, f; (f = files[i]); i++) {
            var dataSource = uploadedFile;
            dataSource.setInitialData(f);
            uploaderHandler.uploadingFiles.push(dataSource);
            uploaderHandler.upRenderer.renderFileItem(dataSource);
        }
    };

    /**
     * @param {string} fileId
     */
    this.startRead = function(fileId) {
        var file = uploaderHandler.searchFile(fileId);
        if (null != file) {
            // one
            file.readStatus = file.STATUS_INIT;
            uploaderHandler.upProcessor.initRead(file);
        } else {
            // all
            for (var i = 0; i < uploaderHandler.uploadingFiles.length; i++) {
                uploaderHandler.startRead(uploaderHandler.uploadingFiles[i].localId);
            }
        }
    };

    /**
     * @param {string} fileId
     */
    this.stopRead = function(fileId) {
        var file = uploaderHandler.searchFile(fileId);
        if (null != file) {
            // one
            file.readStatus = file.STATUS_STOP;
            // it stops with next tick
        } else {
            // all recursive
            for (var i = 0; i < uploaderHandler.uploadingFiles.length; i++) {
                uploaderHandler.stopRead(uploaderHandler.uploadingFiles[i].localId);
            }
        }
    };

    /**
     * @param {string} fileId
     */
    this.resumeRead = function(fileId) {
        var file = uploaderHandler.searchFile(fileId);
        if (file != null) {
            // one
            file.readStatus = file.STATUS_RETRY;
            uploaderHandler.upProcessor.failContinue(file);
        } else {
            // all recursive
            for (var i = 0; i < uploaderHandler.uploadingFiles.length; i++) {
                uploaderHandler.resumeRead(uploaderHandler.uploadingFiles[i].localId);
            }
        }
    };

    /**
     * @param {string} fileId
     */
    this.retryRead = function(fileId) {
        var file = uploaderHandler.searchFile(fileId);
        if (file != null) {
            // one
            file.readStatus = file.STATUS_INIT;
            uploaderHandler.upProcessor.failContinue(file);
        } else {
            // all recursive
            for (var i = 0; i < uploaderHandler.uploadingFiles.length; i++) {
                uploaderHandler.retryRead(uploaderHandler.uploadingFiles[i].localId);
            }
        }
    };

    /**
     * @param {string} fileId
     */
    this.abortRead = function(fileId) {
        var file = uploaderHandler.searchFile(fileId);
        if (file != null) {
            // one
            file.readStatus = file.STATUS_DESTROY;
            uploaderHandler.upProcessor.failContinue(file);
        } else {
            // all recursive
            for (var i = 0; i < uploaderHandler.uploadingFiles.length; i++) {
                uploaderHandler.abortRead(uploaderHandler.uploadingFiles[i].localId);
            }
        }
    };

    /**
     * @param {string} localId
     * @returns {null|UploadedFile}
     */
    this.searchFile = function(localId) {
        var file = null;
        for (var i = 0; i < uploaderHandler.uploadingFiles.length; i++) {
            if (uploaderHandler.uploadingFiles[i].localId === localId) {
                file = uploaderHandler.uploadingFiles[i];
            }
        }
        return file;
    };

    this.clearList = function() {
        for (var i = uploaderHandler.uploadingFiles.length; i > 0; i--) {
            uploaderHandler.uploadingFiles.pop();
        }
        uploaderHandler.upRenderer.clearFileSelection();
    };
};

/**
 * Render output to browser
 * Usually main candidate to customization
 */
var UploaderRenderer  = function () {
    /** @var {UploadIdentification} */
    this.upIdent = null;
    /** @var {UploaderQuery} */
    this.upQuery = null;

    /**
     * @param {UploaderQuery} upQuery
     * @param {UploadIdentification} upIdent
     */
    this.init = function(upQuery, upIdent) {
        uploaderRenderer.upQuery = upQuery;
        uploaderRenderer.upIdent = upIdent;
        return uploaderRenderer;
    };

    /**
     * @param {UploadedFile} uploadedFile
     */
    this.renderFileItem = function(uploadedFile) {
        var progress = uploaderRenderer.upQuery.getObjectById(uploaderRenderer.upIdent.baseProgress);
        var progress_bar = progress.clone(true);
        progress_bar.attr(uploaderRenderer.upIdent.localId, uploadedFile.localId);
        var list = uploaderRenderer.upQuery.getObjectById(uploaderRenderer.upIdent.knownBulk);
        list.append(progress_bar);
        var fileName = progress_bar.find(".filename").eq(1);
        fileName.append(uploadedFile.fileName);
        var button1 = progress_bar.find("button").eq(1);
        button1.attr(uploaderRenderer.upIdent.dataKey, uploadedFile.localId);
        var button2 = progress_bar.find("button").eq(2);
        button2.attr(uploaderRenderer.upIdent.dataKey, uploadedFile.localId);
        var button3 = progress_bar.find("button").eq(3);
        button3.attr(uploaderRenderer.upIdent.dataKey, uploadedFile.localId);
    };

    this.clearFileSelection = function() {
        var list = uploaderRenderer.upQuery.getObjectById(uploaderRenderer.upIdent.knownBulk);
        list.children().remove();
    };

    /**
     * @param {UploadedFile} uploadedFile
     */
    this.renderReaded = function(uploadedFile) {
        var node = uploaderRenderer.upQuery.getObjectById(uploadedFile.localId);
        var button = node.find("button").eq(1);
        button.removeAttr("disabled");
        node.attr(uploaderRenderer.upIdent.localId, uploadedFile.serverData);
    };

    /**
     * @param {UploadedFile} uploadedFile
     */
    this.renderFinished = function(uploadedFile) {
        uploaderRenderer.upQuery.setObjectContent(uploaderRenderer.upIdent.elapsedTime, uploaderRenderer.formatTime(uploaderRenderer.getElapsedTime(uploadedFile)));
        uploaderRenderer.upQuery.setObjectContent(uploaderRenderer.upIdent.estimatedTimeLeft, uploaderRenderer.calculateEstimatedTimeLeft(uploadedFile,100));
        uploaderRenderer.upQuery.setObjectContent(uploaderRenderer.upIdent.currentPosition, uploaderRenderer.calculateSize(uploadedFile.fileSize));
        uploaderRenderer.upQuery.setObjectContent(uploaderRenderer.upIdent.totalSize, uploaderRenderer.calculateSize(uploadedFile.fileSize));
        uploaderRenderer.upQuery.setObjectContent(uploaderRenderer.upIdent.estimatedSpeed, uploaderRenderer.calculateSize(0));
        uploaderRenderer.upQuery.setObjectContent(uploaderRenderer.upIdent.percentsComplete, "100%"); // percents

        var node = uploaderRenderer.upQuery.getObjectById(uploadedFile.localId);
        var button = node.find("button").eq(1);
        button.attr("disabled", "disabled");
    };

    /**
     * @param {UploadedFile} uploadedFile
     * @param {*} responseData
     */
    this.consoleError = function(uploadedFile, responseData) {
        console.log({ uploadedFile: uploadedFile, responseData: responseData });
        uploaderRenderer.updateStatus(uploadedFile, uploadedFile.errorMessage);
    };

    /**
     * @param {UploadedFile} uploadedFile
     */
    this.startRead = function(uploadedFile) {
        var node = uploaderRenderer.upQuery.getObjectById(uploadedFile.localId);
        var button = node.find("button").eq(2);
        button.removeAttr("disabled");
    };

    /**
     * @param {UploadedFile} uploadedFile
     */
    this.stopRead = function(uploadedFile) {
        var node = uploaderRenderer.upQuery.getObjectById(uploadedFile.localId);
        var button = node.find("button").eq(2);
        button.attr("disabled", "disabled");
    };

    /**
     * @param {UploadedFile} uploadedFile
     */
    this.resumeRead = function(uploadedFile) {
        var node = uploaderRenderer.upQuery.getObjectById(uploadedFile.localId);
        var button = node.find("button").eq(2);
        button.removeAttr("disabled");
    };

    /**
     * @param {UploadedFile} uploadedFile
     */
    this.updateBar = function(uploadedFile) {
        var node = uploaderRenderer.upQuery.getObjectById(uploadedFile.localId);
        var percent = uploaderRenderer.calculatePercent(uploadedFile);

        uploaderRenderer.upQuery.setObjectContent(uploaderRenderer.upIdent.elapsedTime, uploaderRenderer.formatTime(uploaderRenderer.getElapsedTime(uploadedFile)));
        uploaderRenderer.upQuery.setObjectContent(uploaderRenderer.upIdent.estimatedTimeLeft, uploaderRenderer.calculateEstimatedTimeLeft(uploadedFile, percent));
        uploaderRenderer.upQuery.setObjectContent(uploaderRenderer.upIdent.currentPosition, uploaderRenderer.calculateSize(uploadedFile.lastKnownPart * uploadedFile.partSize));
        uploaderRenderer.upQuery.setObjectContent(uploaderRenderer.upIdent.totalSize, uploaderRenderer.calculateSize(uploadedFile.fileSize));
        uploaderRenderer.upQuery.setObjectContent(uploaderRenderer.upIdent.estimatedSpeed, uploaderRenderer.calculateSize(uploaderRenderer.calculateSpeed(uploadedFile)));
        uploaderRenderer.upQuery.setObjectContent(uploaderRenderer.upIdent.percentsComplete, percent.toString() + "%");

        var button = node.find("." + uploaderRenderer.upIdent.progressCounter).eq(1);
        button.append(percent.toString() + "%");
        button.css('width', percent.toString() + "%");
    };

    /**
     * @param {UploadedFile} uploadedFile
     * @param {string} status
     */
    this.updateStatus = function(uploadedFile, status) {
        var node = uploaderRenderer.upQuery.getObjectById(uploadedFile.localId);
        var errLog = node.find("." + uploaderRenderer.upIdent.errorLog).eq(1);
        errLog.append(status);
    };

    /**
     * calculate passed time
     * @param {UploadedFile} uploadedFile
     * @param {number} percentDone
     * @return {string}
     */
    this.calculateEstimatedTimeLeft = function(uploadedFile, percentDone) {
        var spend = uploaderRenderer.getElapsedTime(uploadedFile);
        if (percentDone > 0) {
            var fullTime = 100 * (spend / percentDone);
            return uploaderRenderer.formatTime(Math.abs(fullTime - spend));
        } else {
            return "N/A";
        }
    };

    /**
     * format time into something sane
     * @param {number} value int
     * @return {string}
     */
    this.formatTime = function(value) {
        var hrs = Math.floor(value / 3600);
        var min = Math.floor(value / 60) - hrs * 60;
        var sec = Math.floor(value % 60);
        return uploaderRenderer.pad(hrs, 2) + ":" + uploaderRenderer.pad(min, 2) + ":" + uploaderRenderer.pad(sec, 2);
    };

    /**
     * @param {number} number
     * @param {number} length
     * @return {string}
     */
    this.pad = function(number, length) {
        var str = "" + number.toString();
        while (str.length < length) {
            str = "0" + str;
        }
        return str;
    };

    /**
     * calculate percents
     * @param {UploadedFile} uploadedFile
     * @return {number}
     */
    this.calculatePercent = function(uploadedFile) {
        var percent = Math.round((uploadedFile.lastKnownPart) / uploadedFile.totalParts);
        return !percent ? 0 : percent * 100;
    };

    /**
     * calculate percents of checked part
     * @param {UploadedFile} uploadedFile
     * @return {number}
     */
    this.calculateCheckedPercent = function(uploadedFile) {
        var percent = Math.round(uploadedFile.lastCheckedPart / uploadedFile.totalParts);
        return !percent ? 0 : percent * 100;
    };

    /**
     * calculate processing speed - bytes/second
     * @param {UploadedFile} uploadedFile
     * @return {number}
     */
    this.calculateSpeed = function(uploadedFile) {
        var elapsedTime = uploaderRenderer.getElapsedTime(uploadedFile);

        if (elapsedTime < 1) {
            return 0;
        }
        if (0 == uploadedFile.lastKnownPart) {
            return 0;
        }
        return (uploadedFile.totalParts * elapsedTime * 100) / uploadedFile.lastKnownPart;
    };

    /**
     * calculate sizes
     * @param {number} bytesProcessed
     * @return {string}
     */
    this.calculateSize = function(bytesProcessed) {
        var sizes = ["Bytes", "KB", "MB", "GB", "TB"];

        if (0 == bytesProcessed){
            return "0 Byte";
        }

        var i = Math.floor(Math.log(bytesProcessed) / Math.log(1024));
        return (Math.round((bytesProcessed / Math.pow(1024, i)) * 100) / 100).toFixed(2) + " " + sizes[i];
    };

    /**
     * amount of passed seconds
     * @param {UploadedFile} uploadedFile
     * @return {number}
     */
    this.getElapsedTime = function(uploadedFile) {
        return uploaderRenderer.getCurrentTime() - uploadedFile.startTime;
    };

    /**
     * current time for init and calculations
     * @return {number}
     */
    this.getCurrentTime = function() {
        return Math.round(new Date().getTime() / 1000);
    }
};

var uploadTranslations = new UploadTranslations();
var uploadIdentification = new UploadIdentification();
var uploadedFile = new UploadedFile();
var uploaderProcessor = new UploaderProcessor();
var uploadInit = new UploadInit();
var uploaderChecker = new UploaderChecker();
var uploaderRunner = new UploaderRunner();
var uploaderFailure = new UploaderFailure();
var uploaderReader = new UploaderReader();
var uploaderEncoder = new UploaderEncoder();
var uploaderChecksum = new UploaderChecksum();
var uploaderQuery = new UploaderQuery();
var uploaderRemoteQuery = new UploaderRemoteQuery();
var uploaderHandler = new UploaderHandler();
var uploaderRenderer = new UploaderRenderer();
