<template>
    <Modal
        v-model="currencyStore.addCurrencyRateModal"
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
            <Button @click="currencyStore.addCurrencyRateModal=false">Беркитиш</Button>
            <Button type="primary" @click="editCurrnecy" :disabled="isAdding" :loading="isAdding">{{isAdding ? 'Махсулот...':'Сақлаш'}}</Button>
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
            isAdding: false,

            rate_err  : false,
            is_invalid: false,
        }
    },
    methods: {
        async editCurrnecy(){ //edit currency rate
            this.isAdding = true
            if(this.currencyStore.currencyRate.rate == '') {
                this.is_invalid = true
                this.rate_err   = true
            } else {
                this.rate_err   = false
            }

            if(this.is_invalid) {
                this.is_invalid = false
                this.isAdding = false
                return this.err('Барча катакларни тўлдириш мажбурий!')
            }
            this.currencyStore.currencyRate.currency_id = 1
            const res = await this.callApi('post', '/app/add_currency_rate', this.currencyStore.currencyRate)

            if(res.status === 200) {
                this.isAdding = false
                this.currencyStore.currencyRates.unshift(res.data);
                this.currencyStore.dollarRate = res.data

                this.s('Махсулот мувоффақиятли сақланди!')
                this.currencyStore.addCurrencyRateModal = false
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

