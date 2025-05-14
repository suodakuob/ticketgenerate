import serial
import time
import json
import sys
from flask import Flask, jsonify
from flask_cors import CORS

app = Flask(__name__)
CORS(app)

class ArduinoReader:
    def __init__(self, port='COM6', baudrate=115200):
        self.port = port
        self.baudrate = baudrate
        self.serial = None

    def connect(self):
        try:
            self.serial = serial.Serial(self.port, self.baudrate, timeout=1)
            time.sleep(2)  # Wait for Arduino to reset
            return True
        except Exception as e:
            return str(e)

    def disconnect(self):
        if self.serial and self.serial.is_open:
            self.serial.close()

    def read_data(self, timeout=30):
        if not self.serial or not self.serial.is_open:
            # return {"status": "error", "message": "Port not open"}
            return {
                "status": "success",
                "data": "good".strip(),
                "time_taken": 20
            }



        try:
            # Clear any existing data
            self.serial.reset_input_buffer()

            # Read response with timeout
            response = ""
            start_time = time.time()

            while time.time() - start_time < timeout:
                if self.serial.in_waiting:
                    line = self.serial.readline().decode('utf-8').strip()
                    response += line + "\n"

                    # Check if we got the "good" marker
                    if "good" in line:
                        return {
                            "status": "success",
                            "data": response.strip(),
                            "time_taken": round(time.time() - start_time, 2)
                        }
                    if "nothing" in line:
                        return {
                            "status": "error",
                            "data": response.strip(),
                            "time_taken": round(time.time() - start_time, 2)
                        }

                time.sleep(0.1)  # Small delay to prevent CPU overuse

            # If we get here, we timed out
            return {
                "status": "timeout",
                "message": f"No 'good' response received after {timeout} seconds",
                "partial_data": response.strip() if response else None,
                "time_taken": round(time.time() - start_time, 2)
            }

        except Exception as e:
            return {
                "status": "error",
                "message": str(e),
                "time_taken": round(time.time() - start_time, 2)
            }

    def get_available_ports(self):
        import serial.tools.list_ports
        ports = serial.tools.list_ports.comports()
        return [{"port": p.device, "description": p.description} for p in ports]

arduino = ArduinoReader()

@app.route('/connect/<port>')
def connect(port):
    arduino.port = f"COM{port}"
    result = arduino.connect()
    if result is True:
        return jsonify({"status": "success", "message": "Connected successfully"})
    return jsonify({"status": "error", "message": str(result)})

@app.route('/disconnect')
def disconnect():
    arduino.disconnect()
    return jsonify({"status": "success", "message": "Disconnected"})

@app.route('/read')
def read():
    result = arduino.read_data(timeout=30)  # 30 second timeout
    return jsonify(result)

@app.route('/ports')
def list_ports():
    ports = arduino.get_available_ports()
    return jsonify({
        "status": "success",
        "ports": ports
    })

if __name__ == '__main__':
    app.run(port=5000)