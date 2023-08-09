<template>
    <StockHeader />
    <div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">
        <Row :gutter="8">
            <Col span="3">
                <router-link :to="{ name: 'productionFinishProductsInout'}" class="nav-link"><Icon type="ios-arrow-back" size="16" /> Орқага қайтиш</router-link>
            </Col>
            <Col span="17" offset="1">

                <ButtonGroup>
                    <Button type="primary" ghost><Text>Партия: {{productionStore.inoutSet.name}}</Text></Button>
                    <Button type="primary" ghost>
                        <Icon v-if="productionStore.inoutSet.inout === 1" type="md-arrow-down" color="#0099ff" />
                        <Icon v-else type="md-arrow-up" color="#ff0000" />

                        <Text v-if="productionStore.inoutSet.inout === 1">{{productionStore.inoutSet.department_from}}</Text>
                        <Text v-else>{{productionStore.inoutSet.department_to}}</Text>
                    </Button>
                </ButtonGroup>
                <ButtonGroup class="ivu-ml">
                    <Button type="error" ghost><strong>{{countTotalUzs}} сўм</strong></Button>
                </ButtonGroup>

            </Col>
            <Col span="3">
                <template v-if="$route.params.inout == 2">
                    <Button type="error" @click="productionStore.fpDetailsAddOutModal = true" class="ivu-mr text-right"><Icon type="md-add" />Махсулот қўшиш</Button>
                </template>
            </Col>
        </Row>
        <Divider />

        <Row><Col span="3">Сўм</Col></Row>
        <Table size="small" :columns="columns" :data="productionStore.inoutSetProducts" :loading="loading" class="mb-5">
            <template #inout="{row}">
                <Icon v-if="row.inout == 1" type="md-arrow-down" color="#0099ff" />
                <Icon v-else type="md-arrow-up" color="#ff0000" />
            </template>
            <template #price="{row}">
                {{row.price.toLocaleString()}}
            </template>
            <template #product_total="{row}">
                <Text type="danger"><strong>{{(row.price * row.count).toLocaleString()}}</strong></Text>
            </template>
        </Table>

        <Row class="ivu-mt" v-if="productionStore.inoutSet.inout === 1"><Col span="8">Ишлатилган хомашё махсулотлари</Col></Row>
        <Row v-if="productionStore.inoutSet.inout === 1">
            <Col span="12">
                <Table size="small" class="table-light" border :columns="fpColumns" :data="productionStore.finishProductRawMaterials" :loading="loading">
                    <template #price="{row}">
                        {{row.price.toLocaleString()}}
                    </template>
                    <template #perItem="{row}">
                        {{ row.count / finish_product_count }}
                    </template>
                    <template #product_total="{row}">
                        <Text type="danger"><strong>{{(row.price * row.count).toLocaleString()}}</strong></Text>
                    </template>
                </Table>
            </Col>
        </Row>

    </div>

    <fpDetailsAddOutModal />
    <fpDetailsEditInModal />
    <fpDetailsEditOutModal />
    <fpDetailsDeleteOutModal />

</template>

<script>
import StockHeader from './parts/header.vue'

import {resolveComponent} from "vue";
import {useProductionStore} from "../../stores/ProductionStore";

import fpDetailsAddOutModal from './parts/finish_products_inout_details/addOutModal'
import fpDetailsEditInModal from './parts/finish_products_inout_details/editInModal'
import fpDetailsEditOutModal from './parts/finish_products_inout_details/editOutModal'
import fpDetailsDeleteOutModal from './parts/finish_products_inout_details/deleteOutModal'

export default {
    name: "raw_materials_inout_details",
    setup() {
        const productionStore = useProductionStore()

        return { productionStore }
    },
    components: {
        StockHeader:StockHeader,
        fpDetailsAddOutModal:    fpDetailsAddOutModal,
        fpDetailsEditInModal:    fpDetailsEditInModal,
        fpDetailsEditOutModal:   fpDetailsEditOutModal,
        fpDetailsDeleteOutModal: fpDetailsDeleteOutModal,
    },
    computed: {
        countTotalUzs(){
            let total = 0
            this.productionStore.inoutSetProducts.forEach((product, index) => {
                total += product.count * product.price
            })

            return this.formatPrice(total).toLocaleString()
        },
    },
    data() {
        return {
            loading: true,
            columns: [
                {
                    type: 'expand',
                    width: 30,
                    render: (h, { row: { color, country} }) => {
                        color = color ? color:'-'
                        country = country ? country:'-'

                        return h('div', 'Ранги: '+color+', Давлат: '+country)
                    }
                },
                {
                    title: "",
                    slot: 'inout',
                    width: "25",
                },
                {
                    title: "Код",
                    key: 'code',
                    width: "180",
                    sortable: true
                },
                {
                    title: "Номи",
                    key: 'name',
                    width: "180",
                    sortable: true
                },
                {
                    title: "Партиядан",
                    key: 'from_set_name',
                    width: "120",
                },
                {
                    title: "Бирлиги",
                    key: 'measurement',
                },
                {
                    title: "Миқдор",
                    key: 'count',
                },
                {
                    title: "Нарх",
                    slot: 'price',
                },
                {
                    title: "Жами",
                    slot: 'product_total',
                },
                {
                    title: " ",
                    key: 'action',
                    width: 120,
                    align: 'right',
                    render: (h, params) => {
                        if(params.row.inout === 1) {
                            return h('div', {class: 'ivu-btn-group ivu-btn-group-default'},
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
                                            disabled: true,

                                        }, {
                                            default() {
                                                return h(resolveComponent('Icon'), {
                                                    type: 'md-close'
                                                })
                                            }
                                        }),
                                ])
                        } else {
                            return h('div', {class: 'ivu-btn-group ivu-btn-group-default'},
                                [
                                    h(resolveComponent('Button'),
                                        {
                                            title:"Тахрирлаш",
                                            type: 'primary',
                                            size: 'small',

                                            onClick: () => {this.showEditOutModal(params.row, params.index)}
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

            fpColumns: [
                {
                    title: "Код",
                    key: 'code',
                    sortable: true
                },
                {
                    title: "Номи",
                    key: 'name',
                    width: "180",
                    sortable: true
                },
                {
                    title: "Бирлиги",
                    key: 'measurement',
                },
                {
                    title: "Битта учун",
                    slot: 'perItem',
                },
                {
                    title: "Миқдор",
                    key: 'count',
                },
                {
                    title: "Нарх",
                    slot: 'price',
                },
                {
                    title: "Жами (сўм)",
                    width: "120",
                    slot: 'product_total',
                },


            ],
            finish_product_count: 0,
        }
    },
    methods: {
        showEditInModal(raw_material, index){
            let finishProductObj = {
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
                inout:              raw_material.inout,
            }

            this.productionStore.departmentFinishProduct = finishProductObj
            this.productionStore.rowIndex                = index
            this.productionStore.fpDetailsEditInModal    = true
        },

        async showEditOutModal(raw_material, index){

            // const rmb = await this.callApi('get', '/app/get_production_rawmaterial/'+raw_material.id+'/'+this.productionStore.department_id+'/'+raw_material.from_set_id)
            const rmb = await this.callApi('get', '/app/get_rm_rawmaterial/'+raw_material.id+'/'+this.productionStore.department_id+'/'+raw_material.from_set_id)
            let stockCount = 0
            if(rmb.data !== '') { stockCount = rmb.data.count }

            const rmd = await this.callApi('get', '/app/get_production_rawmaterial_details/'+raw_material.raw_material_detail_id)
            const rmdData = rmd.data

            let finishProductObj = {
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
                inout:              raw_material.inout,

                stockCount:         stockCount,
                outCount:           (-1) * raw_material.count,
                from_department_id: rmdData.from_department_id,
                to_department_id:   rmdData.to_department_id,
            }

            this.productionStore.departmentFinishProduct = finishProductObj
            this.productionStore.rowIndex                = index
            this.productionStore.fpDetailsEditOutModal   = true

        },
        showDeletingOutModal(raw_material, i){
            this.productionStore.departmentFinishProduct = raw_material
            this.productionStore.rowIndex                = i
            this.productionStore.fpDetailsDeleteOutModal = true
        },
    },
    async created() {
        document.title = "Ишлаб чиқариш | Тайёр махсулот кирим-чиқим"

        this.productionStore.department_id = this.$route.params.department_id
        const setId = this.$route.params.inoutset_id
        const inout = this.$route.params.inout

        const [inoutSetProducts, inoutSet, departmentFinishProducts] = await Promise.all([
            this.callApi('get', '/app/get_production_set_products/' + setId +'/' + inout +'/' + this.productionStore.department_id),
            this.callApi('get', '/app/get_production_set/' + this.productionStore.department_id + "/" + setId),
            this.callApi('get', '/app/get_production_department_finish_products/'+this.productionStore.department_id),

        ])

        this.productionStore.inoutSetProducts   = inoutSetProducts.data
        this.productionStore.inoutSet           = inoutSet.data
        this.productionStore.departmentFinishProducts = departmentFinishProducts.data

        console.log(this.productionStore.inoutSetProducts)

        const finish_product_id = this.productionStore.inoutSetProducts[0].id
        this.finish_product_count = this.productionStore.inoutSetProducts[0].count

        if(this.productionStore.inoutSet.inout === 1) {
            const finishProductRawMaterials = await this.callApi('get', '/app/get_production_finish_product_rawmaterials/' + finish_product_id)
            this.productionStore.finishProductRawMaterials  = finishProductRawMaterials.data
        } else {
            this.productionStore.inoutSetProductsId = []
            this.productionStore.inoutSetRawMaterialsId = []
            this.productionStore.inoutSetProducts.forEach((product, index) => {
                this.productionStore.inoutSetProductsId.push(product.product_id)
                this.productionStore.inoutSetRawMaterialsId.push(product.id)
            })
        }

        this.loading = false
    }
}
</script>
