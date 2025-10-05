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

// === ADJUST THIS after calibration ===
float calibration_factor = 2000.0;  // Change this after testing
// =====================================

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

  // === HX711 Load Cell Setup ===
  scale.begin(LOADCELL_DOUT, LOADCELL_SCK);
  delay(1000);                    // 🕐 Allow HX711 to stabilize
  scale.set_scale(calibration_factor); // 🧭 Use your calibrated value
  scale.tare();                   // 🔄 Zero out the scale
  Serial.println("HX711 initialized and tared!");
}

void loop() {
  // Read sensors
  float t = dht.getTemperature();
  float h = dht.getHumidity();
  float weight = 0;
  int fan_status = 0;

  // Safely read from HX711
  if (scale.wait_ready_timeout(1000)) {
    weight = scale.get_units(10); // average of 10 samples
  } else {
    Serial.println("⚠️ HX711 not ready!");
    weight = 0;
  }

  // LED / Fan control
  if (t > 32) {
    digitalWrite(LED_PIN, HIGH);
    fan_status = 1;
  } else {
    digitalWrite(LED_PIN, LOW);
    fan_status = 0;
  }

  // Print locally
  Serial.printf("Weight: %.2f kg | Temp: %.2f °C | Humidity: %.1f %% | Fan: %d\n",
                weight, t, h, fan_status);

  // Display on LCD
  lcd.clear();
  lcd.setCursor(0, 0);
  lcd.printf("W:%.2fkg F:%d", weight, fan_status);
  lcd.setCursor(0, 1);
  lcd.printf("T:%.1fC H:%.0f%%", t, h);

  // ====== Send to server ======
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    http.begin(serverName);

    // Send data to PHP script
    String postData = "temperature=" + String(t, 2) +
                      "&humidity=" + String(h, 2) +
                      "&weight=" + String(weight, 2) +
                      "&fan_status=" + String(fan_status);

    http.addHeader("Content-Type", "application/x-www-form-urlencoded");
    int httpResponseCode = http.POST(postData);

    if (httpResponseCode > 0) {
      Serial.printf("HTTP Response: %d → %s\n", httpResponseCode, http.getString().c_str());
    } else {
      Serial.printf("HTTP Error: %d\n", httpResponseCode);
    }

    http.end();
  } else {
    Serial.println("⚠️ WiFi Disconnected!");
  }

  delay(1000); // every 5 seconds
}
