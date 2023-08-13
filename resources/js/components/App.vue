<template>
    <div :class="{'sidebar-mini': sidebarMiniClass}">
        <div v-if="loggedUser">

            <div class="_1side_menu _1side_menu_bg" >
                <div class="_1side_menu_content">

                    <div class="_1side_menu_list">
                        <ul class="_1side_menu_list_ul">
                            <template v-for="(permission, i) in permissions" :key="i" v-if="permissions.length">
                                <li v-if="!permission.subPage && permission.read">
                                    <router-link :to="permission.name" custom v-slot="{ href, navigate, isActive, isExactActive }">
                                        <a :class="{ 'router-link-exact-active sidebar-link': subIsActiveFirst(permission.name) }" :href="href" @click="navigate">
                                            <Icon :type="permission.icon" size="24" />
                                            <div style="font-size: 12px;">{{ permission.resourceName }}</div>
                                        </a>
                                    </router-link>
                                </li>
                            </template>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="header">
                <div class="_2menu _box_shadow">

                    <div class="_2menu_logo _1side_menu_bg" :class="{'d-none': sidebarMiniClass}">
                        <ul class="open_button">
                            <li class="company_title">Dowool Textile</li>
                        </ul>
                    </div>

                    <ul class="open_button ">
                        <li>
                            <Icon type="ios-list" @click="this.sidebarMiniClass = !this.sidebarMiniClass" />
                        </li>
                        <li class="font-11">
                            Курс: 1$= <strong class="text-danger">{{ currencyStore.dollarRate.rate }}</strong> сўм <br/>
                            Сана: <span class="text-danger">{{ myDateFormat(currencyStore.dollarRate.updated_at, "mm.dd.yyyy") }}</span>
                        </li>

                    </ul>

                    <ul class="_2menu_ul" :class="{ 'd-none': !subIsActive('/production') }">
                        <li class="production-menu-item" :class="{ 'router-link-active': !subIsActive('/production/') }">
                            <router-link :to="{ name: 'production' }" class="production-menu-link">Ишлаб чиқариш</router-link>
                        </li>
                    </ul>

                    <ul class="_2menu_ul" :class="{ 'd-none': !subIsActive('/production') }">
                        <template v-for="(department, d) in productionDepartments" :key="d">
                            <li class="production-menu-item" :class="{ 'router-link-active': subIsActive('/production/'+department.id) }">
                                <router-link :to="{ name: 'productionRawMaterialsStock', params: {department_id: department.id} }" class="production-menu-link">{{department.name}}</router-link>
                            </li>
                        </template>

                    </ul>

                    <ul class="_2menu_ul" :class="{ 'd-none': !subIsActive('/expenses') }">
                        <li class="production-menu-item" :class="{ 'router-link-active': !subIsActive('/expenses/') }">
                            <router-link :to="{ name: 'expenses' }" class="production-menu-link">Умумий хисобот</router-link>
                        </li>
                        <li class="production-menu-item" :class="{ 'router-link-active': subIsActive('/expenses/communal') }">
                            <router-link :to="{ name: 'communal'}" class="production-menu-link">Коммунал тўловлар</router-link>
                        </li>
                        <li class="production-menu-item" :class="{ 'router-link-active': subIsActive('/expenses/other') }">
                            <router-link :to="{ name: 'otherExpenses'}" class="production-menu-link">Қўшимча харажатлар</router-link>
                        </li>
                    </ul>


                </div>
<!--                <ul class="open_button float-right">-->
<!--                    <li class="float-right1 text-center">-->
<!--                        <a href="/logout">-->
<!--                            <Icon type="ios-log-out" size="20" color="#ff0000" />-->
<!--                            <div>Чиқиш</div>-->
<!--                        </a>-->
<!--                    </li>-->
<!--                    <li class="float-right1 text-center">-->
<!--                        <a href="/logout">-->
<!--                            <Icon type="ios-log-out" size="20" color="#ff0000" />-->
<!--                            <div>Чиқиш</div>-->
<!--                        </a>-->
<!--                    </li>-->
<!--                </ul>-->

                <div class="_2menu _2menu_ceta">
                    <div class="_1side_menu_content" style="width: 200px; text-align: right;">
                        <ul class="open_button">
                            <li class="text-center">
                                <a href="javascript:void(0);">
                                    <Icon type="md-person" size="20" color="#2b85e4" />
                                    <div>{{usersStore.loggedUser.fullName}}</div>
                                </a>
                            </li>
                            <li class="float-right1 text-center">
                                <a href="/logout">
                                    <Icon type="ios-log-out" size="20" color="#ff0000" />
                                    <div>Чиқиш</div>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="content pl-0 pr-0">
            <div class="container-fluid pl-0 pr-0">
                <router-view :key="$route.path"></router-view>
            </div>
        </div>
    </div>
</template>

<script>
import {useCurrenciesStore} from "../stores/CurrenciesStore";
import {useUsersStore} from "../stores/UsersStore";
export default {
    props: ['loggedUser', 'permissions'],
    setup(){
        const currencyStore = useCurrenciesStore()
        const usersStore = useUsersStore()
        return { currencyStore, usersStore }
    },
    data() {
        return {
            productionDepartments: [],
            expenseTypes:       [],
            sidebarMiniClass:   false,
            dollarRate:         0,

            isLoggedIn:         false,
        }
    },
    methods: {
        subIsActive(input) {
            const paths = Array.isArray(input) ? input : [input]
            return paths.some(path => {
                return this.$route.path.indexOf(path) === 0 // current path starts with this path string
            })
        },
        subIsActiveFirst(input) {
            const pathArray = window.location.pathname.split("/");

            input = input.replace('/', '')
            if(pathArray[1] === input) {
                return true;
            } else {
                return false;
            }
        },
    },
    async created(){

        this.usersStore.loggedUser = this.loggedUser
        if(this.loggedUser) {
            let department_type = [2];//Ishlab chiqarish
            const [productionDepartments, currencyData] = await Promise.all([
                this.callApi('get', '/app/get_departments/'+department_type),
                this.callApi('get', '/app/get_currency_rate_last/1')
            ]);
            // const productionDepartments = await this.callApi('get', '/app/get_departments/'+department_type)
            this.productionDepartments = productionDepartments.data

            // const currencyData = await this.callApi('get', '/app/get_currency_rate_last/1')
            this.currencyStore.dollarRate = currencyData.data
        }


    },
}
</script>
