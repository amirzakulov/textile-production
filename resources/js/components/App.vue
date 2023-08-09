<template>
    <div :class="{'sidebar-mini': sidebarMiniClass}">
        <div>
            <!--========== ADMIN SIDE MENU one ========-->
            <div class="_1side_menu _1side_menu_bg" >
<!--                <div class="_1side_menu_logo">-->
<!--                    <h3 style="text-align:center; color: #fff;">Dowool Textile 2</h3>-->
<!--                </div>-->

                <!--~~~~~~~~ MENU CONTENT ~~~~~~~~-->
                <div class="_1side_menu_content">

                    <!--~~~ MENU LIST ~~~~~~-->
                    <div class="_1side_menu_list">
                        <ul class="_1side_menu_list_ul">
                            <li>
                                <router-link :to="{name: 'home'}" custom v-slot="{ href, navigate, isActive, isExactActive }">
                                    <a :class="{ 'router-link-exact-active sidebar-link': isActive || isExactActive || subIsActive('/home') }" :href="href" @click="navigate">
                                        <Icon type="md-home" size="24" />
                                        <div class="_1side_menu_list_ul_item_text">Маълумотлар тахтаси</div>
                                    </a>
                                </router-link>
                            </li>
                            <li>
                                <router-link :to="{name: 'rawMaterials', params: {category_id: 1} }" custom v-slot="{ href, navigate, isActive, isExactActive }">
                                    <a :class="{ 'router-link-exact-active sidebar-link': isActive || isExactActive || subIsActive('/raw-materials') }" :href="href" @click="navigate">
                                        <Icon type="logo-dropbox" size="24"/>
                                        <div class="_1side_menu_list_ul_item_text">Хомашё омбори</div>
                                    </a>
                                </router-link>
                            </li>
                            <li>
                                <router-link :to="{name: 'production'}" custom v-slot="{ href, navigate, isActive, isExactActive }">
                                    <a :class="{ 'router-link-exact-active sidebar-link': isActive || isExactActive || subIsActive('/production') }" :href="href" @click="navigate">
                                        <Icon type="ios-cube" size="24" />
                                        <div class="_1side_menu_list_ul_item_text">Ишлаб чиқариш</div>
                                    </a>
                                </router-link>
                            </li>
                            <li>
                                <router-link to="/finished_products" custom v-slot="{ href, navigate, isActive, isExactActive }">
                                    <a :class="{ 'router-link-exact-active sidebar-link': isActive || isExactActive || subIsActive('/finished_products') }" :href="href" @click="navigate">
                                        <Icon type="ios-cart" size="24" />
                                        <div class="_1side_menu_list_ul_item_text">Тайёр махсулот</div>
                                    </a>
                                </router-link>
                            </li>
                            <li>
                                <router-link to="/fashions" custom v-slot="{ href, navigate, isActive, isExactActive }">
                                    <a :class="{ 'router-link-exact-active sidebar-link': isActive || isExactActive || subIsActive('/fashions') || subIsActive('/fashion') }" :href="href" @click="navigate">
                                        <Icon type="ios-shirt" size="24"/>
                                        <div class="_1side_menu_list_ul_item_text">Моделлар</div>
                                    </a>
                                </router-link>
                            </li>
                            <li>
                                <router-link to="/employees" custom v-slot="{ href, navigate, isActive, isExactActive }">
                                    <a :class="{ 'router-link-exact-active sidebar-link': isActive || isExactActive || subIsActive('/employees') || subIsActive('/employee') }" :href="href" @click="navigate">
                                        <Icon type="ios-people" size="24" />
                                        <div class="_1side_menu_list_ul_item_text">Ходимлар</div>
                                    </a>
                                </router-link>
                            </li>
                            <li>
                                <router-link to="/expenses" custom v-slot="{ href, navigate, isActive, isExactActive }">
                                    <a :class="{ 'router-link-exact-active sidebar-link': isActive || isExactActive || subIsActive('/expenses') }" :href="href" @click="navigate">
                                        <Icon type="md-calculator" size="24" />
                                        <div class="_1side_menu_list_ul_item_text">Харажатлар</div>
                                    </a>
                                </router-link>
                            </li>
                            <li>
                                <router-link to="/currency_rates" custom v-slot="{ href, navigate, isActive, isExactActive }">
                                    <a :class="{ 'router-link-exact-active sidebar-link': isActive || isExactActive || subIsActive('/currency_rates') }" :href="href" @click="navigate">
                                        <Icon type="logo-usd" size="24" />
                                        <div class="_1side_menu_list_ul_item_text">Валюталар</div>
                                    </a>
                                </router-link>
                            </li>
                            <li>
                                <router-link to="/reports" custom v-slot="{ href, navigate, isActive, isExactActive }">
                                    <a :class="{ 'router-link-exact-active sidebar-link': isActive || isExactActive || subIsActive('/reports') }" :href="href" @click="navigate">
                                        <Icon type="md-stats" size="24" />
                                        <div class="_1side_menu_list_ul_item_text">Хисоботлар</div>
                                    </a>
                                </router-link>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <!--========== ADMIN SIDE MENU ========-->

            <!--========= HEADER ==========-->
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

<!--                    <ul class="_2menu_ul" :class="{ 'd-none': !subIsActive('/production') }">-->
<!--                        <li class="production-menu-item" :class="{ 'router-link-active': !subIsActive('/production/') }">-->
<!--                            <router-link :to="{ name: 'production' }" class="production-menu-link">Ишлаб чиқариш</router-link>-->
<!--                        </li>-->
<!--                    </ul>-->

                    <ul class="_2menu_ul" :class="{ 'd-none': !subIsActive('/production') }">
                        <li class="production-menu-item" :class="{ 'router-link-active': !subIsActive('/production/') }">
                            <router-link :to="{ name: 'production' }" class="production-menu-link">Ишлаб чиқариш</router-link>
                        </li>
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

                <div class="_2menu _2menu_ceta">
                    <div class="_1side_menu_content">
                        <ul class="open_button">
                            <li class="text-center"><Icon type="ios-person" /> Admin</li>
                        </ul>
                    </div>
                </div>
            </div>
            <!--========= HEADER ==========-->

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
export default {
    setup(){
        const currencyStore = useCurrenciesStore()
        return { currencyStore }
    },
    data() {
        return {
            productionDepartments: [],
            expenseTypes: [],
            sidebarMiniClass: false,
            dollarRate: 0,
        }
    },
    methods: {
        subIsActive(input) {
            const paths = Array.isArray(input) ? input : [input]
            return paths.some(path => {

                let segment = this.$route.path.indexOf(path) === 0

                return segment // current path starts with this path string
            })
        },
    },
    async created(){
        let department_type = [2];//Ishlab chiqarish
        const productionDepartments = await this.callApi('get', '/app/get_departments/'+department_type)
        this.productionDepartments = productionDepartments.data

        const currencyData = await this.callApi('get', '/app/get_currency_rate_last/1')
        this.currencyStore.dollarRate = currencyData.data
    },
}
</script>
