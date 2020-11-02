import {
    group
  } from 'd3';

export const getLatest = (data,n) =>{
    return Array.from(
        group(data, d => d.device)
        .values())
        .map(d => d[n]);
}

export const getStamp = (data) =>{
  return data[0].timestamp
}