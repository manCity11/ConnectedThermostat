from datetime import datetime
from threading import Thread

import sqlite3
import urllib2
import time

def datetoday(day, month, year):
    d = day
    m = month
    y = year
    if m < 3:
    	z = y-1
    else:
    	z = y
    dayofweek = ( 23*m//9 + d + 4 + y + z//4 - z//100 + z//400 )
    if m >= 3:
    	dayofweek -= 2
    dayofweek = dayofweek%7
    return dayofweek
                                                            
                                                            
                                                            
months = [ 'january', 'february', 'march', 'april', 'may', 'june', 'july',
                                                                      'august', 'september', 'october', 'november', 'december' ]
                                                                      
days =[ 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi',
                                                                             'Dimanche' ]
                                                                             
today = datetime.now()

d = today.day
m = today.month
y = today.year
                                                                             
dayofweek = days[datetoday(d, m, y)-1]
                                                                             
val = ""
pin = 8
#reponse = urllib2.urlopen('http://localhost/arduino/mode/' + str(pin) + '/ouput')
#print reponse.read()

conn = sqlite3.connect('calendrier.db')
c = conn.cursor()
row = c.execute("SELECT etat FROM Calendrier WHERE jour=? AND heure=?", (dayofweek, today.hour)).fetchone()
val = str(row)
if val == "(1,)":
	print "1"
	reponse = urllib2.urlopen('http://localhost/arduino/thermon/')
	print reponse.read()
else:
	print "0"
	reponse = urllib2.urlopen('http://localhost/arduino/thermoff/')
	print reponse.read()
	
conn.close()
time.sleep(1)
