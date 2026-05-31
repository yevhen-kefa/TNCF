# Image de développement pour le frontend React + Vite
# Vite 8 nécessite Node 20.19+ / 22.12+ : on prend la LTS 22.
FROM node:22-alpine

WORKDIR /app

# On copie d'abord les manifestes pour profiter du cache de couches Docker :
# tant que package*.json ne change pas, l'étape npm ci n'est pas rejouée.
COPY package.json package-lock.json ./
RUN npm ci

# Copie du reste du code source (synchronisé ensuite par `docker compose watch`)
COPY . .

# Port du serveur de dev Vite (voir vite.config.js : port 5173, host 0.0.0.0)
EXPOSE 5173

CMD ["npm", "run", "dev"]
