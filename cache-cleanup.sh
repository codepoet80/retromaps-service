#!/bin/bash
find /var/www/maps/cache/*.* -mmin +5 -exec rm -r {} \;

