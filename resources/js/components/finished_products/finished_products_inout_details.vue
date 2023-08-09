<template>
    <StockHeader />
    <div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">
        <Row :gutter="8">
            <Col span="3">
                <router-link :to="{ name: 'fgFinishedProductsInout'}" class="nav-link"><Icon type="ios-arrow-back" size="16" /> Орқага қайтиш</router-link>
            </Col>
            <Col span="17" offset="1">
                <ButtonGroup>
                    <Button type="primary" ghost><Text>Партия: {{fpStore.inoutSet.name}}</Text></Button>
                    <Button type="primary" ghost>
                        <Icon v-if="fpStore.inoutSet.inout === 1" type="md-arrow-down" color="#0099ff" />
                        <Icon v-else type="md-arrow-up" color="#ff0000" />

                        <Text v-if="fpStore.inoutSet.inout === 1">{{fpStore.inoutSet.department_from}}</Text>
                        <Text v-else>{{fpStore.inoutSet.department_to}}</Text>
                    </Button>
                </ButtonGroup>
                <ButtonGroup class="ivu-ml">
                    <Button type="error" ghost><strong>{{countTotalUzs}} сўм</strong></Button>
                </ButtonGroup>

            </Col>
            <Col span="3">
                <template v-if="$route.params.inout == 2">
                    <Button type="error" @click="fpStore.fpDetailsAddOutModal = true" class="ivu-mr text-right"><Icon type="md-add" />Махсулот қўшиш</Button>
                </template>
            </Col>
        </Row>
        <Divider />

        <Row><Col span="3">Сўм</Col></Row>
        <Table size="small" :columns="columns" :data="fpStore.inoutSetProducts" :loading="loading" class="mb-5">
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
    </div>

    <fpDetailsAddOutModal />
    <fpDetailsEditOutModal />
    <fpDetailsDeleteOutModal />
</template>

<script>
import StockHeader from './parts/header.vue'
import {useFinishedProductsStore} from "../../stores/FinishedProductsStore";
import {resolveComponent} from "@vue/runtime-core";


import fpDetailsAddOutModal from './parts/finish_products_inout_details/addOutModal'
import fpDetailsEditOutModal from './parts/finish_products_inout_details/editOutModal'
import fpDetailsDeleteOutModal from './parts/finish_products_inout_details/deleteOutModal'

export default {
    name: "Finished Products Inout Details",
    setup(){
        const fpStore = useFinishedProductsStore()
        return { fpStore }
    },
    components: {
        StockHeader:StockHeader,
        fpDetailsAddOutModal:    fpDetailsAddOutModal,
        fpDetailsEditOutModal:   fpDetailsEditOutModal,
        fpDetailsDeleteOutModal: fpDetailsDeleteOutModal,
    },
    computed: {
        countTotalUzs(){
            let total = 0
            this.fpStore.inoutSetProducts.forEach((product, index) => {
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
                    title: "Партия",
                    key: 'set_name',
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
                        if(params.row.inout === 2) {
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
        }
    },
    methods: {
        async showEditOutModal(raw_material, index){

            const rmb = await this.callApi('get', '/app/get_production_rawmaterial/'+raw_material.id+'/'+this.fpStore.department_id)
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

            this.fpStore.departmentFinishProduct = finishProductObj
            this.fpStore.rowIndex                = index
            this.fpStore.fpDetailsEditOutModal   = true

        },
        showDeletingOutModal(raw_material, i){

            this.fpStore.departmentFinishProduct = raw_material
            this.fpStore.rowIndex                = i
            this.fpStore.fpDetailsDeleteOutModal = true
        },
    },
    async created() {
        document.title = "Тайёр махсулот Омбори | Партия | Махсулотлар"

        const setId = this.$route.params.inoutset_id
        const inout = this.$route.params.inout

        const [inoutSetProducts, inoutSet, currencies, fpFinishProducts] = await Promise.all([
            this.callApi('get', '/app/get_production_set_products/' + setId +'/' + inout +'/' + this.fpStore.department_id),
            this.callApi('get', '/app/get_production_set/' + this.fpStore.department_id + "/" + setId),
            this.callApi('get', '/app/add_currencies'),
            this.callApi('get', '/app/get_fp_finish_products/'+this.fpStore.department_id),
        ])

        this.fpStore.inoutSetProducts   = inoutSetProducts.data
        this.fpStore.inoutSet           = inoutSet.data
        this.fpStore.currencies         = currencies.data
        this.fpStore.fpFinishProducts   = fpFinishProducts.data

        if(this.fpStore.inoutSet.inout === 2) {
            this.fpStore.inoutSetProductsId = []
            this.fpStore.inoutSetRawMaterialsId = []
            this.fpStore.inoutSetProducts.forEach((product, index) => {
                this.fpStore.inoutSetProductsId.push(product.product_id)
                this.fpStore.inoutSetRawMaterialsId.push(product.id)
            })
        }

        this.loading = false
    }
}
</script>
