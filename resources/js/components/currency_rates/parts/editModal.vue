<template>
    <Modal
        v-model="currencyStore.editCurrencyRateModal"
        title="Махсулотни тахрирлаш"
        :mask-closable="false"
        :closable = "false"
        :width = "768"
        @on-visible-change="currencyStore.modalVisibility"
    >

        <Form ref="formValidate" :label-width="180">
            <FormItem label="* Қиймати" class="ivu-mb">
                <Input v-model="currencyStore.currencyRate.rate" placeholder="Қиймати"></Input>
                <Text :class="{ 'd-none': !rate_err }" type="danger">Тўлдириш мажбурий!</Text>
            </FormItem>
        </Form>
        <template #footer>
            <Button @click="currencyStore.editCurrencyRateModal=false">Беркитиш</Button>
            <Button type="primary" @click="editCurrnecy" :disabled="isEditing" :loading="isEditing">{{isEditing ? 'Махсулот...':'Сақлаш'}}</Button>
        </template>
    </Modal>
</template>

<script>
import {useCurrenciesStore} from "../../../stores/CurrenciesStore";

export default {
    name: "Add Fashion Modal",
    setup() {
        const currencyStore = useCurrenciesStore()

        return { currencyStore }
    },
    data(){
        return {
            isEditing: false,

            rate_err  : false,
            is_invalid: false,
        }
    },
    methods: {
        async editCurrnecy(){ //edit product
            this.isEditing = true
            if(this.currencyStore.currencyRate.rate == '') {
                this.is_invalid = true
                this.rate_err   = true
            } else {
                this.rate_err   = false
            }

            if(this.is_invalid) {
                this.is_invalid = false
                this.isEditing = false
                return this.err('Барча катакларни тўлдириш мажбурий!')
            }

            const res = await this.callApi('post', '/app/edit_currency_rate', this.currencyStore.currencyRate)

            if(res.status === 200) {
                this.isEditing = false
                this.currencyStore.currencyRates[this.currencyStore.rowIndex] = res.data

                const lastRate = await this.callApi('get', '/app/get_currency_rate_last/1')
                this.currencyStore.dollarRate = lastRate.data

                this.s('Ўзгаришлар мувоффақиятли сақланди!')
                this.currencyStore.editCurrencyRateModal = false
            } else {
                if(res.status == 422) {
                    if(res.data.errors.name) {
                        this.err(res.data.errors.name[0])
                    }
                } else {
                    this.swr()
                }
            }
        },
    },
}
</script>

