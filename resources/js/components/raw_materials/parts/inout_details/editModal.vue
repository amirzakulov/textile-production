<template>
    <Modal
        v-model="rmStore.rmEditModal"
        :title="rmStore.rawMaterial.name +' ('+rmStore.rawMaterial.code+')'"
        :mask-closable = "false"
        :closable = "false"
        width="768"
        @on-visible-change="rmStore.modalVisibility"
    >

        <Form ref="formValidate" :label-width="180">
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
            <Button @click="rmStore.rmEditModal = false">Беркитиш</Button>
            <Button type="primary" @click="editRmProduct2" :disabled="isEditing" :loading="isEditing">{{isEditing ? 'Махсулот...':'Сақлаш'}}</Button>
        </template>
    </Modal>
</template>

<script>
import {useRawMaterialsStore} from "../../../../stores/RawMaterialsStore";

export default {
    name: "Edit RawMaterial Modal",
    setup() {
        const rmStore = useRawMaterialsStore()

        return { rmStore }
    },
    data(){
        return {
            isEditing: false,

            currency_id_err  : false,
            product_price_err  : false,
            product_count_err  : false,
            is_invalid: false,
        }
    },
    methods: {
        async editRmProduct2(){ //Add tag
            this.isEditing = true
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
                this.isEditing = false
                return this.err('Барча катакларни тўлдириш мажбурий!')
            }

            const res = await this.callApi('post', '/app/edit_rawmaterial', this.rmStore.rawMaterial)

            this.isEditing = false
            if(res.status === 200) {
                if(res.data === false){
                    this.err("Ўзгартириш мумкин эмас!")
                } else {
                    this.s('Мувоффақиятли сақланди!')
                    this.rmStore.rmSetProducts  = res.data;
                }

                this.rmStore.rmEditModal    = false
                this.rmStore.rawMaterial    = {}

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
