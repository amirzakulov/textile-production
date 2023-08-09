<template>
    <ExpenseHeader />
    <div class="_1adminOverveiw_table_recent _border_radious _mar_b30 _p20 pt-0">

        <Row>
            <Col span="16">
                <Card>
                    <ExpenseAddForm></ExpenseAddForm>
                    <Divider />

                    <Table size="small" :columns="columns" :data="otherExpensesStore.expenses" :loading="loading">
                        <template #date="{row}">
                            {{ myDateFormat(row.date, "MM.DD.YYYY") }}
                        </template>
                        <template #amount="{row}">
                            {{ row.amount.toLocaleString() }}
                        </template>
                    </Table>

                </Card>
            </Col>
            <Col span="7" offset="1">
                <Card>
                    <div>
                        <Button type="primary" icon="md-add" @click="otherExpensesStore.expenseTypeAddModal  = true">Харажат тури қўшиш</Button>
                    </div>
                    <Divider />
                    <template v-if="otherExpensesStore.expenseTypes.length">
                        <Row class="bg-light p-2 mb-1"
                        v-for="(expense, i) in otherExpensesStore.expenseTypes" :key="i">
                            <Col span="18"><strong>{{expense.name}}</strong></Col>
                            <Col span="6" class="text-right">
                                <ButtonGroup>
                                    <Button size="small" type="info" @click="showExpenseTypeEditModal(expense, i)"><Icon type="md-create" /></Button>
                                    <Button size="small" type="error" @click="showExpenseTypeDeletingModal(expense, i)" :loading="expense.isDeleting"><Icon type="md-close" /></Button>
                                </ButtonGroup>
                            </Col>
                        </Row>
                    </template>
                </Card>
            </Col>
        </Row>
    </div>

    <!-- Xisoblagich qushish -->
    <ExpenseTypeAddModal></ExpenseTypeAddModal>

    <!-- Xisoblagich tahrirlash -->
    <ExpenseTypeEditModal></ExpenseTypeEditModal>

    <!-- Xisoblagich uchirish -->
    <ExpenseTypeDeleteModal></ExpenseTypeDeleteModal>

    <!-- Xarajatni tahrirlash -->
    <ExpenseEditModal></ExpenseEditModal>

    <!-- Xarajatni uchirish -->
    <ExpenseDeleteModal></ExpenseDeleteModal>

</template>

<script>
import ExpenseHeader from './parts/header.vue'

import ExpenseTypeAddModal from "./parts/expenses/types/addModal.vue";
import ExpenseTypeEditModal from "./parts/expenses/types/editModal.vue";
import ExpenseTypeDeleteModal from "./parts/expenses/types/deleteModal.vue";

import ExpenseAddForm from "./parts/expenses/addForm.vue";
import ExpenseEditModal from "./parts/expenses/editModal.vue";
import ExpenseDeleteModal from "./parts/expenses/deleteModal.vue";

import {useOtherExpensesStore} from "../../stores/OtherExpensesStore";
import {resolveComponent} from "vue";
export default {
    name: "Other Expenses",
    setup() {
        const otherExpensesStore = useOtherExpensesStore()

        return { otherExpensesStore }
    },
    components: {
        ExpenseHeader: ExpenseHeader,

        ExpenseTypeAddModal:  ExpenseTypeAddModal,
        ExpenseTypeEditModal: ExpenseTypeEditModal,
        ExpenseTypeDeleteModal: ExpenseTypeDeleteModal,

        ExpenseAddForm: ExpenseAddForm,
        ExpenseEditModal: ExpenseEditModal,

        ExpenseDeleteModal: ExpenseDeleteModal,
    },
    data() {
        return {
            loading: true,
            columns: [
                {
                    title: "Харажат тури",
                    key: 'name',
                    width: "180",
                    sortable: true,
                    className: 'font-11'
                },
                {
                    title: "Сумма (сўм)",
                    slot: 'amount',
                    className: 'font-11'
                },
                {
                    title: "Сана",
                    slot: 'date',
                    className: 'font-11'
                },
                {
                    title: "Сабаб",
                    key: 'description',
                    className: 'font-11'
                },
                {
                    title: " ",
                    key: 'action',
                    width: 120,
                    align: 'right',
                    className: 'font-11',
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
                                            this.showExpenseEditModal(params.row, params.index)
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
                                            this.showExpenseDeletingModal(params.row, params.index)
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
        showExpenseTypeEditModal(expenseType, index){
            let expenseTypeObj = {
                id:             expenseType.id,
                name:           expenseType.name,
            }

            this.otherExpensesStore.expenseType             = expenseTypeObj
            this.otherExpensesStore.expenseTypeEditModal    = true
            this.otherExpensesStore.rowIndex                = index

        },
        showExpenseTypeDeletingModal(expenseType, i){
            this.otherExpensesStore.expenseType             = expenseType
            this.otherExpensesStore.rowIndex                = i
            this.otherExpensesStore.expenseTypeDeleteModal  = true
        },

        showExpenseEditModal(expense, index){

            let expenseObj = {
                id:              expense.id,
                expense_type_id: expense.expense_type_id,
                amount:          expense.amount,
                description:     expense.description,
                date:            expense.date
            }

            this.otherExpensesStore.expense             = expenseObj
            this.otherExpensesStore.expenseEditModal    = true
            this.otherExpensesStore.rowIndex            = index

        },
        showExpenseDeletingModal(expense, i){

            this.otherExpensesStore.expense             = expense
            this.otherExpensesStore.rowIndex            = i
            this.otherExpensesStore.expenseDeleteModal  = true

        },
    },
    async created(){
        const now         = new Date();
        const year        = now.getFullYear()
        const month       = now.getMonth()
        const day         = now.getDate()
        const firstFullDate = new Date(year, month, day)
        this.otherExpensesStore.expense.date = this.myDateFormat(firstFullDate, 'yyyy-mm-dd')

        const expenseTypes = await this.callApi('get', '/app/get_expense_types/')
        this.otherExpensesStore.expenseTypes = expenseTypes.data

        const expenses = await this.callApi('get', '/app/get_expenses')
        this.otherExpensesStore.expenses = expenses.data
        this.loading = false
    },
}
</script>
