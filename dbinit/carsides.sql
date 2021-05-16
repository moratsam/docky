CREATE DATABASE if not exists carsides;
GRANT ALL PRIVILEGES ON carsides.* TO 'o'@'%';

use carsides;
create table if not exists images( id INT NOT NULL AUTO_INCREMENT, url TEXT NOT NULL, name VARCHAR(200) NOT NULL, PRIMARY KEY ( id ));
