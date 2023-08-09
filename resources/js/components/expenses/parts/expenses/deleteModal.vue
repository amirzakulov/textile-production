<template>

    <!-- Delete tag modal -->
    <Modal v-model="otherExpensesStore.expenseDeleteModal" width="360">
        <template #header>
            <p style="color:#f60;text-align:center">
                <Icon type="ios-close-circle" size="20" />
                <span>Ўчиришни тасдиқлаш?</span>
            </p>
        </template>
        <div style="text-align:center">
            <p>Хақиқатдан хам ўчирасизми?</p>
        </div>
        <template #footer>
<!--            <Button type="error" size="large" long :loading="isDeleting" :disabled="isDeleting" @click="deleteExpense">ХА</Button>-->
            <Row :gutter="8">
                <Col span="12">
                    <Button type="error" size="large" long :loading="isDeleting" :disabled="isDeleting" @click="deleteItem">Хa</Button>
                </Col>
                <Col span="12">
                    <Button size="large" long @click="cancelDelete">ЙЎҚ</Button>
                </Col>
            </Row>
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
            isDeleting: false,
        }
    },
    methods: {
        async deleteItem(){
            this.isDeleting = true
            const res = await this.callApi("post", "/app/delete_expense", this.otherExpensesStore.expense)

            if(res.status === 200) {

                this.otherExpensesStore.expenses.splice(this.otherExpensesStore.rowIndex,1)
                this.s('Харажат мувоффақиятли ўчирилди!')

            } else {
                this.swr();
            }

            this.isDeleting = false
            this.otherExpensesStore.expenseDeleteModal = false
        },
        cancelDelete(){
            this.otherExpensesStore.expenseDeleteModal = false
        }
    },
    created(){}
}
</script>

