# Solar Powered Autonomous Boat (SPAB)

This is a prototype for the software components of the SPAB system.

Details of the various components are below.

## SPAB Backend

This consists of a NodeJS webserver using the Express framework. This is the webserver used by the project, as well as the server handling a basic API.

To run the server navigate to the spab-backend folder and run `npm install` to install all dependencies. Then run the server using NodeJS with one of the following commands:

```bash
# Run spab-backend on Windows with Command Prompt
SET DEBUG=spab-backend:* & npm start

# Run spab-backend on Windows with PowerShell
SET DEBUG=spab-backend:* | npm start

# Run spab-backend on Linux/macOS
DEBUG=spab-backend:* npm start
```

The server is hosted locally on port 7800. We have also temporarily hosted the site and API at [https://spab.toms.directory](https://spab.toms.directory).

## SPAB Client

This is the code for the Angular SPA wireframe that will be used by the SPAB system.

Note that all data displayed is randomly generated mock data. Integration with the rest of the SPAB system and real data will be implemented at a future date.

The client can be located on the website at [http://localhost:7800/dashboard](http://localhost:7800/dashboard) (hosted online [here](https://spab.toms.directory/dashboard)) or by pressing the "login" button on the homepage.

## SPAB Simulator

This is a python simulator for the solar boat that abstracts away hardware details and models how the solar boat could function and communicate with the backend system.

To run the code, first ensure that the NodeJS spab-backend server is running. Also ensure that there are currently navigation waypoints stored in the database for the simulator to "follow". This can be achieved with the following curl request.

```bash
curl --location --request PUT 'http://localhost:7800/api/commands' \
--header 'Content-Type: application/json' \
--data-raw '{
    "commands": [
        {
            "action": "moveTo",
            "lat": -31.980250,
            "long": 115.821163
        },
        {
            "action": "moveTo",
            "lat": -31.980250, 
            "long": 115.822969
        },
        {
            "action": "moveTo",
            "lat": -31.976374, 
            "long": 115.824363
        },
        {
            "action": "moveTo",
            "lat": -31.974417, 
            "long": 115.826251
        },
        {
            "action": "moveTo",
            "lat": -31.973942, 
            "long": 115.826178
        },
        {
            "action": "moveTo",
            "lat": -31.973856, 
            "long": 115.825851
        },
        {
            "action": "moveTo",
            "lat": -31.975423, 
            "long": 115.824206
        },
        {
            "action": "moveTo",
            "lat": -31.976845, 
            "long": 115.822629
        },
        {
            "action": "moveTo",
            "lat": -31.977700, 
            "long": 115.821912
        },
        {
            "action": "moveTo",
            "lat": -31.980250,
            "long": 115.821163
        }
    ]
}'
```

The simulator may then be run using python3. Note that the `requests` and `schedule` libraries will need to be installed.