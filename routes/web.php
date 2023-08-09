<?php
/********************************* ROUTE CLASSESS ************************************************/

use App\Http\Controllers\CurrenciesController;
use App\Http\Controllers\EmployeeSalariesController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\CommunalExpensesController;
use App\Http\Controllers\FashionsController;
use App\Http\Controllers\DepartmentsController;
use App\Http\Controllers\EmployeesController;
use App\Http\Controllers\FinishedProductsController;
use App\Http\Controllers\JobTitlesController;
use App\Http\Controllers\ProductCategoriesController;
use App\Http\Controllers\RawMaterialsController;
use App\Http\Controllers\ProductionController;

use App\Http\Controllers\reports\EmployeesReportController;
use App\Http\Controllers\reports\ExpensesReportController;
use App\Http\Controllers\reports\DepartmentFinishedProductsReportController;
use App\Http\Controllers\reports\DepartmentRawMaterialsReportController;

use Illuminate\Support\Facades\Route;

/********************************* WEB ROUTES ************************************************/


/********************************* REPROTS ************************************************/
//reports
Route::post('app/department_rawmaterial_report', [DepartmentRawMaterialsReportController::class, 'departmentRawMaterialReport']);
Route::post('app/department_finished_products_report', [DepartmentFinishedProductsReportController::class, 'departmentFinishedProductsReport']);
Route::post('app/finished_products_inout_report', [DepartmentFinishedProductsReportController::class, 'finishedProductsInoutReport']);
Route::post('app/rawmaterials_inout_report', [DepartmentFinishedProductsReportController::class, 'rawMaterialsInoutReport']);

Route::post('app/get_monthly_communal_expenses_total', [ExpensesReportController::class, 'getMonthlyCommunalExpensesTotal']);
Route::post('app/get_monthly_other_expenses_total', [ExpensesReportController::class, 'getMonthlyOtherExpensesTotal']);
Route::post('app/communal_expenses_report', [ExpensesReportController::class, 'communalExpenses']);
Route::post('app/expenses_report', [ExpensesReportController::class, 'expenses']);
Route::post('app/employees_salary_report', [EmployeesReportController::class, 'salaryTotal']);


//Raw Materials
Route::get('app/get_rawmaterial/{raw_material_id}', [RawMaterialsController::class, 'getRawMaterial']);
Route::get('app/get_rmstock_products/{category_id}', [RawMaterialsController::class, 'getStockProducts']);
Route::get('app/get_rmsets', [RawMaterialsController::class, 'getSets']);
Route::get('app/get_rmset_products/{set_id}/{inout}', [RawMaterialsController::class, 'getSetProducts']);
Route::get('app/get_products/{department_id}', [RawMaterialsController::class, 'getProducts']);
Route::get('app/get_measurements', [RawMaterialsController::class, 'getMeasurements']);
Route::get('app/get_rm_rawmaterials/{department_id}', [RawMaterialsController::class, 'getDepartmentRawMaterials']);
Route::get('app/get_rm_rawmaterial_balance/{raw_material_balance_id}', [RawMaterialsController::class, 'getRawMaterialByBalanceId']);
Route::get('app/get_rm_rawmaterial/{raw_material_id}/{department_id}/{set_id}', [RawMaterialsController::class, 'getDepartmentRawMaterial']);
Route::get('app/get_rm_rawmaterial_details/{raw_material_details_id}', [RawMaterialsController::class, 'getRawMaterialDetails']);
Route::get('app/get_rm_set/{id}', [RawMaterialsController::class, 'getSet']);
Route::get('app/get_rm_semi_finish_products_stock/{department_id}', [RawMaterialsController::class, 'getSemiFinishStockProducts']);
Route::get('app/get_rm_semi_finish_products/{department_id}', [RawMaterialsController::class, 'getSemiFinishProducts']);
Route::get('app/get_rm_semi_finish_products_sets/{department_id}', [RawMaterialsController::class, 'getSemiFinishProductsSets']);
Route::get('app/get_rm_semi_finish_products_set/{department_id}/{id}', [RawMaterialsController::class, 'getSemiFinishProductsSet']);

Route::post('app/add_rmset_income_products', [RawMaterialsController::class, 'addIncomeProducts']);
Route::post('app/add_rmset_outgoing_products', [RawMaterialsController::class, 'addOutgoingProducts']);
Route::post('app/edit_rm_out_set', [RawMaterialsController::class, 'editOutSet']);
Route::post('app/delete_rm_in_set', [RawMaterialsController::class, 'deleteInSet']);
Route::post('app/delete_rm_out_set', [RawMaterialsController::class, 'deleteOutSet']);
Route::post('app/add_product', [RawMaterialsController::class, 'addProduct']);
Route::post('app/edit_product', [RawMaterialsController::class, 'editProduct']);
Route::post('app/delete_product', [RawMaterialsController::class, 'deleteProduct']);
Route::post('app/add_rawmaterial', [RawMaterialsController::class, 'addIncomeProduct']);
Route::post('app/edit_rawmaterial', [RawMaterialsController::class, 'editIncomeProduct']);
Route::post('app/delete_rawmaterial', [RawMaterialsController::class, 'deleteIncomeProduct']);
Route::post('app/add_rawmaterial_out', [RawMaterialsController::class, 'addOutgoingProduct']);
Route::post('app/edit_rawmaterial_out', [RawMaterialsController::class, 'editOutgoingProduct']);
Route::post('app/delete_rawmaterial_out', [RawMaterialsController::class, 'deleteOutgoingProduct']);
Route::post('app/delete_rm_semi_finish_products_out_set', [RawMaterialsController::class, 'deleteSemiFinishProductsOutSet']);
Route::post('app/edit_rm_semi_finish_product_out', [RawMaterialsController::class, 'editSemiFinishProductOut']);
Route::post('app/delete_rm_semi_finish_product_out', [RawMaterialsController::class, 'deleteSemiFinishProductOut']);
Route::post('app/add_rm_outgoing_semi_finish_products', [RawMaterialsController::class, 'addOutgoingSemiFinishProducts']);



//Production
Route::get('app/get_production_rm_stock/{department_id}', [ProductionController::class, 'getDepartmentRawMaterialsStock']);
Route::get('app/get_production_rawmaterial_sets/{department_id}/{inout?}', [ProductionController::class, 'getDepartmentRawMaterialSets']);
Route::get('app/get_production_finish_products_sets/{department_id}', [ProductionController::class, 'getDepartmentFinishProductsSets']);
Route::get('app/get_production_finish_products_stock/{department_id}', [ProductionController::class, 'getDepartmentFinishProductsStock']);
Route::get('app/get_production_department_rawmaterials/{department_id}', [ProductionController::class, 'getDepartmentRawMaterials']);
Route::get('app/get_production_department_finish_products/{department_id}', [ProductionController::class, 'getDepartmentFinishProducts']);
Route::get('app/get_production_department_products/{department_id}', [ProductionController::class, 'getDepartmentProducts']);
//Route::get('app/get_production_rawmaterial/{raw_material_id}/{department_id}', [ProductionController::class, 'getDepartmentRawMaterial']);
Route::get('app/get_production_rawmaterial_details/{raw_material_details_id}', [ProductionController::class, 'getRawMaterialDetails']);
Route::get('app/get_production_set_products/{set_id}/{inout}/{department_id}', [ProductionController::class, 'getSetProducts']);
Route::get('app/get_production_set/{department_id}/{id}', [ProductionController::class, 'getSet']);
Route::get('app/get_production_semi_finish_products_stock/{department_id}', [ProductionController::class, 'getDepartmentSemiFinishProductsStock']);
Route::get('app/get_production_semi_finish_products/{department_id}', [ProductionController::class, 'getDepartmentSemiFinishProducts']);
Route::get('app/get_production_semi_finish_products_sets/{department_id}', [ProductionController::class, 'getDepartmentSemiFinishProductsSets']);
Route::get('app/get_production_finish_product_rawmaterials/{raw_material_id}', [ProductionController::class, 'getFinishedProductRawMaterials']);

Route::post('app/get_production_income_products', [ProductionController::class, 'getIncomeProducts']);
Route::post('app/add_production_outgoing_products', [ProductionController::class, 'addOutgoingProducts']);
Route::post('app/add_production_product', [ProductionController::class, 'addProduct']);
Route::post('app/edit_production_product', [ProductionController::class, 'editProduct']);
Route::post('app/delete_production_product', [ProductionController::class, 'deleteProduct']);
Route::post('app/add_production_finish_product', [ProductionController::class, 'addFinishProduct']);
Route::post('app/add_production_rawmaterial_out', [ProductionController::class, 'addRawMaterialOutgoingProduct']);
Route::post('app/edit_production_rawmaterial_out', [ProductionController::class, 'editRawMaterialOutgoingProduct']);
Route::post('app/delete_production_rawmaterial_out', [ProductionController::class, 'deleteRawMaterialOutgoingProduct']);

Route::post('app/add_production_finish_product_out', [ProductionController::class, 'addFinishedOutgoingProduct']);
Route::post('app/edit_production_finish_product_in', [ProductionController::class, 'editFinishedIncomeProduct']);
Route::post('app/edit_production_finish_product_out', [ProductionController::class, 'editFinishedOutgoingProduct']);
Route::post('app/delete_production_finish_product_out', [ProductionController::class, 'deleteFinishedOutgoingProduct']);

Route::post('app/add_production_rawmaterial_out_set', [ProductionController::class, 'deleteRmOutSet']);
Route::post('app/edit_production_finish_product_out_set', [ProductionController::class, 'editFinishedProductOutSet']);
Route::post('app/delete_production_finish_product_in_set', [ProductionController::class, 'deleteFinishedProductInSet']);
Route::post('app/delete_production_finish_product_out_set', [ProductionController::class, 'deleteFinishedProductOutSet']);


//Finished Products
Route::get('app/get_fp_products_stock/{department_id}', [FinishedProductsController::class, 'getDepartmentFinishProductsStock']);
Route::get('app/get_fp_finish_products_sets/{department_id}', [FinishedProductsController::class, 'getDepartmentFinishProductsSets']);
Route::get('app/get_fp_finish_products/{department_id}', [FinishedProductsController::class, 'getFinishProducts']);

Route::post('app/add_fp_outgoing_finish_products', [FinishedProductsController::class, 'addOutgoingFinishProducts']);
Route::post('app/fp_finish_product_rawmaterials', [FinishedProductsController::class, 'finishedProductRawMaterials']);
Route::post('app/delete_fp_finish_products_out_set', [FinishedProductsController::class, 'deleteFinishProductsOutSet']);

Route::post('app/add_fp_finish_product_out', [FinishedProductsController::class, 'addOutgoingProduct']);
Route::post('app/edit_fp_finish_product_out', [FinishedProductsController::class, 'editOutgoingProduct']);
Route::post('app/delete_fp_finish_product_out', [FinishedProductsController::class, 'deleteOutgoingProduct']);

//Product Categories
Route::get('app/get_product_categories', [ProductCategoriesController::class, 'getCategories']);

//Fashion
Route::get('app/get_fashions',  [FashionsController::class, 'getFashions']);
Route::get('app/get_fashion/{id}',  [FashionsController::class, 'getFashion']);
Route::post('app/add_fashion',      [FashionsController::class, 'addFashion']);
Route::post('app/edit_fashion',     [FashionsController::class, 'editFashion']);
Route::post('app/delete_fashion',   [FashionsController::class, 'deleteFashion']);
Route::get('app/get_fashion_categories', [FashionsController::class, 'getCategories']);

//Communal Expenses
Route::get('app/get_communal_types', [CommunalExpensesController::class, 'getTypes']);
Route::get('app/get_communal_type/{communal_type_id}', [CommunalExpensesController::class, 'getType']);
Route::get('app/get_communal_devices/{communal_type_id}', [CommunalExpensesController::class, 'getCommunalDevices']);
Route::post('app/edit_device', [CommunalExpensesController::class, 'editDevice']);
Route::post('app/add_device', [CommunalExpensesController::class, 'addDevice']);
Route::post('app/delete_device', [CommunalExpensesController::class, 'deleteDevice']);
Route::get('app/get_devices_payments/{communal_type_id}/{device_id?}', [CommunalExpensesController::class, 'getDevicesPayments']);
Route::post('app/edit_communal_payment', [CommunalExpensesController::class, 'editPayment']);
Route::post('app/add_communal_payment', [CommunalExpensesController::class, 'addPayment']);
Route::post('app/delete_communal_payment', [CommunalExpensesController::class, 'deletePayment']);
Route::get('app/last_communal_payment/{communal_type_id}/{device_id}', [CommunalExpensesController::class, 'lastPayment']);
Route::post('app/prev_next_communal_payment', [CommunalExpensesController::class, 'prevNextPayments']);
Route::post('app/communal_report', [CommunalExpensesController::class, 'communalReport']);

//Expenses
Route::get ('app/get_expense_types', [ExpenseController::class, 'getExpenseTypes']);
Route::post('app/edit_expense_type', [ExpenseController::class, 'editExpenseType']);
Route::post('app/add_expense_type', [ExpenseController::class, 'addExpenseType']);
Route::post('app/delete_expense_type', [ExpenseController::class, 'deleteExpenseType']);
Route::get ('app/get_expenses', [ExpenseController::class, 'getExpenses']);
Route::post('app/add_expense', [ExpenseController::class, 'addExpense']);
Route::post('app/edit_expense', [ExpenseController::class, 'editExpense']);
Route::post('app/delete_expense', [ExpenseController::class, 'deleteExpense']);
Route::post('app/expense_total', [ExpenseController::class, 'expensesTotal']);

//Departments
Route::get('app/get_departments', [DepartmentsController::class, 'getDepartments']);
Route::get('app/get_departments/{type}',    [DepartmentsController::class, 'getDepartmentsByType']);
Route::post('app/get_departments_types',    [DepartmentsController::class, 'getDepartmentsByTypes']);

//Employees
Route::get ('app/get_employees', [EmployeesController::class, 'getAll']);
Route::get ('app/get_active_employees', [EmployeesController::class, 'getActiveEmployees']);
Route::get ('app/get_inactive_employees', [EmployeesController::class, 'getInactiveEmployees']);
Route::post('app/add_employee',         [EmployeesController::class, 'addEmployee']);
Route::post('app/edit_employee',        [EmployeesController::class, 'editEmployee']);
Route::post('app/delete_employee',      [EmployeesController::class, 'deleteEmployee']);
Route::post('app/change_employment_status', [EmployeesController::class, 'changeEmploymentStatus']);

//Job Titles
Route::get('app/get_jobtitles/{type?}', [JobTitlesController::class, 'getJobTitles']);

//Salaries
Route::post('app/get_employees_salaries', [EmployeeSalariesController::class, 'getSalaries']);
Route::post('app/get_monthly_salary_total', [EmployeeSalariesController::class, 'getMonthlySalaryTotal']);
Route::post('app/add_salary', [EmployeeSalariesController::class, 'addSalary']);

//Currencies
Route::get('app/add_currencies', [CurrenciesController::class, 'getCurrencies']);
Route::get('app/get_currency_rates/{currency_id}', [CurrenciesController::class, 'getCurrencyRates']);
Route::get('app/get_currency_rate_last/{currency_id}', [CurrenciesController::class, 'getLastCurrencyRate']);

Route::post('app/add_currency_rate', [CurrenciesController::class, 'addCurrencyRate']);
Route::post('app/edit_currency_rate', [CurrenciesController::class, 'updateCurrencyRate']);
Route::post('app/delete_currency_rate', [CurrenciesController::class, 'deleteCurrencyRate']);



Route::get('/{any}', function() { return view('welcome'); })->where('any', '(.*)');
