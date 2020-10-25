import {
  select,
  scaleLinear,
  scaleOrdinal,
  schemeCategory10,
  scaleTime,
  extent,
  axisLeft,
  axisBottom,
  line,
  curveBasis,
  group
} from 'd3';

import { colorLegend } from './colorLegend';

const svg = select('#diagram');

const width = +svg.attr('width');
const height = +svg.attr('height');




export const renderGraph = data => {
    const title = 'Power over 24 hours';
    
    const xValue = d => d.timestamp;
    const xAxisLabel = 'Time';
    
    const yValue = d => d.power;
    const circleRadius = 6;
    const yAxisLabel = 'Power (kWh)';
    
    const margin = { top: 60, right: 200, bottom: 80, left: 105 };
    const innerWidth = width - margin.left - margin.right;
    const innerHeight = height - margin.top - margin.bottom;
    
    const xScale = scaleTime()
      .domain(extent(data, xValue))
      .range([0, innerWidth])
      .nice();
    
    const yScale = scaleLinear()
      .domain(extent(data, yValue))
      .range([innerHeight, 0])
      .nice();
    
    const g = svg.append('g')
      .attr('transform', `translate(${margin.left},${margin.top})`);
    
    const xAxis = axisBottom(xScale)
      .tickSize(-innerHeight)
      .tickPadding(15);
    
    const yAxis = axisLeft(yScale)
      .tickSize(-innerWidth)
      .tickPadding(10);
    
    const yAxisG = g.append('g').call(yAxis);
    yAxisG.selectAll('.domain').remove();
    
    yAxisG.append('text')
        .attr('class', 'axis-label')
        .attr('y', -60)
        .attr('x', -innerHeight / 2)
        .attr('fill', 'black')
        .attr('transform', `rotate(-90)`)
        .attr('text-anchor', 'middle')
        .text(yAxisLabel);
    
    const xAxisG = g.append('g').call(xAxis)
      .attr('transform', `translate(0,${innerHeight})`);
    
    xAxisG.select('.domain').remove();
    
    xAxisG.append('text')
        .attr('class', 'axis-label')
        .attr('y', 80)
        .attr('x', innerWidth / 2)
        .attr('fill', 'black')
        .text(xAxisLabel);
    
    const lineGenerator = line()
      .x(d => xScale(xValue(d)))
      .y(d => yScale(yValue(d)))
      .curve(curveBasis);
    
    const dataNested = group(data, d => d.device)


    const colorScale = scaleOrdinal(schemeCategory10);
    colorScale.domain(dataNested.keys());

   console.log(Array.from(dataNested.values()))
    g.selectAll('.line-path').data(Array.from(dataNested.values()))
      .enter().append('path')
        .attr('class', 'line-path')
        .attr('d', d => lineGenerator(d))
        .attr('stroke', d=> colorScale(d[0].device));

    g.selectAll('.line-path').data(dataNested.values())
      .attr('d', d => lineGenerator(d))

    g.append('text')
        .attr('class', 'title')
        .attr('y', -10)
        .text(title);
    svg.append('g')
      .attr('transform', `translate(725,200)`)
        .call(colorLegend, {
          colorScale,
          circleRadius: 13,
          spacing: 30,
          textOffset: 15
        });
  };