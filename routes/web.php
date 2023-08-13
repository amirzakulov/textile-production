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

use App\Http\Controllers\UsersController;
use App\Http\Middleware\AdminCheck;
use Illuminate\Support\Facades\Route;

/********************************* REPROTS ************************************************/

Route::prefix('app')->middleware([AdminCheck::class])->group(function(){

    //reports
    Route::post('/department_rawmaterial_report', [DepartmentRawMaterialsReportController::class, 'departmentRawMaterialReport']);
    Route::post('/department_finished_products_report', [DepartmentFinishedProductsReportController::class, 'departmentFinishedProductsReport']);
    Route::post('/finished_products_inout_report', [DepartmentFinishedProductsReportController::class, 'finishedProductsInoutReport']);
    Route::post('/rawmaterials_inout_report', [DepartmentFinishedProductsReportController::class, 'rawMaterialsInoutReport']);

    Route::post('/get_monthly_communal_expenses_total', [ExpensesReportController::class, 'getMonthlyCommunalExpensesTotal']);
    Route::post('/get_monthly_other_expenses_total', [ExpensesReportController::class, 'getMonthlyOtherExpensesTotal']);
    Route::post('/communal_expenses_report', [ExpensesReportController::class, 'communalExpenses']);
    Route::post('/expenses_report', [ExpensesReportController::class, 'expenses']);
    Route::post('/employees_salary_report', [EmployeesReportController::class, 'salaryTotal']);


    //Raw Materials
    Route::get('/get_rawmaterial/{raw_material_id}', [RawMaterialsController::class, 'getRawMaterial']);
    Route::get('/get_rmstock_products/{category_id}', [RawMaterialsController::class, 'getStockProducts']);
    Route::get('/get_rmsets', [RawMaterialsController::class, 'getSets']);
    Route::get('/get_rmset_products/{set_id}/{inout}', [RawMaterialsController::class, 'getSetProducts']);
    Route::get('/get_products/{department_id}', [RawMaterialsController::class, 'getProducts']);
    Route::get('/get_measurements', [RawMaterialsController::class, 'getMeasurements']);
    Route::get('/get_rm_rawmaterials/{department_id}', [RawMaterialsController::class, 'getDepartmentRawMaterials']);
    Route::get('/get_rm_rawmaterial_balance/{raw_material_balance_id}', [RawMaterialsController::class, 'getRawMaterialByBalanceId']);
    Route::get('/get_rm_rawmaterial/{raw_material_id}/{department_id}/{set_id}', [RawMaterialsController::class, 'getDepartmentRawMaterial']);
    Route::get('/get_rm_rawmaterial_details/{raw_material_details_id}', [RawMaterialsController::class, 'getRawMaterialDetails']);
    Route::get('/get_rm_set/{id}', [RawMaterialsController::class, 'getSet']);
    Route::get('/get_rm_semi_finish_products_stock/{department_id}', [RawMaterialsController::class, 'getSemiFinishStockProducts']);
    Route::get('/get_rm_semi_finish_products/{department_id}', [RawMaterialsController::class, 'getSemiFinishProducts']);
    Route::get('/get_rm_semi_finish_products_sets/{department_id}', [RawMaterialsController::class, 'getSemiFinishProductsSets']);
    Route::get('/get_rm_semi_finish_products_set/{department_id}/{id}', [RawMaterialsController::class, 'getSemiFinishProductsSet']);

    Route::post('/add_rmset_income_products', [RawMaterialsController::class, 'addIncomeProducts']);
    Route::post('/add_rmset_outgoing_products', [RawMaterialsController::class, 'addOutgoingProducts']);
    Route::post('/edit_rm_out_set', [RawMaterialsController::class, 'editOutSet']);
    Route::post('/delete_rm_in_set', [RawMaterialsController::class, 'deleteInSet']);
    Route::post('/delete_rm_out_set', [RawMaterialsController::class, 'deleteOutSet']);
    Route::post('/add_product', [RawMaterialsController::class, 'addProduct']);
    Route::post('/edit_product', [RawMaterialsController::class, 'editProduct']);
    Route::post('/delete_product', [RawMaterialsController::class, 'deleteProduct']);
    Route::post('/add_rawmaterial', [RawMaterialsController::class, 'addIncomeProduct']);
    Route::post('/edit_rawmaterial', [RawMaterialsController::class, 'editIncomeProduct']);
    Route::post('/delete_rawmaterial', [RawMaterialsController::class, 'deleteIncomeProduct']);
    Route::post('/add_rawmaterial_out', [RawMaterialsController::class, 'addOutgoingProduct']);
    Route::post('/edit_rawmaterial_out', [RawMaterialsController::class, 'editOutgoingProduct']);
    Route::post('/delete_rawmaterial_out', [RawMaterialsController::class, 'deleteOutgoingProduct']);
    Route::post('/delete_rm_semi_finish_products_out_set', [RawMaterialsController::class, 'deleteSemiFinishProductsOutSet']);
    Route::post('/edit_rm_semi_finish_product_out', [RawMaterialsController::class, 'editSemiFinishProductOut']);
    Route::post('/delete_rm_semi_finish_product_out', [RawMaterialsController::class, 'deleteSemiFinishProductOut']);
    Route::post('/add_rm_outgoing_semi_finish_products', [RawMaterialsController::class, 'addOutgoingSemiFinishProducts']);



    //Production
    Route::get('/get_production_rm_stock/{department_id}', [ProductionController::class, 'getDepartmentRawMaterialsStock']);
    Route::get('/get_production_rawmaterial_sets/{department_id}/{inout?}', [ProductionController::class, 'getDepartmentRawMaterialSets']);
    Route::get('/get_production_finish_products_sets/{department_id}', [ProductionController::class, 'getDepartmentFinishProductsSets']);
    Route::get('/get_production_finish_products_stock/{department_id}', [ProductionController::class, 'getDepartmentFinishProductsStock']);
    Route::get('/get_production_department_rawmaterials/{department_id}', [ProductionController::class, 'getDepartmentRawMaterials']);
    Route::get('/get_production_department_finish_products/{department_id}', [ProductionController::class, 'getDepartmentFinishProducts']);
    Route::get('/get_production_department_products/{department_id}', [ProductionController::class, 'getDepartmentProducts']);
    //Route::get('/get_production_rawmaterial/{raw_material_id}/{department_id}', [ProductionController::class, 'getDepartmentRawMaterial']);
    Route::get('/get_production_rawmaterial_details/{raw_material_details_id}', [ProductionController::class, 'getRawMaterialDetails']);
    Route::get('/get_production_set_products/{set_id}/{inout}/{department_id}', [ProductionController::class, 'getSetProducts']);
    Route::get('/get_production_set/{department_id}/{id}', [ProductionController::class, 'getSet']);
    Route::get('/get_production_semi_finish_products_stock/{department_id}', [ProductionController::class, 'getDepartmentSemiFinishProductsStock']);
    Route::get('/get_production_semi_finish_products/{department_id}', [ProductionController::class, 'getDepartmentSemiFinishProducts']);
    Route::get('/get_production_semi_finish_products_sets/{department_id}', [ProductionController::class, 'getDepartmentSemiFinishProductsSets']);
    Route::get('/get_production_finish_product_rawmaterials/{raw_material_id}', [ProductionController::class, 'getFinishedProductRawMaterials']);

    Route::post('/get_production_income_products', [ProductionController::class, 'getIncomeProducts']);
    Route::post('/add_production_outgoing_products', [ProductionController::class, 'addOutgoingProducts']);
    Route::post('/add_production_product', [ProductionController::class, 'addProduct']);
    Route::post('/edit_production_product', [ProductionController::class, 'editProduct']);
    Route::post('/delete_production_product', [ProductionController::class, 'deleteProduct']);
    Route::post('/add_production_finish_product', [ProductionController::class, 'addFinishProduct']);
    Route::post('/add_production_rawmaterial_out', [ProductionController::class, 'addRawMaterialOutgoingProduct']);
    Route::post('/edit_production_rawmaterial_out', [ProductionController::class, 'editRawMaterialOutgoingProduct']);
    Route::post('/delete_production_rawmaterial_out', [ProductionController::class, 'deleteRawMaterialOutgoingProduct']);

    Route::post('/add_production_finish_product_out', [ProductionController::class, 'addFinishedOutgoingProduct']);
    Route::post('/edit_production_finish_product_in', [ProductionController::class, 'editFinishedIncomeProduct']);
    Route::post('/edit_production_finish_product_out', [ProductionController::class, 'editFinishedOutgoingProduct']);
    Route::post('/delete_production_finish_product_out', [ProductionController::class, 'deleteFinishedOutgoingProduct']);

    Route::post('/add_production_rawmaterial_out_set', [ProductionController::class, 'deleteRmOutSet']);
    Route::post('/edit_production_finish_product_out_set', [ProductionController::class, 'editFinishedProductOutSet']);
    Route::post('/delete_production_finish_product_in_set', [ProductionController::class, 'deleteFinishedProductInSet']);
    Route::post('/delete_production_finish_product_out_set', [ProductionController::class, 'deleteFinishedProductOutSet']);


    //Finished Products
    Route::get('/get_fp_products_stock/{department_id}', [FinishedProductsController::class, 'getDepartmentFinishProductsStock']);
    Route::get('/get_fp_finish_products_sets/{department_id}', [FinishedProductsController::class, 'getDepartmentFinishProductsSets']);
    Route::get('/get_fp_finish_products/{department_id}', [FinishedProductsController::class, 'getFinishProducts']);

    Route::post('/add_fp_outgoing_finish_products', [FinishedProductsController::class, 'addOutgoingFinishProducts']);
    Route::post('/fp_finish_product_rawmaterials', [FinishedProductsController::class, 'finishedProductRawMaterials']);
    Route::post('/delete_fp_finish_products_out_set', [FinishedProductsController::class, 'deleteFinishProductsOutSet']);

    Route::post('/add_fp_finish_product_out', [FinishedProductsController::class, 'addOutgoingProduct']);
    Route::post('/edit_fp_finish_product_out', [FinishedProductsController::class, 'editOutgoingProduct']);
    Route::post('/delete_fp_finish_product_out', [FinishedProductsController::class, 'deleteOutgoingProduct']);

    //Product Categories
    Route::get('/get_product_categories', [ProductCategoriesController::class, 'getCategories']);

    //Fashion
    Route::get('/get_fashions',  [FashionsController::class, 'getFashions']);
    Route::get('/get_fashion/{id}',  [FashionsController::class, 'getFashion']);
    Route::post('/add_fashion',      [FashionsController::class, 'addFashion']);
    Route::post('/edit_fashion',     [FashionsController::class, 'editFashion']);
    Route::post('/delete_fashion',   [FashionsController::class, 'deleteFashion']);
    Route::get('/get_fashion_categories', [FashionsController::class, 'getCategories']);

    Route::get('/get_fashion_details/{fashion_id}', [FashionsController::class, 'getFashionDetails']);
    Route::post('/add_fashion_detail', [FashionsController::class, 'addFashionDetail']);
    Route::post('/edit_fashion_detail', [FashionsController::class, 'editFashionDetail']);
    Route::post('/delete_fashion_detail', [FashionsController::class, 'deleteFashionDetail']);



    //Communal Expenses
    Route::get('/get_communal_types', [CommunalExpensesController::class, 'getTypes']);
    Route::get('/get_communal_type/{communal_type_id}', [CommunalExpensesController::class, 'getType']);
    Route::get('/get_communal_devices/{communal_type_id}', [CommunalExpensesController::class, 'getCommunalDevices']);
    Route::post('/edit_device', [CommunalExpensesController::class, 'editDevice']);
    Route::post('/add_device', [CommunalExpensesController::class, 'addDevice']);
    Route::post('/delete_device', [CommunalExpensesController::class, 'deleteDevice']);
    Route::get('/get_devices_payments/{communal_type_id}/{device_id?}', [CommunalExpensesController::class, 'getDevicesPayments']);
    Route::post('/edit_communal_payment', [CommunalExpensesController::class, 'editPayment']);
    Route::post('/add_communal_payment', [CommunalExpensesController::class, 'addPayment']);
    Route::post('/delete_communal_payment', [CommunalExpensesController::class, 'deletePayment']);
    Route::get('/last_communal_payment/{communal_type_id}/{device_id}', [CommunalExpensesController::class, 'lastPayment']);
    Route::post('/prev_next_communal_payment', [CommunalExpensesController::class, 'prevNextPayments']);
    Route::post('/communal_report', [CommunalExpensesController::class, 'communalReport']);

    //Expenses
    Route::get ('/get_expense_types', [ExpenseController::class, 'getExpenseTypes']);
    Route::post('/edit_expense_type', [ExpenseController::class, 'editExpenseType']);
    Route::post('/add_expense_type', [ExpenseController::class, 'addExpenseType']);
    Route::post('/delete_expense_type', [ExpenseController::class, 'deleteExpenseType']);
    Route::get ('/get_expenses', [ExpenseController::class, 'getExpenses']);
    Route::post('/add_expense', [ExpenseController::class, 'addExpense']);
    Route::post('/edit_expense', [ExpenseController::class, 'editExpense']);
    Route::post('/delete_expense', [ExpenseController::class, 'deleteExpense']);
    Route::post('/expense_total', [ExpenseController::class, 'expensesTotal']);

    //Departments
    Route::get('/get_departments', [DepartmentsController::class, 'getDepartments']);
    Route::get('/get_departments/{type}',    [DepartmentsController::class, 'getDepartmentsByType']);
    Route::post('/get_departments_types',    [DepartmentsController::class, 'getDepartmentsByTypes']);

    //Employees
    Route::get ('/get_employees', [EmployeesController::class, 'getAll']);
    Route::get ('/get_active_employees', [EmployeesController::class, 'getActiveEmployees']);
    Route::get ('/get_inactive_employees', [EmployeesController::class, 'getInactiveEmployees']);
    Route::post('/add_employee',         [EmployeesController::class, 'addEmployee']);
    Route::post('/edit_employee',        [EmployeesController::class, 'editEmployee']);
    Route::post('/delete_employee',      [EmployeesController::class, 'deleteEmployee']);
    Route::post('/change_employment_status', [EmployeesController::class, 'changeEmploymentStatus']);

    //Job Titles
    Route::get('/get_jobtitles/{type?}', [JobTitlesController::class, 'getJobTitles']);

    //Salaries
    Route::post('/get_employees_salaries', [EmployeeSalariesController::class, 'getSalaries']);
    Route::post('/get_monthly_salary_total', [EmployeeSalariesController::class, 'getMonthlySalaryTotal']);
    Route::post('/add_salary', [EmployeeSalariesController::class, 'addSalary']);

    //Currencies
    Route::get('/add_currencies', [CurrenciesController::class, 'getCurrencies']);
    Route::get('/get_currency_rates/{currency_id}', [CurrenciesController::class, 'getCurrencyRates']);
    Route::get('/get_currency_rate_last/{currency_id}', [CurrenciesController::class, 'getLastCurrencyRate']);

    Route::post('/add_currency_rate', [CurrenciesController::class, 'addCurrencyRate']);
    Route::post('/edit_currency_rate', [CurrenciesController::class, 'updateCurrencyRate']);
    Route::post('/delete_currency_rate', [CurrenciesController::class, 'deleteCurrencyRate']);

//Users
    Route::post('/login', [UsersController::class, 'login']);
    Route::get('/get_users', [UsersController::class, 'getUsers']);
    Route::post('/add_user', [UsersController::class, 'addUser']);
    Route::post('/edit_user', [UsersController::class, 'editUser']);
    Route::post('/delete_user', [UsersController::class, 'deleteUser']);
    Route::get('/logged_user', [UsersController::class, 'loggedUser']);

    //Roles
    Route::get("/get_roles", [UsersController::class, 'getRoles']);
    Route::get("/get_role", [UsersController::class, 'getRole']);
    Route::post("/add_role", [UsersController::class, 'addRole']);
    Route::post("/edit_role", [UsersController::class, 'editRole']);
    Route::post("/delete_role", [UsersController::class, 'deleteRole']);
    Route::post("/assign_roles", [UsersController::class, 'assignRoles']);

});

Route::get('/logout', [UsersController::class, 'logout']);
Route::get('/', [UsersController::class, 'index']);
Route::any('/{any}', [UsersController::class, 'index'])->where('any', '(.*)');;

//Route::get('/{any}', function() { return view('welcome'); })->where('any', '(.*)');
