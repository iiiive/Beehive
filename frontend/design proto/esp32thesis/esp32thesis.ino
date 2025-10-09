#include <WiFi.h>
#include <HTTPClient.h>
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
String serverName = "http://192.168.100.141/thesis/Beehive/sensor_insert.php";

// Sensor objects
DHTesp dht;
HX711 scale;

void setup() {
  Serial.begin(115200);
  pinMode(LED_PIN, OUTPUT);

  // Connect to WiFi
  Serial.println("Connecting to WiFi...");
  WiFi.begin(ssid, password);

  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }

  Serial.println("");
  Serial.println("WiFi Connected!");
  Serial.print("IP Address: ");
  Serial.println(WiFi.localIP());

  // Initialize sensors
  dht.setup(DHTPIN, DHTesp::DHT22);
  scale.begin(LOADCELL_DOUT, LOADCELL_SCK);
  scale.set_scale(0.420000); // adjust for your calibration
  scale.tare();              // zero the scale
}

void loop() {
  // Read sensors
  int h = dht.getHumidity();
  float t = dht.getTemperature();
  float weight = 0;
  int fan_status = 0; // 0 = OFF, 1 = ON

  // Check HX711 readiness
  if (scale.wait_ready_timeout(100)) {
    weight = scale.get_units(5);
  } else {
    Serial.println("HX711 not detected or no load cell connected!");
    weight = 0;
  }

  // Fan logic
  if (t > 25.90) {
    digitalWrite(LED_PIN, HIGH);
    fan_status = 1;
  } else {
    digitalWrite(LED_PIN, LOW);
    fan_status = 0;
  }

  // Print to Serial Monitor
  Serial.print("Weight: "); Serial.print(weight / 1000); Serial.print(" kg, ");
  Serial.print("Temp: "); Serial.print(t); Serial.print(" °C, ");
  Serial.print("Humidity: "); Serial.print(h); Serial.print(" %, ");
  Serial.print("Fan Status: "); Serial.println(fan_status);

  // Send data to server
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    String postData = "temperature=" + String(t) +
                      "&humidity=" + String(h) +
                      "&weight=" + String(weight / 1000) +
                      "&fan_status=" + String(fan_status);

    http.begin(serverName);
    http.addHeader("Content-Type", "application/x-www-form-urlencoded");

    int httpResponseCode = http.POST(postData);

    if (httpResponseCode > 0) {
      Serial.print("Data sent successfully, HTTP Response: ");
      Serial.println(httpResponseCode);
    } else {
      Serial.print("Error sending data. HTTP Response: ");
      Serial.println(httpResponseCode);
    }

    http.end();
  } else {
    Serial.println("WiFi disconnected. Trying to reconnect...");
    WiFi.reconnect();
  }

  delay(900000); // send every 15 seconds
}
