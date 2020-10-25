import {
    shuffle,
    range
  } from 'd3';

const round = (x,n) =>{
    return Math.round(x*10**n)/10**n;
}
const snipArray = (x,n) =>{
    const index = Math.floor(Math.random()*(n+1));
    return x.slice(index);
}

const randomNumber = (n) =>{
    return Math.random()*n-Math.random()*n
}
export const generateData = x =>{
    let groups = ["Future Farm","EZONE","Human Movement","EV Charging Network"];
    let devices= ["PV","DC Charger","AC Charger","Battery", "Wind Turbine"];
    shuffle(devices)
    shuffle(groups);
    devices = snipArray(devices,2);
    let hours = x;
    let data = []
    devices.forEach(device=>{
        range(hours).forEach(hour=>
            data.push(
                {
                    timestamp: new Date(2020,10,1,hour),
                    group:groups[devices.indexOf(device)],
                    device: device,
                    power: round(randomNumber(10),2)
                }
            )
        )
    })
    return data
}