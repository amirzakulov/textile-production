<template>
    <Table size="small" :columns="columns" :data="fpStore.inoutSets" :loading="loading" >
        <template #inout="{row}">
            <Icon v-if="row.inout == 1" type="md-arrow-down" color="#0099ff" />
            <Icon v-else type="md-arrow-up" color="#ff0000" />
        </template>
        <template #created_at="{row}">
            {{ new Date(row.created_at).toLocaleString() }}
        </template>
    </Table>
</template>

<script>

import {useFinishedProductsStore} from "../../../../stores/FinishedProductsStore";
import {resolveComponent} from "vue";

export default {
    name: "Production Income By Set",
    setup(){
        const fpStore = useFinishedProductsStore()
        return { fpStore }
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
                    slot: 'created_at'
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
                                type: 'primary',
                                size: 'small',
                                to: {name:'fgFinishedProductsInoutDetails', params: {inoutset_id:params.row.set_id, inout:params.row.inout}},

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
                                type: 'error',
                                size: 'small',
                                loading: params.row.isDeleting,
                                disabled: params.row.inout == 1 ? true:false,

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
        showDeletingModal(inoutSet, i){

            this.fpStore.fpSet      = inoutSet
            this.fpStore.rowIndex   = i
            this.fpStore.fpFinishedProductDeleteSetModal   = true
        }
    },

    async created(){
        const inoutSets = await this.callApi('get', '/app/get_fp_finish_products_sets/'+this.fpStore.department_id)
        this.fpStore.inoutSets = inoutSets.data

        this.loading = false
    }
}
</script>
