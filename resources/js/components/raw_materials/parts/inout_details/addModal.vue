<template>
    <Modal
        v-model="rmStore.rmAddModal"
        title="Махсулот қўшиш"
        :mask-closable = "false"
        :closable = "false"
        width="768"
        @on-visible-change="rmStore.modalVisibility"
    >

        <Form ref="formValidate" :label-width="180">
            <FormItem label="* Махсулот" class="ivu-mb">
                <Select v-model="rmStore.rawMaterial.product_id" filterable placeholder="Махсулотни танланг...">
                    <template v-for="product in rmStore.products" :value="product.id" :key="product.id">
                        <Option v-if="!rmStore.rmSetProductsId.includes(product.id)" :value="product.id">{{ product.name + " (" + product.code + ", " + product.country + ")" }}</Option>
                    </template>
                </Select>
                <Text :class="{ 'd-none': !product_id_err }" type="danger">Тўлдириш мажбурий!</Text>
            </FormItem>
            <FormItem label="* Валюта" class="ivu-mb">
                <Select v-model="rmStore.rawMaterial.currency_id" placeholder="Валютани танланг">
                    <Option v-for="currency in rmStore.currencies" :value="currency.id" :key="currency.id">{{ currency.name}}</Option>
                </Select>
                <Text :class="{ 'd-none': !currency_id_err }" type="danger">Тўлдириш мажбурий!</Text>
            </FormItem>
            <FormItem label="* Нархи" class="ivu-mb">
                <Input v-model="rmStore.rawMaterial.price_value" placeholder="Махсулот Нархи"></Input>
                <Text :class="{ 'd-none': !product_price_err }" type="danger">Тўлдириш мажбурий!</Text>
            </FormItem>
            <FormItem label="* Миқдор" class="ivu-mb">
                <Input v-model="rmStore.rawMaterial.count" placeholder="Махсулот Миқдор"></Input>
                <Text :class="{ 'd-none': !product_count_err }" type="danger">Тўлдириш мажбурий!</Text>
            </FormItem>
        </Form>
        <template #footer>
            <Button @click="rmStore.rmAddModal = false">Беркитиш</Button>
            <Button type="primary" @click="addRmProductIn" :disabled="isAdding" :loading="isAdding">{{isAdding ? 'Махсулот...':'Сақлаш'}}</Button>
        </template>
    </Modal>
</template>

<script>
import {useRawMaterialsStore} from "../../../../stores/RawMaterialsStore";

export default {
    name: "Add RawMaterial Modal",
    setup() {
        const rmStore = useRawMaterialsStore()

        return { rmStore }
    },
    data(){
        return {
            isAdding: false,

            product_id_err  : false,
            currency_id_err  : false,
            product_price_err  : false,
            product_count_err  : false,
            is_invalid: false,
        }
    },
    methods: {
        async addRmProductIn(){ //Add tag
            this.isAdding = true
            if(this.rmStore.rawMaterial.product_id == null) {
                this.is_invalid = true
                this.product_id_err = true
            } else {
                this.product_id_err = false
            }

            if(this.rmStore.rawMaterial.currency_id == null) {
                this.is_invalid = true
                this.currency_id_err   = true
            } else {
                this.currency_id_err   = false
            }

            if(this.rmStore.rawMaterial.price_value == null) {
                this.is_invalid = true
                this.product_price_err   = true
            } else {
                this.product_price_err   = false
            }

            if(this.rmStore.rawMaterial.count == null) {
                this.is_invalid = true
                this.product_count_err   = true
            } else {
                this.product_count_err   = false
            }

            if(this.is_invalid) {
                this.is_invalid = false
                this.isAdding = false
                return this.err('Барча катакларни тўлдириш мажбурий!')
            }

            let incomeProduct = {
                inoutSet:       this.rmStore.rmSet,
                rawMaterial:    this.rmStore.rawMaterial,
                department_id:  this.rmStore.department_id
            }

            const res = await this.callApi('post', '/app/add_rawmaterial', incomeProduct)
            console.log(res.data)
            this.isAdding = false
            if(res.status === 200) {
                this.isAdding = false
                this.rmStore.rmSetProducts  = res.data;
                this.rmStore.rmSetProductsId.push(this.rmStore.rawMaterial.product_id)
                this.rmStore.rmAddModal     = false

                this.s('Мувоффақиятли сақланди!')
                this.rmStore.rawMaterial = {}

            } else {
                if(res.status == 422) {
                    if(res.data.errors.count) {
                        this.err(res.data.errors.count[0])
                    }
                } else {
                    this.swr()
                }
            }
        },
    },
    async created(){

    }
}
</script>
