

import { Component, OnInit, ViewEncapsulation, Injectable } from '@angular/core';
import { Socket } from 'ngx-socket-io';

import olView from 'ol/View';
import olMap from 'ol/Map';
import olFeature from 'ol/Feature';
import olGeolocation from 'ol/Geolocation';
import * as olProj from 'ol/proj';

import { Tile as olTile, Vector as olVector } from 'ol/layer';

import { Attribution as olAttribution, FullScreen as olFullScreen, defaults as olDefaultControls } from 'ol/control';
import { Circle as olCircle, Fill as olFill, Stroke as olStroke, Style as olStyle } from 'ol/style';
import olPoint from 'ol/geom/Point';
import { OSM as olsOSM, Vector as olsVector } from 'ol/source';


@Component({
    selector: 'app-root',
    templateUrl: './app.component.html',
    styleUrls: ['./app.component.scss'],
    encapsulation: ViewEncapsulation.None
})


@Injectable()
export class AppComponent implements OnInit {

    sensorData = {
        temp: '',
        salinity: '',
        sensor1: '',
        sensor2: ''
    }




    constructor(private socket: Socket) { }

    ngOnInit(): void {
        let mapPadding = [0, 334, 0, 0];

        let initPosSet = false;
        let currentPos = [0, 0];

        let mapView = new olView({
            center: olProj.fromLonLat(currentPos),
            zoom: 1,
            padding: mapPadding
        });

        // current position marker
        let mapPosFeature = new olFeature();
        mapPosFeature.setStyle(
            new olStyle({
                image: new olCircle({
                    radius: 10,
                    fill: new olFill({
                        color: '#3399CC',
                    }),
                    stroke: new olStroke({
                        color: '#fff',
                        width: 3,
                    })
                })
            })
        );

        let mapGeolocation = new olGeolocation({
            // enableHighAccuracy must be set to true to have the heading value.
            tracking: true,
            trackingOptions: {
                enableHighAccuracy: true,
            },
            projection: mapView.getProjection()
        });

        mapGeolocation.on('change:position', function () {
            currentPos = mapGeolocation.getPosition();
            var geo = currentPos ? new olPoint(currentPos) : null;

            mapPosFeature.setGeometry(geo);

            if (!initPosSet) {
                mapView.fit(geo, {
                    padding: mapPadding,
                    maxZoom: 12
                });
                initPosSet = true;
            }
        });

        let mapMarkerLayer = new olVector({
            source: new olsVector({
                features: [mapPosFeature],
            })
        });

        let openSeaMapLayer = new olTile({
            source: new olsOSM({
                attributions: [
                    '© <a href="http://www.openseamap.org/">OpenSeaMap</a> contributors.',
                    olsOSM.ATTRIBUTION
                ],
                opaque: false,
                url: 'https://tiles.openseamap.org/seamark/{z}/{x}/{y}.png',
            })
        });

        let map = new olMap({
            target: 'map',
            controls: olDefaultControls({ attribution: false }).extend([
                new olAttribution({
                    collapsible: true,
                    collapseLabel: '<'
                }),
                new olFullScreen({
                    source: 'main',
                })
            ]),
            layers: [
                new olTile({
                    source: new olsOSM()
                }),
                openSeaMapLayer,
                mapMarkerLayer
            ],
            view: mapView
        });

        //setup websocket
        let _self = this;
        this.socket.on('sensorData', function (sensorData) {
            _self.sensorData = sensorData;
        });
    }
    title = 'SPAB Dashboard';
}
