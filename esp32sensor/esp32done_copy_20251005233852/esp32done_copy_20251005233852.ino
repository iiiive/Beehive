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
LiquidCrystal_I2C lcd(0x27, 16, 2);

// === Calibration factor ===
float calibration_factor = 2000.0;  // adjust after calibration

// === Auto-tare control ===
unsigned long last_tare_time = 0;
const unsigned long tare_interval = 60000; // every 60s
const float tare_threshold = 0.05;         // if near 0kg

// === Filtering ===
float smooth_weight = 0;

void setup() {
  Serial.begin(115200);
  pinMode(LED_PIN, OUTPUT);

  // WiFi connection
  WiFi.begin(ssid, password);
  Serial.print("Connecting to WiFi");
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println("\nConnected to WiFi");
  Serial.print("IP: "); Serial.println(WiFi.localIP());

  // LCD
  Wire.begin(27, 26);
  lcd.init();
  lcd.backlight();

  // Sensors
  dht.setup(DHTPIN, DHTesp::DHT22);
  scale.begin(LOADCELL_DOUT, LOADCELL_SCK);
  delay(2000);
  scale.set_scale(calibration_factor);
  scale.tare();
  Serial.println("✅ HX711 initialized and tared!");
}

void loop() {
  float t = dht.getTemperature();
  float h = dht.getHumidity();
  float weight = 0;
  int fan_status = 0;

  if (scale.wait_ready_timeout(1000)) {
    weight = scale.get_units(10);
  } else {
    Serial.println("⚠️ HX711 not ready!");
    weight = 0;
  }

  // ====== Filtering (smooth reading) ======
  smooth_weight = 0.8 * smooth_weight + 0.2 * weight;
  weight = smooth_weight;

  // ====== Auto-return to zero ======
  if (fabs(weight) < tare_threshold) {
    weight = 0.0;  // snap to zero if nearly zero
  }

  // ====== Auto-tare every minute if stable ======
  if (millis() - last_tare_time > tare_interval && fabs(weight) < 0.05) {
    scale.tare();
    last_tare_time = millis();
    Serial.println("🔄 Auto-tared to maintain 0kg baseline.");
  }

  // Fan control
  if (t > 32) {
    digitalWrite(LED_PIN, HIGH);
    fan_status = 1;
  } else {
    digitalWrite(LED_PIN, LOW);
    fan_status = 0;
  }

  // Serial monitor
  Serial.printf("Weight: %.2f kg | Temp: %.2f °C | Humidity: %.1f %% | Fan: %d\n",
                weight, t, h, fan_status);

  // LCD
  lcd.clear();
  lcd.setCursor(0, 0);
  lcd.printf("W:%.2fkg F:%d", weight, fan_status);
  lcd.setCursor(0, 1);
  lcd.printf("T:%.1fC H:%.0f%%", t, h);

  // ====== Send data to PHP ======
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    http.begin(serverName);

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
  }

  delay(1000);
}
