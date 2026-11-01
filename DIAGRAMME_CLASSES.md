# Diagramme de classes FiscalTrack

Ce diagramme représente les classes métier Laravel d'après les modèles, services et migrations présents dans le projet. Les attributs `id`, `created_at` et `updated_at` sont hérités ou générés par Eloquent et les migrations; ils ne sont pas répétés dans chaque classe.

```mermaid
classDiagram
    direction LR

    class Contribuable {
        +string nom
        +string nom_raison_sociale
        +string prenom_sigle
        +string niu
        +string activite_principale
        +string regime
        +string cat
        +string centre_rattachement
        +string ville
        +string quartier
        +string lieux_dit
        +string statut
        +string pass
        +int montant
        +int t1
        +int t2
        +int t3
        +int t4
        +int tdl
        +int impots
        +int loyer
        +int bail
        +int precompte
        +int timbre
        +int frais_paiement
        +int fs_paye
        +int fs_non_paye
        +date ai_igs
        +date ai_bail
        +date ai_precompte
        +date q_igs
        +date q_bail
        +date q_precompte
        +date acf_igs
        +date acf_bail
        +date acf_precompte
        +string lieu
        +string tel
        +string organisme
        +string lien_verification
        +obligations() HasMany
        +setPassAttribute(value) void
        +getPassAttribute(value) string
    }

    class Obligation {
        +int contribuable_id
        +int tracked_doc_type_id
        +int annee
        +string periode
        +date date_limite
        +date date_depot
        +string statut
        +string organisme
        +int montant
        +string resultat_controle
        +string commentaire_controle
        +date date_controle
        +STATUT_A_DECLARER
        +STATUT_DECLARE
        +STATUT_JUSTIFICATIF
        +contribuable() BelongsTo
        +trackedDocType() BelongsTo
        +documents() HasMany
        +notifications() HasMany
        +hasJustificatif() bool
        +isCloturee() bool
        +effectiveStatut() string
        +joursAvantEcheance() int
        +suggestDeadline(annee, periode, periodicite)$ string
        +toFront() array
        +periodeLabel(periode)$ string
        +echeanceBucket(jours, cloturee)$ string
    }

    class TrackedDocType {
        +string nom
        +string periodicite
        +string organisme_defaut
        +statuts() HasMany
        +obligations() HasMany
        +toFront() array
    }

    class DeclarationStatut {
        +int contribuable_id
        +int tracked_doc_type_id
        +string statut
        +contribuable() BelongsTo
        +trackedDocType() BelongsTo
    }

    class Document {
        +string nom
        +string type
        +string fournisseur
        +int montant
        +int contribuable_id
        +int obligation_id
        +string fichier_path
        +string fichier_nom
        +string fichier_mime
        +datetime archived_at
        +contribuable() BelongsTo
        +obligation() BelongsTo
        +scopeActifs(query)$ Query
        +scopeArchives(query)$ Query
        +toFront() array
    }

    class AppNotification {
        +int obligation_id
        +int user_id
        +string type
        +string title
        +string message
        +string dedupe_key
        +datetime read_at
        +obligation() BelongsTo
        +scopeUnread(query)$ Query
        +toFront() array
    }

    class User {
        +string name
        +string email
        +datetime email_verified_at
        +string password
        +string remember_token
        +string role
        +string status
        +string identifiant
    }

    class DossierSuivi {
        +string nom_entreprise
        +int annee
    }

    class FiscalSuiviService {
        +kpis() array
        +syncNotifications() int
        +listObligationsForFront() Collection
        +listNotificationsForFront() Collection
        +mirrorDeclarationStatut(obligation) void
    }

    class DashboardController {
        +index() View
    }

    class SyncEcheanceNotifications {
        +handle(suivi) int
    }

    Contribuable "1" --> "0..*" Obligation : possède
    TrackedDocType "1" --> "0..*" Obligation : définit le type
    Contribuable "1" --> "0..*" DeclarationStatut : statuts historiques
    TrackedDocType "1" --> "0..*" DeclarationStatut : type suivi
    Contribuable "0..1" <-- "0..*" Document : documents
    Obligation "0..1" <-- "0..*" Document : justificatifs
    Obligation "0..1" <-- "0..*" AppNotification : échéances
    FiscalSuiviService ..> Obligation : calcule le suivi
    FiscalSuiviService ..> Document : compte les pièces
    FiscalSuiviService ..> AppNotification : synchronise
    FiscalSuiviService ..> DeclarationStatut : maintient le miroir
    DashboardController ..> FiscalSuiviService : indicateurs du tableau de bord
    SyncEcheanceNotifications ..> FiscalSuiviService : synchronisation planifiée
    User .. AppNotification : user_id nullable, relation Eloquent absente
    DossierSuivi .. Contribuable : aucune relation déclarée
```

## Précisions sur le modèle

- `Contribuable` contient des données d'identification, de contact, de régime fiscal et de suivi de montants/dates. `pass` est chiffré par les accesseurs du modèle.
- `Obligation` est l'entité centrale du suivi par contribuable, type de document, année et période. Son statut effectif et ses échéances sont calculés par ses méthodes.
- `DeclarationStatut` sert de miroir de compatibilité pour l'ancienne matrice de statuts; `FiscalSuiviService::mirrorDeclarationStatut()` l'actualise depuis une obligation.
- `Document` peut être associé à un contribuable et/ou à une obligation. `archived_at` distingue les documents archivés des documents actifs.
- La table `app_notifications` possède un champ nullable `user_id`, mais `AppNotification` ne déclare pas de relation Eloquent vers `User` et la migration ne pose pas de clé étrangère correspondante. Le lien en pointillés signale cette limite du code actuel.
- `DossierSuivi` possède une table et un modèle, mais aucune relation ni méthode métier n'est déclarée dans le modèle actuel.
- `User` hérite des fonctions d'authentification Laravel; seules ses propriétés applicatives sont listées ici.