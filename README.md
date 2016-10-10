# Official AdSpray Adabra Plugin for Magento 1

This is an integration module between [AdSpray Adabra](http://www.adabra.com/) technology and Magento 1.

## Module install and configuration

1. Download ZIP file
2. Expand your ZIP file
3. Copy its content in your Magento root path
4. Login your Magento
5. Flush your cache from "System > Cache Management"
6. Log out
7. Log in again

### How can I understand if my module has been correctly installed?

If you correctly installed your module you will see a new "Adabra" menù entry.

<img src="https://raw.githubusercontent.com/magespecialist/m1-Adabra_Full/master/screenshots/screen_1.png" width="480" />

### Cron

Adabra plugin can work both with Magento cron and system cron depending on your preference.
A **system cron** approach is the recommended way.

#### Option 1: Using System cron (recommended)

1. Open you Magento backend and go to "System > Config > Adabra > Feed > General"
2. Select "No" for "Use Magento Cron"<br /><img src="https://raw.githubusercontent.com/magespecialist/m1-Adabra_Full/master/screenshots/screen_4.png" width="480" />
3. Login your server by SSH
4. Edit your **web-user** crontab file and configure as follows (**change setting** depending on your **preferences and configuration**):<br /><img src="https://raw.githubusercontent.com/magespecialist/m1-Adabra_Full/master/screenshots/screen_7.png" width="480" />  
5. Restart cron service

#### Option 2: Using Magento cron

1. Make sure you Magento cron has been correctly configured
2. Open you Magento backend and go to "System > Config > Adabra > Feed > General"
3. Select "Yes" for "Use Magento Cron"<br /><img src="https://raw.githubusercontent.com/magespecialist/m1-Adabra_Full/master/screenshots/screen_3.png" width="480" />
4. You can adjust the feeds rebuild time according to your preferences. Multiple values can be selected holding CTRL key.<br />We strongly recommend to **select a maximum of 1-2 rebuild times** to avoid a server CPU overload.

### Transmission mode

Depending on your Adabra agreements you can configure your feeds for FTP or HTTP transfer.
You can switch and configure such settings by opening "System > Config > Adabra > Feed > General > HTTP Download" or "System > Config > Adabra > Feed > General > FTP Upload".
 
<img src="https://raw.githubusercontent.com/magespecialist/m1-Adabra_Full/master/screenshots/screen_6.png" width="480" /> 

## Monitoring feeds
 
You can check feed status every time you need from you backend by clicking "Adabra > Feed" menù entry.
  
<img src="https://raw.githubusercontent.com/magespecialist/m1-Adabra_Full/master/screenshots/screen_5.png" width="480" />

If you need you can force a feed rebuild by clicking "Rebuild feeds" button. Rebuild procedure may take several minutes and it is handled asynchronously.



