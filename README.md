# Village des Femmes — Site Web

## Structure des fichiers

```
village-des-femmes/
├── index.html               ← Page Accueil
├── index.css                ← Styles spécifiques à l'accueil
├── nous-soutenir.html       ← Page Nous Soutenir
├── nous-soutenir.css        ← Styles spécifiques à Nous Soutenir
├── aide-accompagnement.html ← Page aide & accompagnement
├── aide-accompagnement.css  ←  Styles spé à la page aide & accompagnement
├── notre-mission.html       ←  Page Notre Mission
├── nore-mission.css         ← Styles spé à la page notre mission
├── contact.html             ←  Page Contact
├── contact.css              ←  Styles spé à la page contact 
├── evenements.html          ←  Page evenements
├── evenements.css           ←  Styles spé à la page événements
├── styles.css               ← Styles communs (header, footer, boutons…)
├── main.js                  ← JavaScript commun
└── images/                  ← Dossier à créer avec tes images
    ├── logo.png

    Les images sont à rajouter encore, il n'y a pour l'instant que le logo 


## Ajouter des images png idéalement pour les icônes. Photos en jpeg ok 

## Palette de couleurs 

| Nom         | Code hex  | 
|-------------|-----------|
  --rose-pale:    #F9D6D8; 
  --rose-vif:     #E75480;
  --rose-bouton:  #C8455A;
  --vert-sauge:   #8FBC8F;
  --vert-fonce:   #3D6B5C;
  --vert-btn:     #4A8A75;
  --noir:         #1a1a1a;
  --blanc:        #FFFFFF;
  --gris-clair:   #F5F5F5;
  --texte:        #2C2C2C;
  --texte-clair:  #666666;

## Pages supplémentaires à créer

UPDATE: toutesles pages sont crées, et remplies

## Lien de don

Dans `main.js`, la fonction `updateLienDon()` construit l'URL du bouton "Je fais un don".
Remplace `#don-${montantSelectionne}` par l'URL de ta plateforme :
- **HelloAsso** : `https://www.helloasso.com/associations/ton-asso/formulaires/don?montant=...`
- **PayPal** : `https://www.paypal.com/donate/?amount=...`
- **Stripe** : selon ta configuration
A FAIRE 
compte asso à creer, il n'y a encore aucun compte de con pour l'asso

## Polices

Le site utilise Google Fonts:
- **Playfair Display** — titres
- **Lato** — corps de texte

14/06/26 CSS changé, body ajouté aux pages "notre mission" "aide et accompagnement""evenemensts""contact"
15/06/26 Modif du read me et du texte de certaines pages html 