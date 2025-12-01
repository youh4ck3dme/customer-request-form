# Customer Request Form

Táto aplikácia je jednoduchý formulár pre odosielanie dopytov zákazníkov, vytvorený pomocou React a TypeScript.

## Funkcie aplikácie

### Hlavné funkcie

- **Formulár pre dopyty**: Používatelia môžu vyplniť formulár s nasledujúcimi poľami:
  - Meno (povinné)
  - Mobil (povinné, telefónne číslo)
  - Email (povinné)
  - Správa (maximálne 160 znakov, s placeholderom)
- **Validácia**: Všetky povinné polia sú overené na strane klienta
- **Odosielanie dát**: Formulár odosiela údaje na externé API endpoint
- **Stavy formulára**: Zobrazuje rôzne stavy (načítavanie, úspech, chyba)

### Nastavenia API

- **Modálne okno nastavení**: Dostupné cez ikonku ozubeného kolesa v pravom hornom rohu
- **Konfigurácia prihlasovacích údajov**: Polia pre WordPress používateľské meno a aplikačné heslo
- **Ukladanie do LocalStorage**: Nastavenia sa uložia do prehliadača a zostanú zachované aj po obnovení stránky
- **Autentifikácia**: Používa Basic Auth hlavičku pre API volania

### Technické vlastnosti

- **Responsive dizajn**: Optimalizované pre rôzne veľkosti obrazoviek
- **Tailwind CSS**: Moderný CSS framework pre štýlovanie
- **TypeScript**: Statické typovanie pre lepšiu spoľahlivosť kódu
- **Vite**: Rýchly development server
- **ESLint**: Kontrola kvality kódu

### API integrácia

- **Endpoint**: `https://apps.gruppa.cloud/wp-json/jet-cct/queries/`
- **Metóda**: POST
- **Autentifikácia**: Basic Auth s údajmi z LocalStorage
- **Chybové hlášky**: Zobrazuje chyby ak nie sú nastavené API kľúče

## Spustenie aplikácie

Najprv spustite development server:

```bash
npm run dev
```

Otvorte [http://localhost:5173](http://localhost:5173) vo vašom prehliadači.

## Použitie

1. Kliknite na ikonku ozubeného kolesa v pravom hornom rohu pre nastavenie API prihlasovacích údajov
2. Vyplňte formulár s vašimi údajmi
3. Kliknite na "Odoslať" pre odoslanie dopytu
4. Po úspešnom odoslaní sa zobrazí potvrdzovacia správa

## Technológie

- React 18
- TypeScript
- Vite
- Tailwind CSS
- Lucide React (ikony)
- ESLint
