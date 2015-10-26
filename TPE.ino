#include "DHT.h"
#include <Bridge.h>
#include <Process.h>
#include <YunServer.h>
#include <YunClient.h>
#include<LiquidCrystal.h>

#define DHTPIN 2
#define DHTTYPE DHT22

#define LCDRS 12
#define LCDE 11
#define LCDD4 6
#define LCDD5 5
#define LCDD6 4
#define LCDD7 3

#define POWER 13
#define L_ON_THERM 8
#define L_CHAUFF 9

// Listen on default port 5555, the webserver on the Yun
// will forward there all the HTTP requests for us.
YunServer server;
String readString;
int maTemp;

DHT dht(DHTPIN, DHTTYPE);
LiquidCrystal lcd(LCDRS, LCDE, LCDD4, LCDD5, LCDD6, LCDD7);

void setup() {
  Serial.begin(9600);

  // Bridge startup
  pinMode(POWER, OUTPUT);
  digitalWrite(POWER, LOW);
  
  pinMode(L_ON_THERM, OUTPUT);
  digitalWrite(L_ON_THERM, LOW);
  
  pinMode(L_CHAUFF, OUTPUT);
  digitalWrite(L_CHAUFF, LOW);
  
  dht.begin();
  lcd.begin(16, 2);
  lcd.print("Bonjour");
  
  float t = dht.readTemperature();
  maTemp = (int)t;
  
  Bridge.begin();

  // Listen for incoming connection only from localhost
  // (no one from the external network could connect)
  server.listenOnLocalhost();
  server.begin();
}

void loop() {
  if(digitalRead(13) == HIGH){
    delay(2000);
    float t = dht.readTemperature();
  
    lcd.clear();
    //Partie LCD
    if(isnan(t)){
      lcd.print("Erreur nan");  
    }
    else{
      lcd.setCursor(0, 0);
      lcd.print("Cour : ");
      lcd.print(t);
      lcd.print("*C");
      lcd.setCursor(0,1);
      lcd.print("Regl : ");
      lcd.print(maTemp);
      lcd.print("*C");
    }
    
    //Partie Processus
    Process p;           
    p.runShellCommand("python /mnt/sda1/TPE/ThermCo.py");
    
    //Partie Thermostat
    if(digitalRead(L_ON_THERM) == HIGH){
      if(t<maTemp){
        digitalWrite(L_CHAUFF, HIGH);
      }
      else{
       digitalWrite(L_CHAUFF, LOW);
      }
    }
    else{
      digitalWrite(L_CHAUFF, LOW);
    }
  }
  else{
    lcd.clear();
    lcd.print("arret");
  }
  // Get clients coming from server
  YunClient client = server.accept();

  // There is a new client?
  if (client) {
    // read the command
    String command = client.readString();
    command.trim();        //kill whitespace
    Serial.println(command);

    if (command == "ledon") {
      digitalWrite(POWER, HIGH);
    }
    else if (command == "ledoff") {
      digitalWrite(POWER, LOW);
    }
    else if (command == "tempplus") {
      maTemp++;
    }
    else if (command == "tempmoins") {
      maTemp--;
    }
    else if (command == "thermon") {
      digitalWrite(L_ON_THERM, HIGH);
    }
    else if (command == "thermoff") {
      digitalWrite(L_ON_THERM, LOW);
    }
    // Close connection and free resources.
    client.stop();
  }
  else{
    digitalWrite(L_ON_THERM, LOW);
    digitalWrite(L_CHAUFF, LOW);
  }
  delay(50); // Poll every 50ms
}




