<template>
    <Modal
        v-model="communalTypesStore.paymentEditModal"
        title="Тўловни тахрирлаш"
        :mask-closable = "false"
        :closable = "false"
        width="560"
    >
        <div>
            <slot name="body">
                <Form label-position="left" :label-width="220">
                    <input type="hidden" v-model="communalTypesStore.payment.id">
                    <FormItem label="Хисоблагич №" class="ivu-mb">
                        <Input v-model="communalTypesStore.payment.device" disabled />
                    </FormItem>
                    <FormItem label="* Охирги кўрсаткич (актив)" class="ivu-mb">
                        <Input v-model="communalTypesStore.payment.active_value" placeholder="Охирги кўрсаткич" />
                        <Text :class="{ 'd-none': !payment_active_value_err }" type="danger" class="m-0 p-0">Тўлдириш мажбурий</Text>
                    </FormItem>
                    <FormItem label="* Нарх (актив)" class="ivu-mb">
                        <Input v-model="communalTypesStore.payment.active_price" placeholder="Нарх" />
                        <Text :class="{ 'd-none': !payment_active_price_err }" type="danger" class="m-0 p-0">Тўлдириш мажбурий</Text>
                    </FormItem>
                    <FormItem label="* Охирги кўрсаткич (реактив)" class="ivu-mb" :class="{ 'd-none': communalTypesStore.payment.communal_type_id != 2 }">
                        <Input v-model="communalTypesStore.payment.reactive_value" placeholder="Охирги кўрсаткич" />
                        <Text :class="{ 'd-none': !payment_reactive_value_err }" type="danger" class="m-0 p-0">Тўлдириш мажбурий</Text>
                    </FormItem>
                    <FormItem label="* Нарх (реактив)" class="ivu-mb" :class="{ 'd-none': communalTypesStore.payment.communal_type_id != 2 }">
                        <Input v-model="communalTypesStore.payment.reactive_price" placeholder="Нарх" />
                        <Text :class="{ 'd-none': !payment_reactive_price_err }" type="danger" class="m-0 p-0">Тўлдириш мажбурий</Text>
                    </FormItem>
                    <FormItem label="* Тўлов" class="ivu-mb">
                        <Input v-model="communalTypesStore.payment.payment" placeholder="Тўлов" />
                        <Text :class="{ 'd-none': !payment_payment_err }" type="danger" class="m-0 p-0">Тўлдириш мажбурий</Text>
                    </FormItem>
                    <FormItem label="* Сана" class="ivu-mb">
                        <DatePicker v-model="communalTypesStore.payment.date" format="yyyy-MM-dd" type="date" placeholder="Санани танланг" />
                        <Text :class="{ 'd-none': !payment_date_err }" type="danger" class="m-0 p-0">Тўлдириш мажбурий</Text>
                    </FormItem>
                </Form>
            </slot>
        </div>
        <template #footer>
            <Button @click="communalTypesStore.paymentEditModal = false">Беркитиш</Button>
           <Button type="primary" @click="savePaymentData" :disabled="isEditing" :loading="isEditing">{{isEditing ? 'Хисоблагич...':'Сақлаш'}}</Button>
        </template>
    </Modal>
</template>
<script>
import { useCommunalTypesStore } from '../../../../../stores/CommunalTypesStore'
export default {
    setup() {
        const communalTypesStore = useCommunalTypesStore()

        return { communalTypesStore }
    },
    data(){
        return {
            isEditing:                  false,
            payment_active_value_err:   false,
            payment_active_price_err:   false,
            payment_reactive_value_err: false,
            payment_reactive_price_err: false,
            payment_payment_err:        false,
            payment_date_err:           false,
            is_invalid:                 false,
        }
    },
    methods: {
        async savePaymentData(){
            const payment = this.communalTypesStore.payment
            const prevNextPayments = await this.callApi("post", "/app/prev_next_communal_payment", payment)

            if(this.communalTypesStore.payment.active_value == '') {
                this.is_invalid                 = true
                this.payment_active_value_err   = true
            } else {
                this.payment_active_value_err   = false
            }

            if(prevNextPayments.data.prev != null && payment.active_value < prevNextPayments.data.prev.active_value ||
            prevNextPayments.data.next != null && payment.active_value >= prevNextPayments.data.next.active_value
            ) {
                this.payment_active_value_err   = false
                return this.err('Охирги кўрсаткич нотўғри!')
            }

            if(this.communalTypesStore.payment.active_price == '') {
                this.is_invalid                 = true
                this.payment_active_price_err   = true
            } else {
                this.payment_active_price_err   = false
            }

            if(this.communalTypesStore.payment.communal_type_id == 2) {
                if(this.communalTypesStore.payment.reactive_value == '') {
                    this.is_invalid                 = true
                    this.payment_reactive_value_err = true
                } else {
                    this.payment_reactive_value_err = false
                }

                if(this.communalTypesStore.payment.reactive_price == '') {
                    this.is_invalid                 = true
                    this.payment_reactive_price_err = true
                } else {
                    this.payment_reactive_price_err = false
                }
            }

            if(this.communalTypesStore.payment.date == '') {
                this.is_invalid         = true
                this.payment_date_err   = true
            } else {
                this.payment_date_err   = false
                this.communalTypesStore.payment.date = this.myDateFormat(this.communalTypesStore.payment.date, 'yyyy-mm-dd')
            }

            if(this.communalTypesStore.payment.payment == '') {
                this.is_invalid             = true
                this.payment_payment_err    = true
            } else {
                this.payment_payment_err    = false
            }

            if(this.is_invalid) {
                this.is_invalid = false
                return this.err('Барча катакларни тўлдириш мажбурий!')
            }

            const res = await this.callApi("post", "/app/edit_communal_payment", this.communalTypesStore.payment)

            if(res.status === 200) {

                const currentPayment    = res.data.current
                const nextPayment       = res.data.next

                this.communalTypesStore.payments[this.communalTypesStore.rowIndex].active_value     = currentPayment.active_value
                this.communalTypesStore.payments[this.communalTypesStore.rowIndex].active_amount    = currentPayment.active_amount
                this.communalTypesStore.payments[this.communalTypesStore.rowIndex].active_price     = currentPayment.active_price
                this.communalTypesStore.payments[this.communalTypesStore.rowIndex].reactive_value   = currentPayment.reactive_value
                this.communalTypesStore.payments[this.communalTypesStore.rowIndex].reactive_amount  = currentPayment.reactive_amount
                this.communalTypesStore.payments[this.communalTypesStore.rowIndex].reactive_price   = currentPayment.reactive_price
                this.communalTypesStore.payments[this.communalTypesStore.rowIndex].payment          = currentPayment.payment
                this.communalTypesStore.payments[this.communalTypesStore.rowIndex].date             = currentPayment.date

                if(!Object.is(nextPayment, null)) {
                    this.communalTypesStore.payments.forEach((payment, index) => {
                        if(nextPayment.id == payment.id) {
                            payment.active_amount   = nextPayment.active_amount
                            payment.reactive_amount = nextPayment.reactive_amount
                        }
                    })
                }

                this.s('Ўзгаришлари мувоффақиятли сақланди!')
                this.communalTypesStore.paymentEditModal = false

            } else {
                if(res.status == 422) {
                    if(res.data.errors.active_value) {
                        this.err(res.data.errors.active_value[0])
                    }
                } else {
                    this.swr()
                }
            }
        }
    },
    created(){}
}
</script>

