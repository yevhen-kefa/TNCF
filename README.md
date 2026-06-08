# TNCF — Tran National des Chemins de Fer

Application web de réservation de billets de train. Le projet couvre tout le
parcours utilisateur : recherche de trajets, sélection de date et d'options,
ajout au panier, paiement et réception du billet (email / PDF).

> Projet réalisé par Mathéo LARIVIERE, Yevhen KEFA, Rayan ESSAIDI et Ethan POLIN.
> Voir le *Dossier de Conception Technique* pour le détail fonctionnel.

## Architecture

L'application repose sur trois conteneurs orchestrés par Docker Compose :

| Service    | Rôle                          | Technologie          | Port hôte → conteneur |
|------------|-------------------------------|----------------------|-----------------------|
| `frontend` | Interface utilisateur (SPA)   | React 19 + Vite + TS | `3000` → `5173`       |
| `backend`  | Logique métier et API REST    | PHP 8.2 + Apache     | `8000` → `80`         |
| `db`       | Base de données               | MongoDB              | `27018` → `27017`     |

```
Navigateur ──► frontend (localhost:3000)
                   │  fetch http://localhost:8000/api_*.php
                   ▼
              backend (localhost:8000) ──► db (mongodb://db:27017)
```

- Le **frontend** appelle le backend via `http://localhost:8000` (depuis le
  navigateur, donc le port publié sur l'hôte).
- Le **backend** se connecte à MongoDB via le nom de service Docker `db`
  (`mongodb://db:27017`, voir [backend/src/Config/Database.php](backend/src/Config/Database.php)).
- Le backend autorise le CORS depuis `http://localhost:3000` avec cookies de
  session (`credentials: include`).

## Prérequis

- [Docker](https://docs.docker.com/get-docker/) et Docker Compose v2
  (`docker compose`, intégré à Docker Desktop).

## Démarrage rapide

Depuis la racine du projet :

```bash
docker compose up --build
```

Puis ouvrir les URLs suivantes :

- Frontend (application) : <http://localhost:3000>
- Backend (page PHP / API) : <http://localhost:8000>
- MongoDB : `localhost:27018` (depuis l'hôte)

Pour développer avec rechargement automatique du frontend (synchronisation du
code dans le conteneur) :

```bash
docker compose watch
```

Pour arrêter et supprimer les conteneurs :

```bash
docker compose down
```

Pour repartir d'une base de données vierge (supprime le volume `mongo_data`) :

```bash
docker compose down -v
```

## Structure du projet

```
TNCF/
├── docker-compose.yml        # Orchestration des 3 services
├── Dockerfile                # Image du backend (PHP 8.2 + Apache + ext. mongodb)
├── composer.json             # Dépendances PHP (dompdf)
├── backend/
│   ├── public/               # Racine web Apache (DocumentRoot)
│   │   ├── index.php         # Page d'accueil PHP
│   │   ├── api_*.php         # Points d'entrée de l'API REST
│   │   └── vendor/           # Dépendances PHP (dompdf) — versionnées
│   └── src/Config/Database.php  # Connexion MongoDB
└── frontend/
    ├── Dockerfile            # Image du frontend (Node 22 + Vite)
    ├── vite.config.js        # Serveur de dev (host 0.0.0.0, port 5173)
    └── src/                  # Code React (pages, composants, contexte panier)
```

## API REST (principaux points d'entrée)

Servis par le backend sur `http://localhost:8000` :

| Méthode | Endpoint                    | Description                          |
|---------|-----------------------------|--------------------------------------|
| GET     | `/navitia.php`              | Recherche de trajets (mock Navitia)  |
| GET     | `/api_train.php`            | Liste des trajets                    |
| POST    | `/api_register.php`         | Inscription utilisateur              |
| POST    | `/api_login.php`            | Connexion                            |
| POST    | `/api_logout.php`           | Déconnexion                          |
| GET     | `/api_user.php`             | Profil de l'utilisateur connecté     |
| POST    | `/api_booking.php`          | Création d'une réservation           |
| GET     | `/api_tickets.php`          | Billets de l'utilisateur             |
| POST    | `/api_export_pdf.php`       | Génération du billet en PDF (dompdf) |
| POST    | `/api_forgot_password.php`  | Demande de réinitialisation          |
| POST    | `/api_reset_password.php`   | Réinitialisation du mot de passe     |

## Recherche de trains (données Navitia)

La recherche de trajets (page *Tickets*) consomme l'API **Navitia** (OpenData
SNCF). Le proxy externe d'origine étant hors service, le backend expose un
**mock local** [backend/public/navitia.php](backend/public/navitia.php) qui
renvoie des trajets TGV au **format Navitia exact** — le frontend
([frontend/src/pages/Tickets.tsx](frontend/src/pages/Tickets.tsx)) l'appelle sur
`http://localhost:8000/navitia.php`, sans logique de parsing à adapter.

Les trajets sont déterministes par itinéraire (mêmes horaires pour un même
couple départ/arrivée), répartis de 06h à 22h30, avec exclusion des départs
passés pour une recherche « aujourd'hui ».

> **Passage à la vraie API** : une fois une clé gratuite obtenue sur
> [navitia.io](https://navitia.io/), suivre les instructions en bas de
> [backend/public/navitia.php](backend/public/navitia.php) (auth Basic, variable
> d'environnement `NAVITIA_TOKEN`) et, au besoin, restaurer l'URL du proxy dans
> `Tickets.tsx`.

## Dépannage

- **`failed to read dockerfile: open Dockerfile: no such file or directory`**
  pour le service `frontend` : le `frontend/Dockerfile` était auparavant exclu
  par `.gitignore` et donc absent après un clone. Il est désormais versionné.
  Vérifiez qu'il existe bien et relancez `docker compose up --build`.
- **Le frontend n'atteint pas le backend** : assurez-vous que le service
  `backend` est démarré et accessible sur <http://localhost:8000>. Les appels
  du frontend ciblent ce port publié sur l'hôte.
- **Aucun train ne s'affiche dans la recherche** : vérifiez que
  <http://localhost:8000/navitia.php> répond bien un JSON `{"journeys":[...]}`.
  Une recherche « aujourd'hui » en fin de soirée renvoie peu ou pas de départs
  (les départs passés sont exclus) — testez avec une date future.
- **Erreur de connexion MongoDB côté backend** : le backend doit utiliser le
  nom de service `db` (et non `localhost`) comme hôte Mongo, conformément à
  [backend/src/Config/Database.php](backend/src/Config/Database.php).
