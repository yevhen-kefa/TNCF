# TNCF — Frontend (React + TypeScript + Vite)

Interface utilisateur de l'application TNCF : recherche de trajets, panier,
réservation, paiement et consultation des billets. Construite avec **React 19**,
**TypeScript** et **Vite**, et routée avec **react-router-dom**.

## Conteneurisation

L'image est décrite par le [`Dockerfile`](./Dockerfile) (base `node:22-alpine`).
Le service de dev Vite écoute sur `0.0.0.0:5173` (voir
[`vite.config.js`](./vite.config.js)) et est publié sur <http://localhost:3000>.

Depuis la racine du projet :

```bash
docker compose up --build        # build + démarrage des 3 services
docker compose watch             # dev avec synchronisation du code
```

## Communication avec le backend

Les pages appellent l'API PHP via `http://localhost:8000` (port du backend
publié sur l'hôte) avec `credentials: 'include'` pour transmettre le cookie de
session. Aucune variable d'environnement n'est requise : l'URL est utilisée
côté navigateur.

## Développement local (hors Docker)

Prérequis : Node 20.19+ ou 22.12+ (Vite 8).

```bash
npm install      # installation des dépendances
npm run dev      # serveur de dev (http://localhost:5173)
npm run build    # build de production (tsc + vite build)
npm run lint     # ESLint
npm run preview  # prévisualisation du build
```

## Structure

```
frontend/
├── Dockerfile
├── vite.config.js
├── index.html
└── src/
    ├── main.tsx            # Point d'entrée
    ├── App.tsx             # Routes de l'application
    ├── pages/              # Pages (Home, Login, Signup, Booking, Cart, ...)
    ├── components/         # Composants réutilisables (TrainCard, TicketCard, ...)
    ├── context/            # CartContext (état du panier)
    └── assets/             # Styles CSS et images
```
