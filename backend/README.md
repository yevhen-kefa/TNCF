# TNCF — Backend (PHP / API REST)

Backend de l'application TNCF : logique métier et API REST consommées par le
frontend React. Construit en **PHP 8.2** servi par **Apache**, avec **MongoDB**
comme base de données.

## Conteneurisation

L'image est décrite par le [`Dockerfile`](../Dockerfile) à la racine du projet
(le contexte de build est la racine). Elle :

- part de `php:8.2-apache` ;
- installe et active l'extension PHP `mongodb` (via PECL) ;
- active `mod_rewrite` ;
- fixe le `DocumentRoot` Apache sur `/var/www/html/public`.

Le dossier `backend/` est monté en volume dans le conteneur
(`./backend:/var/www/html`), donc les modifications de code PHP sont prises en
compte sans reconstruire l'image.

Le service est exposé sur <http://localhost:8000> (port `80` du conteneur).

## Structure

```
backend/
├── public/                 # DocumentRoot Apache
│   ├── index.php           # Page d'accueil
│   ├── api_*.php           # Points d'entrée de l'API
│   ├── .htaccess           # Réécriture d'URL
│   ├── style/              # CSS
│   ├── img/                # Images / SVG
│   └── vendor/             # Dépendances PHP (dompdf) — versionnées
└── src/
    └── Config/Database.php # Connexion MongoDB (mongodb://db:27017)
```

## Base de données

La connexion est centralisée dans
[`src/Config/Database.php`](src/Config/Database.php). L'hôte MongoDB est le nom
de service Docker `db` :

```php
new \MongoDB\Driver\Manager("mongodb://db:27017");
```

Base utilisée : `tncf`. Principales collections : `utilisateurs`, trajets,
réservations, billets.

## CORS et sessions

Les scripts `api_*.php` autorisent les requêtes du frontend
(`http://localhost:3000`) avec `Access-Control-Allow-Credentials: true`. Les
sessions PHP servent à identifier l'utilisateur connecté ; le frontend envoie
donc ses requêtes avec `credentials: 'include'`.

## Dépendances PHP

Gérées par Composer ([`composer.json`](../composer.json) à la racine).
Dépendance principale : `dompdf/dompdf` (génération des billets PDF). Le dossier
`vendor/` est versionné, aucune installation n'est requise pour exécuter les
conteneurs.

Pour mettre à jour les dépendances en local (hors Docker) :

```bash
composer install
```
