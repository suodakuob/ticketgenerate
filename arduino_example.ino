/*
 * Simple Arduino Serial Communication Example
 * This sketch sends data continuously over the serial port
 * and includes the "good" keyword that our Laravel app is looking for
 */

void setup() {
  // Initialize serial communication at 9600 baud rate
  Serial.begin(9600);

  // Wait for serial port to connect
  // (needed for native USB port only)
  while (!Serial) {
    ; // wait for serial port to connect
  }

  // Send an initial message
  Serial.println("Arduino initialized and ready");
}

void loop() {
  // Get some sensor data (replace this with your actual sensors)
  int sensorValue = analogRead(A0);

  // Format data to include the "good" keyword that our Laravel app seeks
  Serial.print("Sensor value: ");
  Serial.print(sensorValue);
  Serial.println(" good");

  // Wait a bit before sending again
  delay(1000);
}
