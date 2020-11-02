var mongoose = require('mongoose');

//Define schema
var Schema = mongoose.Schema;

var CommandSchema = new Schema({
  action: String,
  lat: Number,
  long: Number,
  duration: Number
}, {
  versionKey: false,
  collection: 'commands'
});

//Export function to create "SomeModel" model class
module.exports = mongoose.model('Command', CommandSchema );