<template>
    <CurrencyHeader />
    <!--~~~~~~~ TABLE ONE ~~~~~~~~~-->
    <div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">
        <Row :gutter="8" border="bottom" class="mb-3">
            <Col span="3">
                <Button type="primary" @click="currencyStore.addCurrencyRateModal = true" class="text-white"><Icon type="md-add" class="mr-1" />Қўшиш</Button>
            </Col>
            <Col span="6"></Col>
        </Row>

        <Row :gutter="8">
            <Col span="12">
                <Table size="small" :columns="columns" :data="currencyStore.currencyRates" :loading="loading">
                    <template #created_at="{row}">
                        {{ myDateFormat(row.created_at, "MM.DD.YYYY") }}
                    </template>
                    <template #updated_at="{row}">
                        {{ myDateFormat(row.updated_at, "MM.DD.YYYY") }}
                    </template>
                </Table>
            </Col>
        </Row>
    </div>

    <CurrencyRateAddModal />
    <CurrencyRateEditModal />
    <CurrencyRateDeleteModal />

</template>

<script>
import CurrencyHeader from './parts/header.vue'
import { useCurrenciesStore } from "../../stores/CurrenciesStore";
import {resolveComponent} from "vue";

import CurrencyRateAddModal from "./parts/addModal.vue"
import CurrencyRateEditModal from "./parts/editModal.vue"
import CurrencyRateDeleteModal from "./parts/deleteModal.vue"

export default {
    name: "Currency Rates",
    components: {
        CurrencyHeader: CurrencyHeader,

        CurrencyRateAddModal:    CurrencyRateAddModal,
        CurrencyRateEditModal:   CurrencyRateEditModal,
        CurrencyRateDeleteModal: CurrencyRateDeleteModal,
    },
    setup() {
        const currencyStore = useCurrenciesStore()

        return { currencyStore }
    },
    data() {
        return {
            loading: true,
            columns: [
                {
                    title: "Қиймати (сўм)",
                    key: 'rate',
                },
                {
                    title: "Сана",
                    slot: 'created_at',
                    width: "160",
                    sortable: true
                },
                {
                    title: "Янгиланган Сана",
                    slot: 'updated_at',
                    width: "160",
                    sortable: true
                },
                {
                    title: "action",
                    key: 'action',
                    width: 180,
                    align: 'right',
                    render: (h, params) => {

                        return h('div', {
                                class: 'ivu-btn-group ivu-btn-group-default'
                            },
                            [
                                h(resolveComponent('Button'),
                                    {
                                        title:"Taxrirlash",
                                        type: 'primary',
                                        size: 'small',

                                        onClick: () => {
                                            this.showEditModal(params.row, params.index)
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
                                        title:"O'chirish",
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
    methods : {
        showEditModal(currencyRate, index) {
            let currencyRateObj = {
                id:          currencyRate.id,
                currency_id: currencyRate.currency_id,
                rate:        currencyRate.rate,
            }

            this.currencyStore.currencyRate          = currencyRateObj
            this.currencyStore.rowIndex              = index
            this.currencyStore.editCurrencyRateModal = true
        },
        showDeletingModal(currencyRate, i){
            this.currencyStore.currencyRate             = currencyRate
            this.currencyStore.rowIndex                 = i
            this.currencyStore.deleteCurrencyRateModal  = true
        },
    },
    async created(){
        if(this.$route.params.currency_id === '') {
            this.currencyStore.currency_id = 1
        } else {
            this.currencyStore.currency_id      = this.$route.params.currency_id
        }
        const currencyRates                 = await this.callApi('get', '/app/get_currency_rates/'+this.currencyStore.currency_id)
        this.currencyStore.currencyRates    = currencyRates.data

        this.loading = false
    }
}
</script>



