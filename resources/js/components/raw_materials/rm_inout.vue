<template>
    <StockHeader />
    <div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">
        <Row :gutter="8" border="bottom" class="mb-3">
            <Col span="6">
                <ButtonGroup>
                    <Button type="primary" @click="openProductInModal"><Icon type="md-add" /> Кирим</Button>
                    <Button type="error" @click="openProductOutModal"><Icon type="md-remove" /> Чиқим</Button>
                </ButtonGroup>
            </Col>
        </Row>

        <Table size="small" :columns="columns" :data="rmStore.rmSets" :loading="loading">
            <template #inout="{row}">
                <Icon v-if="row.inout == 1" type="md-arrow-down" color="#0099ff" />
                <Icon v-else type="md-arrow-up" color="#ff0000" />
            </template>
        </Table>
    </div>

    <!-- Xomashyo kirimi -->
    <RawMaterialsIncomeModal />
    <RawMaterialsOutgoingModal />

    <RawMaterialsEditSetModal />
    <RawMaterialsDeleteSetModal />
</template>

<script>
import StockHeader from './parts/header.vue'
import {useRawMaterialsStore} from "../../stores/RawMaterialsStore";
import {useCurrenciesStore} from "../../stores/CurrenciesStore";
import {resolveComponent} from "vue";

import RawMaterialsIncomeModal from './parts/inout/inModal.vue'
import RawMaterialsOutgoingModal from './parts/inout/outModal.vue'

import RawMaterialsEditSetModal from './parts/inout/editSetModal.vue'
import RawMaterialsDeleteSetModal from './parts/inout/deleteSetModal.vue'


export default {
    name: "Raw Materials Income",
    setup() {
        const rmStore = useRawMaterialsStore()
        const currencyStore = useCurrenciesStore()

        return { rmStore, currencyStore }
    },
    components: {
        StockHeader:StockHeader,
        RawMaterialsIncomeModal:    RawMaterialsIncomeModal,
        RawMaterialsOutgoingModal:  RawMaterialsOutgoingModal,

        RawMaterialsEditSetModal:   RawMaterialsEditSetModal,
        RawMaterialsDeleteSetModal: RawMaterialsDeleteSetModal,
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
                    title: "Хужжат №",
                    key: 'name',
                    sortable: true
                },
                {
                    title: "Қабул қилди (ФИШ)",
                    key: 'fullName',
                    width: "200",
                    sortable: true
                },
                {
                    title: "Қаердан",
                    key: 'department_from',
                    sortable: true
                },
                {
                    title: "Қаерга",
                    key: 'department_to',
                    sortable: true
                },
                {
                    title: "Сана",
                    key: 'created_date',
                    sortable: true
                },
                {
                    title: "action",
                    key: 'action',
                    width: 180,
                    align: 'right',
                    render: (h, params) => {

                        return h('div', {class: 'ivu-btn-group ivu-btn-group-default'}, [
                                h(resolveComponent('Button'), {
                                    title:"Кўриш",
                                    type: 'success',
                                    size: 'small',
                                    to: {name:'rmInOutDetails', params: {inoutset_id:params.row.id, inout:params.row.inout}},

                                }, {
                                    default() {
                                        return h(resolveComponent('Icon'), {
                                            type: 'md-redo',
                                            color: "#fff",
                                        })
                                    }
                                }),

                                h(resolveComponent('Button'), {
                                    title:"Кўриш",
                                    type: 'primary',
                                    size: 'small',
                                    disabled: params.row.inout === 1,

                                    onClick: () => {
                                        this.showEditingModal(params.row, params.index)
                                    }

                                }, {
                                    default() {
                                        return h(resolveComponent('Icon'), {
                                            type: 'md-create',
                                            color: "#fff",
                                        })
                                    }
                                }),

                                h(resolveComponent('Button'), {
                                    title:"Ўчириш",
                                    type: 'error',
                                    size: 'small',
                                    loading: params.row.isDeleting,

                                    onClick: () => {
                                        this.showDeletingModal(params.row, params.index)
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
                },

            ],
        }
    },
    methods: {
        showEditingModal(rmSet, i){
            this.rmStore.inoutSet.to_department_id  = rmSet.to_department_id
            this.rmStore.rmSet                      = rmSet
            this.rmStore.rowIndex                   = i
            this.rmStore.rmSetEditModal             = true
        },
        showDeletingModal(rmSet, i){
            this.rmStore.rmSet              = rmSet
            this.rmStore.rowIndex           = i
            this.rmStore.rmSetDeleteModal   = true
        },
        openProductInModal(){
            this.rmStore.created_date = this.myDateFormat(null, "yyyy-mm-dd")
            this.rmStore.productInModal = true
        },
        openProductOutModal(){
            this.rmStore.created_date = this.myDateFormat(null, "yyyy-mm-dd")
            this.rmStore.productOutModal = true
        }
    },
    async created() {
        document.title = "Хомашё Омбори | Кирим"
        let department_types = {types: [2]}
        const [rmSets, products, currencies, departments, departmentRawMaterials, dollarRate] = await Promise.all([
            this.callApi('get', '/app/get_rmsets'),
            this.callApi('get', '/app/get_products/'+this.rmStore.department_id),
            this.callApi('get', '/app/add_currencies'),
            this.callApi('post', '/app/get_departments_types', department_types),
            this.callApi('get', '/app/get_rm_rawmaterials/'+this.rmStore.department_id),
            this.callApi('get', '/app/get_currency_rate_last/1'),
        ])

        this.rmStore.rmSets         = rmSets.data
        this.rmStore.products       = products.data
        this.rmStore.currencies     = currencies.data
        this.rmStore.departments    = departments.data
        this.rmStore.departmentRawMaterials = departmentRawMaterials.data
        this.currencyStore.dollarRate = dollarRate.data

        this.loading = false
    }
}
</script>
