var mongoose = require('mongoose');

//Define schema
var Schema = mongoose.Schema;

var DataSchema = new Schema({
  sensor: Number,
  value: Number,
  lat: Number,
  long: Number,
  time: Date
}, {
  versionKey: false,
  collection: 'data'
});

//Export function to create "SomeModel" model class
module.exports = mongoose.model('Data', DataSchema );