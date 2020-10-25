var express = require('express');
var path = require('path');
var router = express.Router();

// serve dashboard static files
module.exports = express.static(path.join(__dirname, '../../spab-client/dist/spab-client'));
