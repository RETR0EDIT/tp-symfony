# Importation des Réservations

Ce projet Symfony permet d'importer des réservations à partir d'un fichier JSON dans la base de données.

## Prérequis

- PHP 7.4 ou supérieur
- Composer
- Symfony CLI
- Une base de données configurée (par exemple, MySQL)

## Installation

1. Clonez le dépôt :

   ```sh
   git clone https://github.com/votre-utilisateur/tp-symfony.git
   cd tp-symfony

   ```

2. Installez les dépendances :

   ```sh
   composer install
   ```

3. Configurez votre base de données dans le fichier `.env` :

   ```env
   DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name"
   ```

4. Créez la base de données et les schémas :

   ```sh
   php bin/console doctrine:database:create
   php bin/console doctrine:schema:update --force
   ```

## Utilisation

#Liens

http://127.0.0.1:8000/register

http://127.0.0.1:8000/login
