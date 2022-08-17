# Spread visualisation

## 1. Instalačná príručka

1. Stiahnutie repozitáru - ```git clone https://github.com/zuky-dev/spread-visualisation.git```
2. Stiahnutie PHP knižníc - ```composer install``` (v roote projektu)
3. Stiahnutie JS knižníc ```npm install``` a následný build ```npm run build```
4. Pripravenie databázy - create schema, uloženie údajov a pripojení
5. Skopírovanie .env.example súboru s premenovaním na .env a následne spustiť príkaz ```php artisan key:generate```
6. Doplnenie .env súboru (DB pripojenie + ďalsie premenné [podľa časti 1.1](####-1.1-ENV-variables))
7. Spustenie migrácií pomocou ```php artisan migrate```
8. Spustenie projektu
    1. V developer režime stači príkaz ```php artisan serve``` a v druhom termináli zapnutý príkaz ```php artisan orderbook:fetch {--loop}```
    2. Na live serveri treba nastaviť serverové zobrazenie projektu pomocou apache (.htaccess), alebo nginx, na automatický sync treba zapnúť na serveri CRON a smerovať na aplikáciu (nepredpokladám, že by na serveri bol permanentne spustený terminál), POZN.: je treba dorobiť nastavenie CRONu na každých pár sekúnd, namiesto každej minúty (pre dosiahnutie real time feel)
    3. Na live serveri je potrebné vypnúť v .env APP_DEBUG

### 1.1 Premenné prostredia .ENV

Okrem základných premenných prostredia (napr.: na pripojenie k DB), sú dostupné aj dalšie premenné upravujúce správanie aplikácie (aplikácia funguje aj bez ich nastavenia nakoľko sú pridané default hodnoty)

1. ```CEXIO_CURRENCY_1``` - Hodnota 1. meny (default ETH)
2. ```CEXIO_CURRENCY_2``` - Hodnota 1. meny (default EUR)
3. ```CEXIO_API_ORDERBOOK_LIMIT``` - Počet top výsledkov v orderbooku (default 100)
4. ```CEXIO_API_LIMIT``` - Limitácia requestov na Cexio.io api (na deň, default 1x za sekundu)
5. ```CEXIO_CURRENCY_TOO_LOW_CUTOFF``` - Limitácia pod akú cenu aplikácia nezoberie údaje (default 491,16 EUR, v prepočte cca 500 USD)
6. ```CEXIO_API_TAKE_BEST``` - Limitácia koľko najlepších sa bude ukladať (default 5)

### 1.2 Konzolové príkazy

Okrem príkazov na generovanie (ktoré mi uľahšujú prácu), sú dôležité 2 príkazy

1. ```php artisan orderbook:fetch {--loop}``` - V základnom prevedení stiahne momentálny stav orderbooku. Za použitia switchu --loop sa bude príkaz cykliť podľa CEXIO_API_LIMIT
2. ```php artisan orderbook:truncate``` - Prečistí tabuľku orderbook logov (slúži najma pre testové účely)

## 2. Ako aplikácia funguje

Z backend časti si pomocou GuzzleHttp aplikácia sťahuje aktuálne údaje orderbooku z Cex.io, ktoré sa následne filtrujú a ponechávajú sa najlepšie hodnoty, až následne sa získané hodnoty parsujú do db formátu a ukladajú pomocou eloquent.

Okrem toho backend poskytuje aj API endpoint poskytujúci rozhranie pre získavanie dát pre graf. Hodnoty nie sú priamo vytiahnuté z databázy a poslané, ale sú upravené pomocou Resource a Resouce Kolekcie (predchádzanie rozširovaniu informácií o vnútornej štruktúre DB ako aj parse pre konkrétnu API)

Frontend je Vue3 aplikácia s knižnicami Axios (na http dotazovanie) a Chartjs (grafy). Pre rozlíšenie častí Frontendu sa práca s dátami realizuje za využitia Vuex a následným rozosielaním zmien.

### 2.1 Architektúra aplikácie v kocke

Okrem využitia štandardného laravel prístupu (MVC) je využitý aj pattern Service-Repository pre oddelenie business logiky a práce s modelom. API endpoint podlieha jednoduchej validácií (dopredné zabránenie útokom) a vracia JSON response podrobený úprave pomocou resourcov. na Web strane je zabezpečený redirect na konkrétnu URL.

Fe členenie je jednoduché (Vue má zakomponované MVC praktiky). Je pridané iba oddelenie súviosiace s prácou s konkrétnymi dátami.

## 3. Poznámky a možné vylepšnia k implementácií

1. Cex.io orderbook chce v endpointe meny v poradí Krypto, Reálna mena. Pri prehodení env nastavení Cex.io endpoint nebude fungovať
    1. hodilo by sa checkovať prípadne vymieňať poradie premenných
2. Pri rozsiahlejšej aplikácií by som sa priklonil viesť si meny v databáze, odstránil by sa tým horeuvedený problém a zároveň by bolo možné trackovat viac orderbookov
3. Zmenšiť order_book_logs tabuľku (na to aké dáta sú k dispozícií je relatívne obrovská). Vyhodil by som updated_at a deleted_at, meny zmenil na kľúče, prípadne sumu viedol v jednej konkrétnej mene (keby bol smer aplikácie, že sa bude robiť viac orderbookov naraz) a využil konverzné kurzy
4. Pri Crone nastaviť aby nešiel každú minútu ale podľa nastavenia API limitu
5. Upraviť dotazovanie sa z frontendu na lokálne API, aby sa server nepreťažil, poprípade úplne odstrániť setInterval (aj keď je na Promise, takže nemá taký dopad na efektivitu) a používať napr. websockety a bradcastom rozosielať nové údaje
6. Úprava grafickej strany FE
7. Odstánenie nepotrebých častí aplikácie (napr. keď sa nebude rozmýšlať nad prihlasovaním, tak tabuľka userov a default auth sú nepotrebné)
