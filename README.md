# eshop_php


Files needed for server conf (apache2) are in folder _auth. Documents need to be moved in next folders:
first run ~:sudo a2enmod rewrite
000-default.conf &  default-ssl.conf need to be moved to /etc/apache2/sites-available/
Files: HyCA-crl.pem, Hydra_Operating_Systems_CA.crt and HyOS_server.pem need to be moved to /etc/apache2/ssl/
