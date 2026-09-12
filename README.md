# FiscalTrack

Application web de **suivi des déclarations fiscales** et **mini-GED** pour **TIA INTERNATIONAL**.

| Élément | Choix |
| --- | --- |
| Langage | PHP |
| Framework | Laravel |
| Interface | AdminLTE |
| Base de données | MySQL |
| Fichiers (mini-GED) | Supabase Storage |
| Éditeur | Visual Studio Code |

Le cahier des charges est dans [CAHIER_DES_CHARGES.md](./CAHIER_DES_CHARGES.md).

> **Important.** FiscalTrack ne se connecte **pas** à Sage, ni à la DGI, ni à la CNPS. Les statuts fiscaux sont saisis **à la main**. Supabase sert uniquement à **stocker les fichiers** (PDF, scans, justificatifs).

---

## Comment lire ce guide

Ce README s’adresse à un **étudiant de niveau 2** qui débute.

- Exécute les commandes **dans l’ordre**, une par une.
- Une commande = une ligne à coller dans **PowerShell**.
- Si une étape affiche une erreur, lis la section [Erreurs fréquentes](#10-erreurs-fréquentes) avant de continuer.
- Ne saute pas l’installation des outils (étape 1) : sans eux, Laravel ne peut pas démarrer.

**Petit lexique**

| Mot | Signification |
| --- | --- |
| Terminal / PowerShell | La fenêtre où tu tapes les commandes |
| Composer | L’outil qui installe les librairies PHP (comme un “Play Store” pour PHP) |
| Artisan | L’outil de commandes de Laravel (`php artisan ...`) |
| `.env` | Fichier secret de configuration (mot de passe MySQL, clés Supabase). **Ne jamais le partager** |
| Migration | Script qui crée les tables dans MySQL |
| MVC | Modèle / Vue / Contrôleur : organisation du code Laravel |
| Bucket | “Dossier cloud” dans Supabase où on range les fichiers |

---

## 0. Ce que tu vas construire (vue d’ensemble)

```text
Navigateur (AdminLTE)
        |
        v
   Laravel (PHP)
        |
   +----+----+
   |         |
 MySQL    Supabase Storage
 (données)   (fichiers du GED)
```

- **MySQL** : utilisateurs, contribuables, déclarations, échéances.
- **Supabase** : fichiers uploadés (factures, avis, quittances).
- **AdminLTE** : le “habillage” visuel (menu à gauche, tableau de bord).

---

## 1. Installer les outils sur Windows

Installe **dans cet ordre**. Redémarre PowerShell (ferme + réouvre) après chaque installation.

### 1.1 Git

1. Télécharge : [https://git-scm.com/download/win](https://git-scm.com/download/win)
2. Installe avec les options par défaut.
3. Vérifie :

```powershell
git --version
```

Tu dois voir un numéro de version (ex. `git version 2.x.x`).

### 1.2 XAMPP (PHP + MySQL)

XAMPP fournit **PHP** et **MySQL** (via phpMyAdmin).

1. Télécharge : [https://www.apachefriends.org/](https://www.apachefriends.org/)
2. Installe (souvent dans `C:\xampp`).
3. Ouvre le **XAMPP Control Panel**.
4. Clique sur **Start** pour **Apache** et **MySQL**.

Ajoute PHP au PATH Windows :

1. Cherche “variables d’environnement” dans Windows.
2. **Path** → **Modifier** → **Nouveau**.
3. Ajoute :

```text
C:\xampp\php
C:\xampp\mysql\bin
```

Vérifie :

```powershell
php -v
```

Tu dois voir **PHP 8.2 ou plus**. Laravel actuel exige au moins PHP 8.2.

### 1.3 Composer

1. Télécharge : [https://getcomposer.org/Composer-Setup.exe](https://getcomposer.org/Composer-Setup.exe)
2. Pendant l’install, indique le `php.exe` de XAMPP : `C:\xampp\php\php.exe`.
3. Vérifie :

```powershell
composer --version
```

### 1.4 Node.js (npm)

Utile pour compiler les assets front (Vite).

1. Télécharge : [https://nodejs.org/](https://nodejs.org/) (version LTS).
2. Vérifie :

```powershell
node -v
npm -v
```

### 1.5 Visual Studio Code

1. Télécharge : [https://code.visualstudio.com/](https://code.visualstudio.com/)
2. Extensions conseillées : **PHP Intelephense**, **Laravel Extra Intellisense**, **MySQL**.

---

## 2. Aller dans le dossier parent

Dans PowerShell, place-toi dans le dossier **où** tu veux créer `FiscalTrack` (pas encore dedans).

Exemple : si tu travailles sur le Bureau :

```powershell
cd Desktop
```

---

## 3. Créer le projet Laravel

Création **directe** : Composer crée le dossier `FiscalTrack` et installe Laravel dedans.

```powershell
composer create-project laravel/laravel FiscalTrack
```

Cette commande peut prendre **plusieurs minutes**. Attends un message du type `Application ready`.

Entre ensuite dans le projet :

```powershell
cd FiscalTrack
```

Vérifie :

```powershell
php artisan --version
```

Tu dois voir un numéro du type `Laravel Framework 12.x`.

Installe les dépendances JavaScript :

```powershell
npm install
```

Ouvre ce dossier dans **VS Code** (`Fichier` → `Ouvrir un dossier` → `FiscalTrack`), puis un terminal (`Terminal` → `Nouveau terminal`). Les commandes suivantes se lancent **depuis ce dossier**.

---

## 4. Créer la base MySQL

### 4.1 Démarrer MySQL

Dans XAMPP : **MySQL → Start**.

### 4.2 Créer la base `fiscaltrack`

**Option A — phpMyAdmin (plus simple visuellement)**

1. Va sur [http://localhost/phpmyadmin](http://localhost/phpmyadmin)
2. Onglet **Nouvel onglet / Databases / Bases de données**.
3. Nom : `fiscaltrack`
4. Interclassement : `utf8mb4_unicode_ci`
5. **Créer**.

**Option B — ligne de commande**

```powershell
mysql -u root -e "CREATE DATABASE IF NOT EXISTS fiscaltrack CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

> Avec XAMPP, le mot de passe root est souvent **vide**. Si MySQL demande un mot de passe, utilise celui que tu as défini.

---

## 5. Brancher Laravel sur MySQL

Le fichier `.env` est à la racine du projet. C’est **la configuration secrète** de l’application.

### 5.1 Ouvrir `.env`

Dans VS Code : fichier `.env` (il est parfois masqué : affiche les fichiers cachés).

### 5.2 Modifier ces lignes

Remplace le bloc base de données par :

```env
APP_NAME=FiscalTrack
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=fiscaltrack
DB_USERNAME=root
DB_PASSWORD=
```

Laisse `DB_PASSWORD=` vide si tu n’as pas mis de mot de passe MySQL.

### 5.3 Générer la clé d’application (si besoin)

```powershell
php artisan key:generate
```

### 5.4 Vider le cache de config

```powershell
php artisan config:clear
```

### 5.5 Créer les tables Laravel de base

```powershell
php artisan migrate
```

Si ça réussit, tu vois `Migration ... done`. Dans phpMyAdmin, la base `fiscaltrack` contient des tables (`users`, `migrations`, …).

---

## 6. Lancer l’application une première fois

Il faut **deux terminaux** (deux onglets PowerShell).

**Terminal 1 — serveur PHP Laravel**

```powershell
php artisan serve
```

Laisse cette fenêtre ouverte. L’app écoute sur `http://127.0.0.1:8000`.

**Terminal 2 — Vite (CSS / JS)**

```powershell
npm run dev
```

Ouvre le navigateur (Edge) :

```text
http://127.0.0.1:8000
```

Tu dois voir la page d’accueil Laravel.  
Pour arrêter un serveur : `Ctrl + C` dans le terminal.

---

## 7. Intégrer AdminLTE

AdminLTE est le thème d’administration (menu latéral, cartes, tableaux). On utilise le package officiel le plus simple pour débuter : `jeroennoten/laravel-adminlte`.

### 7.1 Installer le package

```powershell
composer require jeroennoten/laravel-adminlte
```

### 7.2 Publier le thème (fichiers CSS/JS + config)

```powershell
php artisan adminlte:install
```

Cette commande copie :

- les fichiers AdminLTE dans `public/vendor`
- la config dans `config/adminlte.php`
- les traductions

Vérifie :

```powershell
php artisan adminlte:status
```

### 7.3 Publier les vues principales (layout)

```powershell
php artisan adminlte:install --only=main_views
```

Les vues se trouvent ensuite dans :

```text
resources/views/vendor/adminlte/
```

### 7.4 Créer une page “tableau de bord” qui utilise AdminLTE

Crée le fichier `resources/views/home.blade.php` :

```blade
@extends('adminlte::page')

@section('title', 'Tableau de bord')

@section('content_header')
    <h1>Tableau de bord FiscalTrack</h1>
@stop

@section('content')
    <div class="alert alert-success">
        AdminLTE est bien branché sur Laravel.
    </div>
    <p>Prochaines étapes : comptes membres, mini-GED, suivi des déclarations.</p>
@stop
```

### 7.5 Déclarer la route

Dans `routes/web.php`, ajoute :

```php
Route::get('/dashboard', function () {
    return view('home');
})->name('dashboard');
```

### 7.6 Tester

Avec `php artisan serve` encore lancé, ouvre :

```text
http://127.0.0.1:8000/dashboard
```

Tu dois voir le menu AdminLTE à gauche.

### 7.7 Personnaliser le menu (plus tard)

Le menu se configure dans `config/adminlte.php`, clé `'menu'`. Exemple à ajouter **quand les pages existeront** :

```php
'menu' => [
    ['header' => 'FiscalTrack'],
    [
        'text' => 'Tableau de bord',
        'url'  => 'dashboard',
        'icon' => 'fas fa-tachometer-alt',
    ],
    [
        'text' => 'Documents (GED)',
        'url'  => 'documents',
        'icon' => 'fas fa-folder-open',
    ],
    [
        'text' => 'Contribuables',
        'url'  => 'contribuables',
        'icon' => 'fas fa-users',
    ],
    [
        'text' => 'Déclarations',
        'url'  => 'declarations',
        'icon' => 'fas fa-file-invoice',
    ],
],
```

Après une modification de `config/adminlte.php` :

```powershell
php artisan config:clear
```

---

## 8. Authentification (connexion des membres)

Les comptes membres sont créés par l’administrateur (voir le CDC). On installe d’abord les écrans de login AdminLTE.

### 8.1 Installer le scaffolding Laravel UI

```powershell
composer require laravel/ui
php artisan ui bootstrap --auth
```

Si Artisan demande de remplacer des fichiers : réponds **yes**.

### 8.2 Remplacer les vues login par celles d’AdminLTE

```powershell
php artisan adminlte:install --only=auth_views
```

### 8.3 Compiler le front Bootstrap (une fois)

```powershell
npm install
npm run build
```

### 8.4 Recréer / mettre à jour les tables

```powershell
php artisan migrate
```

### 8.5 (Optionnel) Créer un premier utilisateur de test

```powershell
php artisan tinker
```

Puis colle ceci, et valide avec `Enter` :

```php
\App\Models\User::create([
    'name' => 'Administrateur',
    'email' => 'admin@fiscaltrack.test',
    'password' => bcrypt('password'),
]);
```

Tape `exit` pour quitter Tinker.

Connexion : `http://127.0.0.1:8000/login`  
E-mail : `admin@fiscaltrack.test`  
Mot de passe : `password`

> Change ce mot de passe dès que tu sors des tests.

### 8.6 Protéger le tableau de bord

Dans `routes/web.php` :

```php
Route::get('/dashboard', function () {
    return view('home');
})->middleware('auth')->name('dashboard');
```

Sans connexion, Laravel renvoie vers `/login`.

---

## 9. Brancher Supabase (fichiers du mini-GED)

**Rappel.** MySQL garde les informations. Supabase stocke les **fichiers**. Il n’y a pas de synchronisation fiscale automatique.

### 9.1 Créer un projet Supabase

1. Va sur [https://supabase.com](https://supabase.com) et crée un compte.
2. **New project**.
3. Nom : `fiscaltrack`.
4. Choisis un mot de passe base (note-le, même si on n’utilise pas la base Postgres de Supabase).
5. Attends que le projet soit **Ready**.

### 9.2 Créer un bucket (dossier cloud)

1. Menu **Storage**.
2. **New bucket**.
3. Nom : `documents`
4. Pour débuter : coche **Public** (plus simple pour tester les URL). Plus tard on pourra le passer en privé.
5. **Create bucket**.

### 9.3 Récupérer les clés

1. **Project Settings** → **API**.
2. Copie :
   - **Project URL** (ex. `https://xxxxxxxxxxxx.supabase.co`)
   - **anon public** key
   - **service_role** key (secrète : uniquement côté Laravel, jamais dans le JavaScript du navigateur)

### 9.4 Ajouter les clés dans `.env`

À la fin du fichier `.env` :

```env
SUPABASE_URL=https://xxxxxxxxxxxx.supabase.co
SUPABASE_ANON_KEY=eyJhbGciOi...
SUPABASE_SERVICE_ROLE_KEY=eyJhbGciOi...
SUPABASE_BUCKET=documents
```

Remplace les `xxxx` et les clés par **tes** valeurs.

### 9.5 Installer le client HTTP (déjà dans Laravel)

Laravel inclut `Illuminate\Support\Facades\Http`. Pas besoin d’un autre package pour un premier test.

Si tu veux le driver S3 (plus tard, option avancée) :

```powershell
composer require league/flysystem-aws-s3-v3 "^3.0" --with-all-dependencies
```

Pour ce projet étudiant, on commence par l’**API Storage REST** de Supabase (plus lisible).

### 9.6 Créer le service PHP

Crée le dossier puis le fichier :

```powershell
New-Item -ItemType Directory -Force -Path "app\Services"
```

Crée `app/Services/SupabaseStorageService.php` :

```php
<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;

class SupabaseStorageService
{
    private function baseUrl(): string
    {
        $url = rtrim(config('services.supabase.url'), '/');
        $bucket = config('services.supabase.bucket');

        return $url.'/storage/v1/object/'.$bucket;
    }

    private function headers(): array
    {
        return [
            'Authorization' => 'Bearer '.config('services.supabase.service_role_key'),
            'apikey' => config('services.supabase.service_role_key'),
        ];
    }

    public function upload(UploadedFile $file, string $folder = 'ged'): string
    {
        $path = $folder.'/'.uniqid().'_'.$file->getClientOriginalName();

        $response = Http::withHeaders($this->headers())
            ->attach('file', file_get_contents($file->getRealPath()), $file->getClientOriginalName())
            ->post($this->baseUrl().'/'.$path);

        if ($response->failed()) {
            throw new \RuntimeException('Échec upload Supabase : '.$response->body());
        }

        return $path;
    }

    public function publicUrl(string $path): string
    {
        $url = rtrim(config('services.supabase.url'), '/');
        $bucket = config('services.supabase.bucket');

        return $url.'/storage/v1/object/public/'.$bucket.'/'.$path;
    }
}
```

### 9.7 Déclarer la config Laravel

Dans `config/services.php`, ajoute dans le tableau retourné :

```php
'supabase' => [
    'url' => env('SUPABASE_URL'),
    'anon_key' => env('SUPABASE_ANON_KEY'),
    'service_role_key' => env('SUPABASE_SERVICE_ROLE_KEY'),
    'bucket' => env('SUPABASE_BUCKET', 'documents'),
],
```

Recharge la config :

```powershell
php artisan config:clear
```

### 9.8 Route de test d’upload (à retirer plus tard)

Dans `routes/web.php` :

```php
use App\Services\SupabaseStorageService;
use Illuminate\Http\Request;

Route::post('/test-upload', function (Request $request, SupabaseStorageService $storage) {
    $request->validate([
        'fichier' => ['required', 'file', 'max:10240'],
    ]);

    $path = $storage->upload($request->file('fichier'));

    return [
        'ok' => true,
        'chemin' => $path,
        'url' => $storage->publicUrl($path),
    ];
})->middleware('auth');
```

Petit formulaire de test : crée `resources/views/test-upload.blade.php` :

```blade
@extends('adminlte::page')

@section('title', 'Test upload')

@section('content_header')
    <h1>Test upload Supabase</h1>
@stop

@section('content')
    <form action="{{ url('/test-upload') }}" method="post" enctype="multipart/form-data">
        @csrf
        <input type="file" name="fichier" required>
        <button type="submit" class="btn btn-primary">Envoyer</button>
    </form>
@stop
```

Route GET :

```php
Route::get('/test-upload', function () {
    return view('test-upload');
})->middleware('auth');
```

### 9.9 Tester

1. `php artisan serve`
2. Connecte-toi.
3. Ouvre `http://127.0.0.1:8000/test-upload`
4. Envoie un PDF ou une image.
5. Dans Supabase → **Storage** → bucket `documents` : le fichier doit apparaître.

Quand le mini-GED réel sera codé, **supprime** ces routes de test.

---

## 10. Erreurs fréquentes

| Erreur | Cause probable | Que faire |
| --- | --- | --- |
| `php n’est pas reconnu` | PHP pas dans le PATH | Ajoute `C:\xampp\php` au PATH, **ferme et rouvre** PowerShell |
| `composer n’est pas reconnu` | Composer pas installé / PATH | Réinstalle Composer, rouvre PowerShell |
| `could not find driver` (PDO MySQL) | Extension MySQL PHP désactivée | Dans `C:\xampp\php\php.ini`, décommente `extension=pdo_mysql` et `extension=mysqli`, redémarre |
| `Access denied for user root` | Mauvais mot de passe MySQL | Vérifie `DB_PASSWORD` dans `.env` |
| `SQLSTATE[HY000] [2002]` | MySQL pas démarré | XAMPP → Start MySQL |
| `Project directory is not empty` | Un dossier `FiscalTrack` existe déjà | Choisis un autre nom, ou supprime / vide le dossier avant de relancer `composer create-project` |
| Page blanche / 500 | Clé APP ou cache | `php artisan key:generate` puis `php artisan config:clear` |
| AdminLTE sans CSS | Assets non publiés | `php artisan adminlte:install --force` |
| Upload Supabase 401 / 403 | Mauvaise clé ou bucket | Vérifie `.env`, utilise la **service_role** côté serveur, nom du bucket `documents` |
| Port 8000 déjà utilisé | Un ancien `artisan serve` tourne encore | `Ctrl + C` dans l’ancien terminal, ou `php artisan serve --port=8001` |

---

## 11. Commandes du quotidien (à retenir)

Toujours travailler depuis la racine du projet (le dossier qui contient `artisan`).

| But | Commande |
| --- | --- |
| Lancer le site | `php artisan serve` |
| Compiler le front (dev) | `npm run dev` |
| Compiler le front (final) | `npm run build` |
| Créer / mettre à jour les tables | `php artisan migrate` |
| Annuler la dernière migration | `php artisan migrate:rollback` |
| Vider le cache | `php artisan config:clear` |
| Voir les routes | `php artisan route:list` |
| Créer un contrôleur | `php artisan make:controller NomController` |
| Créer un modèle + migration | `php artisan make:model Nom -m` |
| Voir la version Laravel | `php artisan --version` |

Démarrage typique **chaque jour** :

1. XAMPP : Start **MySQL**
2. Terminal 1 : `php artisan serve`
3. Terminal 2 : `npm run dev`
4. Navigateur : `http://127.0.0.1:8000`

---

## 12. Ordre recommandé pour coder ensuite

Quand l’installation est OK, suis le CDC dans cet ordre :

1. Comptes membres (rôles admin / comptable)
2. Mini-GED (upload vers Supabase + métadonnées en MySQL)
3. Contribuables
4. Déclarations + échéances
5. Notifications in-app (Scheduler Laravel)

```powershell
php artisan make:model Document -m
php artisan make:controller DocumentController --resource
```

Les mises à jour de statut fiscal restent **manuelles** (pas d’API DGI / CNPS / Sage).

---

## 13. Fichiers à ne jamais committer

Ne mets **pas** sur Git :

- `.env` (mots de passe, clés Supabase)
- `vendor/` (installé par Composer)
- `node_modules/` (installé par npm)
- `storage/logs/`

Le fichier `.gitignore` de Laravel gère déjà la plupart de ces cas. Après un `git clone`, un autre étudiant devra faire :

```powershell
composer install
npm install
copy .env.example .env
php artisan key:generate
```

Puis remplir `.env` (MySQL + Supabase) et lancer `php artisan migrate`.
