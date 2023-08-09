<template>
    <Modal
        v-model="productionStore.productAddModal"
        title="Махсулот қўшиш"
        :mask-closable = "false"
        :closable = "false"
        width="768"
        @on-visible-change="productionStore.modalVisibility"
    >

        <Form ref="formValidate" :label-width="180">
            <FormItem label="* Номи" class="ivu-mb">
                <Input v-model="productionStore.departmentProduct.name" placeholder="Махсулот номи"/>
                <Text :class="{ 'd-none': !name_err }" type="danger">Тўлдириш мажбурий!</Text>
            </FormItem>
            <FormItem label="* Коди" class="ivu-mb">
                <Input v-model="productionStore.departmentProduct.code" placeholder="Махсулот коди"/>
                <Text :class="{ 'd-none': !code_err }" type="danger">Тўлдириш мажбурий!</Text>
            </FormItem>
            <FormItem label="* Бирлик" class="ivu-mb">
                <Select v-model="productionStore.departmentProduct.measurement" placeholder="Махсулот бирлиги">
                    <Option v-for="measurement in productionStore.measurements" :value="measurement.value" :key="measurement.value">{{ measurement.label }}</Option>
                </Select>
                <Text :class="{ 'd-none': !measurement_err }" type="danger">Тўлдириш мажбурий!</Text>
            </FormItem>
            <FormItem label="* Модел" class="ivu-mb">
                <Select v-model="productionStore.departmentProduct.fashion_id" placeholder="Махсулот бирлиги">
                    <Option v-for="fashion in productionStore.fashions" :value="fashion.id" :key="fashion.id">{{ fashion.name }}</Option>
                </Select>
                <Text :class="{ 'd-none': !fashion_err }" type="danger">Тўлдириш мажбурий!</Text>
            </FormItem>
        </Form>
        <template #footer>
            <Button @click="productionStore.productAddModal=false">Беркитиш</Button>
            <Button type="primary" @click="addDepartmentProduct" :disabled="isAdding" :loading="isAdding">{{isAdding ? 'Махсулот...':'Сақлаш'}}</Button>
        </template>
    </Modal>
</template>

<script>
import {useProductionStore} from "../../../../stores/ProductionStore";

export default {
    name: "Add Product Modal",
    setup() {
        const productionStore = useProductionStore()

        return { productionStore }
    },
    data(){
        return {
            isAdding: false,

            name_err  : false,
            code_err  : false,
            measurement_err  : false,
            fashion_err  : false,
            is_invalid: false,
        }
    },
    methods: {

        async addDepartmentProduct(){
            this.isAdding = true
            if(this.productionStore.departmentProduct.name === '') {
                this.is_invalid = true
                this.name_err   = true
            } else {
                this.name_err   = false
            }

            if(this.productionStore.departmentProduct.code === '') {
                this.is_invalid = true
                this.code_err   = true
            } else {
                this.code_err   = false
            }

            if(this.productionStore.departmentProduct.measurement === '') {
                this.is_invalid = true
                this.measurement_err   = true
            } else {
                this.measurement_err   = false
            }

            if(this.productionStore.departmentProduct.fashion_id === '') {
                this.is_invalid = true
                this.fashion_err   = true
            } else {
                this.fashion_err   = false
            }

            if(this.is_invalid) {
                this.is_invalid = false
                this.isAdding = false
                return this.err('Барча катакларни тўлдириш мажбурий!')
            }

            this.productionStore.departmentProduct.department_id  = this.productionStore.department_id

            const res = await this.callApi('post', '/app/add_production_product', this.productionStore.departmentProduct)

            if(res.status === 200) {
                this.isAdding = false
                this.productionStore.departmentProducts.unshift(res.data);
                this.productionStore.productAddModal = false

                this.s('Махсулот мувоффақиятли қўшилди!')
                this.productionStore.departmentProduct = {}

            } else {
                if(res.status == 422) {
                    if(res.data.errors.name) {
                        this.err(res.data.errors.name[0])
                    }

                    if(res.data.errors.code) {
                        this.err(res.data.errors.code[0])
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
