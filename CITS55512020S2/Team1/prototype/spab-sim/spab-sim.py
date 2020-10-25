#for running things over a long period of time
import schedule
import time
from datetime import datetime
#communicating with backend
import requests
#utility
import numpy as np
import random
import math

#api endpoint
API_ENDPOINT = "http://localhost:7800/api"

#max speed (lat/long per second)
SPEED = 0.00002 # roughly 2 m/s (unrealistic) for dev purposes
#current position
LAT, LONG = -31.98023, 115.821145
#current list of commands
commands = []
#waypoints
waypoints = []


# This small piece of codes, simulates life as a SPAB.
# It is intended to simulate the communication between
# the SPAB and the backend server.

# What this simulator does
#   1) Update commands sent to the SPAB
#   2) Follows commands by chaning "position" over time
#   3) Delete commands once done with them
#   4) Periodically report sensor data at the given location



def generateWaypoints(currentLat, currentLong, destLat, destLong):
    dy, dx = (destLat-currentLat), (destLong-currentLong)
    distance = math.sqrt( (dy)**2 + (dx)**2 )
    numSteps = math.ceil(distance/SPEED)
    # heading = math.atan2(dy, dx)
    # nsSpeed, ewSpeed = SPEED * math.sin(heading), SPEED * math.cos(heading)
    return list( zip( np.linspace(currentLat, destLat, numSteps), np.linspace(currentLong, destLong, numSteps) ) ) #using linspace to approximate and avoid doing maths

def updateCommands():
    global commands, waypoints, LAT, LONG
    print(datetime.now(), "Fetching commands")
    r = requests.get(f"{API_ENDPOINT}/commands")
    if not r.ok:
        return

    try:
        newCommands = r.json()
        print(f"\tReceived {len(newCommands)} commands")
        # TODO - do more checking/validation of commands here
        commands = newCommands
    except Exception as e:
        print("ERROR - Could not get commands")
        return

    #plot course!
    if len(commands) > 0:
        waypoints = generateWaypoints(LAT, LONG, commands[0]["lat"], commands[0]["long"])
        print(f"\tMoving to point ({commands[0]['lat']}, {commands[0]['long']})")
    
def move():
    global commands, waypoints, LAT, LONG
    if len(commands) == 0:
        return #do nothing
    
    #move to next waypoint
    if len(waypoints) > 0:
        (LAT, LONG) = waypoints.pop(0)
        print(f"\t({LAT}, {LONG})")
    else:
        # delete current command
        deleteCommand(commands.pop(0))
        # move to next command & popilate waypoints
        if len(commands) > 0:
            waypoints = generateWaypoints(LAT, LONG, commands[0]["lat"], commands[0]["long"])
            print(f"\tMoving to point ({commands[0]['lat']}, {commands[0]['long']})")

def deleteCommand(command):
    print(datetime.now(), f"Deleting completed command {command['_id']}")
    r = requests.delete(f"{API_ENDPOINT}/commands", json={'ids': [command['_id']] })
    if r.ok:
        print("\tDeleted")
    else:
        print("ERROR - could not delete command")
        print(r.content)

def sendSensorData():
    print(datetime.now(), "Sending sensor data")
    r = requests.post(f"{API_ENDPOINT}/data", data={ "sensor": 2, "value": random.normalvariate(17.0, 0.5), "lat": LAT, "long": LONG, "time": datetime.now() })
    if not r.ok:
        pass #TODO - handle request failure


schedule.every().second.do(move)
schedule.every(15).seconds.do(updateCommands)
schedule.every(30).seconds.do(sendSensorData)

while True:
    schedule.run_pending()
    time.sleep(1)