<template>
    <Modal
        v-model="otherExpensesStore.expenseEditModal"
        title="Тўловни тахрирлаш"
        :mask-closable = "false"
        :closable = "false"
        width="560"
    >
        <div>
            <slot name="body">
                <Form label-position="left" :label-width="180">
                    <FormItem label="* Харажат тури" class="ivu-mb">
                        <Select v-model="otherExpensesStore.expense.expense_type_id">
                            <template v-for="expenseType in otherExpensesStore.expenseTypes" :key="expenseType.id">
                                <Option :value="expenseType.id">{{expenseType.name}}</Option>
                            </template>
                        </Select>
                        <Text :class="{ 'd-none': !expense_type_err }" type="danger" class="m-0 p-0">Тўлдириш мажбурий</Text>
                    </FormItem>
                    <FormItem label="* Сумма (сўм)" class="ivu-mb">
                        <Input v-model="otherExpensesStore.expense.amount" placeholder="Сумма (сўм)" />
                        <Text :class="{ 'd-none': !expense_amount_err }" type="danger" class="m-0 p-0">Тўлдириш мажбурий</Text>
                    </FormItem>
                    <FormItem label="* Сана" class="ivu-mb">
                        <DatePicker v-model="otherExpensesStore.expense.date" format="yyyy-MM-dd" type="date" placeholder="Санани танланг" />
                        <Text :class="{ 'd-none': !expense_date_err }" type="danger" class="m-0 p-0">Тўлдириш мажбурий</Text>
                    </FormItem>
                    <FormItem label="* Сабаб" class="ivu-mb">
                        <Input v-model="otherExpensesStore.expense.description" placeholder="Сабаб" />
                        <Text :class="{ 'd-none': !expense_description_err }" type="danger" class="m-0 p-0">Тўлдириш мажбурий</Text>
                    </FormItem>

                </Form>
            </slot>
        </div>
        <template #footer>
            <Button @click="otherExpensesStore.expenseEditModal = false">Беркитиш</Button>
           <Button type="primary" @click="saveExpenseData" :disabled="isEditing" :loading="isEditing">{{isEditing ? 'Харажат тури...':'Сақлаш'}}</Button>
        </template>
    </Modal>
</template>
<script>
import { useOtherExpensesStore } from "../../../../stores/OtherExpensesStore";
export default {
    setup() {
        const otherExpensesStore = useOtherExpensesStore()

        return { otherExpensesStore }
    },
    data(){
        return {
            isEditing:              false,
            expense_type_err:       false,
            expense_amount_err:     false,
            expense_date_err:       false,
            expense_description_err:false,
            is_invalid:             false,
        }
    },
    methods: {
        async saveExpenseData(){
            if(this.otherExpensesStore.expense.expense_type_id == '') {
                this.is_invalid         = true
                this.expense_type_err  = true
            } else {
                this.expense_type_err  = false
            }

            if(this.otherExpensesStore.expense.amount == '') {
                this.is_invalid         = true
                this.expense_amount_err  = true
            } else {
                this.expense_amount_err  = false
            }

            if(this.otherExpensesStore.expense.description == '') {
                this.is_invalid         = true
                this.expense_description_err  = true
            } else {
                this.expense_description_err  = false
            }

            if(this.otherExpensesStore.expense.date == '') {
                this.is_invalid         = true
                this.payment_date_err    = true
            } else {
                this.payment_date_err    = false
                this.otherExpensesStore.expense.date = this.myDateFormat(this.otherExpensesStore.expense.date, 'yyyy-mm-dd')
            }

            if(this.is_invalid) {
                this.is_invalid = false
                return this.err('Барча катакларни тўлдириш мажбурий!')
            }

            const res = await this.callApi("post", "/app/edit_expense", this.otherExpensesStore.expense)

            if(res.status === 200) {

                this.otherExpensesStore.expenses[this.otherExpensesStore.rowIndex]  = res.data

                this.s('Ўзгаришлари мувоффақиятли сақланди!')
                this.otherExpensesStore.expenseEditModal = false

            } else {
                if(res.status == 422) {
                    if(res.data.errors.amount) {
                        this.err(res.data.errors.amount[0])
                    }
                } else {
                    this.swr()
                }
            }
        }
    },
    created(){

    }
}
</script>

