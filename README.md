# Village des Femmes — Site Web

## Structure des fichiers

```
village-des-femmes/
├── index.html          ← Page Accueil
├── index.css           ← Styles spécifiques à l'accueil
├── nous-soutenir.html  ← Page Nous Soutenir
├── nous-soutenir.css   ← Styles spécifiques à Nous Soutenir
├── styles.css          ← Styles communs (header, footer, boutons…)
├── main.js             ← JavaScript commun
└── images/             ← Dossier à créer avec tes images
    ├── logo.png
    ├── hero-accueil.jpg
    ├── hero-soutenir.jpg
    ├── mission.jpg
    ├── event1.jpg
    ├── event2.jpg
    └── event3.jpg
```

## Comment ajouter tes images

1. Crée un dossier `images/` à côté des fichiers HTML
2. Place tes fichiers avec les noms indiqués ci-dessus
   (ou change les `src="..."` dans le HTML selon tes noms de fichiers)
3. Le logo doit être en PNG avec fond transparent si possible

## Palette de couleurs

| Nom         | Code hex  | Utilisation                    |
|-------------|-----------|--------------------------------|
| Rose pâle   | `#F9D6D8` | Fonds de sections              |
| Rose vif    | `#E75480` | Boutons, accents, liens actifs |
| Vert sauge  | `#8FBC8F` | Icônes secondaires             |
| Vert foncé  | `#3D6B5C` | Footer, bandeau                |
| Blanc       | `#FFFFFF` | Fond principal                 |

## Pages supplémentaires à créer

Ces pages sont référencées dans la navigation mais pas encore créées :
- `notre-mission.html`
- `aide-accompagnement.html`
- `evenements.html`
- `contact.html`
- `benevole.html`

UPDATE : Ces pages ont bien été crées et mises à jour avec header et footer

Pour chaque nouvelle page : copie la structure de `index.html`
(header + footer), crée un `.css` dédié, et remplace uniquement
la section `<main>` entre le header et le footer.

## Lien de don

Dans `main.js`, la fonction `updateLienDon()` construit l'URL du bouton "Je fais un don".
Remplace `#don-${montantSelectionne}` par l'URL de ta plateforme :
- **HelloAsso** : `https://www.helloasso.com/associations/ton-asso/formulaires/don?montant=...`
- **PayPal** : `https://www.paypal.com/donate/?amount=...`
- **Stripe** : selon ta configuration

## Polices

Le site utilise Google Fonts (chargées automatiquement) :
- **Playfair Display** — titres
- **Lato** — corps de texte
