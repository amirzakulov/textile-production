<template>
    <Modal
        v-model="fashionsStore.editModal"
        title="Махсулотни тахрирлаш"
        :mask-closable="false"
        :closable = "false"
        :width = "768"
    >

        <Form ref="formValidate" :label-width="180">
            <FormItem label="* Код" class="ivu-mb">
                <Input v-model="fashionsStore.fashion.code" placeholder="Код"></Input>
                <Text :class="{ 'd-none': !code_err }" type="danger">Тўлдириш мажбурий!</Text>
            </FormItem>
            <FormItem label="* Номи" class="ivu-mb">
                <Input v-model="fashionsStore.fashion.name" placeholder="Модел номи"></Input>
                <Text :class="{ 'd-none': !name_err }" type="danger">Тўлдириш мажбурий!</Text>
            </FormItem>
            <FormItem label="* Тури" class="ivu-mb">
                <Select v-model="fashionsStore.fashion.fashion_category_id" placeholder="Тури">
                    <Option v-for="category in fashionsStore.categories" :value="category.id" :key="category.id" v-bind:value="category.id">{{ category.name }}</Option>
                </Select>
            </FormItem>
            <FormItem label="Қисқача маълумот" prop="productDescription" class="ivu-mb">
                <Input type="textarea" :rows="4" v-model="fashionsStore.fashion.description" placeholder="Қисқача маълумот"></Input>
            </FormItem>

        </Form>
        <template #footer>
            <Button @click="fashionsStore.editModal=false">Беркитиш</Button>
            <Button type="primary" @click="editFashion" :disabled="isEditing" :loading="isEditing">{{isEditing ? 'Махсулот...':'Сақлаш'}}</Button>
        </template>
    </Modal>
</template>

<script>
import {useFashionsStore} from "../../../stores/FashionsStore";

export default {
    name: "Add Fashion Modal",
    setup() {
        const fashionsStore = useFashionsStore()

        return { fashionsStore }
    },
    data(){
        return {
            isEditing: false,

            name_err  : false,
            code_err  : false,
            category_err  : false,
            is_invalid: false,
        }
    },
    methods: {
        async editFashion(){ //edit product
            this.isEditing = true
            if(this.fashionsStore.fashion.code == '') {
                this.is_invalid = true
                this.code_err   = true
            } else {
                this.code_err   = false
            }

            if(this.fashionsStore.fashion.name == '') {
                this.is_invalid = true
                this.name_err   = true
            } else {
                this.name_err   = false
            }

            if(this.fashionsStore.fashion.fashion_category_id == '') {
                this.is_invalid = true
                this.category_err   = true
            } else {
                this.category_err   = false
            }

            if(this.is_invalid) {
                this.is_invalid = false
                this.isEditing = false
                return this.err('Барча катакларни тўлдириш мажбурий!')
            }

            const res = await this.callApi('post', '/app/edit_fashion', this.fashionsStore.fashion)

            if(res.status === 200) {
                this.isEditing = false
                this.fashionsStore.fashions[this.fashionsStore.rowIndex] = res.data

                this.s('Махсулот ўзгаришлари мувоффақиятли сақланди!')
                this.fashionsStore.editModal = false
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

