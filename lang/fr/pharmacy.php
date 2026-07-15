<?php

return [

    'nav' => [
        'dashboard' => 'Tableau de bord',
        'categories' => 'Catégories',
        'medicines' => 'Médicaments',
        'suppliers' => 'Fournisseurs',
        'batches' => 'Lots',
        'sales' => 'Ventes',
        'users' => 'Utilisateurs',
        'activity_log' => 'Journal d\'activité',
        'settings' => 'Paramètres',
        'reports' => 'Rapports',
    ],

    'model' => [
        'category' => 'Catégorie',
        'medicine' => 'Médicament',
        'supplier' => 'Fournisseur',
        'batch' => 'Lot',
        'sale' => 'Vente',
        'user' => 'Utilisateur',
    ],

    'category' => [
        'name' => 'Nom',
        'medicines_count' => 'Médicaments',
        'created_at' => 'Créé le',
        'updated_at' => 'Modifié le',
    ],

    'medicine' => [
        'category' => 'Catégorie',
        'uncategorized' => 'Sans catégorie',
        'name' => 'Nom',
        'generic_name' => 'Nom générique',
        'barcode' => 'Code-barres',
        'unit' => 'Unité',
        'selling_price' => 'Prix de vente',
        'purchase_price' => 'Prix d\'achat',
        'alert_threshold' => 'Seuil d\'alerte',
        'stock' => 'Stock',
    ],

    'supplier' => [
        'name' => 'Nom',
        'phone' => 'Téléphone',
        'wilaya' => 'Wilaya',
        'email' => 'Adresse e-mail',
        'batches_count' => 'Lots fournis',
    ],

    'batch' => [
        'medicine' => 'Médicament',
        'supplier' => 'Fournisseur',
        'quantity' => 'Quantité',
        'remaining_quantity' => 'Quantité restante',
        'purchase_price' => 'Prix d\'achat',
        'expiry_date' => 'Date d\'expiration',
        'stock_status' => 'État du stock',
        'in_stock' => 'En stock',
        'empty' => 'Épuisé',
        'all' => 'Tous',
    ],

    'sale' => [
        'sale_number' => 'Vente n°',
        'cashier' => 'Caissier',
        'items' => 'Articles',
        'medicine' => 'Médicament',
        'batch' => 'Lot',
        'quantity' => 'Quantité',
        'unit_price' => 'Prix unitaire',
        'subtotal' => 'Sous-total',
        'total' => 'Total',
        'payment_method' => 'Mode de paiement',
        'cash' => 'Espèces',
        'card' => 'Carte',
        'insurance' => 'Assurance',
        'barcode_scan' => 'Scanner le code-barres',
    ],

    'user' => [
        'name' => 'Nom',
        'email' => 'Adresse e-mail',
        'password' => 'Mot de passe',
        'password_help' => 'Laisser vide pour conserver le mot de passe actuel lors de la modification.',
        'role' => 'Rôle',
    ],

    'activity' => [
        'when' => 'Quand',
        'area' => 'Domaine',
        'event' => 'Événement',
        'record_number' => 'Enregistrement n°',
        'by' => 'Par',
        'system' => 'Système',
        'created' => 'Créé',
        'updated' => 'Modifié',
        'deleted' => 'Supprimé',
        'before' => 'Avant',
        'after' => 'Après',
        'what_changed' => 'Ce qui a changé',
        'details' => 'Détails',
        'changed_by' => 'Modifié par',
        'model' => 'Modèle',
    ],

    'settings' => [
        'title' => 'Gérer les paramètres',
        'store_name' => 'Nom du magasin',
        'currency' => 'Devise',
        'currency_help' => 'ex. DZD, USD, EUR',
        'phone' => 'Téléphone',
        'address' => 'Adresse',
        'tax_rate' => 'Taux de taxe (%)',
        'save' => 'Enregistrer les paramètres',
        'saved_title' => 'Paramètres enregistrés',
    ],

    'reports' => [
        'title' => 'Rapports',
        'period' => 'Période',
        'daily' => 'Quotidien',
        'monthly' => 'Mensuel',
        'date' => 'Date',
        'generate' => 'Générer le PDF',
    ],

    'actions' => [
        'edit' => 'Modifier',
        'view' => 'Voir',
        'delete' => 'Supprimer',
        'delete_selected' => 'Supprimer la sélection',
        'new' => 'Nouveau',
        'save' => 'Enregistrer',
        'cancel' => 'Annuler',
    ],

];
