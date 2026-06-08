#!/bin/bash
echo "=========== Import datas for TNCF ==========="
mongoimport --db tncf --collection utilisateur --type csv --headerline --file /docker-entrypoint-initdb.d/config.utilisateur.csv
mongoimport --db tncf --collection train --type csv --headerline --file /docker-entrypoint-initdb.d/config.train.csv
mongoimport --db tncf --collection voyage --type csv --headerline --file /docker-entrypoint-initdb.d/config.voyage.csv
mongoimport --db tncf --collection billet --type csv --headerline --file /docker-entrypoint-initdb.d/config.billet.csv

echo "=========== Finish ==========="
echo "=========== Import completed ==========="