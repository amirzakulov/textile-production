<template>
    <Table size="small" :columns="columns" :data="productionStore.inoutSets" :loading="loading" >
        <template #inout="{row}">
            <Icon v-if="row.inout == 1" type="md-arrow-down" color="#0099ff" />
            <Icon v-else type="md-arrow-up" color="#ff0000" />
        </template>
        <template #created_date="{row}">
            {{ new Date(row.created_date).toLocaleString() }}
        </template>
    </Table>
</template>

<script>

import {useProductionStore} from "../../../../stores/ProductionStore";
import {resolveComponent} from "vue";

export default {
    name: "Production Income By Set",
    setup(){
        const productionStore = useProductionStore()
        return { productionStore }
    },
    data(){
        return {
            loading: true,
            columns: [
                {
                    title: "",
                    slot: 'inout',
                    width: "25",
                },
                {
                    title: "Партия №",
                    key: 'name',
                    sortable: true
                },
                {
                    title: "Қабул қилди (ФИШ)",
                    key: 'fullName',
                    width: "180",
                },
                {
                    title: "Қаердан",
                    key: 'department_from'
                },
                {
                    title: "Қаерга",
                    key: 'department_to'
                },
                {
                    title: "Сана",
                    slot: 'created_date'
                },
                {
                    title: " ",
                    key: 'action',
                    width: 180,
                    align: 'right',
                    render: (h, params) => {

                        return h('div', {class: 'ivu-btn-group ivu-btn-group-default'}, [
                            h(resolveComponent('Button'), {
                                title:"Кўриш",
                                type: 'success',
                                size: 'small',
                                to: {name:'productionFinishProductsInOutDetails', params: {department_id:this.productionStore.department_id, inoutset_id:params.row.id, inout:params.row.inout}},

                            }, {
                                default() {
                                    return h(resolveComponent('Icon'), {
                                        type: 'md-redo',
                                        color: "#fff",
                                    })
                                }
                            }),

                            h(resolveComponent('Button'), {
                                title:"Ўчириш",
                                type: 'primary',
                                size: 'small',
                                loading: params.row.isDeleting,
                                disabled: params.row.inout === 1,

                                onClick: () => {
                                    this.showEditingModal(params.row, params.index)
                                }
                            }, {
                                default() {
                                    return h(resolveComponent('Icon'), {
                                        type: 'md-create'
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
        showEditingModal(departmentInoutSet, i){
            this.productionStore.inoutSet.to_department_id = departmentInoutSet.to_department_id
            this.productionStore.departmentInoutSet = departmentInoutSet
            this.productionStore.rowIndex           = i
            this.productionStore.fpSetEditModal     = true
        },
        showDeletingModal(departmentInoutSet, i){
            this.productionStore.departmentInoutSet = departmentInoutSet
            this.productionStore.rowIndex           = i
            this.productionStore.fpSetDeleteModal   = true
        }
    },

    async created(){
        const inoutSets = await this.callApi('get', '/app/get_production_finish_products_sets/'+this.productionStore.department_id)
        this.productionStore.inoutSets = inoutSets.data

        this.loading = false
    }
}
</script>
