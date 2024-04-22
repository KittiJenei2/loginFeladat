# loginFeladat
A feladat elkészítése során használjon git-t. A feladathoz hozzon létre egy 'develop' nevű branch-t. Ebből hozzon létre egy login branch-t és abban dolgozzon.
Amikor végzett a login-t mergelje a develop-ba.
Ebben a feladatban csak OOP kódot használjon. 
Különítse el a formok megjelenítését és az adatbázis műveleteket. 
Hozzon létre egy User osztályt a login és a regisztráció kezelésére. 

Hozzon létre "Users" adattáblát az alábbi mezőkkel:
- id int, primary key, autoincrement
- is_active tinyint default false
- name varchar(50) not null
- email varchar(25) not null unique
- password varchar(50) not null
- token varchar(100)
- token_valid_until datetime 
- created_at datetime default now()
- registered_at datetime
- picture varchar(50)
- deleted_at datetime 
A táblát programból hozza létre.

A menühöz adjon hozzá egy [Login] gombot. A [Login] gomb megnyomására jelenítse meg a Bejelentkezés képernyőt. Bejelentkezni emil cím és jelszó megadásával lehet a [Bejelentkezés] gombra kattintva.
A képernyő tartalmazzon még egy Elfelejtett jelszó és egy Regisztráció lehetőséget.
A Regisztráció form tartalmazza a Név, Email, Jelszó, Jelszó újra mezőket. Erős jelszó ellenőrzése egyelőre opcionális. A form beküldését követően generáljunk egy tokent, állítsuk be az érvényességét +10 percre, mentsük el az adatokat az adatbázisban és küldjünk egy emilt a megadott címre.
Az emil tartalmazza a regisztráció befejéséhez szükséges url-t. pl http://localhost:83/raktar?[token] és a határidőt Y-m-d H:i:s formátumban.
Ha a felhasználó a megadott határidőn belül az url-re kattint, akkor fejezzük be a regisztrációt, ellenkező esetben tájékoztassuk, hogy a token lejárt és ajánljuk fel neki az újraküldés lehetőséget. Eben az esetben küldjünk neki egy emilt egy új tokennel.
A regisztráció befejezése:
- az 'is_active' mező legyen true
- a 'token' és a 'token_valid_until' mező legyen null
- a 'registered_at' mező tartalmazza a regisztráció időpontját
Sikeres regisztráció esetén küldjünk egy emilt és tájékoztassuk a felhasználót a regisztráció tényéről. 
Az emil tartalmazza a bejelentkezéshez szükséges url-t, pl. http://localhost:83/raktar/login.php

A jelszó titkosítva legyen tárolva (php: password_hash() függvény).
Tesztelje a bejelentkezést a frissen regisztrált felhasználóval. Sikeres bejelentkezést követően írjon ki egy üdvözlő üzenetet.

Ajánlások:
/*************
 * A projekt ajánlott könyvtárstruktúrája:

projekt
- app
-- Views
---- Page.php
---- PageUser.php
-- Controllers
---- Request.php
---- UserController.php
-- Models
---- User.php
-- Database
---- UserRepository.php
---- Install.php
- index.php
- composer.json
 *************/
 
/*****************************
A composer.json fájl tartalma:
{
    "autoload": {
        "psr-4": {
          "App\\": "app/"
        }
    }
}	
******************************/

/***************
A projekt könyvtárában parancssorból futtassa:

> composer dumpautoload -o
> composer phpmailer/phpmailer
****************/

/***********
 * index.php tartalma
 ***********/
<?php
session_start();
include 'vendor\autoload.php';

use App\Views\Page;
use App\Controllers\UserController;
use App\Controllers\Request;
use App\Database\Install;

Page::head();
if (!Install::dbExists()) {
    Page::installBtn();
}
else {
    Page::nav();
}
Request::handle();
Page::footer();
