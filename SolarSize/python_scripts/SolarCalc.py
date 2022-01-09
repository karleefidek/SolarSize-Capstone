import math
from pandas.core.base import DataError
import pvlib
from pvlib.forecast import GFS
import pandas
import urllib.request, json
from datetime import datetime
import statistics
import sys
import socket
import requests

#class SolarInsolation:
class ImportData():
    def  __init__(self, latitude, longitude, timeZone, moduleTilt, startDate, endDate):
        self.latitude = float(latitude)
        self.longitude = float(longitude)
        self.timeZone = float(timeZone) #-5 is regina
        self.moduleTilt = float(moduleTilt)
        #Stored as Pandas Time Series of hourly results
        self.pressureMean = 0.0
        self.ghiValues = []
        self.szaValues = []
        self.dniValues = []
        self.dhiValues = []
        self.dewPointValues = []
        #dni values estimated via DIRINT model. https://pvlib-python.readthedocs.io/en/latest/generated/pvlib.irradiance.dirint.html
        #Pandas Date Time Index
        self.times = []

        self.startDate = datetime.fromisoformat(startDate).strftime('%Y%m%d') 
        self.endDate = datetime.fromisoformat(endDate).strftime('%Y%m%d') 
        #JSON Response
        self.APIResponse = []

    #Call Power API using specified latitude and longitude values, start and end dates
    def getResponseFromAPI(self):
        success = False
        count = 0
        while(success == False or count <= 5):
            try:
            #https://power.larc.nasa.gov/api/temporal/daily/point?parameters=ALLSKY_SFC_SW_DWN,CLRSKY_SFC_SW_DWN,ALLSKY_KT,ALLSKY_NKT,ALLSKY_SFC_LW_DWN,ALLSKY_SFC_PAR_TOT,CLRSKY_SFC_PAR_TOT,ALLSKY_SFC_UVA,ALLSKY_SFC_UVB,ALLSKY_SFC_UV_INDEX,WS2M&community=RE&longitude=-104.9423&latitude=50.3724&start=20160115&end=20170315&format=CSV
                with urllib.request.urlopen(f"https://power.larc.nasa.gov/api/temporal/hourly/point?Time=LST&parameters=ALLSKY_SFC_SW_DWN,SZA,T2M,T2MDEW,PS,WS10M,WD10M&community=RE&longitude={self.longitude}&latitude={self.latitude}&start={self.startDate}&end={self.endDate}&format=JSON",timeout=3) as url:
                    data = json.load(url)
                    #print(data)
                    self.APIResponse = data
                    success = True
            except (socket.timeout) as error:
                success = False
            count +=1
            
            

    #Used to parse date from getResponseFromAPI and format it into arrays for use with DNIfromGHI.
    def parseAndCalculateJSON(self):
        #All Sky conditions GHI horizontal plane on Earth Surface. W/m^2
        #Get datetime values and convert to a DateTimeIndex.
        dateList = list(self.APIResponse["properties"]["parameter"]["ALLSKY_SFC_SW_DWN"].keys())
        dateListConverted = (datetime.strptime(i, "%Y%m%d%H") for i in dateList)
        self.times = pandas.DatetimeIndex(dateListConverted, freq='H')
        self.ghiValues = pandas.Series(list(self.APIResponse["properties"]["parameter"]["ALLSKY_SFC_SW_DWN"].values()),index=self.times)
        #print(self.ghiValues)
        #Coordinates, Lat/Long, Elevation
        #print(self.APIResponse["geometry"]["coordinates"]) 
        #Solar Zenith Angle
        #print(self.APIResponse["properties"]["parameter"]["SZA"])
        self.szaValues = pandas.Series(list(self.APIResponse["properties"]["parameter"]["SZA"].values()),index=self.times)
        #Temperature at 2 Meters C
        #print(self.APIResponse["properties"]["parameter"]["T2M"])
        #Dew Point C
        #print(self.APIResponse["properties"]["parameter"]["T2MDEW"])
        self.dewPointValues = pandas.Series(list(self.APIResponse["properties"]["parameter"]["T2MDEW"].values()),index=self.times)
        #Pressure at surface, kPa
        #change to pa = kpa * 1000
        
        paList = [float(i) * 1000 for i in list(self.APIResponse["properties"]["parameter"]["PS"].values())]
        self.pressureValues = pandas.Series(paList,index=self.times)
        self.pressureMean = statistics.mean(paList)
        #print(self.pressureMean)
        #print(self.APIResponse["properties"]["parameter"]["PS"])
        #Wind speed at 10 meters
        #print(self.APIResponse["properties"]["parameter"]["WS10M"])
         #Wind direction at 10 meters
        #print(self.APIResponse["properties"]["parameter"]["WD10M"])
         #Total photosynthesis light at horizontal. Likely need to convert like GHI to the angle of solar panels.
        #print(self.APIResponse["properties"]["parameter"]["ALLSKY_SFC_PAR_TOT"])
        
        #Calculate DNI from DIRINT model using values gotten fromthe API. Use GHI and DNI to get DHI.
    def calcDNIandDHI(self):
        self.dniValues = pvlib.irradiance.dirint(self.ghiValues,self.szaValues,self.times,self.pressureValues,True,self.dewPointValues)   
        #self.dniValues.to_csv(r"C:\Users\kaden\Documents\Fall 2021\ENSE 400\Solar Size Docs\dniData.txt",header=True,index_label="time")
        #GHI = DNI cos(SZA) + DHI\
        #Thus we must multiply the DNI values by cos(SZA). and then find DHI by GHI - DNI Cos(SZA)
        szaDNI = []
        sza = self.szaValues.to_list()
        for i,dniValue in enumerate(self.dniValues.to_list()):
            #print(dniValue)
            szaDNI.append(dniValue * math.cos(math.radians(sza[i])))

        #Output the szaDNI calculated values.
        self.szaDNIValues = pandas.Series(szaDNI,index=self.times)
        #self.szaDNIValues.to_csv(r"C:\Users\kaden\Documents\Fall 2021\ENSE 400\Solar Size Docs\szaDniData.txt",header=True,index_label="time")
        #Subtract GHI - DNI cos(sza)
        self.dhiValues = self.ghiValues.subtract(self.szaDNIValues, fill_value=0.0)
        #self.ghiValues.to_csv(r"C:\Users\kaden\Documents\Fall 2021\ENSE 400\Solar Size Docs\ghiData.txt",header=True,index_label="time")
        #self.dhiValues.to_csv(r"C:\Users\kaden\Documents\Fall 2021\ENSE 400\Solar Size Docs\dhiData.txt",header=True,index_label="time")
    """
    solarRadiation is the solar radition measured perpendicular to the sun. (Value from the API)
    https://www.pveducation.org/pvcdrom/properties-of-sunlight/solar-radiation-on-a-tilted-surface
    """
    #using models here  https://www.pveducation.org/pvcdrom/properties-of-sunlight/making-use-of-tmy-data#footnote1_j7gwlmm
    def calculateModuleRadiation(self):
        #Angle of the sun.?
        moduleGHIValues = []
        for times in self.times:
            self.dayOfYear = times.dayofyear
            self.currentHour = times.hour
            #print(self.dayOfYear)
            #print(self.currentHour)
            self.hourAngle = self.getHourAngle()
            self.declinationAngle = 23.45 * math.sin(math.radians(abs((360/365)*(284 + self.dayOfYear))))
            self.dhiValue = self.dhiValues.at[times]
            self.dniValue = self.dniValues.at[times]
            #print(self.dhiValue)
            #print(self.dniValue)
            #Angle of the sun compared to latitude.
            self.elevationAngle = 90 - self.latitude + self.declinationAngle
            moduleDNIValue = self.calculateModuleDNI()
            moduleDHIValue = self.calculateModuleDHI()
            moduleGHIValue = moduleDHIValue + moduleDNIValue
            moduleGHIValues.append(moduleGHIValue)
        self.moduleGHIValues = moduleGHIValues
    # Declination Angle of Sun = 23.45 sin (360/365(284 + d))
    # Latitude
    # Elevation Angle = 90 - Latitude + declination
    # Sunrise = 12 - 1 / 15 deg cos^-1 (-sin(latitude)*sin(elevationAngle) / cos(latitude) * cos(elevationAngle))
    # Sunset = 12 + 1/ 15 deg cos^-1 (-sin(latitude) *)

    #https://www.pveducation.org/pvcdrom/properties-of-sunlight/solar-time
    def getHourAngle(self):
        #Local Standard Time Meridian
        LSTM = self.timeZone * 15
        #For Equation of Time
        B = 360/365 * (self.dayOfYear - 81)
        #Equation of Time
        EoT = (9.87 * math.sin(math.radians(2*B))) - (7.53 *  math.cos(math.radians(B))) - (1.5 * (math.sin(math.radians(B))))
        #Time Correction Factor
        TC = 4 * (self.longitude - LSTM) + EoT
        #Local Solar Time
        LST = self.currentHour + (TC/60)
        if(LST < 0):
            LST = 24 - abs(LST)
        HRA = 15 * (LST - 12)
        #print("LSTM:",LSTM)
        #print("Eot",EoT)
        #print("TC",TC)
        #print("LST",LST)
        return HRA
    #Direct Normal Irradiance (DNI)
    def calculateModuleDNI(self):
        #Aim south for now.
        moduleAzimuth = 0
        #Complex equation from https://www.pveducation.org/pvcdrom/properties-of-sunlight/making-use-of-tmy-data#footnote1_j7gwlmm
        moduleDNI = self.dniValue * ((math.sin(math.radians(self.declinationAngle))*math.sin(math.radians(self.latitude))*math.cos(math.radians(self.moduleTilt))) - (math.sin(math.radians(self.declinationAngle))*math.cos(math.radians(self.latitude))*math.sin(math.radians(self.moduleTilt))*math.cos(math.radians(moduleAzimuth))) + (math.cos(math.radians(self.declinationAngle))*math.cos(math.radians(self.latitude))*math.cos(math.radians(self.moduleTilt))*math.cos(math.radians(self.hourAngle))) + (math.cos(math.radians(self.declinationAngle))*math.sin(math.radians(self.latitude))*math.sin(math.radians(self.moduleTilt))*math.cos(math.radians(moduleAzimuth))*math.cos(math.radians(self.hourAngle))) + (math.cos(math.radians(self.declinationAngle))*math.sin(math.radians(moduleAzimuth))*math.sin(math.radians(self.hourAngle)*math.sin(math.radians(self.moduleTilt)))))
        #print(self.dniValue)
        #print(self.declinationAngle)
        #print(self.latitude)
        #print(self.moduleTilt)
        #print(self.hourAngle)
        if (moduleDNI < 0):
            return 0.0
        return moduleDNI
    #Diffuse Module - Using a simple equation for now will have to adapt as we go.
    def calculateModuleDHI(self):
        moduleDHI = self.dhiValue * ((180-self.moduleTilt)/180)
        if (moduleDHI < 0):
            return 0.0
        return moduleDHI
    #def getModuleAngleSolar(self):
        #Solar Radiation amount of tilted module.
        #solarTiltedModule = self.solarRadiation * math.sin(math.radians(self.moduleTilt + self.elevationAngle))

    #Using a generalized approach for now. Future will have to include specifics such as inverter, current, etc.
    def getEstimatedPowerProduction(self):
        # E = A * r * H * PR
        #r is solar panel efficiency REC 345 is at 17.20%.
        #H = irradiance from before.
        #PR lets say 15% loss for now. 0.85
        # A total area is 260 * 2.01 = 522.6m^2 
        estimatedPower = []
        for ghiValue in self.moduleGHIValues:
            E = 522.6 * 0.1720 * 0.90 * ghiValue
            estimatedPower.append(E)
        self.estimatedModulePower = estimatedPower        

    def printEstimatedPower(self):
        print(self.estimatedModulePower,(self.times).strftime("%Y-%m-%dT%H:%M:%S.000Z").tolist())


#Modelling using POWER API data and DIRINT model then converting to module irradiance 
#Good until few-months close to real time.  Historicaal usage at the moment.
#solarCalc = SolarInsolation()

def main():
    #
    latitude = sys.argv[1]
    longitude = sys.argv[2]
    timeZone = sys.argv[3]
    moduleTilt = sys.argv[4]
    startDate = sys.argv[5]
    endDate = sys.argv[6]
    dataImport = ImportData(latitude,longitude,timeZone,moduleTilt,startDate,endDate)
    dataImport.getResponseFromAPI()
    dataImport.parseAndCalculateJSON()
    dataImport.calcDNIandDHI()
    dataImport.calculateModuleRadiation()
    dataImport.getEstimatedPowerProduction()
    dataImport.printEstimatedPower()

if __name__ == "__main__":
    main()
#Modelling using PVLib forecasting.
#Good for close to real-time.
#model = GFS()
#start = pandas.Timestamp(datetime(2021,10,25), tz='Canada/Saskatchewan')
#end = start + pandas.Timedelta(days=7)

#data = model.get_processed_data(50.4452,-104.6189,start,end)
#print(data.head())

#class DNIfromGHI:
#    def __init__(self):
#        self.a = 1
#    def pvLibDirintModel(self):
#        self.a = 1


# class DISCModel:
#      def __init__(self,latitude,longitude,timeZone,pressure,ghiValues,moduleTilt):
#         self.latitude = latitude
#         self.longitude = longitude
#         self.timeZone = timeZone
#         self.pressure = pressure
#         self.ghiValues = ghiValues
#         self.moduleTilt = moduleTilt
#         self.startTime
#         self.endTime

#      def inputVariables(self,latitude,longitude,timeZone,pressure,ghiValue,moduleTilt):
#          self.lwe