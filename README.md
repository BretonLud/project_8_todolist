ToDoList
========

Base du projet #8 : Améliorez un projet existant

https://openclassrooms.com/projects/ameliorer-un-projet-existant-1

### Prérequis

***

Installer docker

### Installation du projet

***

Pour commencer on clone le projet:

<pre><code><strong>git clone git@github.com:BretonLud/project_8_todolist.git
</strong></code></pre>

Ensuite il faut se rendre dans le dossier du projet

```
cd project_8_todolist
```

Créer un fichier à la racine du projet .env.local

```
nano .env.local
```

Paramétrer les variables d'environnement avec vos informations: (le smtp paramétré est celui de MailHog)

```
MARIADB_USER=username
MARIADB_PASSWORD=password
MARIADB_ROOT_PASSWORD=root
DATABASE_URL="mysql://username:password@database:3306/todolist"
```

Créer un fichier à la racine du projet .env.test.local

```
nano .env.test.local
```

Paramétrer les variables d'environnement avec vos informations:

```
DATABASE_URL="mysql://user:password@database:3306/todolist"
```

Lancer les containers docker avec les 2 commandes suivantes :

```
 docker compose build
 docker compose up -d
```

Vous avez accès site sur l'adresse suivante :

```
localhost:8888
```

Vous avez accès au coverage sur l'adresse suivante :

```
localhost:8889
```

Vous avez accès à 2 comptes :

```
identifiant: admin
password: admin.1234

et 

email: user
password: user.1234

```

Amusez vous ensuite :)