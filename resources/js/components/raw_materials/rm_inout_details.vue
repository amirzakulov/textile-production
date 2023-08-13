<template>
    <StockHeader />
    <div class="_1adminOverveiw_table_recent _box_shadow _border_radious bg-transparent border p-3">
        <Row :gutter="8">
            <Col span="3">
                <router-link :to="{ name: 'rmInOut'}" class="nav-link text-white1"><Icon type="ios-arrow-back" size="16" /> Орқага қайтиш</router-link>
            </Col>
            <Col span="17" offset="1">

                <ButtonGroup>
                    <Button type="primary" class="text-white1" ghost><strong>Партия: {{this.rmStore.rmSet.name}}</strong></Button>
                    <Button type="primary" class="text-white1" ghost>
                        <Icon v-if="this.rmStore.rmSet.inout === 1" type="md-arrow-down" />
                        <Icon v-else type="md-arrow-up" color="#ff0000" />

                        <strong v-if="this.rmStore.rmSet.inout === 1">{{this.rmStore.rmSet.department_from}}</strong>
                        <strong v-else>{{this.rmStore.rmSet.department_to}}</strong>
                    </Button>
                </ButtonGroup>
                <ButtonGroup class="ivu-ml">
                    <Button type="error" class="text-white1" ghost><strong>{{countTotalUsd}} $</strong></Button>
                    <Button type="error" class="text-white1" ghost><strong>{{countTotalUzs}} сўм</strong></Button>
                </ButtonGroup>

            </Col>
            <Col span="3">
                <Button v-if="this.rmStore.rmSet.inout === 1" type="primary" @click="rmStore.rmAddModal = true" class="ivu-mr text-right"><Icon type="md-add" />Махсулот қўшиш</Button>
                <Button v-else type="error" @click="rmStore.rmAddOutModal = true" class="ivu-mr text-right"><Icon type="md-add" />Махсулот қўшиш</Button>
            </Col>
        </Row>
<!--        <Divider />-->
    </div>
    <div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 p-3">

        <Row v-if="productsInUsd.length"><Col span="3">Доллар</Col></Row>
        <Table v-if="productsInUsd.length" size="small" :columns="columns" :data="productsInUsd" :loading="loading" class="mb-5">
            <template #inout="{row}">
                <Icon v-if="row.inout == 1" type="md-arrow-down" color="#0099ff" />
                <Icon v-else type="md-arrow-up" color="#ff0000" />
            </template>
            <template #currency_rate="{row}">
                {{row.currency_id === 1 ? row.currency_rate.toLocaleString() : ""}}
            </template>
            <template #price_value="{row}">
                {{row.price_value.toLocaleString()}}
            </template>
            <template #product_total="{row}">
                <Text type="danger"><strong>{{(row.price_value * row.count).toLocaleString()}}</strong></Text>
            </template>
        </Table>

        <Row v-if="productsInSum.length"><Col span="3">Сўм</Col></Row>
        <Table v-if="productsInSum.length" size="small" :columns="columns" :data="productsInSum" :loading="loading">
            <template #inout="{row}">
                <Icon v-if="row.inout === 1" type="md-arrow-down" color="#0099ff" />
                <Icon v-else type="md-arrow-up" color="#ff0000" />
            </template>
            <template #currency_rate="{row}">
                {{row.currency_id === 1 ? row.currency_rate.toLocaleString() : ""}}
            </template>
            <template #price_value="{row}">
                {{row.price_value.toLocaleString()}}
            </template>
            <template #product_total="{row}">
                <Text type="danger"><strong>{{(row.price_value * row.count).toLocaleString()}}</strong></Text>
            </template>
        </Table>

    </div>

    <RmAddModal />
    <RmEditModal />
    <RmDeleteModal />

    <RmAddOutModal />
    <RmEditOutModal />
    <RmDeleteOutModal />
</template>

<script>
import StockHeader from './parts/header.vue'
import {useRawMaterialsStore} from "../../stores/RawMaterialsStore";
import {resolveComponent} from "vue";

import RmAddModal from './parts/inout_details/addModal.vue'
import RmEditModal from './parts/inout_details/editModal.vue'
import RmDeleteModal from './parts/inout_details/deleteModal.vue'

import RmAddOutModal from './parts/inout_details/addOutModal.vue'
import RmEditOutModal from './parts/inout_details/editOutModal.vue'
import RmDeleteOutModal from './parts/inout_details/deleteOutModal.vue'

export default {
    name: "Raw Materials Income",
    components: {
        StockHeader:StockHeader,

        RmAddModal: RmAddModal,
        RmEditModal:RmEditModal,
        RmDeleteModal:RmDeleteModal,

        RmAddOutModal:RmAddOutModal,
        RmEditOutModal:RmEditOutModal,
        RmDeleteOutModal:RmDeleteOutModal,
    },
    setup() {
        const rmStore = useRawMaterialsStore()

        return { rmStore }
    },
    computed: {
        countTotalUzs(){
            let total = 0
            this.rmStore.rmSetProducts.forEach((product, index) => {
                if(product.currency_id === 2){
                    total += product.count * product.price_value
                }
            })

            return this.formatPrice(total).toLocaleString()
        },
        countTotalUsd(){
            let total = 0
            this.rmStore.rmSetProducts.forEach((product, index) => {
                if(product.currency_id === 1){
                    total += product.count * product.price_value
                }
            })

            return this.formatPrice(total).toLocaleString()
        },
        productsInUsd(){
            let products = []
            this.rmStore.rmSetProducts.forEach((product, index) => {
                if(product.currency_id === 1) {
                    products.push(product)
                }
            })

            return products
        },
        productsInSum(){
            let products = []
            this.rmStore.rmSetProducts.forEach((product, index) => {
                if(product.currency_id === 2) {
                    products.push(product)
                }
            })

            return products
        },
    },
    data() {
        return {
            rmSetProductsId: [],
            loading: true,
            columns: [
                {
                    title: "",
                    slot: 'inout',
                    width: "25",
                },
                {
                    title: "Код",
                    key: 'code',
                    sortable: true
                },
                {
                    title: "Номи",
                    key: 'name',
                    width: "200",
                    sortable: true
                },
                {
                    title: "Партия",
                    key: 'set_name',
                    width: "120",
                },
                {
                    title: "Ранг",
                    key: 'color',
                    width: "120",
                },
                {
                    title: "Давлат",
                    key: 'country',
                    width: "120",
                },
                {
                    title: "Бирлик",
                    key: 'measurement',
                    width: "80",
                },
                {
                    title: "Миқдор",
                    key: 'count',
                },
                {
                    title: "Валюта",
                    key: 'currency_name',
                },
                {
                    title: "Курс",
                    slot: 'currency_rate',
                },
                {
                    title: "Нарх",
                    slot: 'price_value',
                },
                {
                    title: "Жами",
                    slot: 'product_total',
                    width: "120",
                },
                {
                    title: "action",
                    key: 'action',
                    width: 120,
                    align: 'right',
                    render: (h, params) => {
                        if(params.row.inout === 1){
                            return h('div', {
                                    class: 'ivu-btn-group ivu-btn-group-default'
                                },
                                [
                                    h(resolveComponent('Button'),
                                    {
                                        title:"Тахрирлаш",
                                        type: 'primary',
                                        size: 'small',

                                        onClick: () => {
                                            this.showEditInModal(params.row, params.index)
                                        }
                                    }, {
                                        default() {
                                            return h(resolveComponent('Icon'), {
                                                type: 'md-create'
                                            })
                                        }
                                    }),

                                    h(resolveComponent('Button'),
                                    {
                                        title:"Ўчириш",
                                        type: 'error',
                                        size: 'small',
                                        loading: params.row.isDeleting,

                                        onClick: () => {
                                            this.showDeletingInModal(params.row, params.index)
                                        }
                                    }, {
                                        default() {
                                            return h(resolveComponent('Icon'), {
                                                type: 'md-close'
                                            })
                                        }
                                    }),
                                ])
                        } else {
                            return h('div', {
                                    class: 'ivu-btn-group ivu-btn-group-default'
                                },
                                [
                                    h(resolveComponent('Button'),
                                        {
                                            title:"Тахрирлаш",
                                            type: 'primary',
                                            size: 'small',

                                            onClick: () => {
                                                this.showEditOutModal(params.row, params.index)
                                            }
                                        }, {
                                            default() {
                                                return h(resolveComponent('Icon'), {
                                                    type: 'md-create'
                                                })
                                            }
                                        }),

                                    h(resolveComponent('Button'),
                                        {
                                            title:"Ўчириш",
                                            type: 'error',
                                            size: 'small',
                                            loading: params.row.isDeleting,

                                            onClick: () => {
                                                this.showDeletingOutModal(params.row, params.index)
                                            }
                                        }, {
                                            default() {
                                                return h(resolveComponent('Icon'), {
                                                    type: 'md-close'
                                                })
                                            }
                                        }),
                                ])
                        }
                    }
                },

            ],
        }
    },
    methods: {
        showEditInModal(raw_material, index){

            let rawMaterialObj = {
                raw_material_id:    raw_material.id,
                raw_material_detail_id: raw_material.raw_material_detail_id,
                product_id:         raw_material.product_id,
                name:               raw_material.name,
                code:               raw_material.code,
                currency_id:        raw_material.currency_id,
                price_value:        raw_material.price_value,
                count:              raw_material.count,
                department_id:      raw_material.department_id,
                set_id:             raw_material.set_id,
                from_set_id:        raw_material.from_set_id,
                inout:              raw_material.inout,
            }

            this.rmStore.rawMaterial = rawMaterialObj
            this.rmStore.rowIndex    = index
            this.rmStore.rmEditModal = true
        },
        showDeletingInModal(raw_material, i){
            this.rmStore.rawMaterial  = raw_material
            this.rmStore.rowIndex     = i
            this.rmStore.rmDeleteModal  = true
        },

        async showEditOutModal(raw_material, index){

            const rmb = await this.callApi('get', '/app/get_rm_rawmaterial/'+raw_material.id+'/'+this.rmStore.department_id+'/'+raw_material.from_set_id)
            let stockCount = 0
            if(rmb.data !== '') { stockCount = rmb.data.count }

            const rmd = await this.callApi('get', '/app/get_rm_rawmaterial_details/'+raw_material.raw_material_detail_id)
            const rmdData = rmd.data

            let rawMaterialObj = {
                raw_material_id:    raw_material.id,
                raw_material_detail_id: raw_material.raw_material_detail_id,
                product_id:         raw_material.product_id,
                name:               raw_material.name,
                code:               raw_material.code,
                currency_id:        raw_material.currency_id,
                price_value:        raw_material.price_value,
                price:              raw_material.price,
                count:              (-1) * raw_material.count,
                department_id:      raw_material.department_id,
                set_id:             raw_material.set_id,
                from_set_id:        raw_material.from_set_id,
                inout:              raw_material.inout,

                stockCount:         stockCount,
                outCount:           (-1) * raw_material.count,
                from_department_id: rmdData.from_department_id,
                to_department_id:   rmdData.to_department_id,
            }

            this.rmStore.rawMaterial    = rawMaterialObj
            this.rmStore.rowIndex       = index
            this.rmStore.rmEditOutModal = true

        },
        showDeletingOutModal(raw_material, i){

            this.rmStore.rawMaterial        = raw_material
            this.rmStore.rowIndex           = i
            this.rmStore.rmDeleteOutModal   = true
        },
    },
    async created() {
        document.title = "Хомашё Омбори | Партия | Махсулотлар"

        const setId = this.$route.params.inoutset_id
        const inout = this.$route.params.inout

        const [rmSetProducts, rmSet, currencies, products, departmentRawMaterials] = await Promise.all([
            this.callApi('get', '/app/get_rmset_products/' + setId +'/' + inout),
            this.callApi('get', '/app/get_rm_set/' + setId),
            this.callApi('get', '/app/add_currencies'),
            this.callApi('get', '/app/get_products/'+this.rmStore.department_id),
            this.callApi('get', '/app/get_rm_rawmaterials/'+this.rmStore.department_id),
        ])

        this.rmStore.rmSetProducts  = rmSetProducts.data
        this.rmStore.rmSet          = rmSet.data
        this.rmStore.currencies     = currencies.data
        this.rmStore.products       = products.data
        this.rmStore.departmentRawMaterials   = departmentRawMaterials.data

        this.rmStore.rmSetProducts.forEach((product, index) => {
            this.rmStore.rmSetProductsId.push(product.product_id)
            this.rmStore.rmSetRawMaterialsId.push(product.id)
        })

        this.loading = false
    }
}
</script>
