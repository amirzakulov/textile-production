<template>
    <Modal
        v-model="communalTypesStore.deviceAddModal"
        title="Хисоблагич қўшиш"
        :mask-closable = "false"
        :closable = "false"
        width="560"
    >
        <div>
            <slot name="body">
                <Form label-position="left">
                    <FormItem label="* Хисоблагич" class="mb-0">
                        <Input v-model="communalTypesStore.device.name" placeholder="Хисоблагич"></Input>
                        <Text :class="{ 'd-none': !device_name_err }" type="danger" class="m-0 p-0">Тўлдириш мажбурий</Text>
                    </FormItem>
                </Form>
            </slot>
        </div>
        <template #footer>
            <Button @click="communalTypesStore.deviceAddModal = false">Беркитиш</Button>
           <Button type="primary" @click="addDeviceData" :disabled="isAdding" :loading="isAdding">{{isAdding ? 'Хисоблагич...':'Қўшиш'}}</Button>
        </template>
    </Modal>
</template>
<script>
import { useCommunalTypesStore } from '../../../../../stores/CommunalTypesStore'
export default {
    // props: ["communalAddData"],
    setup() {
        const communalTypesStore = useCommunalTypesStore()

        return { communalTypesStore }
    },
    data(){
        return {
            isAdding:          false,
            device_name_err:    false,
            is_invalid:         false,
        }
    },
    methods: {
        async addDeviceData(){
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

            const res = await this.callApi("post", "/app/add_device", this.communalTypesStore.device)

            if(res.status === 200) {
                this.communalTypesStore.devices.unshift(res.data);
                this.communalTypesStore.device.name = ''
                this.s('Хисоблагич мувоффақиятли сақланди!')
                this.communalTypesStore.deviceAddModal = false
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
    created(){

    }
}
</script>

