<template>
    <Modal
        v-model="fashionsStore.editFashionDetailsModal"
        title="Махсулотни тахрирлаш"
        :mask-closable="false"
        :closable = "false"
        :width = "768"
        @on-visible-change="fashionsStore.modalVisibility"
    >

        <Form ref="formValidate" :label-width="180">
            <FormItem label="* Махсулот номи" class="ivu-mb">
                <Select v-model="fashionsStore.fashionDetail.product_id" filterable placeholder="Махсулотни танланг...">
                    <Option v-for="product in fashionsStore.products" :value="product.id" :key="product.id">{{ product.name + " (" + product.code + ")" }}</Option>
                </Select>
                <Text :class="{ 'd-none': !product_id_err }" type="danger">Тўлдириш мажбурий!</Text>
            </FormItem>
            <FormItem label="* Миқдор"  class="ivu-mb">
                <Input v-model="fashionsStore.fashionDetail.count" placeholder="Махсулот Миқдор"></Input>
                <Text :class="{ 'd-none': !count_err }" type="danger">Тўлдириш мажбурий!</Text>
            </FormItem>
            <FormItem label="Нахр"  class="ivu-mb">
                <Input v-model="fashionsStore.fashionDetail.price" placeholder="Махсулот Нархи"></Input>
                <Text :class="{ 'd-none': !price_err }" type="danger">Тўлдириш мажбурий!</Text>
            </FormItem>
        </Form>
        <template #footer>
            <Button @click="fashionsStore.editFashionDetailsModal=false">Беркитиш</Button>
            <Button type="primary" @click="editFashion" :disabled="isEditing" :loading="isEditing">{{isEditing ? 'Махсулот...':'Сақлаш'}}</Button>
        </template>
    </Modal>
</template>

<script>
import {useFashionsStore} from "../../../../stores/FashionsStore";

export default {
    name: "Edit Fashion Modal",
    setup() {
        const fashionsStore = useFashionsStore()

        return { fashionsStore }
    },
    data(){
        return {
            isEditing: false,

            product_id_err  : false,
            count_err  : false,
            price_err  : false,
            is_invalid: false,
        }
    },
    methods: {
        async editFashion(){ //edit product
            this.isEditing = true
            if(this.fashionsStore.fashionDetail.product_id == '') {
                this.is_invalid = true
                this.code_err   = true
            } else {
                this.code_err   = false
            }

            if(this.fashionsStore.fashionDetail.count == '') {
                this.is_invalid = true
                this.name_err   = true
            } else {
                this.name_err   = false
            }

            if(this.is_invalid) {
                this.is_invalid = false
                this.isEditing = false
                return this.err('Барча катакларни тўлдириш мажбурий!')
            }

            console.log(this.fashionsStore.fashionDetail)
            const res = await this.callApi('post', '/app/edit_fashion_detail', this.fashionsStore.fashionDetail)
            console.log(res.data)

            if(res.status === 200) {
                this.isEditing = false
                this.fashionsStore.fashionDetails[this.fashionsStore.rowIndex] = res.data

                this.s('Махсулот ўзгаришлари мувоффақиятли сақланди!')
                this.fashionsStore.editFashionDetailsModal = false
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

