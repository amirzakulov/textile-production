<template>
    <Modal
        v-model="rmStore.productEditModal"
        title="Махсулот тахрирлаш"
        :mask-closable = "false"
        :closable = "false"
        width="768"
        @on-visible-change="rmStore.modalVisibility"
    >

        <Form ref="formValidate" :label-width="180">
            <FormItem label="* Номи" class="ivu-mb">
                <Input v-model="rmStore.product.name" placeholder="Махсулот номи"></Input>
                <Text :class="{ 'd-none': !name_err }" type="danger">Тўлдириш мажбурий!</Text>
            </FormItem>
            <FormItem label="* Бирлик" class="ivu-mb">
                <Select v-model="rmStore.product.measurement" placeholder="Махсулот бирлиги">
                    <Option v-for="measurement in rmStore.measurements" :value="measurement.value" :key="measurement.value">{{ measurement.label }}</Option>
                </Select>
                <Text :class="{ 'd-none': !measurement_err }" type="danger">Тўлдириш мажбурий!</Text>
            </FormItem>
            <FormItem label="* Категория" class="ivu-mb">
                <Select v-model="rmStore.product.category_id" placeholder="Категория">
                    <Option v-for="category in rmStore.categories" :value="category.id" :key="category.id">{{ category.name }}</Option>
                </Select>
                <Text :class="{ 'd-none': !category_err }" type="danger">Тўлдириш мажбурий!</Text>
            </FormItem>
            <FormItem label="Минимал миқдор" class="ivu-mb">
                <Input v-model="rmStore.product.min_count" placeholder="Минимал миқдор"></Input>
            </FormItem>
            <FormItem label="Коди" class="ivu-mb">
                <Input v-model="rmStore.product.code" placeholder="Махсулот коди"></Input>
            </FormItem>
            <FormItem label="Ранги" class="ivu-mb">
                <Input v-model="rmStore.product.color" placeholder="Махсулот ранги"></Input>
            </FormItem>
            <FormItem label="Давлати" class="ivu-mb">
                <Input v-model="rmStore.product.country" placeholder="Махсулот келтирилган давлат"></Input>
            </FormItem>
            <FormItem label="Қўшимча маълумотлар">
                <Input v-model="rmStore.product.description" type="textarea" :autosize="{minRows: 3,maxRows: 5}" placeholder="Қўшимча маълумотлар..."></Input>
            </FormItem>
        </Form>
        <template #footer>
            <Button @click="rmStore.productEditModal = false">Беркитиш</Button>
            <Button type="primary" @click="editRmProduct" :disabled="isEditing" :loading="isEditing">{{isEditing ? 'Махсулот...':'Сақлаш'}}</Button>
        </template>
    </Modal>
</template>

<script>
import {useRawMaterialsStore} from "../../../../stores/RawMaterialsStore";

export default {
    name: "Edit Product Modal",
    setup() {
        const rmStore = useRawMaterialsStore()

        return { rmStore }
    },
    data(){
        return {
            isEditing: false,

            category_err  : false,
            name_err  : false,
            measurement_err  : false,
            is_invalid: false,
        }
    },
    methods: {
        async editRmProduct(){ //Add tag
            this.isEditing = true
            if(this.rmStore.product.name == null) {
                this.is_invalid = true
                this.name_err   = true
            } else {
                this.name_err   = false
            }

            if(this.rmStore.product.category_id == null) {
                this.is_invalid = true
                this.category_err   = true
            } else {
                this.category_err   = false
            }

            if(this.rmStore.product.measurement == null) {
                this.is_invalid = true
                this.measurement_err   = true
            } else {
                this.measurement_err   = false
            }

            if(this.is_invalid) {
                this.is_invalid = false
                return this.err('Барча катакларни тўлдириш мажбурий!')
            }

            const res = await this.callApi('post', '/app/edit_product', this.rmStore.product)

            if(res.status === 200) {
                this.isEditing = false
                this.rmStore.productsTmp[this.rmStore.rowIndex] = res.data;
                this.rmStore.productEditModal = false

                this.s('Махсулот мувоффақиятли қўшилди!')
                // this.rmStore.product = {}

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
    async created(){

    }
}
</script>
