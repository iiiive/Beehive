#include <WiFi.h>
#include <HTTPClient.h>
#include <Wire.h>
#include <LiquidCrystal_I2C.h>
#include <DHTesp.h>
#include "HX711.h"

// Pin definitions
#define DHTPIN 15
#define LED_PIN 19
#define LOADCELL_DOUT 21
#define LOADCELL_SCK 22

// WiFi credentials
char ssid[] = "huaweiwifi4g";
char pass[] = "huawei4gwifiii";

// PHP script URL
String serverName = "http://192.168.100.99/thesis/Beehive/sensor_insert.php";;

// Sensor objects
DHTesp dht;
HX711 scale;
LiquidCrystal_I2C lcd(0x27, 16, 2); // LCD I2C address

void setup() {
  Serial.begin(115200);
  pinMode(LED_PIN, OUTPUT);

  // Connect to WiFi
  WiFi.begin(ssid, pass);
  Serial.print("Connecting to WiFi");
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println("\nWiFi Connected!");

  // Initialize I2C on custom SDA/SCL pins for LCD
  Wire.begin(27, 26); // SDA = 27, SCL = 26
  lcd.init();
  lcd.backlight();
  Serial.println("LCD initialized on SDA=27, SCL=26");

  // Initialize sensors
  dht.setup(DHTPIN, DHTesp::DHT22);
  scale.begin(LOADCELL_DOUT, LOADCELL_SCK);
  scale.set_scale(0.420000); // adjust for your calibration
  scale.tare();              // zero the scale
}

void loop() {
  // Read sensors
  float h = dht.getHumidity();
  float t = dht.getTemperature();
  float weight = 0;
  int fan_status = 0; // 0 = OFF, 1 = ON

  // Check if HX711 is ready
  if (scale.wait_ready_timeout(100)) {
    weight = scale.get_units(5);
  } else {
    Serial.println("HX711 not detected or no load cell connected!");
    weight = 0;
  }

  // LED logic: turn on if temp > 25°C
  if (t > 32) {
    digitalWrite(LED_PIN, HIGH);
    fan_status = 1;
  } else {
    digitalWrite(LED_PIN, LOW);
    fan_status = 0;
  }

  // Print to Serial
  Serial.print("Weight: "); Serial.print(weight); Serial.print(" g, ");
  Serial.print("Temp: "); Serial.print(t); Serial.print(" °C, ");
  Serial.print("Humidity: "); Serial.print(h); Serial.print(" %, ");
  Serial.print("Fan Status: "); Serial.println(fan_status);

  // Print to LCD
  lcd.clear();
  lcd.setCursor(0, 0);
  lcd.print("W:"); lcd.print(weight); lcd.print("g ");
  lcd.print("T:"); lcd.print(t); lcd.print("C");
  lcd.setCursor(0, 1);
  lcd.print("H:"); lcd.print(h); lcd.print("% ");
  lcd.print("F:"); lcd.print(fan_status);

  // Send data to PHP
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    http.begin(serverName);
    http.addHeader("Content-Type", "application/x-www-form-urlencoded");

    String postData = "temp=" + String(t) + "&hum=" + String(h) + "&weight=" + String(weight) + "&fan_status=" + String(fan_status);
    int httpResponseCode = http.POST(postData);

    if (httpResponseCode > 0) {
      Serial.println("Data sent successfully");
      String response = http.getString();
      Serial.println(response);
    } else {
      Serial.print("Error sending POST: ");
      Serial.println(httpResponseCode);
    }

    http.end();
  } else {
    Serial.println("WiFi Disconnected!");
  }

  delay(2000); // wait 2 seconds before next loop
}
