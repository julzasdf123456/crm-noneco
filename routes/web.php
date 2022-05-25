<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\ServiceConnectionsController;
use App\Http\Controllers\MemberConsumersController;

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
Route::get('/tickets/download-tickets-summary-report/{ticketParam}/{from}/{to}/{area}', [App\Http\Controllers\TicketsController::class, 'downloadTicketsSummaryReport'])->name('tickets.download-tickets-summary-report');
Route::get('/tickets/disconnection-assessments', [App\Http\Controllers\TicketsController::class, 'disconnectionAssessments'])->name('tickets.disconnection-assessments');
Route::get('/tickets/get-disconnection-results', [App\Http\Controllers\TicketsController::class, 'getDisconnectionResults'])->name('tickets.get-disconnection-results');
Route::get('/tickets/disconnection-results-route', [App\Http\Controllers\TicketsController::class, 'disconnectionResultsRoute'])->name('tickets.disconnection-results-route');
Route::get('/tickets/create-and-print-disconnection-tickets/{period}/{route}', [App\Http\Controllers\TicketsController::class, 'createAndPrintDisconnectionTickets'])->name('tickets.create-and-print-disconnection-tickets');
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
Route::resource('readingSchedules', App\Http\Controllers\ReadingSchedulesController::class);


Route::get('/rates/upload-rate', [App\Http\Controllers\RatesController::class, 'uploadRate'])->name('rates.upload-rate');
Route::post('/rates/validate-rate-upload', [App\Http\Controllers\RatesController::class, 'validateRateUpload'])->name('rates.validate-rate-upload');
Route::get('/rates/view-rates/{servicePeriod}', [App\Http\Controllers\RatesController::class, 'viewRates'])->name('rates.view-rates');
Route::post('/rates/delete-rates/{servicePeriod}', [App\Http\Controllers\RatesController::class, 'deleteRates'])->name('rates.delete-rates');
Route::resource('rates', App\Http\Controllers\RatesController::class);

Route::get('/readings/reading-monitor', [App\Http\Controllers\ReadingsController::class, 'readingMonitor'])->name('readings.reading-monitor');
Route::get('/readings/reading-monitor-view/{servicePeriod}', [App\Http\Controllers\ReadingsController::class, 'readingMonitorView'])->name('readings.reading-monitor-view');
Route::get('/readings/get-readings-from-meter-reader', [App\Http\Controllers\ReadingsController::class, 'getReadingsFromMeterReader'])->name('readings.get-readings-from-meter-reader');
Route::resource('readings', App\Http\Controllers\ReadingsController::class);

Route::get('/bills/unbilled-readings', [App\Http\Controllers\BillsController::class, 'unbilledReadings'])->name('bills.unbilled-readings');
Route::get('/bills/unbilled-readings-console/{servicePeriod}', [App\Http\Controllers\BillsController::class, 'unbilledReadingsConsole'])->name('bills.unbilled-readings-console');
Route::get('/bills/zero-readings-view/{readingId}', [App\Http\Controllers\BillsController::class, 'zeroReadingsView'])->name('bills.zero-readings-view');
Route::get('/bills/average-bill/{readingId}', [App\Http\Controllers\BillsController::class, 'averageBill'])->name('bills.average-bill');
Route::get('/bills/rebill-reading-adjustment/{readingId}', [App\Http\Controllers\BillsController::class, 'rebillReadingAdjustment'])->name('bills.rebill-reading-adjustment');
Route::post('/bills/rebill/{readingId}', [App\Http\Controllers\BillsController::class, 'rebill'])->name('bills.rebill');
Route::get('/bills/adjust-bill/{billId}', [App\Http\Controllers\BillsController::class, 'adjustBill'])->name('bills.adjust-bill');
Route::get('/bills/fetch-bill-adjustment-data', [App\Http\Controllers\BillsController::class, 'fetchBillAdjustmentData'])->name('bills.fetch-bill-adjustment-data');
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
Route::get('/bills/print-group-billing/{memberConsumerId}/{period}', [App\Http\Controllers\BillsController::class,  'printGroupBilling'])->name('bills.print-group-billing');
Route::get('/bills/print-single-bill-new-format/{billId}', [App\Http\Controllers\BillsController::class,  'printSingleBillNewFormat'])->name('bills.print-single-bill-new-format');
Route::get('/bills/print-single-bill-old/{billId}', [App\Http\Controllers\BillsController::class,  'printSingleBillOld'])->name('bills.print-single-bill-old');
Route::get('/bills/bulk-print-bill', [App\Http\Controllers\BillsController::class,  'bulkPrintBill'])->name('bills.bulk-print-bill');
Route::get('/bills/get-routes-from-town', [App\Http\Controllers\BillsController::class,  'getRoutesFromTown'])->name('bills.get-routes-from-town');
Route::get('/bills/print-bulk-bill-new-format/{period}/{town}/{route}', [App\Http\Controllers\BillsController::class,  'printBulkBillNewFormat'])->name('bills.print-bulk-bill-new-format');
Route::resource('bills', App\Http\Controllers\BillsController::class);


Route::resource('readingImages', App\Http\Controllers\ReadingImagesController::class);

Route::get('/collectibles/ledgerize', [App\Http\Controllers\CollectiblesController::class, 'ledgerize'])->name('collectibles.ledgerize');
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
Route::resource('transactionIndices', App\Http\Controllers\TransactionIndexController::class);


Route::resource('transactionDetails', App\Http\Controllers\TransactionDetailsController::class);

Route::get('/paid_bills/search', [App\Http\Controllers\PaidBillsController::class, 'search'])->name('paidBills.search');
Route::get('/paid_bills/fetch-details', [App\Http\Controllers\PaidBillsController::class, 'fetchDetails'])->name('paidBills.fetch-details');
Route::get('/paid_bills/fetch-account', [App\Http\Controllers\PaidBillsController::class, 'fetchAccount'])->name('paidBills.fetch-account');
Route::get('/paid_bills/fetch-payable', [App\Http\Controllers\PaidBillsController::class, 'fetchPayable'])->name('paidBills.fetch-payable');
Route::get('/paid_bills/save-paid-bill-and-print', [App\Http\Controllers\PaidBillsController::class, 'savePaidBillAndPrint'])->name('paidBills.save-paid-bill-and-print');
Route::get('/paid_bills/print-bill-payment/{paidBillId}', [App\Http\Controllers\PaidBillsController::class, 'printBillPayment'])->name('paidBills.print-bill-payment');
Route::get('/paid_bills/or-cancellation', [App\Http\Controllers\PaidBillsController::class, 'orCancellation'])->name('paidBills.or-cancellation');
Route::get('/paid_bills/search-or', [App\Http\Controllers\PaidBillsController::class, 'searchOR'])->name('paidBills.search-or');
Route::get('/paid_bills/fetch-or-details', [App\Http\Controllers\PaidBillsController::class, 'fetchORDetails'])->name('paidBills.fetch-or-details');
Route::get('/paid_bills/request-cancel-or', [App\Http\Controllers\PaidBillsController::class, 'requestCancelOR'])->name('paidBills.request-cancel-or');
Route::get('/paid_bills/request-bills-payment-unlock', [App\Http\Controllers\PaidBillsController::class, 'requestBillsPaymentUnlock'])->name('paidBills.request-bills-payment-unlock');
Route::get('/paid_bills/bapa-payments', [App\Http\Controllers\PaidBillsController::class, 'bapaPayments'])->name('paidBills.bapa-payments');
Route::get('/paid_bills/search-bapa', [App\Http\Controllers\PaidBillsController::class, 'searchBapa'])->name('paidBills.search-bapa');
Route::get('/paid_bills/bapa-payment-console/{bapaName}', [App\Http\Controllers\PaidBillsController::class, 'bapaPaymentConsole'])->name('paidBills.bapa-payment-console');
Route::get('/paid_bills/get-bills-from-bapa', [App\Http\Controllers\PaidBillsController::class, 'getBillsFromBapa'])->name('paidBills.get-bills-from-bapa');
Route::get('/paid_bills/save-bapa-payments', [App\Http\Controllers\PaidBillsController::class, 'saveBapaPayments'])->name('paidBills.save-bapa-payments');
Route::get('/paid_bills/bills-collection', [App\Http\Controllers\PaidBillsController::class, 'billsCollection'])->name('paidBills.bills-collection');
Route::resource('paidBills', App\Http\Controllers\PaidBillsController::class);


Route::resource('disconnectionHistories', App\Http\Controllers\DisconnectionHistoryController::class);

Route::get('/disco_notice_histories/generate-nod', [App\Http\Controllers\DiscoNoticeHistoryController::class, 'generateNod'])->name('discoNoticeHistories.generate-nod');
Route::get('/disco_notice_histories/get-disco-list-preview', [App\Http\Controllers\DiscoNoticeHistoryController::class, 'getDiscoListPreview'])->name('discoNoticeHistories.get-disco-list-preview');
Route::get('/disco_notice_histories/print-reroute', [App\Http\Controllers\DiscoNoticeHistoryController::class, 'printReroute'])->name('discoNoticeHistories.print-reroute');
Route::get('/disco_notice_histories/print-disconnection-list/{period}/{area}', [App\Http\Controllers\DiscoNoticeHistoryController::class, 'printDisconnectionList'])->name('discoNoticeHistories.print-disconnection-list');
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
Route::resource('bAPAReadingSchedules', App\Http\Controllers\BAPAReadingSchedulesController::class);


Route::resource('bAPAPayments', App\Http\Controllers\BAPAPaymentsController::class);


Route::resource('distributionSystemLosses', App\Http\Controllers\DistributionSystemLossController::class);


Route::resource('rateItems', App\Http\Controllers\RateItemsController::class);


Route::resource('changeMeterLogs', App\Http\Controllers\ChangeMeterLogsController::class);


Route::resource('accountGLCodes', App\Http\Controllers\AccountGLCodesController::class);


Route::resource('dCRSummaryTransactions', App\Http\Controllers\DCRSummaryTransactionsController::class);
Route::get('/d_c_r_summary_transactions/sales-dcr-monitor', [App\Http\Controllers\DCRSummaryTransactionsController::class, 'salesDcrMonitor'])->name('dCRSummaryTransactions.sales-dcr-monitor');


Route::resource('banks', App\Http\Controllers\BanksController::class);


Route::get('/b_a_p_a_adjustments/adjust-bapa/{bapaName}', [App\Http\Controllers\BAPAAdjustmentsController::class, 'adjustBapaPayments'])->name('bAPAAdjustments.adjust-bapa');
Route::get('/b_a_p_a_adjustments/search-bapa', [App\Http\Controllers\BAPAAdjustmentsController::class, 'searchBapa'])->name('bAPAAdjustments.search-bapa');
Route::get('/b_a_p_a_adjustments/save-bapa-adjustments', [App\Http\Controllers\BAPAAdjustmentsController::class, 'saveBapaAdjustments'])->name('bAPAAdjustments.save-bapa-adjustments');
Route::resource('bAPAAdjustments', App\Http\Controllers\BAPAAdjustmentsController::class);


Route::resource('bAPAAdjustmentDetails', App\Http\Controllers\BAPAAdjustmentDetailsController::class);
