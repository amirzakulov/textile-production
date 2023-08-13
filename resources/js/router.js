import { createRouter, createWebHistory } from 'vue-router'

import home from './components/dashboard/home.vue'
import login from './components/auth/login.vue'
//Stock of raw materials
import rawMaterials from './components/raw_materials/index.vue'
import rmInOut from './components/raw_materials/rm_inout.vue'
import rmInOutDetails from './components/raw_materials/rm_inout_details.vue'
import rmProducts from './components/raw_materials/products.vue'
import rmSemiFinishProducts from './components/raw_materials/semi_finish_products_stock'
import rmSemiFinishProductsInOut from './components/raw_materials/semi_finish_products_inout'
import rmSemiFinishProductsInOutDetails from './components/raw_materials/semi_finish_products_inout_details'
import rmReports from './components/raw_materials/reports.vue'

//Production
import production from './components/production/index.vue'
import productionRawMaterialsStock from "./components/production/raw_materials_stock.vue"
import productionRawMaterialsInout from "./components/production/raw_materials_inout.vue"
import productionRawMaterialsInOutDetails from "./components/production/raw_materials_inout_details"
import productionDepartmentProducts from "./components/production/products.vue"
import productionFinishProductsStock from "./components/production/finish_products_stock.vue"
import productionFinishProductsInout from "./components/production/finish_products_inout"
import productionFinishProductsInOutDetails from "./components/production/finish_products_inout_details"
import productionSemiFinishProductsStock from "./components/production/semi_finish_products_stock"
import productionSemiFinishProductsInout from "./components/production/semi_finish_products_inout"
import productionSemiFinishProductsInoutDetails from "./components/production/semi_finish_products_inout_details"
import productionReports from "./components/production/reports"

//Finished Products
import fgFinishedProductsStock from './components/finished_products/index.vue'
import fgFinishedProductsInout from './components/finished_products/finished_products_inout.vue'
import fgFinishedProductsInoutDetails from './components/finished_products/finished_products_inout_details.vue'
import fgReports from './components/finished_products/reports.vue'
import fgReports2 from './components/finished_products/reports2.vue'

//Fashions
import fashions from "./components/fashions/index.vue"
import fashion from './components/fashions/fashion.vue'

// Employees
import employees from "./components/employees/index.vue"
import payroll from "./components/employees/payroll.vue"
import fired from "./components/employees/fired.vue"
import jobTitles from "./components/employees/job_titles.vue"

//Expenses
import expenses from "./components/expenses/index.vue"
import communal from "./components/expenses/communal.vue"
import communalType from "./components/expenses/communal_type.vue"
import otherExpenses from "./components/expenses/otherExpenses.vue"

//currencies
import currencyRates from "./components/currency_rates/index.vue"

//reports
import reports from "./components/reports/index.vue"

//Users
import users from './components/users/index.vue'

//permissions
import roles from './components/permissions/roles.vue'
import permissions from './components/permissions/assignRole.vue'

const routes = [
    //project routes
    {
        path: '/',
        component: home,
        name: 'home'
    },
    {
        path: '/login',
        component: login,
        name: 'login'
    },

    // rawMaterials
    {
        path: '/raw-materials/:category_id?',
        component: rawMaterials,
        name: 'rawMaterials'
    },
    {
        path: "/raw-materials/inout",
        component: rmInOut,
        name: "rmInOut",
    },
    {
        path: "/raw-materials/inout/details/:inoutset_id/:inout",
        component: rmInOutDetails,
        name: "rmInOutDetails",
    },
    {
        path: '/raw-materials/products',
        component: rmProducts,
        name: 'rmProducts'
    },
    {
        path: '/raw-materials/semi-finish-products',
        component: rmSemiFinishProducts,
        name: 'rmSemiFinishProducts'
    },
    {
        path: '/raw-materials/semi-finish-products-inout',
        component: rmSemiFinishProductsInOut,
        name: 'rmSemiFinishProductsInOut'
    },
    {
        path: '/raw-materials/semi-finish-products-inout/details/:inoutset_id/:inout',
        component: rmSemiFinishProductsInOutDetails,
        name: 'rmSemiFinishProductsInOutDetails'
    },
    {
        path: '/raw-materials/reports',
        component: rmReports,
        name: 'rmReports'
    },

    // Production
    {
        path: '/production',
        component: production,
        name: 'production'
    },
    {
        path: '/production/:department_id',
        component: productionRawMaterialsStock,
        name: 'productionRawMaterialsStock'
    },
    {
        path: '/production/:department_id/raw_materials_inout',
        component: productionRawMaterialsInout,
        name: 'productionRawMaterialsInout'
    },
    {
        path: '/production/:department_id/raw_materials_inout/details/:inoutset_id/:inout',
        component: productionRawMaterialsInOutDetails,
        name: 'productionRawMaterialsInOutDetails'
    },
    {
        path: '/production/:department_id/department_products',
        component: productionDepartmentProducts,
        name: 'productionDepartmentProducts'
    },
    {
        path: '/production/:department_id/finish_products',
        component: productionFinishProductsStock,
        name: 'productionFinishProductsStock'
    },
    {
        path: '/production/:department_id/finish_products_inout',
        component: productionFinishProductsInout,
        name: 'productionFinishProductsInout'
    },
    {
        path: '/production/:department_id/finish_products_inout/details/:inoutset_id/:inout',
        component: productionFinishProductsInOutDetails,
        name: 'productionFinishProductsInOutDetails'
    },
    {
        path: '/production/:department_id/semi_finish_products',
        component: productionSemiFinishProductsStock,
        name: 'productionSemiFinishProductsStock'
    },
    {
        path: '/production/:department_id/semi_finish_products_inout',
        component: productionSemiFinishProductsInout,
        name: 'productionSemiFinishProductsInout'
    },
    {
        path: '/production/:department_id/semi_finish_products_inout/details/:inoutset_id/:inout',
        component: productionSemiFinishProductsInoutDetails,
        name: 'productionSemiFinishProductsInoutDetails'
    },
    {
        path: '/production/:department_id/reports',
        component: productionReports,
        name: 'productionReports'
    },

    // Finished Products
    {
        path: '/finished_products',
        component: fgFinishedProductsStock,
        name: 'fgFinishedProductsStock'
    },
    {
        path: '/finished_products/finished_products_inout',
        component: fgFinishedProductsInout,
        name: 'fgFinishedProductsInout'
    },
    {
        path: '/finished_products/finished_products_inout/details/:inoutset_id/:inout',
        component: fgFinishedProductsInoutDetails,
        name: 'fgFinishedProductsInoutDetails'
    },
    {
        path: '/finished_products/reports',
        component: fgReports,
        name: 'fgReports'
    },
    {
        path: '/finished_products/reports2',
        component: fgReports2,
        name: 'fgReports2'
    },

    // Fashions
    {
        path: '/fashions',
        component: fashions,
        name: 'fashions'
    },
    {
        path: '/fashions/:fashion_id',
        component: fashion,
        name: 'fashion'
    },

// Employees
    {
        path: '/employees',
        component: employees,
        name: 'employees'
    },
    {
        path: '/employees/payroll',
        component: payroll,
        name: 'payroll'
    },
    {
        path: '/employees/fired',
        component: fired,
        name: 'fired'
    },
    {
        path: '/employees/titles',
        component: jobTitles,
        name: 'jobTitles'
    },

    //Expenses
    {
        path: '/expenses',
        component: expenses,
        name: 'expenses'
    },
    {
        path: '/expenses/communal',
        component: communal,
        name: 'communal'
    },
    {
        path: '/expenses/communal/:communal_type_id',
        component: communalType,
        name: 'communalType'
    },
    {
        path: '/expenses/other',
        component: otherExpenses,
        name: 'otherExpenses'
    },

    //Currency Rates
    {
        path: '/currency_rates/:currency_id?',
        component: currencyRates,
        name: 'currencyRates'
    },


    //Reports
    {
        path: '/reports',
        component: reports,
        name: 'reports'
    },


    //Users
    {
        path: "/users",
        component: users,
        name: "users"
    },

    //permissions
    {
        path: "/roles",
        component: roles,
        name: "roles"
    },
    {
        path: "/roles/permissions",
        component: permissions,
        name: "permissions"
    },

]

const router = createRouter({
    history: createWebHistory(process.env.BASE_URL),
    routes,
});

export default router
