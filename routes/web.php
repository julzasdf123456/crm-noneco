<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\ServiceConnectionsController;
use App\Http\Controllers\MemberConsumersController;
use App\Http\Controllers\PaidBillsController;

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

Auth::routes();

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/home/get-unassigned-meters', [HomeController::class, 'fetchUnassignedMeters'])->name('home.get-unassigned-meters');
Route::get('/home/get-new-service-connections', [HomeController::class, 'fetchNewServiceConnections'])->name('home.get-new-service-connections');
Route::get('/home/get-approved-service-connections', [HomeController::class, 'fetchApprovedServiceConnections'])->name('home.get-approved-service-connections');
Route::get('/home/get-for-engergization', [HomeController::class, 'fetchForEnergization'])->name('home.get-for-engergization');
Route::get('/home/get-inspection-report', [HomeController::class, 'fetchInspectionReport'])->name('home.get-inspection-report');
Route::get('/home/get-inspection-large-load', [HomeController::class, 'fetchInspectionLargeLoad'])->name('home.get-inspection-large-load');
Route::get('/home/get-bom-large-load', [HomeController::class, 'fetchBomLargeLoad'])->name('home.get-bom-large-load');
Route::get('/home/get-transformer-large-load', [HomeController::class, 'fetchTransformerLargeLoad'])->name('home.get-transformer-large-load');
Route::get('/home/dash-get-collection-summary', [HomeController::class, 'dashGetCollectionSummary'])->name('home.dash-get-collection-summary');
Route::get('/home/dash-get-collection-summary-graph', [HomeController::class, 'dashGetCollectionSummaryGraph'])->name('home.dash-get-collection-summary-graph');

// ADD PERMISSIONS TO ROLES
Route::get('/roles/add-permissions/{id}', [RoleController::class, 'addPermissions'])->name('roles.add_permissions');
Route::post('/roles/create-role-permissions', [RoleController::class, 'createRolePermissions']);

// ADD ROLES TO USER
Route::get('/users/add-user-roles/{id}', [UsersController::class, 'addUserRoles'])->name('users.add_user_roles');
Route::post('/users/create-user-roles', [UsersController::class, 'createUserRoles']);
Route::get('/users/add-user-permissions/{id}', [UsersController::class, 'addUserPermissions'])->name('users.add_user_permissions');
Route::post('/users/create-user-permissions', [UsersController::class, 'createUserPermissions']);
Route::get('/users/remove-permission/{id}/{permission}', [UsersController::class, 'removePermission'])->name('users.remove_permission');
Route::get('/users/remove-roles/{id}', [UsersController::class, 'clearRoles'])->name('users.remove_roles');

Route::post('/users/authenticate', [UsersController::class, 'authenticate'])->name('users.authenticate');
Route::resource('users', UsersController::class);

Route::resource('roles', App\Http\Controllers\RoleController::class);

Route::resource('permissions', App\Http\Controllers\PermissionController::class);


Route::get('/member_consumers/assess_checklists/{id}', [MemberConsumersController::class, 'assessChecklists'])->name('memberConsumers.assess-checklists');
Route::get('/member_consumers/fetchmemberconsumer', [MemberConsumersController::class, 'fetchmemberconsumer'])->name('memberConsumers.fetch-member-consumers');
Route::get('/member_consumers/capture-image/{id}', [MemberConsumersController::class, 'captureImage'])->name('memberConsumers.capture-image');
Route::get('/member_consumers/print-membership-application/{id}', [MemberConsumersController::class, 'printMembershipApplication'])->name('memberConsumers.print-membership-application');
Route::get('/member_consumers/print-certificate/{id}', [MemberConsumersController::class, 'printCertificate'])->name('memberConsumers.print-certificate');
Route::resource('memberConsumers', MemberConsumersController::class);


Route::resource('memberConsumerTypes', App\Http\Controllers\MemberConsumerTypesController::class);


Route::resource('towns', App\Http\Controllers\TownsController::class);


Route::resource('barangays', App\Http\Controllers\BarangaysController::class);
Route::get('/barangays/get-barangays-json/{townId}', [App\Http\Controllers\BarangaysController::class, 'getBarangaysJSON']);

Route::get('/member_consumer_spouses/create/{consumerId}', [App\Http\Controllers\MemberConsumerSpouseController::class, 'create'])->name('memberConsumerSpouses.create');
Route::get('/member_consumer_spouses/index', [App\Http\Controllers\MemberConsumerSpouseController::class, 'index'])->name('memberConsumerSpouses.index');
Route::post('/member_consumer_spouses/store', [App\Http\Controllers\MemberConsumerSpouseController::class, 'store'])->name('memberConsumerSpouses.store');
Route::get('/member_consumer_spouses/edit/{consumerId}', [App\Http\Controllers\MemberConsumerSpouseController::class, 'edit'])->name('memberConsumerSpouses.edit');
Route::patch('/member_consumer_spouses/update/{id}', [App\Http\Controllers\MemberConsumerSpouseController::class, 'update'])->name('memberConsumerSpouses.update');
// Route::resource('memberConsumerSpouses', App\Http\Controllers\MemberConsumerSpouseController::class);

Route::get('/service_connections/fetchserviceconnections', [ServiceConnectionsController::class, 'fetchserviceconnections'])->name('serviceConnections.fetch-service-connections');
Route::get('/service_connections/selectmembership', [ServiceConnectionsController::class, 'selectMembership'])->name('serviceConnections.selectmembership');
Route::get('/service_connections/fetchmemberconsumer', [ServiceConnectionsController::class, 'fetchmemberconsumer'])->name('serviceConnections.fetch-member-consumers');
Route::get('/service_connections/create_new/{consumerId}', [ServiceConnectionsController::class, 'createNew'])->name('serviceConnections.create_new');
Route::get('/service_connections/create_new_step_two/{scId}', [ServiceConnectionsController::class, 'createNewStepTwo'])->name('serviceConnections.create_new_step_two');
Route::get('/service_connections/assess_checklists/{id}', [ServiceConnectionsController::class, 'assessChecklists'])->name('serviceConnections.assess-checklists');
Route::get('/service_connections/update_checklists/{id}', [ServiceConnectionsController::class, 'updateChecklists'])->name('serviceConnections.update-checklists');
Route::get('/service_connections/move_to_trash/{id}', [ServiceConnectionsController::class, 'moveToTrash'])->name('serviceConnections.move-to-trash');
Route::get('/service_connections/trash', [ServiceConnectionsController::class, 'trash'])->name('serviceConnections.trash');
Route::get('/service_connections/restore/{id}', [ServiceConnectionsController::class, 'restore'])->name('serviceConnections.restore');
Route::get('/service_connections/fetchserviceconnectiontrash', [ServiceConnectionsController::class, 'fetchserviceconnectiontrash'])->name('serviceConnections.fetch-service-connection-trash');
Route::get('/service_connections/energization', [ServiceConnectionsController::class, 'energization'])->name('serviceConnections.energization');
Route::get('/service_connections/print_order/{id}', [ServiceConnectionsController::class, 'printOrder'])->name('serviceConnections.print-order');
Route::post('/service_connections/change-station-crew', [ServiceConnectionsController::class, 'changeStationCrew']);
Route::post('/service_connections/update-energization-status', [ServiceConnectionsController::class, 'updateEnergizationStatus']);
Route::get('/service_connections/select_application_type/{consumerId}', [ServiceConnectionsController::class, 'selectApplicationType'])->name('serviceConnections.select-application-type');
Route::post('/service_connections/relay_account_type/{consumerId}', [ServiceConnectionsController::class, 'relayApplicationType'])->name('serviceConnections.relay-account-type');
Route::get('/service_connections/dashboard', [ServiceConnectionsController::class, 'dashboard'])->name('serviceConnections.dashboard');
Route::get('/service_connections/large-load-inspections', [ServiceConnectionsController::class, 'largeLoadInspections'])->name('serviceConnections.large-load-inspections');
Route::post('/service_connections/large-load-inspection-update', [ServiceConnectionsController::class, 'largeLoadInspectionUpdate'])->name('serviceConnections.large-load-inspection-update');
Route::get('/service_connections/bom-index', [ServiceConnectionsController::class, 'bomIndex'])->name('serviceConnections.bom-index');
Route::get('/service_connections/bom-assigning/{scId}', [ServiceConnectionsController::class, 'bomAssigning'])->name('serviceConnections.bom-assigning');
Route::get('/service_connections/forward-to-transformer-assigning/{scId}', [ServiceConnectionsController::class, 'forwardToTransformerAssigning'])->name('serviceConnections.forward-to-transformer-assigning');
Route::get('/service_connections/transformer-assigning/{scId}', [ServiceConnectionsController::class, 'transformerAssigning'])->name('serviceConnections.transformer-assigning');
Route::get('/service_connections/transformer_index', [ServiceConnectionsController::class, 'transformerIndex'])->name('serviceConnections.transformer-index');
Route::get('/service_connections/pole-assigning/{scId}', [ServiceConnectionsController::class, 'poleAssigning'])->name('serviceConnections.pole-assigning');
Route::get('/service_connections/quotation-summary/{scId}', [ServiceConnectionsController::class, 'quotationSummary'])->name('serviceConnections.quotation-summary');
Route::get('/service_connections/spanning-assigning/{scId}', [ServiceConnectionsController::class, 'spanningAssigning'])->name('serviceConnections.spanning-assigning');
Route::get('/service_connections/forward-to-verficaation/{scId}', [ServiceConnectionsController::class, 'forwardToVerification'])->name('serviceConnections.forward-to-verficaation');
Route::get('/service_connections/largeload-predefined-materials/{scId}/{options}', [ServiceConnectionsController::class, 'largeLoadPredefinedMaterials'])->name('serviceConnections.largeload-predefined-materials');
Route::get('/service_connections/fleet-monitor', [ServiceConnectionsController::class, 'fleetMonitor'])->name('serviceConnections.fleet-monitor');
Route::get('/service_connections/metering-equipment-assigning/{scId}', [ServiceConnectionsController::class, 'meteringEquipmentAssigning'])->name('serviceConnections.metering-equipment-assigning');
Route::get('/service_connections/daily-monitor', [ServiceConnectionsController::class, 'dailyMonitor'])->name('serviceConnections.daily-monitor');
Route::get('/service_connections/fetch-daily-monitor-applications-data', [ServiceConnectionsController::class, 'fetchDailyMonitorApplicationsData'])->name('serviceConnections.fetch-daily-monitor-applications-data');
Route::get('/service_connections/fetch-daily-monitor-energized-data', [ServiceConnectionsController::class, 'fetchDailyMonitorEnergizedData'])->name('serviceConnections.fetch-daily-monitor-energized-data');
Route::get('/service_connections/applications-report', [ServiceConnectionsController::class, 'applicationsReport'])->name('serviceConnections.applications-report');
Route::get('/service_connections/fetch-applications-report', [ServiceConnectionsController::class, 'fetchApplicationsReport'])->name('serviceConnections.fetch-applications-report');
Route::post('/service_connections/download-applications-report', [ServiceConnectionsController::class, 'downloadApplicationsReport'])->name('serviceConnections.download-applications-report');
Route::get('/service_connections/energization-report', [ServiceConnectionsController::class, 'energizationReport'])->name('serviceConnections.energization-report');
Route::get('/service_connections/fetch-energization-report', [ServiceConnectionsController::class, 'fetchEnergizationReport'])->name('serviceConnections.fetch-energization-report');
Route::post('/service_connections/download-energization-report', [ServiceConnectionsController::class, 'downloadEnergizationReport'])->name('serviceConnections.download-energization-report');
Route::get('/service_connections/fetch-application-count-via-status', [ServiceConnectionsController::class, 'fetchApplicationCountViaStatus'])->name('serviceConnections.fetch-application-count-via-status');
Route::get('/service_connections/print-service-connection-application/{id}', [ServiceConnectionsController::class, 'printServiceConnectionApplication'])->name('serviceConnections.print-service-connection-application');
Route::get('/service_connections/print-service-connection-contract/{id}', [ServiceConnectionsController::class, 'printServiceConnectionContract'])->name('serviceConnections.print-service-connection-contract');
Route::get('/service_connections/relocation-search', [ServiceConnectionsController::class, 'relocationSearch'])->name('serviceConnections.relocation-search');
Route::get('/service_connections/create-relocation/{id}', [ServiceConnectionsController::class, 'createRelocation'])->name('serviceConnections.create-relocation');
Route::get('/service_connections/change-name-search', [ServiceConnectionsController::class, 'changeNameSearch'])->name('serviceConnections.change-name-search');
Route::get('/service_connections/create-change-name/{id}', [ServiceConnectionsController::class, 'createChangeName'])->name('serviceConnections.create-change-name');
Route::post('/service_connections/store-change-name', [ServiceConnectionsController::class, 'storeChangeName'])->name('serviceConnections.store-change-name');
Route::get('/service_connections/approve-change-name/{id}', [ServiceConnectionsController::class, 'approveForChangeName'])->name('serviceConnections.approve-change-name');
Route::get('/service_connections/bypass-approve-inspection/{inspectionId}', [ServiceConnectionsController::class, 'bypassApproveInspection'])->name('serviceConnections.bypass-approve-inspection');
Route::get('/service_connections/re-installation-search', [ServiceConnectionsController::class, 'reInstallationSearch'])->name('serviceConnections.re-installation-search');
Route::get('/service_connections/create-re-installation/{id}', [ServiceConnectionsController::class, 'createReInstallation'])->name('serviceConnections.create-re-installation');
Route::get('/service_connections/print-contract-without-membership/{id}', [ServiceConnectionsController::class, 'printContractWithoutMembership'])->name('serviceConnections.print-contract-without-membership');
Route::get('/service_connections/print-application-form-without-membership/{id}', [ServiceConnectionsController::class, 'printApplicationFormWithoutMembership'])->name('serviceConnections.print-application-form-without-membership');
Route::get('/service_connections/metering-installation', [ServiceConnectionsController::class, 'meteringInstallation'])->name('serviceConnections.metering-installation');
Route::get('/service_connections/download-metering-installation-report/{town}/{from}/{to}', [ServiceConnectionsController::class, 'downloadMeteringInstallation'])->name('serviceConnections.download-metering-installation-report');
Route::get('/service_connections/detailed-summary', [ServiceConnectionsController::class, 'detailedSummary'])->name('serviceConnections.detailed-summary');
Route::get('/service_connections/download-detailed-summary/{town}/{from}/{to}', [ServiceConnectionsController::class, 'downloadDetailedSummary'])->name('serviceConnections.download-detailed-summary');
Route::get('/service_connections/summary-report', [ServiceConnectionsController::class, 'summaryReport'])->name('serviceConnections.summary-report');
Route::get('/service_connections/mriv', [ServiceConnectionsController::class, 'mriv'])->name('serviceConnections.mriv');
Route::get('/service_connections/print-mriv/{town}/{from}/{to}', [ServiceConnectionsController::class, 'printMriv'])->name('serviceConnections.print-mriv');
Route::get('/service_connections/update-status', [ServiceConnectionsController::class, 'updateStatus'])->name('serviceConnections.update-status');
Route::get('/service_connections/sevice-connections-report', [ServiceConnectionsController::class, 'serviceConnectionsReport'])->name('serviceConnections.sevice-connections-report');
Route::get('/service_connections/print-sevice-connections-report/{town}/{from}/{to}', [ServiceConnectionsController::class, 'printServiceConnectionsReport'])->name('serviceConnections.print-sevice-connections-report');
Route::resource('serviceConnections', App\Http\Controllers\ServiceConnectionsController::class);


Route::resource('serviceConnectionAccountTypes', App\Http\Controllers\ServiceConnectionAccountTypesController::class);


Route::get('/service_connection_inspections/create_step_two/{scId}', [App\Http\Controllers\ServiceConnectionInspectionsController::class, 'createStepTwo'])->name('serviceConnectionInspections.create-step-two');
Route::resource('serviceConnectionInspections', App\Http\Controllers\ServiceConnectionInspectionsController::class);


Route::get('/service_connection_mtr_trnsfrmrs/assigning', [App\Http\Controllers\ServiceConnectionMtrTrnsfrmrController::class, 'assigning'])->name('serviceConnectionMtrTrnsfrmrs.assigning');
Route::get('/service_connection_mtr_trnsfrmrs/create_step_three/{scId}', [App\Http\Controllers\ServiceConnectionMtrTrnsfrmrController::class, 'createStepThree'])->name('serviceConnectionMtrTrnsfrmrs.create-step-three');
Route::resource('serviceConnectionMtrTrnsfrmrs', App\Http\Controllers\ServiceConnectionMtrTrnsfrmrController::class);


Route::resource('serviceConnectionMatPayables', App\Http\Controllers\ServiceConnectionMatPayablesController::class);


Route::resource('serviceConnectionPayParticulars', App\Http\Controllers\ServiceConnectionPayParticularsController::class);


Route::resource('serviceConnectionMatPayments', App\Http\Controllers\ServiceConnectionMatPaymentsController::class);

Route::get('/service_connection_pay_tansactions/create_step_four/{scId}', [App\Http\Controllers\ServiceConnectionPayTransactionController::class, 'createStepFour'])->name('serviceConnectionPayTransactions.create-step-four');
Route::resource('serviceConnectionPayTransactions', App\Http\Controllers\ServiceConnectionPayTransactionController::class);


Route::resource('serviceConnectionTotalPayments', App\Http\Controllers\ServiceConnectionTotalPaymentsController::class);


Route::resource('serviceConnectionTimeframes', App\Http\Controllers\ServiceConnectionTimeframesController::class);


Route::post('/member_consumer_checklists/complyChecklists/{id}', [App\Http\Controllers\MemberConsumerChecklistsController::class, 'complyChecklists'])->name('memberConsumerChecklists.comply-checklists');
Route::resource('memberConsumerChecklists', App\Http\Controllers\MemberConsumerChecklistsController::class);


Route::resource('memberConsumerChecklistsReps', App\Http\Controllers\MemberConsumerChecklistsRepController::class);


Route::resource('serviceConnectionChecklistsReps', App\Http\Controllers\ServiceConnectionChecklistsRepController::class);

Route::post('/service_connection_checklists_reps/complyChecklists/{id}', [App\Http\Controllers\ServiceConnectionChecklistsController::class, 'complyChecklists'])->name('serviceConnectionChecklists.comply-checklists');
Route::post('/service_connection_checklists_reps/save-file-and-comply-checklist', [App\Http\Controllers\ServiceConnectionChecklistsController::class, 'saveFileAndComplyChecklist']);
Route::get('/service_connection_checklists_reps/assess-checklist-completion/{scId}', [App\Http\Controllers\ServiceConnectionChecklistsController::class, 'assessChecklistCompletion'])->name('serviceConnectionChecklists.assess-checklist-completion');
Route::get('/service_connection_checklists_reps/download-file/{scId}/{folder}/{file}', [App\Http\Controllers\ServiceConnectionChecklistsController::class, 'downloadFile'])->name('serviceConnectionChecklists.download-file');
Route::resource('serviceConnectionChecklists', App\Http\Controllers\ServiceConnectionChecklistsController::class);


Route::resource('serviceConnectionCrews', App\Http\Controllers\ServiceConnectionCrewController::class);


Route::get('/service_accounts/pending-accounts/', [App\Http\Controllers\ServiceAccountsController::class, 'pendingAccounts'])->name('serviceAccounts.pending-accounts');
Route::get('/service_accounts/account-migration/{id}', [App\Http\Controllers\ServiceAccountsController::class, 'accountMigration'])->name('serviceAccounts.account-migration');
Route::get('/service_accounts/account-migration-step-two/{id}', [App\Http\Controllers\ServiceAccountsController::class, 'accountMigrationStepTwo'])->name('serviceAccounts.account-migration-step-two');
Route::get('/service_accounts/account-migration-step-three/{id}', [App\Http\Controllers\ServiceAccountsController::class, 'accountMigrationStepThree'])->name('serviceAccounts.account-migration-step-three');
Route::get('/service_accounts/update_step_one/{id}', [App\Http\Controllers\ServiceAccountsController::class, 'updateStepOne'])->name('serviceAccounts.update-step-one');
Route::get('/service_accounts/merge-all-bill-arrears/{id}', [App\Http\Controllers\ServiceAccountsController::class,  'mergeAllBillArrears'])->name('serviceAccounts.merge-all-bill-arrears');
Route::get('/service_accounts/unmerge-all-bill-arrears/{id}', [App\Http\Controllers\ServiceAccountsController::class,  'unmergeAllBillArrears'])->name('serviceAccounts.unmerge-all-bill-arrears');
Route::get('/service_accounts/unmerge-bill-arrear/{billId}', [App\Http\Controllers\ServiceAccountsController::class,  'unmergeBillArrear'])->name('serviceAccounts.unmerge-bill-arrear');
Route::get('/service_accounts/merge-bill-arrear/{billId}', [App\Http\Controllers\ServiceAccountsController::class,  'mergeBillArrear'])->name('serviceAccounts.merge-bill-arrear');
Route::get('/service_accounts/accounts-map-view', [App\Http\Controllers\ServiceAccountsController::class,  'accountsMapView'])->name('serviceAccounts.accounts-map-view');
Route::get('/service_accounts/get-accounts-by-town', [App\Http\Controllers\ServiceAccountsController::class,  'getAccountsByTown'])->name('serviceAccounts.get-accounts-by-town');
Route::get('/service_accounts/bapa', [App\Http\Controllers\ServiceAccountsController::class,  'bapa'])->name('serviceAccounts.bapa');
Route::get('/service_accounts/create-bapa', [App\Http\Controllers\ServiceAccountsController::class,  'createBapa'])->name('serviceAccounts.create-bapa');
Route::get('/service_accounts/get-routes-from-district', [App\Http\Controllers\ServiceAccountsController::class,  'getRoutesFromDistrict'])->name('serviceAccounts.get-routes-from-district');
Route::get('/service_accounts/add-to-bapa', [App\Http\Controllers\ServiceAccountsController::class,  'addToBapa'])->name('serviceAccounts.add-to-bapa');
Route::get('/service_accounts/bapa-view/{bapaName}', [App\Http\Controllers\ServiceAccountsController::class,  'bapaView'])->name('serviceAccounts.bapa-view');
Route::get('/service_accounts/remove-bapa-by-route', [App\Http\Controllers\ServiceAccountsController::class,  'removeBapaByRoute'])->name('serviceAccounts.remove-bapa-by-route');
Route::get('/service_accounts/remove-bapa-by-account', [App\Http\Controllers\ServiceAccountsController::class,  'removeBapaByAccount'])->name('serviceAccounts.remove-bapa-by-account');
Route::get('/service_accounts/update-bapa/{bapaName}', [App\Http\Controllers\ServiceAccountsController::class,  'updateBapa'])->name('serviceAccounts.update-bapa');
Route::get('/service_accounts/search-accout-bapa', [App\Http\Controllers\ServiceAccountsController::class,  'searchAccountBapa'])->name('serviceAccounts.search-accout-bapa');
Route::get('/service_accounts/add-single-account-to-bapa', [App\Http\Controllers\ServiceAccountsController::class,  'addSingleAccountToBapa'])->name('serviceAccounts.add-single-account-to-bapa');
Route::get('/service_accounts/reading-account-grouper', [App\Http\Controllers\ServiceAccountsController::class,  'readingAccountGrouper'])->name('serviceAccounts.reading-account-grouper');
Route::get('/service_accounts/account-grouper-view/{townCode}', [App\Http\Controllers\ServiceAccountsController::class,  'accountGrouperView'])->name('serviceAccounts.account-grouper-view');
Route::get('/service_accounts/account-grouper-organizer/{townCode}/{groupCode}', [App\Http\Controllers\ServiceAccountsController::class,  'accountGrouperOrganizer'])->name('serviceAccounts.account-grouper-organizer');
Route::get('/bills/bapa-view-readings/{period}/{bapaName}', [App\Http\Controllers\ServiceAccountsController::class,  'bapaViewReadings'])->name('bills.bapa-view-readings');
Route::get('/service_accounts/re-sequence-accounts', [App\Http\Controllers\ServiceAccountsController::class,  'reSequenceAccounts'])->name('serviceAccounts.re-sequence-accounts');
Route::get('/service_accounts/update-gps-coordinates', [App\Http\Controllers\ServiceAccountsController::class,  'updateGPSCoordinates'])->name('serviceAccounts.update-gps-coordinates');
Route::get('/service_accounts/search-global', [App\Http\Controllers\ServiceAccountsController::class,  'searchGlobal'])->name('serviceAccounts.search-global');
Route::get('/service_accounts/termed-payment-accounts', [App\Http\Controllers\ServiceAccountsController::class,  'termedPaymentAccounts'])->name('serviceAccounts.termed-payment-accounts');
Route::get('/service_accounts/disconnect-manual', [App\Http\Controllers\ServiceAccountsController::class,  'disconnectManual'])->name('serviceAccounts.disconnect-manual');
Route::get('/service_accounts/reconnect-manual', [App\Http\Controllers\ServiceAccountsController::class,  'reconnectManual'])->name('serviceAccounts.reconnect-manual');
Route::get('/service_accounts/apprehend-manual', [App\Http\Controllers\ServiceAccountsController::class,  'apprehendManual'])->name('serviceAccounts.apprehend-manual');
Route::get('/service_accounts/pullout-manual', [App\Http\Controllers\ServiceAccountsController::class,  'pulloutManual'])->name('serviceAccounts.pullout-manual');
Route::get('/service_accounts/change-name', [App\Http\Controllers\ServiceAccountsController::class,  'changeName'])->name('serviceAccounts.change-name');
Route::get('/service_accounts/relocation-form/{accountNo}/{scId}', [App\Http\Controllers\ServiceAccountsController::class,  'relocationForm'])->name('serviceAccounts.relocation-form');
Route::get('/service_accounts/print-ledger/{id}/{from}/{to}', [App\Http\Controllers\ServiceAccountsController::class,  'printLedger'])->name('serviceAccounts.print-ledger');
Route::post('/service_accounts/store-relocation', [App\Http\Controllers\ServiceAccountsController::class,  'storeRelocation'])->name('serviceAccounts.store-relocation');
Route::get('/service_accounts/search-for-captured', [App\Http\Controllers\ServiceAccountsController::class,  'searchForCaptured'])->name('serviceAccounts.search-for-captured');
Route::get('/service_accounts/print-bapa-bills-list/{bapaName}/{period}', [App\Http\Controllers\ServiceAccountsController::class,  'printBapaBillsList'])->name('serviceAccounts.print-bapa-bills-list');
Route::get('/service_accounts/confirm-change-name/{id}', [App\Http\Controllers\ServiceAccountsController::class, 'confirmChangeName'])->name('serviceAccounts.confirm-change-name');
Route::post('/service_accounts/update-name', [App\Http\Controllers\ServiceAccountsController::class, 'updateName'])->name('serviceAccounts.update-name');
Route::get('/service_accounts/search-bapa-ajax', [App\Http\Controllers\ServiceAccountsController::class, 'searchBapaAjax'])->name('serviceAccounts.search-bapa-ajax');
Route::get('/service_accounts/rename-bapa', [App\Http\Controllers\ServiceAccountsController::class, 'renameBapa'])->name('serviceAccounts.rename-bapa');
Route::get('/service_accounts/validate-old-account-no', [App\Http\Controllers\ServiceAccountsController::class, 'validateOlAccountNo'])->name('serviceAccounts.validate-old-account-no');
Route::get('/service_accounts/manual-account-migration-one', [App\Http\Controllers\ServiceAccountsController::class, 'manualAccountMigrationOne'])->name('serviceAccounts.manual-account-migration-one');
Route::post('/service_accounts/store-manual', [App\Http\Controllers\ServiceAccountsController::class, 'storeManual'])->name('serviceAccounts.store-manual');
Route::get('/service_accounts/manual-account-migration-two/{id}', [App\Http\Controllers\ServiceAccountsController::class, 'manualAccountMigrationTwo'])->name('serviceAccounts.manual-account-migration-two');
Route::post('/service_accounts/store-meters-manual', [App\Http\Controllers\ServiceAccountsController::class, 'storeMetersManual'])->name('serviceAccounts.store-meters-manual');
Route::get('/service_accounts/manual-account-migration-three/{id}', [App\Http\Controllers\ServiceAccountsController::class, 'manualAccountMigrationThree'])->name('serviceAccounts.manual-account-migration-three');
Route::post('/service_accounts/store-transformer-manual', [App\Http\Controllers\ServiceAccountsController::class, 'storeTransformerManual'])->name('serviceAccounts.store-transformer-manual');
Route::get('/service_accounts/change-meter-manual', [App\Http\Controllers\ServiceAccountsController::class, 'changeMeterManual'])->name('serviceAccounts.change-meter-manual');
Route::get('/service_accounts/change-meter-manual-console/{id}', [App\Http\Controllers\ServiceAccountsController::class, 'changeMeterManualConsole'])->name('serviceAccounts.change-meter-manual-console');
Route::post('/service_accounts/store-change-meter-manual', [App\Http\Controllers\ServiceAccountsController::class, 'storeChangeMeterManual'])->name('serviceAccounts.store-change-meter-manual');
Route::get('/service_accounts/relocation-manual', [App\Http\Controllers\ServiceAccountsController::class, 'relocationManual'])->name('serviceAccounts.relocation-manual');
Route::get('/service_accounts/relocation-form-manual/{id}', [App\Http\Controllers\ServiceAccountsController::class, 'relocationFormManual'])->name('serviceAccounts.relocation-form-manual');
Route::get('/service_accounts/print-group-bills-list/{period}/{groupId}', [App\Http\Controllers\ServiceAccountsController::class, 'printGroupBillsList'])->name('serviceAccounts.print-group-bills-list');
Route::get('/service_accounts/check-available-account-numbers', [App\Http\Controllers\ServiceAccountsController::class, 'checkAvailableAccountNumbers'])->name('serviceAccounts.check-available-account-numbers');
Route::get('/service_accounts/disco-pullout-appr', [App\Http\Controllers\ServiceAccountsController::class, 'discoPulloutAppr'])->name('serviceAccounts.disco-pullout-appr');
Route::get('/service_accounts/download-disco-pullout-appr/{from}/{to}/{town}/{status}/{period}', [App\Http\Controllers\ServiceAccountsController::class, 'downloadDiscoPulloutAppr'])->name('serviceAccounts.download-disco-pullout-appr');
Route::get('/service_accounts/account-grouper-edit/{day}/{town}', [App\Http\Controllers\ServiceAccountsController::class, 'accountGrouperEdit'])->name('serviceAccounts.account-grouper-edit');
Route::get('/service_accounts/fetch-route-from-mreader', [App\Http\Controllers\ServiceAccountsController::class, 'fetchRouteFromMeterReader'])->name('serviceAccounts.fetch-route-from-mreader');
Route::get('/service_accounts/move-route', [App\Http\Controllers\ServiceAccountsController::class, 'moveRoute'])->name('serviceAccounts.move-route');
Route::get('/service_accounts/route-checker', [App\Http\Controllers\ServiceAccountsController::class, 'routeChecker'])->name('serviceAccounts.route-checker');
Route::get('/service_accounts/net-metering', [App\Http\Controllers\ServiceAccountsController::class, 'netMetering'])->name('serviceAccounts.net-metering');
Route::get('/service_accounts/net-metering-dashboard', [App\Http\Controllers\ServiceAccountsController::class, 'netMeteringDashboard'])->name('serviceAccounts.net-metering-dashboard');
Route::get('/service_accounts/add-prepayment-balance-manually', [App\Http\Controllers\ServiceAccountsController::class, 'addPrepaymentBalanceManually'])->name('serviceAccounts.add-prepayment-balance-manually');
Route::get('/service_accounts/remove-download-tag', [App\Http\Controllers\ServiceAccountsController::class, 'removeDownloadedTag'])->name('serviceAccounts.remove-download-tag');
Route::get('/service_accounts/contestable-accounts', [App\Http\Controllers\ServiceAccountsController::class, 'contestableAccounts'])->name('serviceAccounts.contestable-accounts');
Route::get('/service_accounts/coop-consumption-accounts', [App\Http\Controllers\ServiceAccountsController::class, 'coopConsumptionAccounts'])->name('serviceAccounts.coop-consumption-accounts');
Route::get('/service_accounts/account-list', [App\Http\Controllers\ServiceAccountsController::class, 'accountList'])->name('serviceAccounts.account-list');
Route::get('/service_accounts/view-account-list', [App\Http\Controllers\ServiceAccountsController::class, 'viewAccountList'])->name('serviceAccounts.view-account-list');
Route::get('/service_accounts/download-account-list/{town}/{status}', [App\Http\Controllers\ServiceAccountsController::class, 'downloadAccountList'])->name('serviceAccounts.download-account-list');
Route::get('/service_accounts/meter-readers', [App\Http\Controllers\ServiceAccountsController::class, 'meterReaders'])->name('serviceAccounts.meter-readers');
Route::get('/service_accounts/meter-readers-view/{id}', [App\Http\Controllers\ServiceAccountsController::class, 'meterReadersView'])->name('serviceAccounts.meter-readers-view');
Route::get('/service_accounts/get-accounts-by-meter-reader', [App\Http\Controllers\ServiceAccountsController::class, 'getAccountsByMeterReader'])->name('serviceAccounts.get-accounts-by-meter-reader');
Route::get('/service_accounts/meter-readers-add-account/{meterReader}/{group}', [App\Http\Controllers\ServiceAccountsController::class, 'meterReadersAddAccount'])->name('serviceAccounts.meter-readers-add-account');
Route::get('/service_accounts/search-account-for-meter-reader', [App\Http\Controllers\ServiceAccountsController::class, 'searchAccountForMeterReader'])->name('serviceAccounts.search-account-for-meter-reader');
Route::get('/service_accounts/change-meter-reader', [App\Http\Controllers\ServiceAccountsController::class, 'changeMeterReader'])->name('serviceAccounts.change-meter-reader');
Route::resource('serviceAccounts', App\Http\Controllers\ServiceAccountsController::class);


Route::resource('serviceConnectionLgLoadInsps', App\Http\Controllers\ServiceConnectionLgLoadInspController::class);


Route::get('/structures/get-structures-json', [App\Http\Controllers\StructuresController::class, 'getStructuresJson'])->name('structures.get-structures-json');
Route::get('/structures/get-structures-by-type', [App\Http\Controllers\StructuresController::class, 'getStructuresByType'])->name('structures.get-structures-by-type');
Route::resource('structures', App\Http\Controllers\StructuresController::class);


Route::resource('materialAssets', App\Http\Controllers\MaterialAssetsController::class);


Route::resource('materialsMatrices', App\Http\Controllers\MaterialsMatrixController::class);


Route::resource('billOfMaterialsIndices', App\Http\Controllers\BillOfMaterialsIndexController::class);


Route::resource('billOfMaterialsDetails', App\Http\Controllers\BillOfMaterialsDetailsController::class);

Route::post('/structure_assignments/insert-structure-assignment', [App\Http\Controllers\StructureAssignmentsController::class, 'insertStructureAssignment']);
Route::get('/structure_assignments/delete-brackets', [App\Http\Controllers\StructureAssignmentsController::class, 'deleteBrackets'])->name('structureAssignments.delete-brackets');
Route::get('/structure_assignments/get-bracket-structure', [App\Http\Controllers\StructureAssignmentsController::class, 'getBracketStructure'])->name('structureAssignments.get-bracket-structure');
Route::resource('structureAssignments', App\Http\Controllers\StructureAssignmentsController::class);


Route::get('/bill_of_materials_matrices/view/{scId}', [App\Http\Controllers\BillOfMaterialsMatrixController::class, 'view'])->name('billOfMaterialsMatrices.view');
Route::get('/bill_of_materials_matrices/download-bill-of-materials/{scId}',  [App\Http\Controllers\BillOfMaterialsMatrixController::class, 'downloadBillOfMaterials'])->name('billOfMaterialsMatrices.download-bill-of-materials');
Route::get('/bill_of_materials_matrices/get-bill-of-materials-json/', [App\Http\Controllers\BillOfMaterialsMatrixController::class, 'getBillOfMaterialsJson'])->name('billOfMaterialsMatrices.get-bill-of-materials-json');
Route::post('/bill_of_materials_matrices/insert-transformer-bracket', [App\Http\Controllers\BillOfMaterialsMatrixController::class, 'insertTransformerBracket'])->name('billOfMaterialsMatrices.insert-transformer-bracket');
Route::get('/bill_of_materials_matrices/get-bill-of-materials-brackets/', [App\Http\Controllers\BillOfMaterialsMatrixController::class, 'getBillOfMaterialsBrackets'])->name('billOfMaterialsMatrices.get-bill-of-materials-brackets');
Route::post('/bill_of_materials_matrices/insert-pole', [App\Http\Controllers\BillOfMaterialsMatrixController::class, 'insertPole'])->name('billOfMaterialsMatrices.insert-pole');
Route::get('/bill_of_materials_matrices/fetch-poles/', [App\Http\Controllers\BillOfMaterialsMatrixController::class, 'fetchPoles'])->name('billOfMaterialsMatrices.fetch-poles');
Route::get('/bill_of_materials_matrices/delete-pole/', [App\Http\Controllers\BillOfMaterialsMatrixController::class, 'deletePole'])->name('billOfMaterialsMatrices.delete-pole');
Route::get('/bill_of_materials_matrices/delete-material/', [App\Http\Controllers\BillOfMaterialsMatrixController::class, 'deleteMaterial'])->name('billOfMaterialsMatrices.delete-material');
Route::post('/bill_of_materials_matrices/add-custom-material', [App\Http\Controllers\BillOfMaterialsMatrixController::class, 'addCustomMaterial'])->name('billOfMaterialsMatrices.add-custom-material');
Route::post('/bill_of_materials_matrices/insert-spanning-materials', [App\Http\Controllers\BillOfMaterialsMatrixController::class, 'insertSpanningMaterials'])->name('billOfMaterialsMatrices.insert-spanning-materials');
Route::get('/bill_of_materials_matrices/fetch-span-material/', [App\Http\Controllers\BillOfMaterialsMatrixController::class, 'fetchSpanMaterials'])->name('billOfMaterialsMatrices.fetch-span-material');
Route::get('/bill_of_materials_matrices/delete-span-material/', [App\Http\Controllers\BillOfMaterialsMatrixController::class, 'deleteSpanMaterial'])->name('billOfMaterialsMatrices.delete-span-material');
Route::post('/bill_of_materials_matrices/insert-sdw-materials', [App\Http\Controllers\BillOfMaterialsMatrixController::class, 'insertSDWMaterials'])->name('billOfMaterialsMatrices.insert-sdw-materials');
Route::post('/bill_of_materials_matrices/insert-special-equipment', [App\Http\Controllers\BillOfMaterialsMatrixController::class, 'insertSpecialEquipment'])->name('billOfMaterialsMatrices.insert-special-equipment');
Route::get('/bill_of_materials_matrices/fetch-equipments/', [App\Http\Controllers\BillOfMaterialsMatrixController::class, 'fetchEquipments'])->name('billOfMaterialsMatrices.fetch-equipments');
Route::resource('billOfMaterialsMatrices', App\Http\Controllers\BillOfMaterialsMatrixController::class);


Route::resource('transformerIndices', App\Http\Controllers\TransformerIndexController::class);

Route::post('/transformers_assigned_matrices/create-ajax', [App\Http\Controllers\TransformersAssignedMatrixController::class, 'createAjax'])->name('transformersAssignedMatrices.create-ajax');
Route::get('/transformers_assigned_matrices/fetch-transformers', [App\Http\Controllers\TransformersAssignedMatrixController::class, 'fetchTransformers'])->name('transformersAssignedMatrices.fetch-transformers');
Route::resource('transformersAssignedMatrices', App\Http\Controllers\TransformersAssignedMatrixController::class);


Route::resource('poleIndices', App\Http\Controllers\PoleIndexController::class);


Route::resource('billsOfMaterialsSummaries', App\Http\Controllers\BillsOfMaterialsSummaryController::class);


Route::resource('spanningIndices', App\Http\Controllers\SpanningIndexController::class);


Route::resource('spanningDatas', App\Http\Controllers\SpanningDataController::class);


Route::resource('preDefinedMaterials', App\Http\Controllers\PreDefinedMaterialsController::class);


Route::post('/preDefinedMaterialsMatrices/update-data/', [App\Http\Controllers\PreDefinedMaterialsMatrixController::class, 'updateData']);
Route::get('/preDefinedMaterialsMatrices/re-init/{scId}/{options}', [App\Http\Controllers\PreDefinedMaterialsMatrixController::class, 'reInit'])->name('preDefinedMaterialsMatrices.re-init');
Route::post('/preDefinedMaterialsMatrices/add-material/', [App\Http\Controllers\PreDefinedMaterialsMatrixController::class, 'addMaterial']);
Route::resource('preDefinedMaterialsMatrices', App\Http\Controllers\PreDefinedMaterialsMatrixController::class);


Route::post('/member_consumer_images/create-image/', [App\Http\Controllers\MemberConsumerImagesController::class, 'createImage'])->name('memberConsumerImages.create-image');
Route::get('/member_consumer_images/get-image/{id}', [App\Http\Controllers\MemberConsumerImagesController::class, 'getImage'])->name('memberConsumerImages.get-image');
Route::resource('memberConsumerImages', App\Http\Controllers\MemberConsumerImagesController::class);


Route::get('/tickets/create-select', [App\Http\Controllers\TicketsController::class, 'createSelect'])->name('tickets.create-select');
Route::get('/tickets/get-create-ajax', [App\Http\Controllers\TicketsController::class, 'getCreateAjax'])->name('tickets.get-create-ajax');
Route::get('/tickets/create-new/{id}', [App\Http\Controllers\TicketsController::class, 'createNew'])->name('tickets.create-new');
Route::get('/tickets/fetch-tickets', [App\Http\Controllers\TicketsController::class, 'fetchTickets'])->name('tickets.fetch-tickets');
Route::get('/tickets/print-ticket/{id}', [App\Http\Controllers\TicketsController::class, 'printTicket'])->name('tickets.print-ticket');
Route::get('/tickets/trash', [App\Http\Controllers\TicketsController::class, 'trash'])->name('tickets.trash');
Route::get('/tickets/restore-ticket/{id}', [App\Http\Controllers\TicketsController::class, 'restoreTicket'])->name('tickets.restore-ticket');
Route::post('/tickets/update-date-filed', [App\Http\Controllers\TicketsController::class, 'updateDateFiled'])->name('tickets.update-date-filed');
Route::post('/tickets/update-date-downloaded', [App\Http\Controllers\TicketsController::class, 'updateDateDownloaded'])->name('tickets.update-date-downloaded');
Route::post('/tickets/update-date-arrival', [App\Http\Controllers\TicketsController::class, 'updateDateArrival'])->name('tickets.update-date-arrival');
Route::post('/tickets/update-execution', [App\Http\Controllers\TicketsController::class, 'updateExecution'])->name('tickets.update-execution');
Route::get('/tickets/dashboard', [App\Http\Controllers\TicketsController::class, 'dashboard'])->name('tickets.dashboard');
Route::get('/tickets/fetch-dashboard-tickets-trend', [App\Http\Controllers\TicketsController::class, 'fetchDashboardTicketsTrend'])->name('tickets.fetch-dashboard-tickets-trend');
Route::get('/tickets/get-ticket-statistics', [App\Http\Controllers\TicketsController::class, 'getTicketStatistics'])->name('tickets.get-ticket-statistics');
Route::get('/tickets/get-ticket-statistics-details', [App\Http\Controllers\TicketsController::class, 'getTicketStatisticsDetails'])->name('tickets.get-ticket-statistics-details');
Route::get('/tickets/kps-monitor', [App\Http\Controllers\TicketsController::class, 'kpsMonitor'])->name('tickets.kps-monitor');
Route::get('/tickets/get-kps-ticket-crew-graph', [App\Http\Controllers\TicketsController::class, 'getKpsTicketCrewGraph'])->name('tickets.get-kps-ticket-crew-graph');
Route::get('/tickets/get-ticket-avg-hours', [App\Http\Controllers\TicketsController::class, 'getTicketCrewAverageHours'])->name('tickets.get-ticket-avg-hours');
Route::get('/tickets/get-overall-avg-kps', [App\Http\Controllers\TicketsController::class, 'getOverAllAverageKps'])->name('tickets.get-overall-avg-kps');
Route::get('/tickets/change-meter', [App\Http\Controllers\TicketsController::class, 'changeMeter'])->name('tickets.change-meter');
Route::get('/tickets/create-change-meter/{accountNumber}', [App\Http\Controllers\TicketsController::class, 'createChangeMeter'])->name('tickets.create-change-meter');
Route::get('/tickets/assessments-change-meter', [App\Http\Controllers\TicketsController::class, 'changeMeterAssessments'])->name('tickets.assessments-change-meter');
Route::get('/tickets/assessments-ordinary-ticket', [App\Http\Controllers\TicketsController::class, 'ordinaryTicketsAssessment'])->name('tickets.assessments-ordinary-ticket');
Route::get('/tickets/assess-change-meter-form/{ticketId}', [App\Http\Controllers\TicketsController::class, 'assessChangeMeterForm'])->name('tickets.assess-change-meter-form');
Route::post('/tickets/update-change-meter-assessment', [App\Http\Controllers\TicketsController::class, 'updateChangeMeterAssessment'])->name('tickets.update-change-meter-assessment');
Route::post('/tickets/update-ordinary-ticket-assessment', [App\Http\Controllers\TicketsController::class, 'updateOrdinaryTicketAssessment'])->name('tickets.update-ordinary-ticket-assessment');
Route::get('/tickets/ticket-summary-report', [App\Http\Controllers\TicketsController::class, 'ticketSummaryReport'])->name('tickets.ticket-summary-report');
Route::get('/tickets/get-ticket-summary-report', [App\Http\Controllers\TicketsController::class, 'getTicketSummaryResults'])->name('tickets.get-ticket-summary-report');
Route::get('/tickets/ticket-summary-report-download-route', [App\Http\Controllers\TicketsController::class, 'ticketSummaryReportDownloadRoute'])->name('tickets.ticket-summary-report-download-route');
Route::get('/tickets/download-tickets-summary-report/{ticketParam}/{from}/{to}/{area}/{status}', [App\Http\Controllers\TicketsController::class, 'downloadTicketsSummaryReport'])->name('tickets.download-tickets-summary-report');
Route::get('/tickets/disconnection-assessments', [App\Http\Controllers\TicketsController::class, 'disconnectionAssessments'])->name('tickets.disconnection-assessments');
Route::get('/tickets/get-disconnection-results', [App\Http\Controllers\TicketsController::class, 'getDisconnectionResults'])->name('tickets.get-disconnection-results');
Route::get('/tickets/disconnection-results-route', [App\Http\Controllers\TicketsController::class, 'disconnectionResultsRoute'])->name('tickets.disconnection-results-route');
Route::get('/tickets/create-and-print-disconnection-tickets/{period}/{route}', [App\Http\Controllers\TicketsController::class, 'createAndPrintDisconnectionTickets'])->name('tickets.create-and-print-disconnection-tickets');
Route::get('/tickets/ticket-tally', [App\Http\Controllers\TicketsController::class, 'ticketTally'])->name('tickets.ticket-tally');
Route::get('/tickets/get-ticket-tally', [App\Http\Controllers\TicketsController::class, 'getTicketTally'])->name('tickets.get-ticket-tally');
Route::get('/tickets/download-ticket-tally/{town}/{from}/{to}', [App\Http\Controllers\TicketsController::class, 'downloadTicketTally'])->name('tickets.download-ticket-tally');
Route::get('/tickets/kps-summary-report', [App\Http\Controllers\TicketsController::class, 'kpsSummaryReport'])->name('tickets.kps-summary-report');
Route::get('/tickets/download-kps-summary-report/{town}/{from}/{to}', [App\Http\Controllers\TicketsController::class, 'downloadKpsSummaryReport'])->name('tickets.download-kps-summary-report');
Route::resource('tickets', App\Http\Controllers\TicketsController::class);


Route::resource('ticketsRepositories', App\Http\Controllers\TicketsRepositoryController::class);


Route::resource('ticketLogs', App\Http\Controllers\TicketLogsController::class);

Route::post('/special_equipment_materials/create-material', [App\Http\Controllers\SpecialEquipmentMaterialsController::class, 'createEquipment']);
Route::resource('specialEquipmentMaterials', App\Http\Controllers\SpecialEquipmentMaterialsController::class);


Route::resource('serviceConnectionImages', App\Http\Controllers\ServiceConnectionImagesController::class);


Route::resource('billingTransformers', App\Http\Controllers\BillingTransformersController::class);


Route::resource('billingMeters', App\Http\Controllers\BillingMetersController::class);


Route::resource('meterReaders', App\Http\Controllers\MeterReadersController::class);


Route::resource('meterReaderTrackNames', App\Http\Controllers\MeterReaderTrackNamesController::class);

Route::get('/meter_reader_tracks/get-tracks-by-tracknameid', [App\Http\Controllers\MeterReaderTracksController::class, 'getTracksByTrackNameId'])->name('meterReaderTracks.get-tracks-by-tracknameid');
Route::resource('meterReaderTracks', App\Http\Controllers\MeterReaderTracksController::class);


Route::get('/damage_assessments/get-objects', [App\Http\Controllers\DamageAssessmentController::class, 'getObjects'])->name('damageAssessments.get-objects');
Route::get('/damage_assessments/search-pole', [App\Http\Controllers\DamageAssessmentController::class, 'searchPole'])->name('damageAssessments.search-pole');
Route::get('/damage_assessments/view-pole', [App\Http\Controllers\DamageAssessmentController::class, 'viewPole'])->name('damageAssessments.view-pole');
Route::post('/damage_assessments/update-ajax', [App\Http\Controllers\DamageAssessmentController::class, 'updateAjax'])->name('damageAssessments.update-ajax');
Route::resource('damageAssessments', App\Http\Controllers\DamageAssessmentController::class);


Route::get('/reading_schedules/update-schedule/{userId}', [App\Http\Controllers\ReadingSchedulesController::class, 'updateSchedule'])->name('readingSchedules.update-schedule');
Route::get('/reading_schedules/view-schedule/{userId}', [App\Http\Controllers\ReadingSchedulesController::class, 'viewSchedule'])->name('readingSchedules.view-schedule');
Route::get('/reading_schedules/get-latest-schedule', [App\Http\Controllers\ReadingSchedulesController::class, 'getLatestSchedule'])->name('readingSchedules.get-latest-schedule');
Route::get('/reading_schedules/reading-schedule-index', [App\Http\Controllers\ReadingSchedulesController::class, 'readingScheduleIndex'])->name('readingSchedules.reading-schedule-index');
Route::get('/reading_schedules/view-meter-reading-scheds-in-period/{period}', [App\Http\Controllers\ReadingSchedulesController::class, 'viewMeterReadingSchedsInPeriod'])->name('readingSchedules.view-meter-reading-scheds-in-period');
Route::get('/reading_schedules/create-reading-schedule', [App\Http\Controllers\ReadingSchedulesController::class, 'createReadingSchedule'])->name('readingSchedules.create-reading-schedule');
Route::post('/reading_schedules/store-reading-schedule', [App\Http\Controllers\ReadingSchedulesController::class, 'storeReadingSchedules'])->name('readingSchedules.store-reading-schedule');
Route::resource('readingSchedules', App\Http\Controllers\ReadingSchedulesController::class);


Route::get('/rates/upload-rate', [App\Http\Controllers\RatesController::class, 'uploadRate'])->name('rates.upload-rate');
Route::post('/rates/validate-rate-upload', [App\Http\Controllers\RatesController::class, 'validateRateUpload'])->name('rates.validate-rate-upload');
Route::get('/rates/view-rates/{servicePeriod}', [App\Http\Controllers\RatesController::class, 'viewRates'])->name('rates.view-rates');
Route::post('/rates/delete-rates/{servicePeriod}', [App\Http\Controllers\RatesController::class, 'deleteRates'])->name('rates.delete-rates');
Route::get('/rates/get-rate', [App\Http\Controllers\RatesController::class, 'getRate'])->name('rates.get-rate');
Route::resource('rates', App\Http\Controllers\RatesController::class);

Route::get('/readings/reading-monitor', [App\Http\Controllers\ReadingsController::class, 'readingMonitor'])->name('readings.reading-monitor');
Route::get('/readings/reading-monitor-view/{servicePeriod}', [App\Http\Controllers\ReadingsController::class, 'readingMonitorView'])->name('readings.reading-monitor-view');
Route::get('/readings/get-readings-from-meter-reader', [App\Http\Controllers\ReadingsController::class, 'getReadingsFromMeterReader'])->name('readings.get-readings-from-meter-reader');
Route::get('/readings/manual-reading', [App\Http\Controllers\ReadingsController::class, 'manualReading'])->name('readings.manual-reading');
Route::get('/readings/manual-reading-console/{id}', [App\Http\Controllers\ReadingsController::class, 'manualReadingConsole'])->name('readings.manual-reading-console');
Route::get('/readings/captured-readings-console/{id}/{readId}/{day}/{bapaName}', [App\Http\Controllers\ReadingsController::class, 'capturedReadingsConsole'])->name('readings.captured-readings-console');
Route::get('/readings/get-computed-bill', [App\Http\Controllers\ReadingsController::class, 'getComputedBill'])->name('readings.get-computed-bill');
Route::post('/readings/create-manual-billing', [App\Http\Controllers\ReadingsController::class, 'createManualBilling'])->name('readings.create-manual-billing');
Route::get('/readings/captured-readings', [App\Http\Controllers\ReadingsController::class, 'capturedReadings'])->name('readings.captured-readings');
Route::get('/readings/mark-as-done', [App\Http\Controllers\ReadingsController::class, 'markAsDone'])->name('readings.mark-as-done');
Route::get('/readings/fetch-account', [App\Http\Controllers\ReadingsController::class, 'fetchAccount'])->name('readings.fetch-account');
Route::get('/readings/view-full-report/{period}/{meterReader}/{day}/{town}', [App\Http\Controllers\ReadingsController::class, 'viewFullReport'])->name('readings.view-full-report');
Route::get('/readings/view-full-report-bapa/{period}/{bapaName}', [App\Http\Controllers\ReadingsController::class, 'viewFullReportBapa'])->name('readings.view-full-report-bapa');
Route::get('/readings/get-previous-readings', [App\Http\Controllers\ReadingsController::class, 'getPreviousReadings'])->name('readings.get-previous-readings');
Route::get('/readings/create-manual-billing-ajax', [App\Http\Controllers\ReadingsController::class, 'createManualBillingAjax'])->name('readings.create-manual-billing-ajax');
Route::get('/readings/check-if-account-has-bill', [App\Http\Controllers\ReadingsController::class, 'checkIfAccountHasBill'])->name('readings.check-if-account-has-bill');
Route::post('/readings/create-bill-for-captured-reading', [App\Http\Controllers\ReadingsController::class, 'createBillForCapturedReading'])->name('readings.create-bill-for-captured-reading');
Route::get('/readings/print-old-format-adjusted/{period}/{day}/{town}/{meterReader}', [App\Http\Controllers\ReadingsController::class, 'printOldFormatAdjusted'])->name('readings.print-old-format-adjusted');
Route::get('/readings/print-new-format-adjusted/{period}/{day}/{town}/{meterReader}', [App\Http\Controllers\ReadingsController::class, 'printNewFormatAdjusted'])->name('readings.print-new-format-adjusted');
Route::get('/readings/print-old-format-adjusted-bapa/{period}/{bapaName}', [App\Http\Controllers\ReadingsController::class, 'printOldFormatAdjustedBapa'])->name('readings.print-old-format-adjusted-bapa');
Route::get('/readings/print-new-format-adjusted-bapa/{period}/{bapaName}', [App\Http\Controllers\ReadingsController::class, 'printNewFormatAdjustedBapa'])->name('readings.print-new-format-adjusted-bapa');
Route::get('/readings/print-unbilled-by-status/{period}/{day}/{town}/{meterReader}/{status}', [App\Http\Controllers\ReadingsController::class, 'printUnbilledList'])->name('readings.print-unbilled-by-status');
Route::get('/readings/print-other-unbilled-list/{period}/{day}/{town}/{meterReader}', [App\Http\Controllers\ReadingsController::class, 'printOtherUnbilledList'])->name('readings.print-other-unbilled-list');
Route::get('/readings/billed-and-unbilled-reports', [App\Http\Controllers\ReadingsController::class, 'billAndUnbilledReport'])->name('readings.billed-and-unbilled-reports');
Route::get('/readings/print-billed-unbilled/{type}/{meterReader}/{day}/{period}/{town}/{status}', [App\Http\Controllers\ReadingsController::class, 'printBilledUnbilled'])->name('readings.print-billed-unbilled');
Route::get('/readings/print-disco-active/{meterReader}/{day}/{period}/{town}', [App\Http\Controllers\ReadingsController::class, 'printDiscoActive'])->name('readings.print-disco-active');
Route::get('/readings/billed-and-unbilled-reports-bapa', [App\Http\Controllers\ReadingsController::class, 'billAndUnbilledReportBapa'])->name('readings.billed-and-unbilled-reports-bapa');
Route::get('/readings/print-billed-unbilled-bapa/{type}/{bapaName}/{period}/{status}', [App\Http\Controllers\ReadingsController::class, 'printBilledUnbilledBapa'])->name('readings.print-billed-unbilled-bapa');
Route::get('/readings/efficiency-report', [App\Http\Controllers\ReadingsController::class, 'efficiencyReport'])->name('readings.efficiency-report');
Route::get('/readings/print-efficiency-report/{meterReader}/{month}/{office}', [App\Http\Controllers\ReadingsController::class, 'printEfficiencyReport'])->name('readings.print-efficiency-report');
Route::get('/readings/print-bapa-reading-list', [App\Http\Controllers\ReadingsController::class, 'printBapaReadingList'])->name('readings.print-bapa-reading-list');
Route::get('/readings/search-print-bapa-reading-list', [App\Http\Controllers\ReadingsController::class, 'searchPrintBapaReadingList'])->name('readings.search-print-bapa-reading-list');
Route::get('/readings/print-bapa-reading-list-to-paper/{bapaName}/{period}', [App\Http\Controllers\ReadingsController::class, 'printBapaReadingListToPaper'])->name('readings.print-bapa-reading-list-to-paper');
Route::get('/readings/print-bulk-new-format-mreader/{period}/{day}/{town}/{mreader}', [App\Http\Controllers\ReadingsController::class, 'printBulkBillNewFormatMreader'])->name('readings.print-bulk-new-format-mreader');
Route::get('/readings/print-bulk-old-format-mreader/{period}/{day}/{town}/{mreader}', [App\Http\Controllers\ReadingsController::class, 'printBulkBillOldFormatMreader'])->name('readings.print-bulk-old-format-mreader');
Route::get('/readings/print-group-reading-list/{bapaName}/{period}', [App\Http\Controllers\ReadingsController::class, 'printGroupReadingList'])->name('readings.print-group-reading-list');
Route::get('/readings/get-meter-readers', [App\Http\Controllers\ReadingsController::class, 'getMeterReaders'])->name('readings.get-meter-readers');
Route::get('/readings/erroneous-readings', [App\Http\Controllers\ReadingsController::class, 'erroneousReading'])->name('readings.erroneous-readings');
Route::get('/readings/abrupt-increase-decrease', [App\Http\Controllers\ReadingsController::class, 'abruptIncreaseDecrease'])->name('readings.abrupt-increase-decrease');
Route::get('/readings/analyze-abrupt-increase-decrease', [App\Http\Controllers\ReadingsController::class, 'analyzeAbruptIncreaseDecrease'])->name('readings.analyze-abrupt-increase-decrease');
Route::get('/readings/show-excemptions-per-route', [App\Http\Controllers\ReadingsController::class, 'showExcemptionsPerRoute'])->name('readings.show-excemptions-per-route');
Route::get('/readings/show-disconnected-per-route', [App\Http\Controllers\ReadingsController::class, 'showDisconnectedPerRoute'])->name('readings.show-disconnected-per-route');
Route::get('/readings/show-outstanding-per-route', [App\Http\Controllers\ReadingsController::class, 'showOutstandingPerRoute'])->name('readings.show-outstanding-per-route');
Route::get('/readings/disco-per-mreader', [App\Http\Controllers\ReadingsController::class, 'discoPerMeterReader'])->name('readings.disco-per-mreader');
Route::get('/readings/print-disco-per-mreader/{period}/{meterReader}', [App\Http\Controllers\ReadingsController::class, 'printDiscoPerMeterReader'])->name('readings.print-disco-per-mreader');
Route::get('/readings/uncollected-per-mreader', [App\Http\Controllers\ReadingsController::class, 'uncollectedPerMeterReader'])->name('readings.uncollected-per-mreader');
Route::get('/readings/print-uncollected-per-mreader/{period}/{meterReader}', [App\Http\Controllers\ReadingsController::class, 'printUncollectedPerMeterReader'])->name('readings.print-uncollected-per-mreader');
Route::get('/readings/excemptions-per-mreader', [App\Http\Controllers\ReadingsController::class, 'excemptionsPerMeterReader'])->name('readings.excemptions-per-mreader');
Route::get('/readings/print-excemptions-per-mreader/{period}/{meterReader}', [App\Http\Controllers\ReadingsController::class, 'printExcemptionsPerMeterReader'])->name('readings.print-excemptions-per-mreader');
Route::get('/readings/disco-per-bapa', [App\Http\Controllers\ReadingsController::class, 'discoPerBapa'])->name('readings.disco-per-bapa');
Route::resource('readings', App\Http\Controllers\ReadingsController::class);

Route::get('/bills/unbilled-readings', [App\Http\Controllers\BillsController::class, 'unbilledReadings'])->name('bills.unbilled-readings');
Route::get('/bills/unbilled-readings-console/{servicePeriod}', [App\Http\Controllers\BillsController::class, 'unbilledReadingsConsole'])->name('bills.unbilled-readings-console');
Route::get('/bills/zero-readings-view/{readingId}', [App\Http\Controllers\BillsController::class, 'zeroReadingsView'])->name('bills.zero-readings-view');
Route::get('/bills/average-bill/{readingId}', [App\Http\Controllers\BillsController::class, 'averageBill'])->name('bills.average-bill');
Route::get('/bills/rebill-reading-adjustment/{readingId}', [App\Http\Controllers\BillsController::class, 'rebillReadingAdjustment'])->name('bills.rebill-reading-adjustment');
Route::post('/bills/rebill/{readingId}', [App\Http\Controllers\BillsController::class, 'rebill'])->name('bills.rebill');
Route::get('/bills/adjust-bill/{billId}', [App\Http\Controllers\BillsController::class, 'adjustBill'])->name('bills.adjust-bill');
Route::get('/bills/fetch-bill-adjustment-data', [App\Http\Controllers\BillsController::class, 'fetchBillAdjustmentData'])->name('bills.fetch-bill-adjustment-data');
Route::get('/bills/fetch-net-metering-bill-adjustment-data', [App\Http\Controllers\BillsController::class, 'fetchNetMeteringBillAdjustmentData'])->name('bills.fetch-net-metering-bill-adjustment-data');
Route::get('/bills/all-bills', [App\Http\Controllers\BillsController::class,  'allBills'])->name('bills.all-bills');
Route::get('/bills/bill-arrears-unlocking', [App\Http\Controllers\BillsController::class,  'billArrearsUnlocking'])->name('bills.bill-arrears-unlocking');
Route::get('/bills/unlock-bill-arrear/{id}', [App\Http\Controllers\BillsController::class,  'unlockBillArrear'])->name('bills.unlock-bill-arrear');
Route::get('/bills/reject-unlock-bill-arrear/{id}', [App\Http\Controllers\BillsController::class,  'rejectUnlockBillArrear'])->name('bills.reject-unlock-bill-arrear');
Route::get('/bills/grouped-billing', [App\Http\Controllers\BillsController::class,  'groupedBilling'])->name('bills.grouped-billing');
Route::get('/bills/create-group-billing-step-one', [App\Http\Controllers\BillsController::class,  'createGroupBillingStepOne'])->name('bills.create-group-billing-step-one');
Route::get('/bills/create-group-billing-step-one-pre-select', [App\Http\Controllers\BillsController::class,  'createGroupBillingStepOnePreSelect'])->name('bills.create-group-billing-step-one-pre-select');
Route::get('/bills/create-group-billing-step-two/{memberConsumerId}', [App\Http\Controllers\BillsController::class,  'createGroupBillingStepTwo'])->name('bills.create-group-billing-step-two');
Route::post('/bills/store-group-billing-step-one', [App\Http\Controllers\BillsController::class,  'storeGroupBillingStepOne'])->name('bills.store-group-billing-step-one');
Route::get('/bills/fetch-member-consumers', [App\Http\Controllers\BillsController::class,  'fetchMemberConsumers'])->name('bills.fetch-member-consumers');
Route::get('/bills/search-account', [App\Http\Controllers\BillsController::class,  'searchAccount'])->name('bills.search-account');
Route::get('/bills/add-to-group', [App\Http\Controllers\BillsController::class,  'addToGroup'])->name('bills.add-to-group');
Route::get('/bills/remove-from-group', [App\Http\Controllers\BillsController::class,  'removeFromGroup'])->name('bills.remove-from-group');
Route::get('/bills/grouped-billing-view/{memberConsumerId}', [App\Http\Controllers\BillsController::class,  'groupedBillingView'])->name('bills.grouped-billing-view');
Route::get('/bills/grouped-billing-bill-view/{memberConsumerId}/{period}', [App\Http\Controllers\BillsController::class,  'groupedBillingBillView'])->name('bills.grouped-billing-bill-view');
Route::get('/bills/add-two-percent', [App\Http\Controllers\BillsController::class,  'add2Percent'])->name('bills.add-two-percent');
Route::get('/bills/remove-two-percent', [App\Http\Controllers\BillsController::class,  'remove2Percent'])->name('bills.remove-two-percent');
Route::get('/bills/add-five-percent', [App\Http\Controllers\BillsController::class,  'add5Percent'])->name('bills.add-five-percent');
Route::get('/bills/remove-five-percent', [App\Http\Controllers\BillsController::class,  'remove5Percent'])->name('bills.remove-five-percent');
Route::get('/bills/print-group-billing/{memberConsumerId}/{period}/{withSurcharge}', [App\Http\Controllers\BillsController::class,  'printGroupBilling'])->name('bills.print-group-billing');
Route::get('/bills/print-single-bill-new-format/{billId}', [App\Http\Controllers\BillsController::class,  'printSingleBillNewFormat'])->name('bills.print-single-bill-new-format');
Route::get('/bills/print-single-bill-old/{billId}', [App\Http\Controllers\BillsController::class,  'printSingleBillOld'])->name('bills.print-single-bill-old');
Route::get('/bills/bulk-print-bill', [App\Http\Controllers\BillsController::class,  'bulkPrintBill'])->name('bills.bulk-print-bill');
Route::get('/bills/get-routes-from-town', [App\Http\Controllers\BillsController::class,  'getRoutesFromTown'])->name('bills.get-routes-from-town');
Route::get('/bills/print-bulk-bill-new-format/{period}/{town}/{route}/{day}', [App\Http\Controllers\BillsController::class,  'printBulkBillNewFormat'])->name('bills.print-bulk-bill-new-format');
Route::get('/bills/print-bulk-bill-old-format/{period}/{town}/{route}', [App\Http\Controllers\BillsController::class,  'printBulkBillOldFormat'])->name('bills.print-bulk-bill-old-format');
Route::get('/bills/print-bulk-bill-old-format-bapa/{period}/{bapaName}/{from}/{route}', [App\Http\Controllers\BillsController::class,  'printBulkBillOldFormatBapa'])->name('bills.print-bulk-bill-old-format-bapa');
Route::get('/bills/print-bulk-bill-new-format-bapa/{period}/{bapaName}/{from}/{route}', [App\Http\Controllers\BillsController::class,  'printBulkBillNewFormatBapa'])->name('bills.print-bulk-bill-new-format-bapa');
Route::get('/bills/bapa-manual-billing', [App\Http\Controllers\BillsController::class,  'bapaManualBilling'])->name('bills.bapa-manual-billing');
Route::get('/bills/search-bapa-for-billing', [App\Http\Controllers\BillsController::class,  'searchBapaForBilling'])->name('bills.search-bapa-for-billing');
Route::get('/bills/bapa-manual-billing-console/{bapaName}', [App\Http\Controllers\BillsController::class,  'bapaManualBillingConsole'])->name('bills.bapa-manual-billing-console');
Route::get('/bills/get-bill-computation', [App\Http\Controllers\BillsController::class,  'getBillComputation'])->name('bills.get-bill-computation');
Route::get('/bills/bill-manually', [App\Http\Controllers\BillsController::class,  'billManually'])->name('bills.bill-manually');
Route::get('/bills/fetch-billed-consumers-from-reading', [App\Http\Controllers\BillsController::class,  'fetchBilledConsumersFromReading'])->name('bills.fetch-billed-consumers-from-reading');
Route::get('/bills/request-cancel-bill', [App\Http\Controllers\BillsController::class,  'requestCancelBill'])->name('bills.request-cancel-bill');
Route::get('/bills/bills-cancellation-approval', [App\Http\Controllers\BillsController::class,  'billsCancellationApproval'])->name('bills.bills-cancellation-approval');
Route::get('/bills/approve-bill-cancellation-request/{id}', [App\Http\Controllers\BillsController::class,  'approveBillCancellationRequest'])->name('bills.approve-bill-cancellation-request');
Route::get('/bills/reject-bill-cancellation-request/{id}', [App\Http\Controllers\BillsController::class,  'rejectBillCancellationRequest'])->name('bills.reject-bill-cancellation-request');
Route::get('/bills/change-meter-readings/{account}/{period}', [App\Http\Controllers\BillsController::class,  'changeMeterReadings'])->name('bills.change-meter-readings');
Route::post('/bills/bill-change-meters', [App\Http\Controllers\BillsController::class,  'billChangeMeters'])->name('bills.bill-change-meters');
Route::get('/bills/adjustment-reports', [App\Http\Controllers\BillsController::class,  'adjustmentReports'])->name('bills.adjustment-reports');
Route::get('/bills/print-adjustment-report/{type}/{period}', [App\Http\Controllers\BillsController::class,  'printAdjustmentReport'])->name('bills.print-adjustment-report');
Route::get('/bills/mark-as-paid', [App\Http\Controllers\BillsController::class, 'markAsPaid'])->name('bills.mark-as-paid');
Route::get('/bills/dashboard', [App\Http\Controllers\BillsController::class, 'dashboard'])->name('bills.dashboard');
Route::get('/bills/dashboard-reading-monitor', [App\Http\Controllers\BillsController::class, 'dashboardReadingMonitor'])->name('bills.dashboard-reading-monitor');
Route::get('/bills/change-bapa-duedate', [App\Http\Controllers\BillsController::class, 'changeBapaDueDate'])->name('bills.change-bapa-duedate');
Route::get('/bills/print-bulk-bill-old-format-group/{period}/{groupId}', [App\Http\Controllers\BillsController::class,  'printBulkBillOldFormatGroup'])->name('bills.print-bulk-bill-old-format-group');
Route::get('/bills/print-bulk-bill-new-format-group/{period}/{groupId}', [App\Http\Controllers\BillsController::class,  'printBulkBillNewFormatGroup'])->name('bills.print-bulk-bill-new-format-group');
Route::get('/bills/delete-bill-and-reading-ajax', [App\Http\Controllers\BillsController::class, 'deleteBillAndReadingAjax'])->name('bills.delete-bill-and-reading-ajax');
Route::get('/bills/kwh-monitoring', [App\Http\Controllers\BillsController::class, 'kwhMonitoring'])->name('bills.kwh-monitoring');
Route::get('/bills/fetch-kwh-data', [App\Http\Controllers\BillsController::class, 'fetchKwhData'])->name('bills.fetch-kwh-data');
Route::get('/bills/group-bill-all', [App\Http\Controllers\BillsController::class, 'groupBillingBillAll'])->name('bills.group-bill-all');
Route::get('/bills/close-billing/{period}', [App\Http\Controllers\BillsController::class, 'closeBilling'])->name('bills.close-billing');
Route::get('/bills/lifeliners-report', [App\Http\Controllers\BillsController::class, 'lifelinersReport'])->name('bills.lifeliners-report');
Route::get('/bills/print-lifeliners/{town}/{period}/{khwused}', [App\Http\Controllers\BillsController::class, 'printLifeliners'])->name('bills.print-lifeliners');
Route::get('/bills/senior-citizen-report', [App\Http\Controllers\BillsController::class, 'seniorCitizenReport'])->name('bills.senior-citizen-report');
Route::get('/bills/print-senior-citizen/{town}/{period}', [App\Http\Controllers\BillsController::class, 'printSeniorCitizen'])->name('bills.print-senior-citizen');
Route::get('/bills/get-minified-collection-efficiency', [App\Http\Controllers\BillsController::class, 'getMinifiedCollectionEfficiency'])->name('bills.get-minified-collection-efficiency');
Route::get('/bills/government-tax-report', [App\Http\Controllers\BillsController::class, 'governmentTaxReport'])->name('bills.government-tax-report');
Route::get('/bills/print-government-tax-report/{period}/{town}/{route}', [App\Http\Controllers\BillsController::class, 'printGovernmentTaxReport'])->name('bills.print-government-tax-report');
Route::get('/bills/outstanding-report', [App\Http\Controllers\BillsController::class, 'outstandingReport'])->name('bills.outstanding-report');
Route::get('/bills/download-outstanding-report/{asOf}/{town}/{status}', [App\Http\Controllers\BillsController::class, 'downloadOutstandingReport'])->name('bills.download-outstanding-report');
Route::get('/bills/disconnected-reports', [App\Http\Controllers\BillsController::class, 'disconnectedReports'])->name('bills.disconnected-reports');
Route::get('/bills/print-single-net-metering/{billId}', [App\Http\Controllers\BillsController::class,  'printSingleNetMetering'])->name('bills.print-single-net-metering');
Route::get('/bills/adjust-bill-net-metering/{billId}', [App\Http\Controllers\BillsController::class, 'adjustBillNetMetering'])->name('bills.adjust-bill-net-metering');
Route::get('/bills/get-billing-adjustment-history', [App\Http\Controllers\BillsController::class, 'getBillingAdjustmentHistory'])->name('bills.get-billing-adjustment-history');
Route::get('/bills/show-uncollected-dashboard', [App\Http\Controllers\BillsController::class, 'showUncollectedDashboard'])->name('bills.show-uncollected-dashboard');
Route::get('/bills/unbilled-no-meter-readers', [App\Http\Controllers\BillsController::class, 'unbilledNoMeterReaders'])->name('bills.unbilled-no-meter-readers');
Route::get('/bills/all-billed', [App\Http\Controllers\BillsController::class, 'allBilled'])->name('bills.all-billed');
Route::get('/bills/download-all-billed/{town}/{period}/{type}', [App\Http\Controllers\BillsController::class, 'downloadAllBilled'])->name('bills.download-all-billed');
Route::get('/bills/newly-energized', [App\Http\Controllers\BillsController::class, 'newlyEnergizedConsumers'])->name('bills.newly-energized');
Route::get('/bills/download-newly-energized/{town}/{period}', [App\Http\Controllers\BillsController::class, 'downloadNewlyEnergized'])->name('bills.download-newly-energized');
Route::get('/bills/show-unbilled-dashboard', [App\Http\Controllers\BillsController::class, 'showUnbilledDashboard'])->name('bills.show-unbilled-dashboard');
Route::get('/bills/outstanding-report-mreader', [App\Http\Controllers\BillsController::class, 'outstandingReportMreader'])->name('bills.outstanding-report-mreader');
Route::get('/bills/download-outstanding-report-mreader/{asOf}/{meterReader}/{status}', [App\Http\Controllers\BillsController::class, 'downloadOutstandingReportMreader'])->name('bills.download-outstanding-report-mreader');
Route::get('/bills/outstanding-report-bapa', [App\Http\Controllers\BillsController::class, 'outstandingReportBAPA'])->name('bills.outstanding-report-bapa');
Route::get('/bills/download-outstanding-report-bapa/{asOf}/{bapa}/{status}', [App\Http\Controllers\BillsController::class, 'downloadOutstandingReportBAPA'])->name('bills.download-outstanding-report-bapa');
Route::get('/bills/adjustment-reports-with-gl', [App\Http\Controllers\BillsController::class,  'adjustmentReportsWithGL'])->name('bills.adjustment-reports-with-gl');
Route::get('/bills/cancelled-bills', [App\Http\Controllers\BillsController::class,  'cancelledBills'])->name('bills.cancelled-bills');
Route::get('/bills/print-cancelled-bills/{from}/{to}/{area}', [App\Http\Controllers\BillsController::class,  'printCancelledBills'])->name('bills.print-cancelled-bills');
Route::get('/bills/detailed-adjustments', [App\Http\Controllers\BillsController::class,  'detailedAdjustments'])->name('bills.detailed-adjustments');
Route::get('/bills/remove-residual-credit/{id}', [App\Http\Controllers\BillsController::class,  'removeResidualCredit'])->name('bills.remove-residual-credit');
Route::get('/bills/get-netmetering-eported-energy-report', [App\Http\Controllers\BillsController::class,  'getNetMeteringEportedEnergyReport'])->name('bills.get-netmetering-eported-energy-report');
Route::get('/bills/get-netmetering-imported-energy-report', [App\Http\Controllers\BillsController::class,  'getNetMeteringImportedEnergyReport'])->name('bills.get-netmetering-imported-energy-report');
Route::get('/bills/net-metering-report', [App\Http\Controllers\BillsController::class,  'netMeteringReport'])->name('bills.net-metering-report');
Route::get('/bills/print-net-metering-report/{period}', [App\Http\Controllers\BillsController::class,  'printNetMeteringReport'])->name('bills.print-net-metering-report');
Route::resource('bills', App\Http\Controllers\BillsController::class);


Route::resource('readingImages', App\Http\Controllers\ReadingImagesController::class);

Route::get('/collectibles/ledgerize', [App\Http\Controllers\CollectiblesController::class, 'ledgerize'])->name('collectibles.ledgerize');
Route::get('/collectibles/add-to-month', [App\Http\Controllers\CollectiblesController::class, 'addToMonth'])->name('collectibles.add-to-month');
Route::post('/collectibles/clear-ledger/{id}', [App\Http\Controllers\CollectiblesController::class, 'clearLedger'])->name('collectibles.clear-ledger');
Route::resource('collectibles', App\Http\Controllers\CollectiblesController::class);


Route::resource('arrearsLedgerDistributions', App\Http\Controllers\ArrearsLedgerDistributionController::class);

Route::get('/transaction_indices/service-connection-collection', [App\Http\Controllers\TransactionIndexController::class, 'serviceConnectionCollection'])->name('transactionIndices.service-connection-collection');
Route::get('/transaction_indices/get-payable-details', [App\Http\Controllers\TransactionIndexController::class, 'getPayableDetails'])->name('transactionIndices.get-payable-details');
Route::get('/transaction_indices/get-payable-total', [App\Http\Controllers\TransactionIndexController::class, 'getPayableTotal'])->name('transactionIndices.get-payable-total');
Route::get('/transaction_indices/get-power-load-payables', [App\Http\Controllers\TransactionIndexController::class, 'getPowerLoadPayables'])->name('transactionIndices.get-power-load-payables');
Route::get('/transaction_indices/save-and-print-or-service-connections', [App\Http\Controllers\TransactionIndexController::class, 'saveAndPrintORServiceConnections'])->name('transactionIndices.save-and-print-or-service-connections');
Route::get('/transaction_indices/print-or-service-connections/{transactionIndexId}', [App\Http\Controllers\TransactionIndexController::class, 'printORServiceConnections'])->name('transactionIndices.print-or-service-connections');
Route::get('/transaction_indices/uncollected-arrears', [App\Http\Controllers\TransactionIndexController::class, 'uncollectedArrears'])->name('transactionIndices.uncollected-arrears');
Route::get('/transaction_indices/search-arrear-collectibles', [App\Http\Controllers\TransactionIndexController::class, 'searchArrearCollectibles'])->name('transactionIndices.search-arrear-collectibles');
Route::get('/transaction_indices/fetch-arrear-details', [App\Http\Controllers\TransactionIndexController::class, 'fetchArrearDetails'])->name('transactionIndices.fetch-arrear-details');
Route::get('/transaction_indices/save-arrear-transaction', [App\Http\Controllers\TransactionIndexController::class, 'saveArrearTransaction'])->name('transactionIndices.save-arrear-transaction');
Route::get('/transaction_indices/ledger-arrears-collection/{accountNo}', [App\Http\Controllers\TransactionIndexController::class, 'ledgerArrearsCollection'])->name('transactionIndices.ledger-arrears-collection');
Route::get('/transaction_indices/save-ledger-arrear-transaction', [App\Http\Controllers\TransactionIndexController::class, 'saveLedgerArrearTransaction'])->name('transactionIndices.save-ledger-arrear-transaction');
Route::get('/transaction_indices/print-or-termed-ledger-arrears/{transactionIndexId}', [App\Http\Controllers\TransactionIndexController::class, 'printORTermedLedgerArrears'])->name('transactionIndices.print-or-termed-ledger-arrears');
Route::get('/transaction_indices/other-payments', [App\Http\Controllers\TransactionIndexController::class, 'otherPayments'])->name('transactionIndices.other-payments');
Route::get('/transaction_indices/search-consumer', [App\Http\Controllers\TransactionIndexController::class, 'searchConsumer'])->name('transactionIndices.search-consumer');
Route::get('/transaction_indices/fetch-account-details', [App\Http\Controllers\TransactionIndexController::class, 'fetchAccountDetails'])->name('transactionIndices.fetch-account-details');
Route::get('/transaction_indices/fetch-payable-details', [App\Http\Controllers\TransactionIndexController::class, 'fetchPayableDetails'])->name('transactionIndices.fetch-payable-details');
Route::get('/transaction_indices/print-other-payments/{transactionIndexId}', [App\Http\Controllers\TransactionIndexController::class, 'printOtherPayments'])->name('transactionIndices.print-other-payments');
Route::get('/transaction_indices/reconnection-collection', [App\Http\Controllers\TransactionIndexController::class, 'reconnectionCollection'])->name('transactionIndices.reconnection-collection');
Route::get('/transaction_indices/search-disconnected-consumers', [App\Http\Controllers\TransactionIndexController::class, 'searchDisconnectedConsumers'])->name('transactionIndices.search-disconnected-consumers');
Route::get('/transaction_indices/get-arrears-data', [App\Http\Controllers\TransactionIndexController::class, 'getArrearsData'])->name('transactionIndices.get-arrears-data');
Route::get('/transaction_indices/save-reconnection-transaction', [App\Http\Controllers\TransactionIndexController::class, 'saveReconnectionTransaction'])->name('transactionIndices.save-reconnection-transaction');
Route::get('/transaction_indices/add-check-payment', [App\Http\Controllers\TransactionIndexController::class, 'addCheckPayment'])->name('transactionIndices.add-check-payment');
Route::get('/transaction_indices/delete-check-payment', [App\Http\Controllers\TransactionIndexController::class, 'deleteCheckPayment'])->name('transactionIndices.delete-check-payment');
Route::get('/transaction_indices/browse-ors', [App\Http\Controllers\TransactionIndexController::class, 'browseORs'])->name('transactionIndices.browse-ors');
Route::get('/transaction_indices/browse-ors-view/{id}/{paymentType}', [App\Http\Controllers\TransactionIndexController::class, 'browseORView'])->name('transactionIndices.browse-ors-view');
Route::get('/transaction_indices/print-or-transactions/{transactionIndexId}', [App\Http\Controllers\TransactionIndexController::class, 'printOrTransactions'])->name('transactionIndices.print-or-transactions');
Route::get('/transaction_indices/print-reconnection-collection/{transactionIndexId}', [App\Http\Controllers\TransactionIndexController::class, 'printOrReconnection'])->name('transactionIndices.print-reconnection-collection');
Route::get('/transaction_indices/or-maintenance', [App\Http\Controllers\TransactionIndexController::class, 'orMaintenance'])->name('transactionIndices.or-maintenance');
Route::get('/transaction_indices/update-or-number', [App\Http\Controllers\TransactionIndexController::class, 'updateORNumber'])->name('transactionIndices.update-or-number');
Route::resource('transactionIndices', App\Http\Controllers\TransactionIndexController::class);


Route::resource('transactionDetails', App\Http\Controllers\TransactionDetailsController::class);

Route::get('/paid_bills/search', [PaidBillsController::class, 'search'])->name('paidBills.search');
Route::get('/paid_bills/fetch-details', [PaidBillsController::class, 'fetchDetails'])->name('paidBills.fetch-details');
Route::get('/paid_bills/fetch-account', [PaidBillsController::class, 'fetchAccount'])->name('paidBills.fetch-account');
Route::get('/paid_bills/fetch-payable', [PaidBillsController::class, 'fetchPayable'])->name('paidBills.fetch-payable');
Route::get('/paid_bills/save-paid-bill-and-print', [PaidBillsController::class, 'savePaidBillAndPrint'])->name('paidBills.save-paid-bill-and-print');
Route::get('/paid_bills/print-bill-payment/{paidBillId}', [PaidBillsController::class, 'printBillPayment'])->name('paidBills.print-bill-payment');
Route::get('/paid_bills/or-cancellation', [PaidBillsController::class, 'orCancellation'])->name('paidBills.or-cancellation');
Route::get('/paid_bills/search-or', [PaidBillsController::class, 'searchOR'])->name('paidBills.search-or');
Route::get('/paid_bills/fetch-or-details', [PaidBillsController::class, 'fetchORDetails'])->name('paidBills.fetch-or-details');
Route::get('/paid_bills/request-cancel-or', [PaidBillsController::class, 'requestCancelOR'])->name('paidBills.request-cancel-or');
Route::get('/paid_bills/request-bills-payment-unlock', [PaidBillsController::class, 'requestBillsPaymentUnlock'])->name('paidBills.request-bills-payment-unlock');
Route::get('/paid_bills/bapa-payments', [PaidBillsController::class, 'bapaPayments'])->name('paidBills.bapa-payments');
Route::get('/paid_bills/search-bapa', [PaidBillsController::class, 'searchBapa'])->name('paidBills.search-bapa');
Route::get('/paid_bills/bapa-payment-console/{bapaName}', [PaidBillsController::class, 'bapaPaymentConsole'])->name('paidBills.bapa-payment-console');
Route::get('/paid_bills/get-bills-from-bapa', [PaidBillsController::class, 'getBillsFromBapa'])->name('paidBills.get-bills-from-bapa');
Route::get('/paid_bills/save-bapa-payments', [PaidBillsController::class, 'saveBapaPayments'])->name('paidBills.save-bapa-payments');
Route::get('/paid_bills/bills-collection', [PaidBillsController::class, 'billsCollection'])->name('paidBills.bills-collection');
Route::get('/paid_bills/get-adjusted-bapa-bills', [PaidBillsController::class, 'getAdjustedBapaBills'])->name('paidBills.get-adjusted-bapa-bills');
Route::get('/paid_bills/add-check-payments', [PaidBillsController::class, 'addCheckPayments'])->name('paidBills.add-check-payments');
Route::get('/paid_bills/delete-check-payment', [PaidBillsController::class, 'deleteCheckPayment'])->name('paidBills.delete-check-payment');
Route::get('/paid_bills/fetch-account-by-old-account-number', [PaidBillsController::class, 'fetchAccountByOldAccountNumber'])->name('paidBills.fetch-account-by-old-account-number');
Route::get('/paid_bills/print-bapa-payments/{dcrNum}', [PaidBillsController::class, 'printBapaPayments'])->name('paidBills.print-bapa-payments');
Route::get('/paid_bills/get-ors-from-range', [PaidBillsController::class, 'getORsFromRange'])->name('paidBills.get-ors-from-range');
Route::get('/paid_bills/add-denomination', [PaidBillsController::class, 'addDenomination'])->name('paidBills.add-denomination');
Route::get('/paid_bills/third-party-collection', [PaidBillsController::class, 'thirdPartyCollection'])->name('paidBills.third-party-collection');
Route::get('/paid_bills/upload-third-party-collection', [PaidBillsController::class, 'uploadThirdPartyCollection'])->name('paidBills.upload-third-party-collection');
Route::post('/paid_bills/validate-tpc-upload', [PaidBillsController::class, 'validateTpcUpload'])->name('paidBills.validate-tpc-upload');
Route::get('/paid_bills/tcp-upload-validator/{seriesNo}', [PaidBillsController::class, 'tcpUploadValidator'])->name('paidBills.tcp-upload-validator');
Route::get('/paid_bills/deposit-double-payments/{seriesNo}', [PaidBillsController::class, 'depositDoublePayments'])->name('paidBills.deposit-double-payments');
Route::get('/paid_bills/post-payments/{seriesNo}', [PaidBillsController::class, 'postPayments'])->name('paidBills.post-payments');
Route::get('/paid_bills/third-party-collection-dcr/{source}/{date}/{series}/{postingDate}', [PaidBillsController::class, 'thirdPartyCollectionDCR'])->name('paidBills.third-party-collection-dcr');
Route::get('/paid_bills/clear-all-tcp-uploads', [PaidBillsController::class, 'clearAllTcpUploads'])->name('paidBills.clear-all-tcp-uploads');
Route::get('/paid_bills/cancel-or-admin', [PaidBillsController::class, 'cancelORAdmin'])->name('paidBills.cancel-or-admin');
Route::get('/paid_bills/collection-summary-report', [PaidBillsController::class, 'collectionSummaryReport'])->name('paidBills.collection-summary-report');
Route::get('/paid_bills/print-collection-summary-report/{from}/{to}/{town}', [PaidBillsController::class, 'printCollectionSummaryReport'])->name('paidBills.print-collection-summary-report');
Route::get('/paid_bills/aging-report', [PaidBillsController::class, 'agingReport'])->name('paidBills.aging-report');
Route::get('/paid_bills/print-aging-report/{town}/{asOf}', [PaidBillsController::class, 'printAgingReport'])->name('paidBills.print-aging-report');
Route::get('/paid_bills/third-party-report', [PaidBillsController::class, 'thirdPartyReport'])->name('paidBills.third-party-report');
Route::get('/paid_bills/print-third-party-report/{day}/{town}', [PaidBillsController::class, 'printThirdPartyReport'])->name('paidBills.print-third-party-report');
Route::get('/paid_bills/third-party-api-console', [PaidBillsController::class, 'thirdPartyAPIConsole'])->name('paidBills.third-party-api-console');
Route::get('/paid_bills/third-party-collection-api-dcr', [PaidBillsController::class, 'thirdPartyCollectionAPIDCR'])->name('paidBills.third-party-collection-api-dcr');
Route::get('/paid_bills/clear-deposit', [PaidBillsController::class, 'clearDeposit'])->name('paidBills.clear-deposit');
Route::get('/paid_bills/fix-third-party-dcr', [PaidBillsController::class, 'fixThirdPartyDCR'])->name('paidBills.fix-third-party-dcr');
Route::resource('paidBills', PaidBillsController::class);

Route::get('/disconnection_histories/generate-turn-off-list', [App\Http\Controllers\DisconnectionHistoryController::class, 'generateTurnOffList'])->name('disconnectionHistories.generate-turn-off-list');
Route::get('/disconnection_histories/get-turn-off-list-preview', [App\Http\Controllers\DisconnectionHistoryController::class, 'getTurnOffListPreview'])->name('disconnectionHistories.get-turn-off-list-preview');
Route::get('/disconnection_histories/get-turn-off-list-preview-route', [App\Http\Controllers\DisconnectionHistoryController::class, 'getTurnOffListPreviewRoute'])->name('disconnectionHistories.get-turn-off-list-preview-route');
Route::get('/disconnection_histories/print-turn-off-list/{period}/{area}/{meterReader}/{day}', [App\Http\Controllers\DisconnectionHistoryController::class, 'printTurnOffList'])->name('disconnectionHistories.print-turn-off-list');
Route::get('/disconnection_histories/print-turn-off-list-route/{period}/{area}/{route}', [App\Http\Controllers\DisconnectionHistoryController::class, 'printTurnOffListRoute'])->name('disconnectionHistories.print-turn-off-list-route');
Route::resource('disconnectionHistories', App\Http\Controllers\DisconnectionHistoryController::class);

Route::get('/disco_notice_histories/generate-nod', [App\Http\Controllers\DiscoNoticeHistoryController::class, 'generateNod'])->name('discoNoticeHistories.generate-nod');
Route::get('/disco_notice_histories/get-disco-list-preview', [App\Http\Controllers\DiscoNoticeHistoryController::class, 'getDiscoListPreview'])->name('discoNoticeHistories.get-disco-list-preview');
Route::get('/disco_notice_histories/print-reroute', [App\Http\Controllers\DiscoNoticeHistoryController::class, 'printReroute'])->name('discoNoticeHistories.print-reroute');
Route::get('/disco_notice_histories/get-disco-list-preview-route', [App\Http\Controllers\DiscoNoticeHistoryController::class, 'getDiscoListPreviewRoute'])->name('discoNoticeHistories.get-disco-list-preview-route');
Route::get('/disco_notice_histories/print-disconnection-list/{period}/{area}/{meterReader}/{day}', [App\Http\Controllers\DiscoNoticeHistoryController::class, 'printDisconnectionList'])->name('discoNoticeHistories.print-disconnection-list');
Route::get('/disco_notice_histories/print-disconnection-list-route/{period}/{area}/{route}', [App\Http\Controllers\DiscoNoticeHistoryController::class, 'printDisconnectionListRoute'])->name('discoNoticeHistories.print-disconnection-list-route');
Route::resource('discoNoticeHistories', App\Http\Controllers\DiscoNoticeHistoryController::class);


Route::resource('accountPayables', App\Http\Controllers\AccountPayablesController::class);


Route::get('/cache_other_payments/fetch-cached', [App\Http\Controllers\CacheOtherPaymentsController::class, 'fetchCached'])->name('cacheOtherPayments.fetch-cached');
Route::get('/cache_other_payments/save-other-payments', [App\Http\Controllers\CacheOtherPaymentsController::class, 'saveOtherPayments'])->name('cacheOtherPayments.save-other-payments');
Route::resource('cacheOtherPayments', App\Http\Controllers\CacheOtherPaymentsController::class);


Route::get('/pending_bill_adjustments/open-reading-adjustments/{servicePeriod}', [App\Http\Controllers\PendingBillAdjustmentsController::class, 'openReadingAdjustments'])->name('pendingBillAdjustments.open-reading-adjustments');
Route::get('/pending_bill_adjustments/confirm-all-adjustments/{servicePeriod}', [App\Http\Controllers\PendingBillAdjustmentsController::class, 'confirmAllAdjustments'])->name('pendingBillAdjustments.confirm-all-adjustments');
Route::get('/pending_bill_adjustments/confirm-adjustment/{pendingAdjustmentId}', [App\Http\Controllers\PendingBillAdjustmentsController::class, 'confirmAdjustment'])->name('pendingBillAdjustments.confirm-adjustment');
Route::resource('pendingBillAdjustments', App\Http\Controllers\PendingBillAdjustmentsController::class);


Route::get('/o_r_assignings/get-last-or', [App\Http\Controllers\ORAssigningController::class, 'getLastOR'])->name('oRAssignings.get-last-or');
Route::get('/o_r_assignings/get-next-or', [App\Http\Controllers\ORAssigningController::class, 'getNextOR'])->name('oRAssignings.get-next-or');
Route::resource('oRAssignings', App\Http\Controllers\ORAssigningController::class);


Route::post('/kwh_sales/generate-new/', [App\Http\Controllers\KwhSalesController::class, 'generateNew'])->name('kwhSales.generate-new');
Route::post('/kwh_sales/save-sales-report', [App\Http\Controllers\KwhSalesController::class, 'saveSalesReport'])->name('kwhSales.save-sales-report');
Route::get('/kwh_sales/view-sales/{id}', [App\Http\Controllers\KwhSalesController::class, 'viewSales'])->name('kwhSales.view-sales');
Route::get('/kwh_sales/print-report/{id}', [App\Http\Controllers\KwhSalesController::class, 'printReport'])->name('kwhSales.print-report');
Route::get('/kwh_sales/sales-distribution', [App\Http\Controllers\KwhSalesController::class, 'salesDistribution'])->name('kwhSales.sales-distribution');
Route::get('/kwh_sales/sales-distribution-view/{period}', [App\Http\Controllers\KwhSalesController::class, 'salesDistributionView'])->name('kwhSales.sales-distribution-view');
Route::get('/kwh_sales/consolidated-per-town/{period}', [App\Http\Controllers\KwhSalesController::class, 'consolidatedPerTown'])->name('kwhSales.consolidated-per-town');
Route::get('/kwh_sales/summary-of-sales/{period}', [App\Http\Controllers\KwhSalesController::class, 'summaryOfSales'])->name('kwhSales.summary-of-sales');
Route::get('/kwh_sales/print-summary-of-sales/{period}', [App\Http\Controllers\KwhSalesController::class, 'printSummaryOfSales'])->name('kwhSales.print-summary-of-sales');
Route::get('/kwh_sales/dashboard-get-annual-sales-graph', [App\Http\Controllers\KwhSalesController::class, 'dashboardGetAnnualSalesGraph'])->name('kwhSales.dashboard-get-annual-sales-graph');
Route::get('/kwh_sales/dashboard-get-annual-sales-pie-graph', [App\Http\Controllers\KwhSalesController::class, 'dashboardGetAnnualSalesPieGraph'])->name('kwhSales.dashboard-get-annual-sales-pie-graph');
Route::get('/kwh_sales/kwh-sales-expanded', [App\Http\Controllers\KwhSalesController::class, 'kwhSalesExpanded'])->name('kwhSales.kwh-sales-expanded');
Route::get('/kwh_sales/kwh-sales-expanded-view/{route}/{town}/{period}', [App\Http\Controllers\KwhSalesController::class, 'kwhSalesExpandedView'])->name('kwhSales.kwh-sales-expanded-view');
Route::get('/kwh_sales/download-kwh-sales-expanded/{period}/{town}', [App\Http\Controllers\KwhSalesController::class, 'downloadKwhSalesExpanded'])->name('kwhSales.download-kwh-sales-expanded');
Route::get('/kwh_sales/download-merged-sales/{period}', [App\Http\Controllers\KwhSalesController::class, 'downloadMergedSales'])->name('kwhSales.download-merged-sales');
Route::get('/kwh_sales/download-summary-per-consumer-type/{period}', [App\Http\Controllers\KwhSalesController::class, 'downloadSummaryPerConsumerType'])->name('kwhSales.download-summary-per-consumer-type');
Route::get('/kwh_sales/download-consolidated-per-district/{period}', [App\Http\Controllers\KwhSalesController::class, 'downloadConsolidatedPerDistrict'])->name('kwhSales.download-consolidated-per-district');
Route::get('/kwh_sales/validate-confirm-user', [App\Http\Controllers\KwhSalesController::class, 'validateConfirmUser'])->name('kwhSales.validate-confirm-user');
Route::resource('kwhSales', App\Http\Controllers\KwhSalesController::class);


Route::get('/pre_payment_balances/search', [App\Http\Controllers\PrePaymentBalanceController::class, 'search'])->name('prePaymentBalances.search');
Route::get('/pre_payment_balances/get-balance-details', [App\Http\Controllers\PrePaymentBalanceController::class, 'getBalanceDetails'])->name('prePaymentBalances.get-balance-details');
Route::resource('prePaymentBalances', App\Http\Controllers\PrePaymentBalanceController::class);


Route::resource('prePaymentTransHistories', App\Http\Controllers\PrePaymentTransHistoryController::class);


Route::get('/notifiers/get-notifications', [App\Http\Controllers\NotifiersController::class, 'getNotifications'])->name('notifiers.get-notifications');
Route::resource('notifiers', App\Http\Controllers\NotifiersController::class);


Route::get('/o_r_cancellations/approve-bills-or-cancellation/{orCancellationId}', [App\Http\Controllers\ORCancellationsController::class, 'approveBillsORCancellation'])->name('oRCancellations.approve-bills-or-cancellation');
Route::get('/o_r_cancellations/other-payments', [App\Http\Controllers\ORCancellationsController::class, 'otherPaymentsORCancellation'])->name('oRCancellations.other-payments');
Route::get('/o_r_cancellations/fetch-transaction-indices', [App\Http\Controllers\ORCancellationsController::class, 'fetchTransactionIndices'])->name('oRCancellations.fetch-transaction-indices');
Route::get('/o_r_cancellations/fetch-transaction-details', [App\Http\Controllers\ORCancellationsController::class, 'fetchTransactionDetails'])->name('oRCancellations.fetch-transaction-details');
Route::get('/o_r_cancellations/fetch-transaction-particulars', [App\Http\Controllers\ORCancellationsController::class, 'fetchParticulars'])->name('oRCancellations.fetch-transaction-particulars');
Route::get('/o_r_cancellations/attempt-cancel-transaction-or', [App\Http\Controllers\ORCancellationsController::class, 'attemptCancelTransactionOR'])->name('oRCancellations.attempt-cancel-transaction-or');
Route::get('/o_r_cancellations/show-other-payments/{id}', [App\Http\Controllers\ORCancellationsController::class, 'showOtherPayments'])->name('oRCancellations.show-other-payments');
Route::get('/o_r_cancellations/approve-transaction-cancellation/{id}', [App\Http\Controllers\ORCancellationsController::class, 'approveTransactionCancellation'])->name('oRCancellations.approve-transaction-cancellation');
Route::resource('oRCancellations', App\Http\Controllers\ORCancellationsController::class);


Route::get('/b_a_p_a_reading_schedules/show-schedules/{period}', [App\Http\Controllers\BAPAReadingSchedulesController::class, 'showSchedules'])->name('bAPAReadingSchedules.show-schedules');
Route::get('/b_a_p_a_reading_schedules/add-schedule', [App\Http\Controllers\BAPAReadingSchedulesController::class, 'addSchedule'])->name('bAPAReadingSchedules.add-schedule');
Route::get('/b_a_p_a_reading_schedules/get-bapas', [App\Http\Controllers\BAPAReadingSchedulesController::class, 'getBapas'])->name('bAPAReadingSchedules.get-bapas');
Route::get('/b_a_p_a_reading_schedules/remove-bapa-from-sched', [App\Http\Controllers\BAPAReadingSchedulesController::class, 'removeBapaFromSched'])->name('bAPAReadingSchedules.remove-bapa-from-sched');
Route::get('/b_a_p_a_reading_schedules/remove-downloaded-status-from-bapa', [App\Http\Controllers\BAPAReadingSchedulesController::class, 'removeDownloadedStatusFromBapa'])->name('bAPAReadingSchedules.remove-downloaded-status-from-bapa');
Route::resource('bAPAReadingSchedules', App\Http\Controllers\BAPAReadingSchedulesController::class);


Route::resource('bAPAPayments', App\Http\Controllers\BAPAPaymentsController::class);


Route::resource('distributionSystemLosses', App\Http\Controllers\DistributionSystemLossController::class);


Route::resource('rateItems', App\Http\Controllers\RateItemsController::class);


Route::resource('changeMeterLogs', App\Http\Controllers\ChangeMeterLogsController::class);


Route::resource('accountGLCodes', App\Http\Controllers\AccountGLCodesController::class);


Route::resource('dCRSummaryTransactions', App\Http\Controllers\DCRSummaryTransactionsController::class);
Route::get('/d_c_r_summary_transactions/sales-dcr-monitor', [App\Http\Controllers\DCRSummaryTransactionsController::class, 'salesDcrMonitor'])->name('dCRSummaryTransactions.sales-dcr-monitor');
Route::get('/d_c_r_summary_transactions/print-dcr/{teller}/{day}', [App\Http\Controllers\DCRSummaryTransactionsController::class, 'printDcr'])->name('dCRSummaryTransactions.print-dcr');
Route::get('/d_c_r_summary_transactions/dashboard', [App\Http\Controllers\DCRSummaryTransactionsController::class, 'collectionDashboard'])->name('dCRSummaryTransactions.dashboard');
Route::get('/d_c_r_summary_transactions/get-collection-per-area', [App\Http\Controllers\DCRSummaryTransactionsController::class, 'dashboardGetCollectionPerArea'])->name('dCRSummaryTransactions.get-collection-per-area');
Route::get('/d_c_r_summary_transactions/collection-office-expand/{office}', [App\Http\Controllers\DCRSummaryTransactionsController::class, 'collectionOfficeEpand'])->name('dCRSummaryTransactions.collection-office-expand');
Route::get('/d_c_r_summary_transactions/get-gl-code-payment-details', [App\Http\Controllers\DCRSummaryTransactionsController::class, 'getGLCodePaymentDetails'])->name('dCRSummaryTransactions.get-gl-code-payment-details');
Route::get('/d_c_r_summary_transactions/application-dcr-summary', [App\Http\Controllers\DCRSummaryTransactionsController::class, 'applicationDcrSummary'])->name('dCRSummaryTransactions.application-dcr-summary');
Route::get('/d_c_r_summary_transactions/fix-dcr', [App\Http\Controllers\DCRSummaryTransactionsController::class, 'fixDcr'])->name('dCRSummaryTransactions.fix-dcr');
Route::get('/d_c_r_summary_transactions/get-gl-code-payment-details-api', [App\Http\Controllers\DCRSummaryTransactionsController::class, 'getGLCodePaymentDetailsApi'])->name('dCRSummaryTransactions.get-gl-code-payment-details-api');


Route::resource('banks', App\Http\Controllers\BanksController::class);


Route::get('/b_a_p_a_adjustments/adjust-bapa/{bapaName}', [App\Http\Controllers\BAPAAdjustmentsController::class, 'adjustBapaPayments'])->name('bAPAAdjustments.adjust-bapa');
Route::get('/b_a_p_a_adjustments/search-bapa', [App\Http\Controllers\BAPAAdjustmentsController::class, 'searchBapa'])->name('bAPAAdjustments.search-bapa');
Route::get('/b_a_p_a_adjustments/save-bapa-adjustments', [App\Http\Controllers\BAPAAdjustmentsController::class, 'saveBapaAdjustments'])->name('bAPAAdjustments.save-bapa-adjustments');
Route::get('/b_a_p_a_adjustments/search-bapa-monitor', [App\Http\Controllers\BAPAAdjustmentsController::class, 'searchBapaMonitor'])->name('bAPAAdjustments.search-bapa-monitor');
Route::get('/b_a_p_a_adjustments/get-bapa-monitor-search-results', [App\Http\Controllers\BAPAAdjustmentsController::class, 'getBapaMonitorSearchResults'])->name('bAPAAdjustments.get-bapa-monitor-search-results');
Route::get('/b_a_p_a_adjustments/bapa-collection-monitor-console/{bapaName}', [App\Http\Controllers\BAPAAdjustmentsController::class, 'bapaCollectionMonitorConsole'])->name('bAPAAdjustments.bapa-collection-monitor-console');
Route::get('/b_a_p_a_adjustments/print-voucher/{representative}/{bapaName}/{period}/{discount}/{dateAdjusted}', [App\Http\Controllers\BAPAAdjustmentsController::class, 'printVoucher'])->name('bAPAAdjustments.print-voucher');
Route::get('/b_a_p_a_adjustments/update-bapa-adjustment', [App\Http\Controllers\BAPAAdjustmentsController::class, 'updateBapaPercentage'])->name('bAPAAdjustments.update-bapa-adjustment');
Route::get('/b_a_p_a_adjustments/delete-bapa-adjustment', [App\Http\Controllers\BAPAAdjustmentsController::class, 'deleteBapaPercentage'])->name('bAPAAdjustments.delete-bapa-adjustment');
Route::get('/b_a_p_a_adjustments/remove-account-from-voucher', [App\Http\Controllers\BAPAAdjustmentsController::class, 'removeAccountFromVoucher'])->name('bAPAAdjustments.remove-account-from-voucher');
Route::resource('bAPAAdjustments', App\Http\Controllers\BAPAAdjustmentsController::class);


Route::resource('bAPAAdjustmentDetails', App\Http\Controllers\BAPAAdjustmentDetailsController::class);


Route::get('/paidBillsDetails/delete-payment-details', [App\Http\Controllers\PaidBillsDetailsController::class, 'deletePaymentDetails'])->name('paidBillsDetails.delete-payment-details');
Route::resource('paidBillsDetails', App\Http\Controllers\PaidBillsDetailsController::class);


Route::resource('transacionPaymentDetails', App\Http\Controllers\TransacionPaymentDetailsController::class);


Route::resource('billsOriginals', App\Http\Controllers\BillsOriginalController::class);


Route::resource('accountNameHistories', App\Http\Controllers\AccountNameHistoryController::class);


Route::resource('mastPoles', App\Http\Controllers\MastPolesController::class);


Route::resource('dCRIndices', App\Http\Controllers\DCRIndexController::class);


Route::resource('accountLocationHistories', App\Http\Controllers\AccountLocationHistoryController::class);


Route::resource('denominations', App\Http\Controllers\DenominationsController::class);


Route::get('/excemptions/new-excemptions', [App\Http\Controllers\ExcemptionsController::class, 'newExcemption'])->name('excemptions.new-excemptions');
Route::get('/excemptions/search-account-excemption', [App\Http\Controllers\ExcemptionsController::class, 'searchAccountExcemption'])->name('excemptions.search-account-excemption');
Route::get('/excemptions/add-excemption', [App\Http\Controllers\ExcemptionsController::class, 'addExcemption'])->name('excemptions.add-excemption');
Route::get('/excemptions/get-excemptions-ajax', [App\Http\Controllers\ExcemptionsController::class, 'getExcemptionsAjax'])->name('excemptions.get-excemptions-ajax');
Route::get('/excemptions/remove-excemption', [App\Http\Controllers\ExcemptionsController::class, 'removeExcemption'])->name('excemptions.remove-excemption');
Route::get('/excemptions/print-excemptions/{town}', [App\Http\Controllers\ExcemptionsController::class, 'printExcemptions'])->name('excemptions.print-excemptions');
Route::resource('excemptions', App\Http\Controllers\ExcemptionsController::class);


Route::get('/katas_ng_vats/add-katas/{series}', [App\Http\Controllers\KatasNgVatController::class, 'addKatas'])->name('katasNgVats.add-katas');
Route::get('/katas_ng_vats/add-account-to-katas', [App\Http\Controllers\KatasNgVatController::class, 'addAccountToKatas'])->name('katasNgVats.add-account-to-katas');
Route::get('/katas_ng_vats/search-account', [App\Http\Controllers\KatasNgVatController::class, 'searchAccount'])->name('katasNgVats.search-account');
Route::get('/katas_ng_vats/fetch-katas', [App\Http\Controllers\KatasNgVatController::class, 'fetchKatas'])->name('katasNgVats.fetch-katas');
Route::get('/katas_ng_vats/delete-katas', [App\Http\Controllers\KatasNgVatController::class, 'deleteKatas'])->name('katasNgVats.delete-katas');
Route::resource('katasNgVats', App\Http\Controllers\KatasNgVatController::class);


Route::resource('katasNgVatTotals', App\Http\Controllers\KatasNgVatTotalController::class);

Route::get('/third_party_tokens/regenerate-token/{id}', [App\Http\Controllers\ThirdPartyTokensController::class, 'regenerateToken'])->name('thirdPartyTokens.regenerate-token');
Route::resource('thirdPartyTokens', App\Http\Controllers\ThirdPartyTokensController::class);


Route::resource('events', App\Http\Controllers\EventsController::class);

Route::get('/event_attendees/add-attendees/{eventId}', [App\Http\Controllers\EventAttendeesController::class, 'addAttendees'])->name('eventAttendees.add-attendees');
Route::get('/event_attendees/search-account-for-attendees', [App\Http\Controllers\EventAttendeesController::class, 'searchAccountForAttendees'])->name('eventAttendees.search-account-for-attendees');
Route::get('/event_attendees/add-attendance', [App\Http\Controllers\EventAttendeesController::class, 'addAttendance'])->name('eventAttendees.add-attendance');
Route::get('/event_attendees/get-attendees', [App\Http\Controllers\EventAttendeesController::class, 'getAttendees'])->name('eventAttendees.get-attendees');
Route::get('/event_attendees/delete', [App\Http\Controllers\EventAttendeesController::class, 'delete'])->name('eventAttendees.delete');
Route::get('/event_attendees/add-walkin', [App\Http\Controllers\EventAttendeesController::class, 'addWalkin'])->name('eventAttendees.add-walkin');
Route::resource('eventAttendees', App\Http\Controllers\EventAttendeesController::class);


Route::resource('signatories', App\Http\Controllers\SignatoriesController::class);

Route::get('/demand_letters/per-account/{acctNo}/{asOf}', [App\Http\Controllers\DemandLettersController::class, 'perAccount'])->name('demandLetters.per-account');
Route::get('/demand_letters/print-per-account/{acctNo}/{asOf}', [App\Http\Controllers\DemandLettersController::class, 'printPerAccount'])->name('demandLetters.print-per-account');
Route::get('/demand_letters/search-account-for-demand-letter', [App\Http\Controllers\DemandLettersController::class, 'searchAccountForDemandLetter'])->name('demandLetters.search-account-for-demand-letter');
Route::get('/demand_letters/per-route/{route}/{asOf}/{town}', [App\Http\Controllers\DemandLettersController::class, 'perRoute'])->name('demandLetters.per-route');
Route::get('/demand_letters/search-route', [App\Http\Controllers\DemandLettersController::class, 'searchRoute'])->name('demandLetters.search-route');
Route::get('/demand_letters/print-per-route/{route}/{asOf}/{town}', [App\Http\Controllers\DemandLettersController::class, 'printPerRoute'])->name('demandLetters.print-per-route');
Route::resource('demandLetters', App\Http\Controllers\DemandLettersController::class);


Route::resource('demandLetterMonths', App\Http\Controllers\DemandLetterMonthsController::class);
