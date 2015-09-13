import time
import sys
import BaseHTTPServer
import urlparse
from cgi import parse_header, parse_multipart
from urlparse import parse_qs
from subprocess import call
import serial
import threading
from threading import Thread, Lock
import json
import ConfigParser

config = ConfigParser.ConfigParser()
config.read('/var/www/fabui/python/config.ini')

try:
#     pass 
    task_id = str(sys.argv[1])

except:
    print("Missing params")
    sys.exit()


HOST_NAME = ''
PORT_NUMBER = 9002
keepRunning = True
valuesChanged = True
jsonEnc = json.JSONEncoder()


def shutdown():
    global keepRunning
    keepRunning = False
    serialPort.write('M0')
    

values = { 
        'Kp' : 1,
        'Ki' : 0,
        'Kd' : 0,
        'setpoint' : 200,
        'inc-sp' : 10,
        'dec-sp' : 10,
        'auto-target' : 200,
        'actual-target' : 0,
        'extruder' : 0,
        'cycle' : 8,
        'pid-type' : 'cl',
}

def calcParams():
    if values['pid-type'] == 'cl': # in ('cl', 'pe', 'so', 'no'):
        values['Kp'] = 0.6 * serialPort.lastKuReply
        values['Ki'] = 2.0 * values['Kp'] / serialPort.lastTuReply
        values['Kd'] = values['Kp'] * serialPort.lastTuReply / 8
        
    if values['pid-type'] == 'pe': # in ('cl', 'pe', 'so', 'no'):
        values['Kp'] = 0.7 * serialPort.lastKuReply
        values['Ki'] = 2.5 * values['Kp'] / serialPort.lastTuReply
        values['Kd'] = 3 * values['Kp'] * serialPort.lastTuReply / 20
        
    if values['pid-type'] == 'so': # in ('cl', 'pe', 'so', 'no'):
        values['Kp'] = 0.33 * serialPort.lastKuReply
        values['Ki'] = 2.0 * values['Kp'] / serialPort.lastTuReply
        values['Kd'] = values['Kp'] * serialPort.lastTuReply / 3
        
    if values['pid-type'] == 'no': # in ('cl', 'pe', 'so', 'no'):
        values['Kp'] = 0.2 * serialPort.lastKuReply
        values['Ki'] = 2.0 * values['Kp'] / serialPort.lastTuReply
        values['Kd'] = values['Kp'] * serialPort.lastTuReply / 3
    

def processValueChange(postvars):
    if postvars['param'][0] in ('extruder', 'cycle'):
        try:
            values[postvars['param'][0]] = int(postvars['value'][0])
        except:
            pass
        
    elif postvars['param'][0] in ('Kp', 'Ki', 'Kd', 'setpoint', 'inc-sp', 'dec-sp', 'auto-target'):
        try:
            values[postvars['param'][0]] = float(postvars['value'][0])
        except:
            pass
        
    elif postvars['param'][0] == 'pid-type':
        values[postvars['param'][0]] = postvars['value'][0]
        calcParams()
        
        
         
    global valuesChanged
    valuesChanged = False
            
            

def handlePostRequest(postvars):
    responsvars = {}
    global valuesChanged

    
    print postvars
    if postvars['type'][0] == 'command':
        responsvars['type'] = 'command'
        if postvars['action'][0] == 'shutdown':
            shutdown()
            
        if postvars['action'][0] == 'auto':
            serialPort.write('M303 E%d S%0.2f C%d' % (values['extruder'], values['auto-target'], values['cycle']))
            values['actual-target'] =  values['auto-target']
            serialPort.autoActive = True
            valuesChanged = True
            responsvars['result'] = 'ok'
            
        if postvars['action'][0] == 'set':
            serialPort.write('M104 E%d S%0.2f' % (values['extruder'], values['setpoint']))
            values['actual-target'] = values['setpoint']
            responsvars['result'] = 'ok'
            
        if postvars['action'][0] == 'inc':
            values['setpoint'] += values['inc-sp']
            if values['setpoint'] > 250.0: values['setpoint'] = 250.0
            serialPort.write('M104 E%d S%0.2f' % (values['extruder'], values['setpoint']))
            values['actual-target'] = values['setpoint']
            valuesChanged = True
            responsvars['result'] = 'ok'
            
        if postvars['action'][0] == 'dec':
            values['setpoint'] -= values['inc-sp']
            if values['setpoint'] < 0.0: values['setpoint'] = 0.0
            serialPort.write('M104 E%d S%0.2f' % (values['extruder'], values['setpoint']))
            values['actual-target'] = values['setpoint']
            valuesChanged = True
            responsvars['result'] = 'ok'
            
        if postvars['action'][0] == 'apply':
            serialPort.write('M301 P%0.2f I%0.2f D%0.2f' % (values['Kp'], values['Ki'], values['Kd']))
            responsvars['result'] = 'ok'
        
        if postvars['action'][0] == 'save':
            serialPort.write('M500')
            responsvars['result'] = 'ok'
        
        if postvars['action'][0] == 'get-param':
            serialPort.write('M503')
            responsvars['result'] = 'ok'
        
        
    elif postvars['type'][0] == 'valueChange':
        responsvars['type'] = 'valueChange'
        processValueChange(postvars)
#         print 'ValueChange:', postvars['param'][0], values[postvars['param'][0]]
        
        responsvars['values'] = values
        
        
    elif postvars['type'][0] == 'update':
        
        responsvars['type'] = 'update'
        responsvars['temp'] = serialPort.lastTemperatureReply
        responsvars['bias'] = serialPort.lastBiasReply
        responsvars['Ku'] = serialPort.lastKuReply
        responsvars['Tu'] = serialPort.lastTuReply
        responsvars['min'] = serialPort.lastMinReply
        responsvars['max'] = serialPort.lastMaxReply
        responsvars['actual-target'] = values['actual-target']
        responsvars['valuesChanged'] = valuesChanged
    
    
    return jsonEnc.encode(responsvars)

def calcPidParam():
    pass

class MyHandler(BaseHTTPServer.BaseHTTPRequestHandler):

    def do_POST(s):
        """Respond to a POST request."""
        s.send_response(200)
        s.send_header("Content-type", "json")
        s.send_header("Access-Control-Allow-Origin", "*")
        s.end_headers()
        
        length = int(s.headers.getheader('content-length'))
        postvars = parse_qs(s.rfile.read(length), keep_blank_values=1)
        s.wfile.write(handlePostRequest(postvars))


class GcodeSerial(serial.Serial):
         
    def __init__(self, serial_port, serial_baud, timeout):
        serial.Serial.__init__(self, serial_port, serial_baud, timeout=timeout)
        self.received = 0
        self.sent = 0
        self.lastTemperatureReply = 0.0
        self.lastKuReply = 1.0
        self.lastTuReply = 1.0
        self.lastBiasReply = 1.0
        self.lastMaxReply = 1.0
        self.lastMinReply = 0.0
        self.autoActive = False
        self.listenThread = Thread(target=self.listener)
        self.listenThread.setDaemon(True)
        self.listenThread.start()
        self.pollThread = Thread(target=self.tempPoll)
        self.pollThread.setDaemon(True)
        self.pollThread.start()
        
    def write(self, data):
        
        print 'serial Send:', data
        return serial.Serial.write(self, data + '\r\n')
    
    def tempPoll(self):
        while keepRunning:
            if not self.autoActive: 
                self.write('M105')
            time.sleep(0.5)
    
    def listener(self):
        global valuesChanged
      
        resend = 0
        
        serial_in=""    
        while keepRunning:
            
            while serial_in=="" and keepRunning:
                 
                serial_in=self.readline().rstrip()
                #time.sleep(0.05)
                pass #wait!
            print 'serial in', serial_in
            if serial_in=="ok":
                #print "received ok"
                self.received+=1
                #print "sent: "+str(sent) +" rec: " +str(received)
    
            ##error
            if serial_in[:6]=="Resend":
                #resend line
                resend=serial_in.split(":")[1].strip()
                self.received-=1 #lost a line!
#                 trace("Error: Line no "+str(resend) + " has not been received correctly")
                

            if serial_in[:5]=="ok T:":
                
#                 ok T:76.38 @:127
                #Collected M105: Get Extruder & bed Temperature (reply)
                #EXAMPLE:
                #ok T:219.7 /220.0 B:26.3 /0.0 T0:219.7 /220.0 @:35 B@:0
                #trace(serial_in);
                self.lastTemperatureReply = float(serial_in[5:].split(' ')[0].strip())
                                
                self.received+=1
            
                ## temp report (wait)    
            if serial_in[:6]==" bias:":    
#                 bias: 38 d: 38 min: 108.32 max: 110.82
#                 Ku: 38.71 Tu: 24.90
                self.lastBiasReply = float(serial_in.strip().split(' ')[1].strip())
                self.lastMinReply = float(serial_in.strip().split(' ')[5].strip())
                self.lastMaxReply = float(serial_in.strip().split(' ')[7].strip())

            if serial_in[:4]==" Ku:":    
#                 bias: 38 d: 38 min: 108.32 max: 110.82
#                 Ku: 38.71 Tu: 24.90
                
                self.autoActive = False
                self.lastKuReply = float(serial_in.strip().split(' ')[1].strip())
                self.lastTuReply = float(serial_in.strip().split(' ')[3].strip())
                calcParams()
                serialPort.write('M300')
                values['actual-target'] = 0.0
                
                valuesChanged = True
                
            if serial_in[:12]=="echo:   M301":  
                #echo:   M301 P15.00 I5.00 D30.00
                values['Kp'] = float(serial_in[12:].strip().split(' ')[0].strip().replace('P',''))
                values['Ki'] = float(serial_in[12:].strip().split(' ')[1].strip().replace('I',''))
                values['Kd'] = float(serial_in[12:].strip().split(' ')[2].strip().replace('D',''))
                valuesChanged = True
            
            
            serial_in=""

'''#### SERIAL PORT COMMUNICATION ####'''
serial_port = config.get('serial', 'port')
serial_baud = config.get('serial', 'baud')
 
serialPort = GcodeSerial(serial_port, serial_baud, 0.5)
serialPort.flushInput()        


server_class = BaseHTTPServer.HTTPServer
httpd = server_class((HOST_NAME, PORT_NUMBER), MyHandler)

try:
    while keepRunning: 
        httpd.handle_request()
except:
    pass
httpd.server_close()


call (['sudo php /var/www/fabui/application/plugins/pidtune/ajax/finalize.php '+str(task_id)+' PIDtune'], shell=True)