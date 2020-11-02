const { json } = require('express');
var express = require('express');
var mongoose = require('mongoose');
var router = express.Router();

// setup database connection
var mongoDB = 'mongodb+srv://admin:revproject@spabbuddy.hww2b.mongodb.net/solarboat';
mongoose.connect(mongoDB, {useNewUrlParser: true, useUnifiedTopology: true});

var db = mongoose.connection;
db.on('error', console.error.bind(console, 'MongoDB connection error:'));

var CommandModel = require('../models/command');
var DataModel = require('../models/data');


// Routing------------------------------------------------------------------------------------------------------------------------

/* Add commands to the boats path (used by interface)
 * Currently replaces all commands with completely new ones
 */
router.put('/commands', function(req, res, next) {
  validCommands = req.body.commands.filter(command => validCommand(command)).map(command => formatCommand(command));

  //delete old commands
  console.log("[DB] Clearing db commands collection")
  CommandModel.deleteMany({}, function (err) {});

  //add new commands
  console.log("[DB] Inserting new commands");
  CommandModel.insertMany(validCommands).then(function(){
    console.log("[DB] Commands inserted");
    //return
    res.sendStatus(204);
  });

});


/* Gets commands that need to be followed (used by SPAB AND intercase) */
router.get('/commands', function(req, res, next) {

  console.log("[DB] Fetching commands");
  CommandModel.find( {} , function (err, commands) {
    console.log("[DB] Retrieved " + commands.length + " commands")
    res.json(commands);
  });
  
});


/* Deletes a command to signify it has been completed (used by SPAB)*/
router.delete('/commands', function(req, res, next) {
  //validate request
  var ids = req.body.ids;
  if (ids == null || !Array.isArray(ids)){
    res.status(400);
    res.json({"message": "Expected request body to have member 'ids' containing an array of ObjectIds for deleting"});
    return;
  }

  CommandModel.deleteMany({ "_id": {"$in": ids} }, function (err) {
    if (err) {
      res.status(400);
      res.json({"message": "There was an error deleting the specified commands"});
      return;
    }else{
      //send success
      res.sendStatus(204);
    }
  });
});


/* stores a piece of sensor data in the database */
router.post('/data', function(req, res, next) {
  if(req.body == null){
    res.status(400);
    res.json({"message": "Expected a request body containing a data point"});
    return;
  }

  var obj = req.body;
  if (obj.time == null){
    obj.time = Date.now();
  }

  DataModel.create(obj, function(err, document){
    if (err) {
      res.status(400);
      res.json({"message": "There was an error inserting the datapoint"});
      return;
    }else{
      //send success
      res.json(document);
    }
  });
});


/* Retrieves sensor data from the database */
router.get('/data', function(req, res, next) {
  var fromDate = Date.parse(req.query.from)
  var toDate = Date.parse(req.query.to)

  var criteria = {};

  //set time criteria
  if ( !isNaN(fromDate) || !isNaN(toDate) ){
    criteria.time = {};
    if ( !isNaN(fromDate) ){
      criteria.time["$gte"] = fromDate;
    }
    if ( !isNaN(toDate) ){
      criteria.time["$lt"] = toDate;
    }
  }
  //set sensor criteria
  if (req.query.sensor != null){
    criteria["sensor"] = req.query.sensor;
  }

  //search in database
  console.log("[DB] Searching for data matching criteria");
  DataModel.find( criteria, function (err, data) {
    if (err) {
      res.status(400);
      res.json({"message": "There was an error fetching the selected data points"});
      return;
    }else{
      //return data
      console.log("[DB] Found " + data.length + " data points");
      res.json(data);
    }
  });
});


// HELPER FUNCTIONS ------------------------------------------------------------------------------------------------------------
function validCommand(command){
  //check action
  if (["moveTo"].indexOf(command.action) == -1){ //only moveTo is supported
    return false;
  }
  //check latitude, longitude & duration
  if (command.lat == null || typeof(command.lat) != "number"
  || command.long == null || typeof(command.long) != "number"
  || (command.duration != null && typeof(command.duration) != "number") ){
    return false;
  }
  return true;
}

function formatCommand(command){
  var obj = {'action': command.action, 'lat': command.lat, 'long': command.long};
  if (command.duration != null){
    obj.duration = command.duration; //only store duration if present
  }
  return obj;
}

function validData(data){

}

module.exports = router;
