<template>
    <Modal
        v-model="rmStore.rmEditOutModal"
        :title="rmStore.rawMaterial.name +' ('+rmStore.rawMaterial.code+')'"
        :mask-closable = "false"
        :closable = "false"
        width="768"
        @on-visible-change="rmStore.modalVisibility"
    >

        <Form ref="formValidate" :label-width="180">
            <FormItem label="* Миқдор" class="ivu-mb">
                <Input v-model="rmStore.rawMaterial.count"
                       @on-change="countChanged"
                       placeholder="Махсулот Миқдор"></Input>
                <Paragraph class="m-0 p-0">Омборда: {{rmStore.rawMaterial.stockCount}}</Paragraph>
                <Text :class="{ 'd-none': !raw_material_count_err.display }" type="danger">{{ raw_material_count_err.message }}</Text>
            </FormItem>
        </Form>
        <template #footer>
            <Button @click="rmStore.rmEditOutModal = false">Беркитиш</Button>
            <Button type="primary" @click="editRmProductOut" :disabled="isEditing" :loading="isEditing">{{isEditing ? 'Махсулот...':'Сақлаш'}}</Button>
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
            raw_material_count_err: {display: false, message:"Тўлдириш мажбурий"},
            is_invalid: false,
        }
    },
    methods: {
        async editRmProductOut(){
            this.isEditing = true
            let stockCount  = this.rmStore.rawMaterial.stockCount + this.rmStore.rawMaterial.outCount
            if(this.rmStore.rawMaterial.count === "" || this.rmStore.rawMaterial.count == 0) {
                this.is_invalid = true
                this.raw_material_count_err.display   = true
                this.raw_material_count_err.message   = "Тўлдириш мажбурий"

            } else if(this.rmStore.rawMaterial.count > stockCount) {
                this.is_invalid = true
                this.raw_material_count_err.display   = true
                this.raw_material_count_err.message   = "Омборда бунча махсулот йўқ"
            } else {
                this.raw_material_count_err.display   = false
            }

            if(this.is_invalid) {
                this.is_invalid = false
                this.isEditing = false
                return this.err('Барча катакларни тўлдириш мажбурий!')
            }

            const res = await this.callApi('post', '/app/edit_rawmaterial_out', this.rmStore.rawMaterial)

            this.isEditing = false
            if(res.status === 200) {
                if(res.data === false){
                    this.err("Ўзгартириш мумкин эмас!")
                } else {
                    this.rmStore.rmSetProducts  = res.data;
                    this.s('Мувоффақиятли сақланди!')
                }

                this.rmStore.rmEditOutModal = false
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

        async countChanged(){
            let count       = this.rmStore.rawMaterial.count
            let stockCount  = this.rmStore.rawMaterial.stockCount
            let outCount    = this.rmStore.rawMaterial.outCount
            if(count > (stockCount + outCount)) {
                this.raw_material_count_err.display = true;
                this.raw_material_count_err.message = "Омборда бунча махсулот йўқ!";
            } else {
                this.raw_material_count_err.display = false;
                this.raw_material_count_err.message = "";
            }
        },

    },
    async created(){}
}
</script>
