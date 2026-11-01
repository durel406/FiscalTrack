# Diagramme de classes FiscalTrack

Le diagramme ci-dessous représente les principales classes métier et les relations déclarées dans les modèles Eloquent et les migrations.

```mermaid
classDiagram
    class User {
        +int id
        +string name
        +string email
        +string password
        +string role
        +string identifiant
        +string status
    }

    class Contribuable {
        +int id
        +string nom
        +string nom_raison_sociale
        +string prenom_sigle
        +string niu
        +string regime
        +string cat
        +string statut
        +string organisme
        +string tel
        +obligations()
    }

    class TrackedDocType {
        +int id
        +string nom
        +string periodicite
        +string organisme_defaut
        +statuts()
        +obligations()
    }

    class DeclarationStatut {
        +int id
        +int contribuable_id
        +int tracked_doc_type_id
        +string statut
        +contribuable()
        +trackedDocType()
    }

    class Obligation {
        +int id
        +int contribuable_id
        +int tracked_doc_type_id
        +int annee
        +string periode
        +date date_limite
        +date date_depot
        +string statut
        +string organisme
        +int montant
        +contribuable()
        +trackedDocType()
        +documents()
        +notifications()
    }

    class Document {
        +int id
        +string nom
        +string type
        +string fournisseur
        +int montant
        +int contribuable_id
        +int obligation_id
        +string fichier_path
        +datetime archived_at
        +contribuable()
        +obligation()
    }

    class AppNotification {
        +int id
        +int obligation_id
        +int user_id
        +string type
        +string title
        +string message
        +datetime read_at
        +obligation()
    }

    class DossierSuivi {
        +int id
        +string nom_entreprise
        +int annee
    }

    Contribuable "1" --> "0..*" Obligation : possède
    TrackedDocType "1" --> "0..*" Obligation : définit le type
    Contribuable "1" --> "0..*" DeclarationStatut : statuts
    TrackedDocType "1" --> "0..*" DeclarationStatut : type suivi
    Contribuable "0..1" --> "0..*" Document : associé à
    Obligation "0..1" --> "0..*" Document : justificatifs
    Obligation "0..1" --> "0..*" AppNotification : déclenche
    User "0..1" ..> "0..*" AppNotification : user_id nullable, sans relation Eloquent
```

## Remarques sur le modèle actuel

- `DeclarationStatut` associe un contribuable à un type de document suivi. La base impose un seul statut par couple contribuable/type.
- Une obligation est unique pour un contribuable, un type, une année et une période.
- `Document.contribuable_id` et `Document.obligation_id` sont nullable; ils peuvent être absents.
- `AppNotification.user_id` existe en base, mais aucune clé étrangère ni relation Eloquent vers `User` n'est définie.
- `DossierSuivi` n'a actuellement aucune relation déclarée avec les autres classes.
- Les attributs affichés sont les principaux attributs métier; les champs d'horodatage Laravel (`created_at`, `updated_at`) sont omis pour garder le schéma lisible.