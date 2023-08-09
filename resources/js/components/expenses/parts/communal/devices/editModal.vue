<template>
    <Modal
        v-model="communalTypesStore.deviceEditModal"
        title="Хисоблагични тахрирлаш"
        :mask-closable = "false"
        :closable = "false"
        width="560"
    >
        <div>
            <slot name="body">
                <Form label-position="left">
                    <input type="hidden" v-model="communalTypesStore.device.id">
                    <FormItem label="* Хисоблагич" class="mb-0">
                        <Input v-model="communalTypesStore.device.name" placeholder="Хисоблагич номи"></Input>
                        <Text :class="{ 'd-none': !device_name_err }" type="danger" class="m-0 p-0">Тўлдириш мажбурий</Text>
                    </FormItem>
                </Form>
            </slot>
        </div>
        <template #footer>
            <Button @click="communalTypesStore.deviceEditModal = false">Беркитиш</Button>
           <Button type="primary" @click="saveDeviceData" :disabled="isEditing" :loading="isEditing">{{isEditing ? 'Хисоблагич...':'Сақлаш'}}</Button>
        </template>
    </Modal>
</template>
<script>
import { useCommunalTypesStore } from '../../../../../stores/CommunalTypesStore'
export default {
    setup() {
        const communalTypesStore = useCommunalTypesStore()

        return { communalTypesStore }
    },
    data(){
        return {
            isEditing:          false,
            device_name_err:    false,
            is_invalid:         false,
        }
    },
    methods: {
        async saveDeviceData(){
            if(this.communalTypesStore.device.name == '') {
                this.is_invalid         = true
                this.device_name_err    = true
            } else {
                this.device_name_err    = false
            }

            if(this.is_invalid) {
                this.is_invalid = false
                return this.err('Барча катакларни тўлдириш мажбурий!')
            }

            const res = await this.callApi("post", "/app/edit_device", this.communalTypesStore.device)

            if(res.status === 200) {
                this.communalTypesStore.devices[this.communalTypesStore.rowIndex].name = res.data.name
                this.communalTypesStore.device.name = ''
                this.s('Ўзгаришлари мувоффақиятли сақланди!')
                this.communalTypesStore.deviceEditModal = false
            } else {
                if(res.status == 422) {
                    if(res.data.errors.name) {
                        this.err(res.data.errors.name[0])
                    }
                } else {
                    this.swr()
                }
            }
        }
    },
    created(){}
}
</script>

