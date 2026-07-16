<?php

return [

    // Navigation / resource labels
    'nav' => [
        'dashboard' => 'Dashboard',
        'categories' => 'Categories',
        'medicines' => 'Medicines',
        'suppliers' => 'Suppliers',
        'batches' => 'Batches',
        'sales' => 'Sales',
        'users' => 'Users',
        'activity_log' => 'Activity Log',
        'settings' => 'Settings',
        'reports' => 'Reports',
    ],

    'model' => [
        'category' => 'Category',
        'medicine' => 'Medicine',
        'supplier' => 'Supplier',
        'batch' => 'Batch',
        'sale' => 'Sale',
        'user' => 'User',
    ],

    // Category
    'category' => [
        'name' => 'Name',
        'medicines_count' => 'Medicines',
        'created_at' => 'Created at',
        'updated_at' => 'Updated at',
    ],

    // Medicine
    'medicine' => [
        'category' => 'Category',
        'uncategorized' => 'Uncategorized',
        'name' => 'Name',
        'generic_name' => 'Generic name',
        'barcode' => 'Barcode',
        'unit' => 'Unit',
        'selling_price' => 'Selling price',
        'purchase_price' => 'Purchase price',
        'alert_threshold' => 'Alert threshold',
        'stock' => 'Stock',
    ],

    // Supplier
    'supplier' => [
        'name' => 'Name',
        'phone' => 'Phone',
        'wilaya' => 'Wilaya',
        'email' => 'Email address',
        'batches_count' => 'Batches supplied',
    ],

    // Batch
    'batch' => [
        'medicine' => 'Medicine',
        'supplier' => 'Supplier',
        'quantity' => 'Quantity',
        'remaining_quantity' => 'Remaining quantity',
        'purchase_price' => 'Purchase price',
        'expiry_date' => 'Expiry date',
        'stock_status' => 'Stock status',
        'in_stock' => 'In stock',
        'empty' => 'Empty',
        'all' => 'All',
        'remaining_lte_quantity' => 'Remaining quantity cannot be more than the quantity received.',
    ],

    // Sale
    'sale' => [
        'sale_number' => 'Sale #',
        'cashier' => 'Cashier',
        'items' => 'Items',
        'medicine' => 'Medicine',
        'batch' => 'Batch',
        'quantity' => 'Quantity',
        'unit_price' => 'Unit price',
        'subtotal' => 'Subtotal',
        'total' => 'Total',
        'payment_method' => 'Payment method',
        'cash' => 'Cash',
        'card' => 'Card',
        'insurance' => 'Insurance',
        'barcode_scan' => 'Scan barcode',
        'barcode_scan_placeholder' => 'Click here, then scan — or type a barcode and press Enter',
        'no_medicine_for_barcode' => 'No medicine found for that barcode',
        'no_stock_for_medicine' => 'No stock left for :medicine',
        'available_in_batch' => ':count available in this batch.',
    ],

    // User
    'user' => [
        'name' => 'Name',
        'email' => 'Email address',
        'password' => 'Password',
        'password_help' => 'Leave blank to keep the current password when editing.',
        'role' => 'Role',
        'role_admin' => 'Admin',
        'role_pharmacist' => 'Pharmacist',
        'role_cashier' => 'Cashier',
    ],

    // Activity Log
    'activity' => [
        'when' => 'When',
        'sale_item' => 'Sale item',
        'area' => 'Area',
        'event' => 'Event',
        'record_number' => 'Record #',
        'by' => 'By',
        'system' => 'System',
        'created' => 'Created',
        'updated' => 'Updated',
        'deleted' => 'Deleted',
        'before' => 'Before',
        'after' => 'After',
        'what_changed' => 'What changed',
        'details' => 'Details',
        'changed_by' => 'Changed by',
        'model' => 'Model',
    ],

    // Settings
    'settings' => [
        'title' => 'Manage Settings',
        'store_name' => 'Store name',
        'currency' => 'Currency',
        'currency_help' => 'e.g. DZD, USD, EUR',
        'phone' => 'Phone',
        'address' => 'Address',
        'tax_rate' => 'Tax rate (%)',
        'save' => 'Save settings',
        'saved_title' => 'Settings saved',
    ],

    // Reports
    'reports' => [
        'title' => 'Reports',
        'period' => 'Period',
        'daily' => 'Daily',
        'monthly' => 'Monthly',
        'date' => 'Date',
        'generate' => 'Generate PDF',
        'any_day_in_month' => 'Any day in the month',
    ],

    // Shared / actions
    'actions' => [
        'edit' => 'Edit',
        'view' => 'View',
        'delete' => 'Delete',
        'delete_selected' => 'Delete selected',
        'new' => 'New',
        'save' => 'Save',
        'cancel' => 'Cancel',
    ],

];