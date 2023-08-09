<template>
    <Modal
        v-model="otherExpensesStore.expenseTypeAddModal"
        title="Харажат тури қўшиш"
        :mask-closable = "false"
        :closable = "false"
        width="560"
    >
        <div>
            <slot name="body">
                <Form label-position="left">
                    <FormItem label="* Харажат тури" class="mb-0">
                        <Input v-model="data.name" placeholder="Харажат тури"></Input>
                        <Text :class="{ 'd-none': !expense_type_name_err }" type="danger" class="m-0 p-0">Тўлдириш мажбурий</Text>
                    </FormItem>
                </Form>
            </slot>
        </div>
        <template #footer>
            <Button @click="otherExpensesStore.expenseTypeAddModal = false">Беркитиш</Button>
           <Button type="primary" @click="addExpenseType" :disabled="isAdding" :loading="isAdding">{{isAdding ? 'Харажат тури...':'Қўшиш'}}</Button>
        </template>
    </Modal>
</template>
<script>

import { useOtherExpensesStore } from "../../../../../stores/OtherExpensesStore";
export default {
    data(){
        return {
            isAdding:               false,
            data: {name: ''},
            expense_type_name_err:  false,
            is_invalid:             false,
        }
    },
    setup() {
        const otherExpensesStore = useOtherExpensesStore()

        return { otherExpensesStore }
    },
    methods: {
        async addExpenseType(){
            if(this.data.name == '') {
                this.is_invalid         = true
                this.expense_type_name_err    = true
            } else {
                this.expense_type_name_err    = false
            }

            if(this.is_invalid) {
                this.is_invalid = false
                return this.err('Барча катакларни тўлдириш мажбурий!')
            }

            const res = await this.callApi("post", "/app/add_expense_type", this.data)

            if(res.status === 201) {
                this.otherExpensesStore.expenseTypes.unshift(res.data);
                this.data.name = ''
                this.s('Харажат тури мувоффақиятли сақланди!')
                this.otherExpensesStore.expenseTypeAddModal = false
            } else {
                if(res.status == 422) {
                    if(res.data.errors.name) {
                        this.err(res.data.errors.name[0])
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

