import queue
import threading
import time
import paho.mqtt
import paho.mqtt.client as mqtt
import sqlite3
import json
import logging
import os
import traceback
import datetime
from collections import defaultdict
from logging.handlers import RotatingFileHandler
import schedule
import subprocess

# OTP
deviceOTP = "dev_d4_8a_fc_a5_a2_68"

# Config MQTT
broker_address = "127.0.0.1"
broker_port = 1883
sub_topic = ["data_topic", "command_topic"]
pub_topic = ["web_topic"]
username = "Gateway"
password = "12345678"

device_list_lock = threading.Lock()

# Config Device
device_list = [
    ["dev_a2_b7_65_61_13_9c", "1.0.0", "TBG", [], 0],
    ["dev_a2_b7_65_5f_75_ac", "1.0.0", "TBG", [], 0],
    ["dev_a2_b7_65_50_ea_c8", "1.0.0", "HLG", [], 0],
    ["dev_a2_b7_65_50_d1_2c", "1.0.0", "HLG", [], 0],
    ["dev_a2_b7_65_5f_04_70", "1.0.0", "HDG", [], 0],
    ["dev_a2_b7_65_60_94_cc", "1.0.0", "HDG", [], 0],
    ["dev_a2_b7_65_61_0c_ac", "1.0.0", "TNN", [], 0],
    ["dev_a2_b7_65_5f_96_88", "1.0.0", "TNN", [], 0],
    ["dev_a2_b7_65_60_94_10", "1.0.0", "DNG", [], 0],
    ["dev_a2_b7_65_61_09_18", "1.0.0", "DNG", [], 0],
    ["dev_a2_b7_65_50_c2_50", "1.0.0", "DLT", [], 0],
    ["dev_a2_b7_65_60_ef_2c", "1.0.0", "DLT", [], 0],
    ["dev_a2_b7_65_60_e0_68", "1.0.0", "DAN", [], 0],
    ["dev_a2_b7_65_5f_6b_34", "1.0.0", "DAN", [], 0],
    ["dev_a2_b7_65_50_ce_40", "1.0.0", "NTT", [], 0],
    ["dev_a2_b7_65_5e_55_48", "1.0.0", "NTT", [], 0],
    ["dev_a2_b7_65_50_cc_44", "1.0.0", "MTO", [], 0],
    ["dev_a2_b7_65_61_15_dc", "1.0.0", "MTO", [], 0],
    ["dev_a2_b7_65_60_2c_70", "1.0.0", "CTO", [], 0],
    ["dev_a2_b7_65_50_c0_8c", "1.0.0", "CTO", [], 0], 
]

location_list = [
    ["TBG" , False, 280, [], [], [], 20, []],
    ["HLG" , False, 280, [], [], [], 20, []],
    ["HDG" , False, 280, [], [], [], 20, []],
    ["TNN" , False, 280, [], [], [], 20, []],
    ["DNG" , False, 280, [], [], [], 20, []],
    ["DLT" , False, 280, [], [], [], 20, []],
    ["DAN" , False, 280, [], [], [], 20, []],
    ["NTT" , False, 280, [], [], [], 20, []],
    ["MTO" , False, 280, [], [], [], 20, []],
    ["CTO" , False, 280, [], [], [], 20, []],
]

class SQLiteDB(threading.Thread):
    def __init__(self, db_file, message_queue, pub_queue, timeout = 5):
        super().__init__()
        self.db_file = db_file
        self.connection = None
        self.cursor = None
        self.running = True
        self.message_queue = message_queue
        self.pub_queue = pub_queue
        self.timeout = timeout

    def connect(self):
        try:
            self.connection = sqlite3.connect(self.db_file, timeout=self.timeout)
            self.cursor = self.connection.cursor()            

            if not self.check_table_existence("Location_Info"):
                # Create table
                self.create_table("Location_Info", "user TEXT PRIMARY KEY, location TEXT")

            # self.delete_table("Phone_Info")
            if not self.check_table_existence("Phone_Info"):
                # Create table
                self.create_table("Phone_Info", "number TEXT PRIMARY KEY, creater TEXT, location TEXT, checkPoint INTEGER")

            # self.delete_table("General_Info")
            if not self.check_table_existence("General_Info"):
                # Create table
                self.create_table("General_Info", "location TEXT PRIMARY KEY, is_warning INTEGER, threshold INTEGER, temperates TEXT, notify TEXT, issue TEXT, delta INTEGER, sensor_warning TEXT")

            # Convert empty array to string default
            for location in location_list:
                if not self.check_table_existence(location[0] + "_data"):
                    # Create data draw
                    self.create_table(location[0] + "_data", "time INTEGER, temperates TEXT")

                    # Insert data in "Genaral_info" 
                    data_with_default = ["-65535_-65535_-65535_-65535_-65535_-65535_-65535_-65535_-65535_-65535" if isinstance(item, list) and len(item) == 0 and index == 3 else \
                                        "_________" if isinstance(item, list) and len(item) == 0 and (index == 5 or index == 7) else \
                                        "Lo.1_Lo.2_Lo.3_Lo.4_Lo.5_Lo.6_Lo.7_Lo.8_Lo.9_Lo.10_Lo.11" if isinstance(item, list) and len(item) == 0 and index == 4 else item for index, item in enumerate(location)]
                    
                    self.insert_data("General_Info", data_with_default)

            # Clear buffer
            location_list.clear()

            # Load from database
            rows = self.fetch_data("General_Info", None)

            for row in rows:
                location = [item.split('_') if index == 4 or index == 5\
                                            else [] if index == 7  \
                                            else [int(temperate) for temperate in item.split('_')] if index == 3  \
                                            else False if index == 1 and item == 0  \
                                            else True if index == 1 and item == 1  \
                                            else item for index, item in enumerate(row)]
                location_list.append(location) 

            # Delete old data after 3 days
            current_time_struct = time.localtime()
            three_days_ago = datetime.datetime.fromtimestamp(time.mktime(current_time_struct)) - datetime.timedelta(days=60)
            ago_time_struct = three_days_ago.timetuple()

            logging.info("Begin date: " + str(time.strftime("%d/%m/%y %H:%M:%S", ago_time_struct)))
            old_epoch_time = int(time.mktime(ago_time_struct))

            for location in location_list:
                self.delete_row(location[0] + "_data", "time < " + str(old_epoch_time))

            logging.info("Connected to SQLite database !!!")
        except sqlite3.Error as e:
            self.running = False
            logging.exception("SQLite Exception occurred:\n\n")
        except Exception as e:
            self.running = False
            logging.exception("Exception occurred:\n\n")
            
    def delete_row(self, table_name, condition):
        try:
            sql_query = f"DELETE FROM {table_name} WHERE {condition}"
            self.cursor.execute(sql_query)
            # logging.info("-> Querry: " + sql_query)
            logging.info("-> Table: \"" + str(table_name) + "\" Deteted: " + str(self.cursor.rowcount) + " rows")
            self.connection.commit()
        except sqlite3.Error as e:
            self.running = False
            logging.exception("SQLite Delete Exception occurred:\n\n")

    def update_table(self, table_name, condition):
        try:
            sql_query = f"UPDATE {table_name} SET {condition}"
            self.cursor.execute(sql_query)
            self.connection.commit()
        except sqlite3.Error as e:
            self.running = False
            logging.exception("SQLite Update Exception occurred:\n\n")

    def insert_or_update_table(self, table_name, data):
        try:
            sql_query = f"INSERT OR REPLACE INTO {table_name} VALUES ({','.join(['?' for _ in data])})"
            self.cursor.execute(sql_query, data)
            self.connection.commit()
        except sqlite3.Error as e:
            self.running = False
            logging.exception("SQLite Update Exception occurred:\n\n")

    def create_table(self, table_name, columns):
        try:
            create_table_query = f"CREATE TABLE IF NOT EXISTS {table_name} ({columns})"
            self.cursor.execute(create_table_query)
            self.connection.commit()
        except sqlite3.Error as e:
            self.running = False
            logging.exception("SQLite Create Exception occurred:\n\n")

    def delete_table(self, table_name):
        try:
            delete_table_query = f"DROP TABLE IF EXISTS {table_name}"
            self.cursor.execute(delete_table_query)
            self.connection.commit()
        except sqlite3.Error as e:
            self.running = False
            logging.exception("SQLite Delete table Exception occurred:\n\n")
            
    def check_table_existence(self, table_name):
        try:
            # Thực hiện truy vấn để lấy danh sách các bảng trong cơ sở dữ liệu
            self.cursor.execute("SELECT name FROM sqlite_master WHERE type='table' AND name=?", (table_name,))
            result = self.cursor.fetchone()  # Lấy kết quả của truy vấn

            # Nếu kết quả không rỗng, tức là bảng tồn tại
            if result:
                return True
            else:
                return False
        except sqlite3.Error as e:
            logging.exception("SQLite Check Exist Exception occurred:\n\n")
            return False
        
    def insert_data(self, table_name, data):
        try:
            insert_query = f"INSERT INTO {table_name} VALUES ({','.join(['?' for _ in data])})"
            self.cursor.execute(insert_query, data)
            self.connection.commit()
        except sqlite3.Error as e:
            logging.exception("SQLite Insert Exception occurred:\n\n")

    def fetch_data(self, table_name, conditions):
        try:
            select_query = f"SELECT * FROM {table_name}  WHERE {conditions}"
            if conditions is None:
                select_query = f"SELECT * FROM {table_name}"
            # logging.info(select_query)
            self.cursor.execute(select_query)
            rows = self.cursor.fetchall()
            return rows
        except sqlite3.Error as e:
            self.running = False
            logging.exception("SQLite Fetch Exception occurred:\n\n")

    def close_connection(self):
        if self.connection:
            self.connection.close()
            self.running = False
            logging.info("SQLite connection closed")

    def run(self):
        logging.info("Connecting to SQLiteDB...")
        self.connect()

        while self.running:
            try:
                # Lấy message từ hàng đợi
                message = self.message_queue.get(timeout=1)
                
                if message['type'] == "Store_data":
                    for location in message['data']['locations']:
                        self.insert_data(location[0] + "_data", [message['timestamp'], '_'.join(map(str, location[3]))])

                elif message['type'] == "check_point":
                    # Clear buffer
                    data_filter = []
                    current_epoch_time = int(time.time())

                    # Send SMS if have sensor overload
                    for location in location_list:
                        # logging.info(location[7])
                        if (len(location[7]) > 0) and (location[7][0] != "") and (location[7][0] != -1):     
                            rows = self.fetch_data("Phone_Info", None)                  
                            for row in rows:                                                 
                                if ((current_epoch_time - row[3]) > 60) and (location[0] in row[2].split('_')):
                                    if not row[0] in [iterm[0] for iterm in data_filter]:
                                        data_filter.append([row[0], []])

                                    for data in data_filter:
                                        if row[0] in data[0]:
                                            data[1].append(location[0] + "_" + '_'.join(map(str, location[7])))

                                            # Clear buffer
                                            location[7].clear()
                                            location[7].append(-1)

                    # update checkPoint to delay send SMS about 60s 
                    for sdt in data_filter:
                        self.update_table("Phone_Info", "checkPoint = " + str(current_epoch_time) + " WHERE number = \'" + str(sdt[0]) + "\'")

                    # Send msg via MQTT                      
                    if len(data_filter) > 0:
                        logging.info("-----------------------------") 

                        # Convert data_filter to a JSON-compatible format
                        json_data = []
                        for item in data_filter:
                            json_data.append({
                                "sdt": item[0],
                                "tags": item[1]
                            })                        

                        logging.info(json.dumps(json_data))
                        pub_queue.put({
                            "topic" : deviceOTP + "/command",                            
                            "payload": {
                                "command" : "send_sms",
                                "data" : json.dumps(json_data)
                            }
                        })                            
                        logging.info("-----------------------------\n")  

                elif message['type'] == "MQTT_Message" : 
                    payload_json = json.loads(message['payload'].decode())
                    current_epoch_time = int(time.time())
                    # logging.info(payload_json)
                    if message['topic'] == sub_topic[0]:
                        # Update status device
                        for device in device_list:
                            if ("clientID" in payload_json) and ("raw_data" in payload_json) and (device[0] == payload_json['clientID']):

                                try:
                                    # Semaphore
                                    device_list_lock.acquire()

                                    device[3] = payload_json['raw_data']
                                    device[4] = current_epoch_time
                                    
                                    device_list_lock.release()

                                except Exception as e:
                                    device_list_lock.release()
                                    logging.error("Update data sensor error: {}".format(e))                                    
                                
                    elif message['topic'] == sub_topic[1]:
                        if 'location' in payload_json:
                            for location in location_list:
                                if(location[0] == payload_json['location']):
                                    logging.info("-> " + str(payload_json))

                                    if payload_json['type'] == "Request-Warning-Flag":
                                        if not (payload_json['value'] is None): 
                                            location[1] = payload_json['value']

                                            # Update database
                                            self.update_table("General_Info", "is_warning = " + str(location[1]) + " WHERE location = \'" + str(location[0]) + "\'")

                                    elif payload_json['type'] == "Request-Threshold":
                                        if not (payload_json['value'] is None)  and not (payload_json['delta'] is None): 
                                            location[2] = payload_json['value']
                                            location[6] = payload_json['delta']
            
                                            # Update database
                                            self.update_table("General_Info", "threshold = " + str(location[2]) + ", delta = " + str(location[6]) + " WHERE location = \'" + str(location[0]) + "\'")

                                    elif payload_json['type'] == "Request-Notify" and payload_json['slot'] > 0:
                                        if not payload_json['value'] is None: 
                                            location[4][payload_json['slot'] - 1] = payload_json['value']

                                            # Update database
                                            self.update_table("General_Info", "notify = '" + '_'.join(map(str, location[4])) + "' WHERE location = \'" + str(location[0]) + "\'")

                                    elif payload_json['type'] == "Request-Issue" and payload_json['slot'] > 0:
                                        if not payload_json['value'] is None: 
                                            if payload_json['slot'] != 999:
                                                location[5].append(f"\n[{time.strftime('%d/%m/%Y %H:%M:%S', time.localtime())}] Location \"{location[4][payload_json['slot'] - 1]}\":\n{payload_json['value']}")
                                            else:
                                                location[5].append(f"\n[{time.strftime('%d/%m/%Y %H:%M:%S', time.localtime())}] Total:\n{payload_json['value']}")
                                                
                                            location[5] = location[5][1:]
                                        
                                            self.update_table("General_Info", "issue = '" + '_'.join(map(str, location[5])) + "' WHERE location = \'" + str(location[0]) + "\'")

                                    elif payload_json['type'] == "Request-Delete-Issue":
                                        if not payload_json['user'] is None: 

                                            # Clean data
                                            location[5] = ['', '', '', '', '', '', '', '', '', '']

                                            # Update database
                                            self.update_table("General_Info", "issue = '" + '_'.join(map(str, location[5])) + "' WHERE location = \'" + str(location[0]) + "\'")

                                    elif payload_json['type'] == "Request-Chart" and ('dateStart' in payload_json) and ('dateEnd' in payload_json) and payload_json['slot'] > 0:
                                        data_filter = []

                                        rows = self.fetch_data(location[0] + "_data", "time >= " + str(payload_json['dateStart']) + " and time <= " + str(payload_json['dateEnd']))
                                        for row in rows:
                                            if payload_json['dateStart'] <= row[0] and row[0] <= payload_json['dateEnd']:
                                                data_filter.append({'t': int(row[0]), 'v': int(row[1].split('_')[payload_json['slot'] - 1])})

                                        if len(data_filter) > 0:
                                            for i in range(0, len(data_filter), 10000):
                                                chunk = data_filter[i:i + 10000]
                                                self.pub_queue.put({
                                                    "topic" : payload_json['user'],                            
                                                    "payload": {
                                                        "command" : "chart_data",
                                                        "dateStart" : payload_json['dateStart'],
                                                        "dateEnd"   : payload_json['dateEnd'],
                                                        "length"  : len(data_filter),
                                                        "data": {
                                                            "filter": chunk
                                                        }
                                                    }
                                                })

                                                time.sleep(0.1)
                                        else:
                                            self.pub_queue.put({
                                                "topic" : payload_json['user'],                            
                                                "payload": {
                                                    "command" : "chart_data",
                                                    "dateStart" : payload_json['dateStart'],
                                                    "dateEnd"   : payload_json['dateEnd'],
                                                    "length"  : len(data_filter),
                                                    "data": {
                                                        "filter": data_filter
                                                    }
                                                }
                                            })                                        
    
                        elif payload_json['type'] == "Request-Location":
                            logging.info("-> " + str(payload_json))
                            data_location = []
                            location_for = ""

                            if not ('location_for' in payload_json) or payload_json['location_for'] == "Account":
                                location_for = "Account"
                                rows = self.fetch_data("Location_Info", None)                                
                                for row in rows:
                                    if row[0] == payload_json['user']:
                                        data_location.append(row[1].split('_'))

                            elif payload_json['location_for'] == "Sms":
                                location_for = "Sms"
                                rows = self.fetch_data("Phone_Info", None)
                                for row in rows:
                                    if row[0] == payload_json['user']:
                                        data_location.append(row[2].split('_'))

                            self.pub_queue.put({
                                "topic" : payload_json['leader'],                            
                                "payload": {
                                    "command" : "reponse_location",
                                    "data": {
                                        "user": payload_json['user'],
                                        "location_for" : location_for,
                                        "location": data_location
                                    }
                                }
                            })  
                        
                        elif payload_json['type'] == "Set-Locations":
                            logging.info("-> " + str(payload_json))
                            
                            if payload_json['location_for'] == "Account":
                                self.insert_or_update_table("Location_Info", [str(payload_json['user']), '_'.join(map(str, payload_json['locations']))])
                            elif payload_json['location_for'] == "Sms":
                                self.update_table("Phone_Info", "location = \'" + '_'.join(map(str, payload_json['locations'])) + "\' WHERE number = \'" + str(payload_json['user']) + "\'")

                        elif payload_json['type'] == "Set-Number-Phone":
                            logging.info("-> " + str(payload_json))
                            
                            # Send response
                            owner_number_phone = ""
                            data_filter = []
                            
                            rows = self.fetch_data("Phone_Info", None)
                            for i, row in enumerate(rows):
                                if row[0] == payload_json['number_phone']:
                                    owner_number_phone = row[1]
                                    
                                if (0 <= i) and (i < 20):
                                    data_filter.append(row)
                            
                            if len(owner_number_phone) == 0:
                                # Clear buffer
                                data_filter = []
                                self.insert_or_update_table("Phone_Info", [str(payload_json['number_phone']), str(payload_json['leader']), '_'.join(map(str, payload_json['locations'])), 0])
                                
                                # Update data response
                                rows = self.fetch_data("Phone_Info", None)
                                for i, row in enumerate(rows):                                        
                                    if (0 <= i) and (i < 20):
                                        data_filter.append(row)
                                    
                            self.pub_queue.put({
                                "topic" : payload_json['leader'],                            
                                "payload": {
                                    "command" : "reponse_numbers",
                                    "length"  : len(rows),
                                    "page"  : 1,
                                    "data": {
                                        "owner" : owner_number_phone,
                                        "filter": data_filter
                                    }                                        
                                }
                            })
                                                                    
                        elif payload_json['type'] == "Request-Numbers" or payload_json['type'] == "Delete-Number-Phone":
                            logging.info("-> " + str(payload_json))
                            data_filter = []
                            
                            # Delete phone number
                            if payload_json['type'] == "Delete-Number-Phone":
                                self.delete_row("Phone_Info", "number = \'" + str(payload_json['number_phone']) + "\'")
                                
                            rows = self.fetch_data("Phone_Info", None)
                            for i, row in enumerate(rows):
                                if payload_json['page'] > 0 and (((payload_json['page'] - 1) * 20) <= i) and (i < (payload_json['page'] * 20)):
                                    data_filter.append(row)

                            self.pub_queue.put({
                                "topic" : payload_json['leader'],                            
                                "payload": {
                                    "command" : "reponse_numbers",
                                    "length"  : len(rows),
                                    "page"  : payload_json['page'],
                                    "data": {
                                        "owner" : "",
                                        "filter": data_filter
                                    }                                        
                                }
                            })                                 


                # logging.info("-----> Step: Process Queue Store Data [End]")

            except queue.Empty:
                pass 
            except Exception as e:
                logging.exception("SQL Exception occurred:\n\n")

        self.close_connection()

class MQTTThread(threading.Thread):
    def __init__(self, message_queue, pub_queue):
        threading.Thread.__init__(self)
        self.running = True
        self.connected = False
        self.message_queue = message_queue
        self.pub_queue = pub_queue
        self.client = mqtt.Client(mqtt.CallbackAPIVersion.VERSION2, clean_session=True)

    def on_connect(self, client, userdata, flags, rc, properties):
        if rc == 0:
            logging.info("Connected to RabbitMQ broker !!!")
            for topic in sub_topic:
                self.client.subscribe(topic)

            self.connected = True
        else:
            if rc == 1:
                logging.info("Incorrect protocol version ...")
            elif rc == 2:
                logging.info("Invalid client identifier ...")
            elif rc == 3:
                logging.info("Server unavailable ...")
            elif rc == 4:
                logging.info("Bad username or password ...")
            elif rc == 5:
                logging.info("Not authorised ...")

            self.running = False

    # Hàm xử lý sự kiện khi client bị ngắt kết nối
    def on_disconnect(self, client, userdata, flags, rc, properties):
        self.connected = False
        logging.info("MQTT connection closed")            

    def on_message(self, client, userdata, msg):
        msg_dict = {
            'topic': msg.topic,
            'payload': msg.payload,
            'qos': msg.qos,
            'retain': msg.retain,
            'mid': msg.mid,
            'type': 'MQTT_Message'
        }
        self.message_queue.put(msg_dict)

    def run(self):
        logging.info("Connecting to MQTT broker...")

        self.client.username_pw_set(username, password)   
        self.client.on_connect = self.on_connect
        self.client.on_disconnect = self.on_disconnect
        self.client.on_message = self.on_message
        time.sleep(1)

        while True:
            try:
                self.client.connect(broker_address, broker_port, 60)
                self.client.loop_start() 
                break
            except:
                # Xảy ra lỗi khi kết nối, chờ và thử lại
                logging.error("Connection error. Retrying in 5 seconds...")
                time.sleep(10)
                continue

        while self.running:
            try:
                message = self.pub_queue.get(timeout=1)
                
                # logging.info("-----> Step: Process Queue Publish Data" + str(message))
                if (message["topic"] is not None):
                    device_list_lock.acquire()
                    
                    self.client.publish(message["topic"], json.dumps(message["payload"]))
                    
                    device_list_lock.release()
            except queue.Empty:
                pass          
            except Exception as e:
                device_list_lock.release()
                logging.error("Main loop MQTT: {}".format(e))


        self.client.disconnect()

def init_log() :    
    # Create if the log folder does not exist 
    log_path = "/var/www/tempMonitoringWeb/tool/logging"
    if not os.path.isdir(log_path):
       os.makedirs(log_path)
    
    # Now we will Create and configure logger 
    logging.basicConfig(
        level=logging.DEBUG,
        format='%(asctime)s - %(levelname)s :: %(message)s',
        datefmt='%a, %d %b %Y %H:%M:%S',
        handlers=[
            logging.handlers.RotatingFileHandler(
                filename    = '/var/www/tempMonitoringWeb/tool/logging/main.log', 
                maxBytes    = 20 * 1024 * 1024,
                mode        = 'a', 
                backupCount = 10,
                encoding    = None,
                delay       = 0
            ),
            logging.StreamHandler()      
        ]
    )

def is_within_time_range(start_hour=9, end_hour=21):
    current_hour = datetime.datetime.now().hour
    # return start_hour <= current_hour < end_hour
    return True

def event_function():    
    logging.info("Schedule the event for 2 AM")
    
    sudo_reboot = ["sudo", "reboot"]
    subprocess.run(sudo_reboot, check=True)

if __name__ == "__main__":

    init_log()
    logging.info("\n--------------------\n BEGIN \n--------------------")

    # Schedule the event for 2 AM
    logging.getLogger('schedule').setLevel(logging.WARNING)
    schedule.every().day.at("02:00:00").do(event_function)

    message_queue = queue.Queue()
    pub_queue = queue.Queue()

    mqtt_thread = MQTTThread(message_queue, pub_queue)
    mqtt_thread.start()

    db_thread = SQLiteDB("/var/www/tempMonitoringWeb/tool/data_temperate.db", message_queue, pub_queue)
    db_thread.start()

    # Main thread
    while mqtt_thread.running and db_thread.running:  
        try:
            if mqtt_thread.connected:
                notify_time = query_time = time.time()

                for device in device_list:
                    # Device not response after 5s
                    if (device[4] > 0 and ((int(query_time) - device[4]) > 5)):
                        try:
                            # Semaphore
                            device_list_lock.acquire()

                            device[3].clear()

                            device_list_lock.release()

                        except Exception as e:
                            device_list_lock.release()
                            logging.error("Clean data sensor error: {}".format(e))    

                    # Get data device
                    pub_queue.put({
                        "topic" : device[0] + "/command",                            
                        "payload": {
                            "command": "get_data"
                        }
                    })
                    time.sleep(0.01)
             
                # Time delay
                while True:
                    time.sleep(0.1)

                    if time.time() - notify_time > 1.0:
                        schedule.run_pending() 
                        notify_time = time.time()

                        try:
                            # Semaphore
                            device_list_lock.acquire()

                            # Tạo một từ điển với key là location và value là danh sách các thiết bị tương ứng
                            location_dict = defaultdict(list)
                            for device in device_list:
                                location_dict[device[2]].append(device)
                            
                            # In ra từ điển đã tạo                        
                            for location, devices_filter in location_dict.items():
                                if location in [location_detail[0] for location_detail in location_list]:
                                    index = None

                                    # Find location in list
                                    for i, location_info in enumerate(location_list):
                                        if location_info[0] == location:
                                            index = i
                                            break

                                    # Reset buffer data
                                    if index is not None:
                                        # logging.info(location_list[index][3])
                                        location_list[index][3].clear()
                                        location_list[index][3].extend([-65535] * 11)

                                        for slot, sensor_value in enumerate(location_list[index][3]):
                                            
                                            for device in devices_filter:                                            
                                                slot_error = 0
                                                
                                                if device[3]:
                                                    # logging.info(str(slot) + " " + str(device[3][slot]))
                                                    if (65535 != device[3][slot]):
                                                        slot_error += 1
                                                        location_list[index][3][slot] = device[3][slot]
                                                    elif (65535 == device[3][slot]) and (location_list[index][3][slot] == -65535):
                                                        location_list[index][3][slot] = device[3][slot]                                            

                                                # Setup sensor Wrong
                                                if slot_error > 1:
                                                    logging.info("2 sensors in the same location: " + str(location))

                            # Sub 1*C Di An                           
                            for i, location_info in enumerate(location_list):
                                if location_info[0] == 'DAN' and location_info[3][5] > -65535 and location_info[3][5] < 65535:
                                    location_info[3][5] -= 10
                                    
                                if is_within_time_range():
                                    list_sensor = []

                                    # Find sensor overload in location
                                    for i, temp in enumerate(location_info[3][:-1]):
                                        if (temp > -65535) and (temp < 65535) and (temp >= (location_info[2] + location_info[6])):
                                            list_sensor.append(i + 1)
                                    
                                    if (len(location_info[7]) == 0) and (len(list_sensor) > 0):
                                        location_info[7].extend(list_sensor)
                                    elif (len(location_info[7]) > 0) and (len(list_sensor) == 0):
                                        location_info[7].clear()
                                
                            device_list_lock.release()

                        except Exception as e:
                            device_list_lock.release()
                            logging.error("Publish data sensor error: {}".format(e))   


                        # Notify data device to Web (1s)
                        pub_queue.put({
                            "topic" : pub_topic[0],                            
                            "payload": {
                                "command" : "notify_data",                            
                                "timestamp": int(notify_time),
                                "data": {
                                    "locations": location_list
                                }
                            }
                        })
                        
                    elif time.time() - query_time > 5.0:

                        # Store database
                        message_queue.put({
                            "timestamp": int(notify_time),
                            "data": {
                                "locations": location_list
                            },
                            'type' : 'Store_data'
                        })

                        # Start checkPoint to send SMS
                        message_queue.put({
                            "timestamp": int(notify_time),
                            "data": {
                                "locations": location_list
                            },
                            'type' : 'check_point'
                        })

                        break

            else:
                logging.info("Waitting connect MQTT ...")

                # not connect to MQTT broker
                time.sleep(2)

        except KeyboardInterrupt as e:
            logging.error(e)
            break

    # Exist all of thread
    mqtt_thread.running = False
    mqtt_thread.join()

    db_thread.running = False
    db_thread.join()


