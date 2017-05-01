# ProjetWeb
Sasha Collomé & Axel Rostagny  
Solution de développement retenue : Prestashop  
Support de développement : Mac  
### Répartition des tâches :    
Sasha => Développeur & intégrateur prestashop sur Mac  
Axel => Gestion du Git, documentation & recherche de contenu pour le site  

#### Utilisation
Lancer serveur PHP, ouvrir "mon site web", choix de vue partie admin ou partie client simple

## Avancement des fonctionnalitées demandées
#### Clients
- Client pour les utilisteurs
  - Consulter des produits (listes, page produit...) ✓
  - Rechercher de produits / catégories ✓ 
  - Créer un compte ✓
  - Ajouter des produits à mon panier ✓
  - Passer une commande (**pas de gestion du paiement**, mais envoie d'un email de confirmation) ✓
  - Consulter l'état de mes commandes ✓
  - Modification des informations personnelles (adresse, numéro de téléphone...) ✓
  - Donner une note et écrire un commentaire sur un produit - Not Yet Implemented  
  
- Client pour l'administration
  - Se connecter au _back end_ avec autorisation seulement pour les administrateurs ✓
  - Créer des produits (nom, description, catégorie, image, prix, quantité...etc.) ✓  
  - Faire avancer les étapes de commande (demandée > confirmée > expédiée > livrée) ✓
  - Modérer les notes / commentaires  
  - Créer des administrateurs ✓
  
Les clients doivent être fonctionnels sur les principaux navigateurs récents du marché : 

- Firefox 51 et supérieurs
- Chrome 56 et supérieurs
- Edge 14 et supérieurs
- Internet Explorer 11
- Safari 10 et supérieurs  ✓

Ceux-ci doivent être dynamiques, c'est à dire que certaines actions devront être réalisables sans rechargement de la page.  ✓

#### Serveur
- Apache ou Nginx pour gérer les requêtes entrantes, les URLs, le cache...  ✓
- Langage de scripting
- Système de gestion de base de données
- Authentification (Session ou OAuth)  ✓
- API permettant d'intéragir avec la base de données (à utiliser pour _au moins_ un des 2 clients)
