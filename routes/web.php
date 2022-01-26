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

Route::resource('users', UsersController::class);

Route::resource('roles', App\Http\Controllers\RoleController::class);

Route::resource('permissions', App\Http\Controllers\PermissionController::class);


Route::get('/member_consumers/assess_checklists/{id}', [MemberConsumersController::class, 'assessChecklists'])->name('memberConsumers.assess-checklists');
Route::get('/member_consumers/fetchmemberconsumer', [MemberConsumersController::class, 'fetchmemberconsumer'])->name('memberConsumers.fetch-member-consumers');
Route::get('/member_consumers/capture-image/{id}', [MemberConsumersController::class, 'captureImage'])->name('memberConsumers.capture-image');
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


Route::resource('readings', App\Http\Controllers\ReadingsController::class);
