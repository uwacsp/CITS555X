var express = require('express');
var router = express.Router();

/* GET/POST accounts homepage */
router.get('/', function(req, res, next) {
  res.render('todo', {title: "SPAB: Work In Progress"});
});
router.post('/', function(req, res, next) {
  res.render('todo', {title: "SPAB: Work In Progress"});
});

module.exports = router;
