//import { select,range,json} from 'd3';
import {
    select
  } from 'd3';
import { renderDiagram } from './renderHubSpoke';
import { renderGraph } from './renderLineGraph';
import {generateData} from './generateMockData';
import {getLatest, getStamp} from './getLatestData';

const svg = select('#diagram');
const width = +svg.attr('width');
const height = +svg.attr('height');


let displayingGraph = false;
let data = generateData(24)
let sim = 0
let dataLatest = getLatest(data,sim);
let t0 = getStamp(dataLatest);
let dataSim = dataLatest
console.log(data);
console.log(dataLatest);
console.log(dataSim);



const display = function(){
  svg.selectAll("*").remove();
  if(displayingGraph){
    renderGraph(dataSim)
  }else{
    renderDiagram(dataLatest);
  }
}

window.toggleDisplay = ()=>{
  displayingGraph = !displayingGraph
  display()
}

window.updateDisplay = ()=>{
  sim = 0
  data = generateData(24)
  dataLatest = getLatest(data, sim);
  dataSim = dataLatest
  console.log(data);
  console.log(dataLatest);
  console.log(dataSim);
  display()
};

window.animateDisplay = ()=>{
    sim = (++sim)%24;
    if(sim==0){
      dataSim = []
    }
    dataLatest = getLatest(data, sim);
    dataSim = dataSim.concat(dataLatest);
    console.log(sim);
    console.log(dataSim);
    display()
}



display();


