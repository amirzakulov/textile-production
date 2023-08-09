<template>
    <Form ref="monthlyCounters" label-position="top">
        <Row :gutter="8">
            <Col span="5">
                <FormItem label="* Харажат тури">
                    <Select v-model="data.expense_type_id">
                        <template v-for="expenseType in otherExpensesStore.expenseTypes" :key="expenseType.id">
                            <Option :value="expenseType.id">{{expenseType.name}}</Option>
                        </template>
                    </Select>
                    <Text :class="{ 'd-none': !expense_type_err}" type="danger" class="m-0 p-0">Тўлдириш мажбурий</Text>
                </FormItem>
            </Col>
            <Col span="4">
                <FormItem label="* Сумма (сўм)">
                    <Input v-model="data.amount"></Input>
                    <Text :class="{ 'd-none': !expense_amount_err}" type="danger" class="m-0 p-0">Тўлдириш мажбурий</Text>
                </FormItem>
            </Col>
            <Col span="4">
                <FormItem label="* Сана">
                    <DatePicker v-model="data.date" format="yyyy-MM-dd" type="date" placeholder="Санани танланг" />
                    <Text :class="{ 'd-none': !expense_date_err}" type="danger" class="m-0 p-0">Тўлдириш мажбурий</Text>
                </FormItem>
            </Col>
            <Col span="7">
                <FormItem label="Сабаб">
                    <Input v-model="data.description"></Input>
                </FormItem>
            </Col>

            <Col span="3">
                <FormItem label="&nbsp;&nbsp;" class="text-right">
                    <Button type="primary" style="width:100%" @click="addExpense">Қўшиш</Button>
                </FormItem>
            </Col>
        </Row>
    </Form>
</template>

<script>
import {useOtherExpensesStore} from "../../../../stores/OtherExpensesStore";
export default {
    setup() {
        const otherExpensesStore = useOtherExpensesStore()

        return { otherExpensesStore }
    },
    data(){
        return {
            isAdding:              false,

            data: {
                name:            '',
                expense_type_id: '',
                amount:          '',
                description:     '',
                date:            ''
            },

            expense_type_err:       false,
            expense_amount_err:     false,
            expense_date_err:       false,
            is_invalid:             false,
        }
    },
    methods: {
        async addExpense(){
            if(this.data.expense_type_id == '') {
                this.is_invalid         = true
                this.expense_type_err  = true
            } else {
                this.expense_type_err  = false
            }

            if(this.data.amount == '') {
                this.is_invalid         = true
                this.expense_amount_err  = true
            } else {
                this.expense_amount_err  = false
            }

            if(this.data.date == '') {
                this.is_invalid         = true
                this.expense_date_err    = true
            } else {
                this.expense_date_err    = false
                this.data.date = this.myDateFormat(this.data.date, 'yyyy-mm-dd')
            }

            if(this.is_invalid) {
                this.is_invalid = false
                return this.err('Барча катакларни тўлдириш мажбурий!')
            }

            const res = await this.callApi("post", "/app/add_expense", this.data)

            if(res.status === 200) {
                this.otherExpensesStore.expenses.unshift(res.data);

                this.data.expense_type_id   = ''
                this.data.amount            = ''
                this.data.description       = ''
                this.data.expense_type_id   = ''
                this.s('Ўзгаришлари мувоффақиятли сақланди!')

            } else {
                if(res.status == 422) {
                    if(res.data.errors.expense_type_id) {
                        this.err(res.data.errors.expense_type_id[0])
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

