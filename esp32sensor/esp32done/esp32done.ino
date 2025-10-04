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
const char* ssid = "huaweiwifi4g";
const char* password = "huawei4gwifiii";

// PHP script URL
String serverName = "http://192.168.100.99/thesis/Beehive/sensor_insert.php";

// Sensor objects
DHTesp dht;
HX711 scale;
LiquidCrystal_I2C lcd(0x27, 16, 2); // LCD I2C address

void setup() {
  Serial.begin(115200);
  pinMode(LED_PIN, OUTPUT);

  // Connect WiFi
  WiFi.begin(ssid, password);
  Serial.print("Connecting to WiFi");
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println("\nConnected to WiFi");
  Serial.print("IP: "); Serial.println(WiFi.localIP());

  // Initialize I2C for LCD
  Wire.begin(27, 26); // SDA=27, SCL=26
  lcd.init();
  lcd.backlight();

  // Initialize sensors
  dht.setup(DHTPIN, DHTesp::DHT22);
  scale.begin(LOADCELL_DOUT, LOADCELL_SCK);
  scale.set_scale(0.420000); // calibration factor
  scale.tare();
}

void loop() {
  // Read sensors
  int h = dht.getHumidity();
  float t = dht.getTemperature();
  float weight = 0;
  int fan_status = 0;

  if (scale.wait_ready_timeout(100)) {
    weight = scale.get_units(5);
  } else {
    Serial.println("HX711 not detected!");
    weight = 0;
  }

  if (t > 32) {
    digitalWrite(LED_PIN, HIGH);
    fan_status = 1;
  } else {
    digitalWrite(LED_PIN, LOW);
    fan_status = 0;
  }

  // Print locally
  Serial.printf("Weight: %.2f kg, Temp: %.2f °C, Humidity: %d %%, Fan: %d\n",
                weight/1000, t, h, fan_status);

  lcd.clear();
  lcd.setCursor(0, 0);
  lcd.printf("W:%.2fkg F:%d", weight/1000, fan_status);
  lcd.setCursor(0, 1);
  lcd.printf("T:%.1fC H:%d%%", t, h);

  // ====== Send to server ======
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    http.begin(serverName);

    // Adjust to match your PHP script expected params
    String postData = "temperature=" + String(t) +
                      "&humidity=" + String(h) +
                      "&weight=" + String(weight/1000) +
                      "&fan_status=" + String(fan_status);

    http.addHeader("Content-Type", "application/x-www-form-urlencoded");
    int httpResponseCode = http.POST(postData);

    if (httpResponseCode > 0) {
      Serial.print("HTTP Response code: ");
      Serial.println(httpResponseCode);
      Serial.println(http.getString());  // echo from PHP
    } else {
      Serial.print("Error code: ");
      Serial.println(httpResponseCode);
    }
    http.end();
  } else {
    Serial.println("WiFi Disconnected!");
  }

  delay(5000); // every 5 seconds
}
