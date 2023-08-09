<template>
    <Modal
        v-model="currencyStore.deleteCurrencyRateModal"
        width="360"
    >
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
            <Row :gutter="8">
                <Col span="12">
                    <Button type="error" long :loading="isDeleting" :disabled="isDeleting" @click="deleteFashion">Хa</Button>
                </Col>
                <Col span="12">
                    <Button long @click="cancelDelete">ЙЎҚ</Button>
                </Col>
            </Row>
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
            isDeleting: false,
        }
    },
    methods: {
        async deleteFashion() {
            this.isDeleting = true
            const res = await this.callApi("post", "/app/delete_currency_rate", this.currencyStore.currencyRate)
            if(res.status === 200) {
                this.currencyStore.currencyRates.splice(this.currencyStore.rowIndex, 1)

                const lastRate = await this.callApi('get', '/app/get_currency_rate_last/1')
                this.currencyStore.dollarRate = lastRate.data

                this.s('Мувоффақиятли ўчирилди!')
            } else {
                this.swr();
            }

            this.isDeleting = false
            this.currencyStore.deleteCurrencyRateModal = false
        },
        cancelDelete(){
            this.currencyStore.deleteCurrencyRateModal = false
        }
    },
}
</script>

