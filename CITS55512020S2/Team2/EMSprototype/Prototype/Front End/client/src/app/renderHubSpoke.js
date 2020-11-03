import { select,range} from 'd3';


const svg = select('#diagram');
const width = +svg.attr('width');
const height = +svg.attr('height');


const generateNodePath = (x1,y1,x2,y2,pOff) =>{
    const yp = y1>y2?y1-pOff:y1+pOff
    return d3.line()
      .x([x1,x1,x2])
      .y([y1,yp,y2])
  }
  
  
  const generateElement = (l,i,r,xOff,yOff,pOff) =>{
  
    const x = Math.round(Math.sin(2*Math.PI/l*(i+0.5))*r+xOff);
    const y = Math.round(Math.cos(2*Math.PI/l*(i+0.5))*r+yOff);
    const path  = generateNodePath(x,xOff,y,yOff, pOff)
    return {x:x,y:y,path:path}
  
  }

  const offsetText = (textPos, centrePos, offset)=>{
    return textPos>centrePos?textPos+offset:textPos-offset
  }
  
export const renderDiagram = data =>{
    const nodeRadius = 25
    const nodeStroke = 5
    const radialCentreX = width/2
    const radialCentreY = height/2
    const radialRadius =  height/2-100
    const pathOffsetY = nodeRadius+ 25
    const pathStroke = 10
  
  
    var nodesGroups = [...new Set(data.map(x=>x.group))];
    var nodesPos = range(data.length).map(x=>{return generateElement(data.length,x,radialRadius,radialCentreX,radialCentreY, pathOffsetY);})
    var nodesColours = d3.scaleOrdinal().domain(nodesGroups)
      .range(["#1f77b4","#ff7f0e","#2ca02c","#d62728","#9467bd","#8c564b","#e377c2","#7f7f7f","#bcbd22","#17becf"]);
    var pathColours = d3.scaleOrdinal().domain([true,false])
      .range(["black","red"]);
  
  
  
    svg.selectAll('path').data(data)
      .enter().append('path')
        .attr("d", (d,i) => d3.line()([[nodesPos[i].x,nodesPos[i].y],[nodesPos[i].x,nodesPos[i].y>radialCentreY?nodesPos[i].y-pathOffsetY:nodesPos[i].y+pathOffsetY],[radialCentreX,radialCentreY]]))
        .attr("stroke", "black")
        .attr("fill", "none")
        .attr("stroke-width", (d,i)=>pathStroke + Math.log(Math.abs(d.power+1)))
        .attr("stroke", (d,i)=> pathColours(d.power>=0))
    svg.selectAll('circle').data(data)
      .enter().append('circle')
        .attr('cx', (d,i) => nodesPos[i].x)
        .attr('cy', (d,i) => nodesPos[i].y)
        .attr('r',nodeRadius)
        .attr('fill',"white")
        .attr("stroke", (d,i) => nodesColours(d.group)) 
        .attr('stroke-width',nodeStroke)
    svg.selectAll('.text-value').data(data)
      .enter().append('text')
        .attr('class', 'text-value')
        .attr("x", (d,i) => offsetText(nodesPos[i].x, radialCentreX, 50))
        .attr("y", (d,i) => nodesPos[i].y+10)
        .text((d,i) => d.power + " kWh")
        .attr("text-anchor", (d,i) => (nodesPos[i].x>radialCentreX?"start":"end"))
        .attr("font-family", "sans-serif")
        .attr("font-size", "12px")
        .attr("fill", (d,i)=> pathColours(d.power>=0));
    svg.selectAll('.text-name').data(data)
      .enter().append('text')
        .attr('class', 'line-name')
        .attr("x", (d,i) => offsetText(nodesPos[i].x, radialCentreX, 50))
        .attr("y", (d,i) => nodesPos[i].y -10)
        .text((d,i) => d.device)
        .attr("text-anchor", (d,i) => (nodesPos[i].x>radialCentreX?"start":"end"))
        .attr("font-family", "sans-serif")
        .attr("font-size", "14px")
        .attr("fill", (d,i) => nodesColours(d.group));

    svg.append('circle')
      .attr('cx', radialCentreX)
      .attr('cy', radialCentreY)
      .attr('r', nodeRadius)
      .attr('fill', 'black')
  }