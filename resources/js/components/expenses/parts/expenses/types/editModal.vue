<template>
    <Modal
        v-model="otherExpensesStore.expenseTypeEditModal"
        title="Харажат турини тахрирлаш"
        :mask-closable = "false"
        :closable = "false"
        width="560"
    >
        <div>
            <slot name="body">
                <Form label-position="left">
                    <input type="hidden" v-model="otherExpensesStore.expenseType.id">
                    <FormItem label="* Харажат тури" class="mb-0">
                        <Input v-model="otherExpensesStore.expenseType.name" placeholder="Харажат тури"></Input>
                        <Text :class="{ 'd-none': !expense_type_err }" type="danger" class="m-0 p-0">Тўлдириш мажбурий</Text>
                    </FormItem>
                </Form>
            </slot>
        </div>
        <template #footer>
            <Button @click="otherExpensesStore.expenseTypeEditModal = false">Беркитиш</Button>
           <Button type="primary" @click="saveExpenseData" :disabled="isEditing" :loading="isEditing">{{isEditing ? 'Хисоблагич...':'Сақлаш'}}</Button>
        </template>
    </Modal>
</template>
<script>
import { useOtherExpensesStore } from "../../../../../stores/OtherExpensesStore";
export default {
    setup() {
        const otherExpensesStore = useOtherExpensesStore()

        return { otherExpensesStore }
    },
    data(){
        return {
            isEditing:          false,
            expense_type_err:   false,
            is_invalid:         false,
        }
    },
    methods: {
        async saveExpenseData(){
            if(this.otherExpensesStore.expenseType.name == '') {
                this.is_invalid         = true
                this.expense_type_err    = true
            } else {
                this.expense_type_err    = false
            }

            if(this.is_invalid) {
                this.is_invalid = false
                return this.err('Барча катакларни тўлдириш мажбурий!')
            }

            const res = await this.callApi("post", "/app/edit_expense_type", this.otherExpensesStore.expenseType)

            if(res.status === 200) {
                this.otherExpensesStore.expenseTypes[this.otherExpensesStore.rowIndex].name = res.data.name
                this.otherExpensesStore.expenseType.name = ''
                this.s('Ўзгаришлари мувоффақиятли сақланди!')
                this.otherExpensesStore.expenseTypeEditModal = false
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

