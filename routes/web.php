<?php

use App\Http\Controllers\CakeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\FlavourController;
use App\Http\Controllers\OccasionReminderController;
use App\Http\Controllers\OrderRequestController;
use App\Http\Controllers\SizeController;
use App\Http\Controllers\Storefront\StoreController;
use App\Http\Controllers\AccountRoleController;
use App\Http\Controllers\Auth\OTPController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\CommunicationController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\FinanceDashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SmsTemplateController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserPermissionController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Auth and Social Login routes
// Public registration is disabled; create admins with: php artisan cakecraft:create-admin
Auth::routes(['register' => false]);

// ---------------------------------------------------------------------------
// Public storefront - the website customers see at "/".
// Staff sign in at /login and work in the admin at /dashboard.
// Set CAKECRAFT_STORE_DOMAIN to serve it only on a specific domain (e.g. www.cakecraft.co.ke).
// ---------------------------------------------------------------------------
$storeRoutes = function () {
    Route::get('/', [StoreController::class, 'home'])->name('store.home');
    Route::get('/shop', [StoreController::class, 'shop'])->name('store.shop');
    Route::get('/price-list', [StoreController::class, 'menu'])->name('store.menu');
    Route::get('/collection/{slug}', [StoreController::class, 'collection'])->name('store.collection');
    Route::get('/cake/{slug}', [StoreController::class, 'cake'])->name('store.cake');
    Route::post('/order', [StoreController::class, 'order'])->middleware('throttle:10,1')->name('store.order');
    Route::get('/thank-you/{reference}', [StoreController::class, 'thanks'])->name('store.thanks');
};
if (config('cakecraft.store_domain')) {
    Route::domain(config('cakecraft.store_domain'))->group($storeRoutes);
} else {
    Route::group([], $storeRoutes);
}

Route::get('/session/check', function () {
    return response()->json(['authenticated' => Auth::check()]);
});

Route::get('/session/refresh', function () {
    // Extend session expiration by resetting last activity time
    $lifetime = config('session.lifetime');
    session()->put('last_activity', now()->timestamp);  // Store last activity

    session()->regenerate(); // Optionally regenerate the session

    return response()->json(['success' => true]);
});

Route::get('/refresh-csrf', function () {
    return response()->json(['token' => csrf_token()]);
});

// Language Translation
Route::get('index/{locale}', [HomeController::class, 'lang']);

// Public Invoice PDF
Route::get('/invoice/{id}/download-pdf', [InvoiceController::class, 'publicPdf'])->name('invoices.public_pdf')->middleware('signed');

// Public Payment Page Route
Route::get('/invoice/{id}/pay', [InvoiceController::class, 'publicPay'])->name('invoices.public_pay')->middleware('signed');
Route::post('/invoice/check-transaction-status', [PaymentController::class, 'checkTransactionStatus'])->name('payments.public_status');
Route::post('/invoice/payments/top-up/mpesa', [PaymentController::class, 'mpesaTopUp'])->name('payments.public_topup');
Route::get('/payments/{id}/public-receipt', [PaymentController::class, 'publicReceipt'])->name('payments.public_receipt')->middleware('signed');

Route::middleware(['auth'])->group(function () {
    // OTP Routes
    Route::get('otp/verify', [OTPController::class, 'showVerifyForm'])->name('otp.verify');
    Route::get('otp/auth-app/verify', [OTPController::class, 'showAuthApp'])->name('otp.auth.verify');
    Route::post('otp/verify', [OTPController::class, 'verify'])->name('otp.verify.post');
    Route::post('/otp-resend', [OTPController::class, 'resend'])->name('otp.resend');
});
Route::get('/payment/callback', [PaymentController::class, 'handleGatewayCallback'])->name('payments.paystack.callback');
// Access these routes only when you have enabled OTP
Route::middleware(['auth', 'otp_verified', 'check.active', 'verified.otp'])->group(function () {

    // Dashboard
    Route::middleware(['permission:view dashboard'])->get('/dashboard', [DashboardController::class, 'index'])->name('root');
    Route::middleware(['permission:view financials'])->get('finance-dashboard', [FinanceDashboardController::class, 'index'])->name('finance.dashboard');

    // Branch Management
    Route::middleware(['permission:update branches'])->group(function () {
        Route::resource('branches', BranchController::class);
    });

    // User Permissions
    Route::middleware(['permission:update users'])->group(function () {
        Route::get('/users/{user}/permissions', [UserPermissionController::class, 'edit'])->name('users.permissions.edit');
        Route::post('/users/{user}/permissions', [UserPermissionController::class, 'update'])->name('users.permissions.update');
        Route::get('/permissions/default', [UserPermissionController::class, 'getDefaultPermissions']);
    });
    // Route::middleware(['permission:create users'])->get('/roles', [AccountRoleController::class, 'index'])->name('roles.index');

    Route::middleware(['permission:view roles'])->get('/roles', [AccountRoleController::class, 'index'])->name('roles.index');
    Route::middleware(['permission:create roles'])->post('/roles', [AccountRoleController::class, 'store'])->name('roles.store');
    Route::middleware(['permission:update roles'])->put('/roles/{role}', [AccountRoleController::class, 'update'])->name('roles.update');
    Route::middleware(['permission:delete roles'])->delete('/roles/{role}', [AccountRoleController::class, 'destroy'])->name('roles.destroy');

    // Customers & stock
    Route::get('customers/template', [CustomerController::class, 'downloadTemplate'])->middleware('permission:create customers')->name('customers.template');
    Route::post('customers/import', [CustomerController::class, 'import'])->middleware('permission:create customers')->name('customers.import');
    Route::get('customers/export', [CustomerController::class, 'export'])->middleware('permission:view customers')->name('customers.export');
    Route::get('customers', [CustomerController::class, 'index'])->middleware('permission:view customers')->name('customers.index');
    Route::post('customers', [CustomerController::class, 'store'])->middleware('permission:create customers')->name('customers.store');
    Route::get('customers/{customer}/edit', [CustomerController::class, 'edit'])->middleware('permission:update customers')->name('customers.edit');
    Route::match(['put', 'patch'], 'customers/{customer}', [CustomerController::class, 'update'])->middleware('permission:update customers')->name('customers.update');
    Route::delete('customers/{customer}', [CustomerController::class, 'destroy'])->middleware('permission:delete customers')->name('customers.destroy');

    Route::get('products/export', [ProductController::class, 'export'])->name('products.export');
    Route::post('products/{id}/stock', [ProductController::class, 'adjustStock'])->name('products.stock');
    // Legacy products screen now lives under Cakes
    Route::get('products', fn () => redirect()->route('cakes.index'))->name('products.index');

    // Cake catalogue
    Route::post('cakes/{id}/toggle', [CakeController::class, 'toggle'])->name('cakes.toggle');
    Route::resource('cakes', CakeController::class)->except(['show']);
    Route::post('collections/reorder', [CollectionController::class, 'reorder'])->name('collections.reorder');
    Route::resource('collections', CollectionController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::post('flavours/reorder', [FlavourController::class, 'reorder'])->name('flavours.reorder');
    Route::resource('flavours', FlavourController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::post('delivery-zones/reorder', [\App\Http\Controllers\DeliveryZoneController::class, 'reorder'])->name('zones.reorder');
    Route::resource('delivery-zones', \App\Http\Controllers\DeliveryZoneController::class)->only(['index', 'store', 'update', 'destroy'])->parameters(['delivery-zones' => 'zone'])->names(['index' => 'zones.index', 'store' => 'zones.store', 'update' => 'zones.update', 'destroy' => 'zones.destroy']);
    Route::post('sizes/reorder', [SizeController::class, 'reorder'])->name('sizes.reorder');
    Route::resource('sizes', SizeController::class)->only(['index', 'store', 'update', 'destroy']);

    // Orders placed on the website
    Route::get('web-orders', [OrderRequestController::class, 'index'])->name('order-requests.index');
    Route::put('web-orders/{id}/status', [OrderRequestController::class, 'updateStatus'])->name('order-requests.status');
    Route::post('web-orders/{id}/convert', [OrderRequestController::class, 'convert'])->name('order-requests.convert');

    // Birthday / anniversary reminders
    Route::get('occasions', [OccasionReminderController::class, 'index'])->name('occasions.index');
    Route::post('occasions', [OccasionReminderController::class, 'store'])->name('occasions.store');
    Route::post('occasions/settings', [OccasionReminderController::class, 'saveSettings'])->name('occasions.settings');
    Route::post('occasions/send-bulk', [OccasionReminderController::class, 'sendBulk'])->name('occasions.send-bulk');
    Route::post('occasions/{id}/send', [OccasionReminderController::class, 'send'])->name('occasions.send');
    Route::post('occasions/{id}/toggle', [OccasionReminderController::class, 'toggle'])->name('occasions.toggle');

    // Expenses, suppliers and categories are finance data
    Route::middleware(['permission:view financials'])->group(function () {
        Route::get('expenses-export', [ExpenseController::class, 'export'])->name('expenses.export');
        // Expense Categories Import/Export
        Route::post('expense-categories/import', [\App\Http\Controllers\ExpenseCategoryController::class, 'import'])->name('expense-categories.import');
        Route::get('expense-categories-export', [\App\Http\Controllers\ExpenseCategoryController::class, 'export'])->name('expense-categories.export');
        Route::get('expense-categories/template', [\App\Http\Controllers\ExpenseCategoryController::class, 'template'])->name('expense-categories.template');

        // Suppliers Import/Export
        Route::post('suppliers/import', [\App\Http\Controllers\SupplierController::class, 'import'])->name('suppliers.import');
        Route::get('suppliers-export', [\App\Http\Controllers\SupplierController::class, 'export'])->name('suppliers.export');
        Route::get('suppliers/template', [\App\Http\Controllers\SupplierController::class, 'template'])->name('suppliers.template');

        // Expenses Import
        Route::post('expenses/import', [\App\Http\Controllers\ExpenseController::class, 'import'])->name('expenses.import');
        Route::get('expenses/template', [\App\Http\Controllers\ExpenseController::class, 'template'])->name('expenses.template');

        Route::resource('expense-categories', ExpenseCategoryController::class)->except(['create', 'show']);
        Route::resource('suppliers', SupplierController::class)->except(['create', 'show']);
        Route::resource('expenses', ExpenseController::class)->except(['create', 'show']);
    });

    Route::get('invoices/export', [InvoiceController::class, 'export'])->name('invoices.export');
    Route::get('invoices/{id}/pdf', [InvoiceController::class, 'downloadPdf'])->name('invoices.pdf');
    Route::put('invoices/{id}/deliver', [InvoiceController::class, 'markAsDelivered'])->name('invoices.deliver');
    Route::post('invoices/{id}/update-status', [InvoiceController::class, 'updateStatus'])->name('invoices.update_status');
    Route::post('/invoices/{id}/send-reminder', [InvoiceController::class, 'sendReminder'])->name('invoices.send_reminder');
    Route::post('/invoices/{id}/transfer-overpayment', [PaymentController::class, 'transferToWallet']);
    Route::resource('invoices', InvoiceController::class);

    // Quotations
    Route::post('quotations/{id}/convert', [QuotationController::class, 'convertToInvoice'])->middleware('permission:create invoices')->name('quotations.convert');
    Route::get('quotations/{id}/pdf', [QuotationController::class, 'downloadPdf'])->middleware('permission:view financials')->name('quotations.pdf');
    Route::get('quotations', [QuotationController::class, 'index'])->middleware('permission:view financials')->name('quotations.index');
    Route::get('quotations/create', [QuotationController::class, 'create'])->middleware('permission:create invoices')->name('quotations.create');
    Route::post('quotations', [QuotationController::class, 'store'])->middleware('permission:create invoices')->name('quotations.store');
    Route::get('quotations/{quotation}', [QuotationController::class, 'show'])->middleware('permission:view financials')->name('quotations.show');
    Route::get('quotations/{quotation}/edit', [QuotationController::class, 'edit'])->middleware('permission:update invoices')->name('quotations.edit');
    Route::match(['put', 'patch'], 'quotations/{quotation}', [QuotationController::class, 'update'])->middleware('permission:update invoices')->name('quotations.update');
    Route::delete('quotations/{quotation}', [QuotationController::class, 'destroy'])->middleware('permission:delete invoices')->name('quotations.destroy');

    Route::get('branding', [\App\Http\Controllers\BrandingController::class, 'edit'])->middleware('permission:update settings')->name('branding.edit');
    Route::post('branding', [\App\Http\Controllers\BrandingController::class, 'update'])->middleware('permission:update settings')->name('branding.update');
    Route::get('sms-templates', [SmsTemplateController::class, 'index'])->middleware('permission:view settings')->name('sms-templates.index');
    Route::post('sms-templates', [SmsTemplateController::class, 'store'])->middleware('permission:update settings')->name('sms-templates.store');
    Route::get('/reports', [ReportController::class, 'index'])->middleware('permission:view reports')->name('reports.index');
    Route::get('/reports/receivables', [\App\Http\Controllers\ReceivablesController::class, 'index'])->name('reports.receivables');
    Route::post('/reports/receivables/{customer}/remind', [\App\Http\Controllers\ReceivablesController::class, 'remind'])->name('reports.receivables.remind');

    // Customer Statements
    Route::get('statements', [\App\Http\Controllers\StatementController::class, 'index'])->middleware('permission:view financials')->name('statements.index');
    Route::get('statements/pdf', [\App\Http\Controllers\StatementController::class, 'downloadPdf'])->middleware('permission:view financials')->name('statements.pdf');

    // Profile Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::middleware(['permission:create users'])->get('/settings/fetch-users', [SettingsController::class, 'fetchUsers'])->name('fetchUsers');
    Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::post('/update-profile/{id}', [SettingsController::class, 'updateProfile'])->name('updateProfile');
    Route::post('/update-password/{id}', [SettingsController::class, 'updatePassword'])->name('updatePassword');
    Route::post('/update-account-details/{id}', [SettingsController::class, 'updateAccountDetails'])->name('updateAccountDetails');
    Route::middleware(['permission:update users'])->post('/update-user-status/{id}', [SettingsController::class, 'updateUserStatus'])->name('updateUserStatus');
    Route::middleware(['permission:update users'])->post('/update-user-details/{id}', [SettingsController::class, 'updateUserDetails'])->name('updateUserDetails');
    Route::middleware(['permission:create users'])->post('/add-user', [SettingsController::class, 'addUser'])->name('addUser');
    Route::middleware(['permission:delete users'])->post('/delete-user/{id}', [SettingsController::class, 'deleteUser'])->name('deleteUser');

    Route::post('/account/update-avatar', [SettingsController::class, 'updateAvatar'])->name('account.update.avatar');
    Route::post('/account/update-cover', [SettingsController::class, 'updateCover'])->name('account.update.cover');




    // Accounts



    // Payments
    Route::middleware(['permission:create payments'])->post('payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::get('payments/{id}/edit', [PaymentController::class, 'edit'])->name('payments.edit');
    Route::put('payments/{id}', [PaymentController::class, 'update'])->name('payments.update');
    Route::delete('payments/{id}', [PaymentController::class, 'destroy'])->name('payments.destroy');
    Route::get('payments/{id}/receipt', [PaymentController::class, 'downloadReceipt'])->name('payments.receipt');
    Route::get('payments/export', [PaymentController::class, 'export'])->name('payments.export');

    Route::middleware(['permission:create payments'])->get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::middleware(['permission:view payments'])->get('/payments/invoice/{id}', [PaymentController::class, 'showInvoice'])->name('payments.showInvoice');
    Route::middleware(['permission:view payments'])->get('/payments/invoice/download/{id}', [PaymentController::class, 'downloadInvoicePDF'])->name('payments.downloadInvoicePDF');
    Route::middleware(['permission:create payments'])->get('/payments/top-up', [PaymentController::class, 'topUp'])->name('payments.topup');
    Route::middleware(['permission:create payments'])->post('/payments/top-up/mpesa', [PaymentController::class, 'mpesaTopUp'])->name('payments.topup.mpesa');
    Route::middleware(['permission:view payments'])->get('/payments/history', [PaymentController::class, 'billingHistory'])->name('payments.history');
    Route::middleware(['permission:view payments'])->get('/billing/history', [PaymentController::class, 'ledger'])->name('payments.ledger');
    Route::middleware(['permission:view payments'])->post('check-transaction-status', [PaymentController::class, 'checkTransactionStatus'])->name('payments.transactionStatus');

    Route::middleware(['permission:create payments'])->post('/card-payment', [PaymentController::class, 'redirectToGateway'])->name('payments.topup.card');
    Route::post('payments/buni-stk-push', [PaymentController::class, 'initiateBuniStk'])->name('payments.buni.initiate');

    // Templates
    // Route::middleware(['permission:create templates'])->get('/templates', [TemplateController::class, 'index'])->name('templates.index');


    // AI Model

    // Notifications
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('notifications.index');
        Route::get('/latest', [NotificationController::class, 'getLatestNotifications'])->name('notifications.latest');
        Route::get('/unread-count', [NotificationController::class, 'unreadCount']);
        Route::post('/{notification}/mark-as-read', [NotificationController::class, 'markAsRead']);
        Route::post('/mark-all-read', [NotificationController::class, 'markAllRead']);
        Route::delete('/{notification}', [NotificationController::class, 'destroy']);
        Route::post('/destroy-multiple', [NotificationController::class, 'destroyMultiple']);
    });

    Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {

        Route::get('/communication', [CommunicationController::class, 'index'])->name('communication.index');
        Route::post('/communication/preview', [CommunicationController::class, 'preview'])->name('communication.preview');
        Route::post('/communication/send', [CommunicationController::class, 'send'])->name('communication.send');
        Route::post('/communication/preview-count', [CommunicationController::class, 'previewCount'])->name('communication.previewCount');
    });
});

// Route::get('/error-401', function () {
//     return view('error.custom-401'); // Custom 401 view
// })->name('error.401');

// Route::get('/error-403', function () {
//     return view('error.custom-403'); // Custom 403 view
// })->name('error.403');

// Route::get('/error-404', function () {
//     return view('error.custom-404'); // Custom 404 view
// })->name('error.404');

// Route::get('/error-419', function () {
//     return view('error.custom-419'); // Custom 419 view
// })->name('error.419');

// Route::get('/error-500', function () {
//     return view('error.custom-500'); // Custom 500 view
// })->name('error.500');

// // Fallback Routes
// Route::fallback(function () {
//     return response()->view('error.custom-404', [], 404);
// });
// Route::fallback(function () {
//     return response()->view('error.custom-419', [], 419);
// });

// Route::get('{any}', [App\Http\Controllers\HomeController::class, 'home'])->name('index');
