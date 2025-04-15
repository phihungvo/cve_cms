[Castellano](README.es.md)

### Supported Devices

* **Sinotrack**: Confirmed ST-90X models using the Sinotrack protocol.
* **Coban**: TK303G model confirmed using GP103 protocol.
* **Teltonika**: By TCP using Teltonika protocol.
* **Concox** and **JimiLab**: JM-LL01 model confirmed via GT06 protocol.
* **Queclink**: Confirmed model GV500MA using Queclink protocol.
* **OsmAnd**: Using HTTP OsmAnd protocol.
* **iTriangle / Aquila**: Using the Aquila protocol.

### Features

* **Modern platform with user-friendly interface:** The platform uses Laravel 11 to provide a smooth user experience and an attractive graphical interface.
* **PHP 8.2 compatibility:** Leverages the latest features of PHP 8.2, including performance and security enhancements. It is also compatible with higher versions of PHP.
* **Data Management with MySQL 8:** Uses MySQL 8.0.12 or higher for efficient and secure management of large volumes of tracking data, as well as extensive support for GIS functionality.
* **Real-Time Tracking:** Allows users to track the location and status of their Sinotrack ST-90x devices in real time.
* **Detailed Reporting:** Generates comprehensive reports that aid in decision making and data analysis.
* **Alarms and Notifications:** Configure custom alarms (geofence, motion, speed, etc...) for specific events related to the tracking devices. Notifications can be configured via Telegram.
* **Multi-User Support:** Supports the creation of multiple user accounts with different levels of access and permissions.
* **Public Environment:** If you wish you can generate links for individual trips and share them publicly. You can also directly share a device where all its trips will be publicly visible.

### Requirements

- Linux SO
- PHP 8.3 or higher (bcmath bz2 intl mbstring opcache pdo_mysql pcntl redis sockets xsl zip)
- MySQL 8.0.12 or higher
- Redis
- MQTT 

### Local install 

Install requirement:

```
sudo apt-get update
sudo apt-get install php-dom php-xml
sudo apt install php-mysql
```

1. Launch the setup: `./composer setup`

2. Edit the `.env` file and fill in the necessary variables: `vi .env`

3. Generate the Laravel Key: `php artisan key:generate`

4. Launch the deploy: `./composer deploy`
