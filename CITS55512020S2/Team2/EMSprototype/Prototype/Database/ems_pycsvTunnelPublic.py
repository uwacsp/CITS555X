import psycopg2
from sshtunnel import SSHTunnelForwarder
from sqlalchemy import types, create_engine

import csv
import glob 
import pandas as pd
import numpy as np

#makes sqlalchemy engine under port
def make_engine(port):
    return create_engine('postgresql://{user}:{password}@{host}:{port}/{db}'.format(
        user='*********', # REPLACE WITH USER
        password='*******', #REPLACE WITH PASSWORD
        host='localhost',
        port=port,
        db='therevpr_ocpp'
    ))
#dict for column types
typedict = {'device_name_ext':types.VARCHAR(60),'attribute_name_ext': types.VARCHAR(60)}


#Get latest SunnyBox csv from directory and read it
#Currently uses local files, will replace with FTP in final version
latestsunny = max(glob.glob('C:/Users/Jace/sunny_csv/20*'))
df = pd.read_csv(latestsunny)

#Clean up CSV head
df.drop(df.index[[0,1,2,3,4,5,6]], inplace=True)
df.to_csv ("C:/Users/Jace/sunny_csv/out.csv", index = False)
x = []
sep = ''
with open("C:/Users/Jace/sunny_csv/out.csv") as f:
    for i, line in enumerate(f):
        if i == 0:
            sep = line.rstrip()[-1]
        if i > 0:
            x.append(line.rstrip().split(';'))
x = pd.DataFrame(data=x[1:], columns=x[0])
x.to_csv ("C:/Users/Jace/sunny_csv/out.csv",index = False) #out2


#Pick desired columns into final csv to copy to database, clean up column types
df3 = pd.read_csv("C:/Users/Jace/sunny_csv/out.csv",header =None) #out2
df3 = df3.loc[:,[0,5]]
df3[0] = pd.to_datetime(df3[0])
df3.dropna(inplace=True)
df3.columns = ["Timestamp","value"]
df3["device_name_ext"] = "791225 (Solar)"
df3["attribute_name_ext"] = "Power (kWh)"
df3.to_csv("C:/Users/Jace/sunny_csv/out.csv",index=False) #out3

try:
    with SSHTunnelForwarder(
            ('therevproject.com', 22),
            ssh_username="******", #REPLACE WITH USER
            ssh_password="*********", #REPLACE WITH PASSWORD
            remote_bind_address=('localhost', 5432)) as server:

            #connect to server and create sqlalchemy engine
            server.start()
            print ("server connected")
            engine = make_engine(server.local_bind_port)
            print ("engine created")

            params = {
                'database': 'therevpr_ocpp',
                'user': "******", #REPLACE WITH USER
                'password': '******', #REPLACE WITH PASSWORD
                'host': 'localhost',
                'port': server.local_bind_port
                }

            conn = psycopg2.connect(**params)
            print("connection established")
            curs = conn.cursor()
            print ("database connected")

            #transfer to database
            df = pd.read_csv("C:/Users/Jace/sunny_csv/out.csv") #out3
            df['Timestamp'] = pd.to_datetime(df['Timestamp'])
            df.to_sql('ems_transaction_ext_table',engine, if_exists='append', index = False,chunksize = 10000, dtype = typedict)
            conn.commit()
            print("csv copied to database")

            #close all connections
            if(conn):
                engine.dispose()
                curs.close()
                conn.close()
                server.stop()
                print("PostgreSQL connection is closed")
                
        
except:
    print ("Connection Failed")
