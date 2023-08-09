<template>
    <Form label-position="top">
        <Row :gutter="8">
            <Col span="5">
                <FormItem label="Хисоблагич">
                    <Select v-model="data.device_id" @on-change="onDeviceSelected(data.device_id)">
                        <template v-for="device in communalTypesStore.devices" :key="device.id">
                            <Option :value="device.id">{{device.name}}</Option>
                        </template>
                    </Select>
                    <Text :class="{'d-none': !payment_deviceId_err}" type="danger" class="m-0 p-0">Тўлдириш мажбурий</Text>
                </FormItem>
            </Col>
            <Col span="5">
                <FormItem label="Тўлов" placeholder="Тўлов">
                    <Input v-model="data.payment"></Input>
                    <Text :class="{ 'd-none': !payment_payment_err }" type="danger" class="m-0 p-0">Тўлдириш мажбурий</Text>
                </FormItem>
            </Col>
            <Col span="5">
                <FormItem label="Сана">
                    <DatePicker v-model="data.date" format="yyyy-MM-dd" type="date"
                                :options="communalTypesStore.restrictDaysOption"
                                placeholder="Санани танланг" />
                    <Text :class="{ 'd-none': !payment_date_err }" type="danger" class="m-0 p-0">Тўлдириш мажбурий</Text>
                </FormItem>
            </Col>

        </Row>
        <br>
        <Row :gutter="8">
            <Col span="5">
                <FormItem label="Охирги кўрсаткич (актив)">
                    <Input v-model="data.active_value"></Input>
                    <Text :class="{ 'd-none': !payment_active_value_err.status }" type="danger" class="m-0 p-0">{{payment_active_value_err.message}}</Text>
                </FormItem>
            </Col>
            <Col span="5">
                <FormItem :label="`Нарх (актив, 1 ${communalTypesStore.communalTypeData.measurement})`">
                    <Input v-model="data.active_price"></Input>
                    <Text :class="{ 'd-none': !payment_active_price_err }" type="danger" class="m-0 p-0">Тўлдириш мажбурий</Text>
                </FormItem>
            </Col>

            <Col span="5">
                <FormItem label="&nbsp;&nbsp;" class="text-right">
                    <Button type="primary" style="width:100%" @click="addPayment">Қўшиш</Button>
                </FormItem>
            </Col>
        </Row>
    </Form>
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
            isAdding:                   false,
            data: {
                device_id:        '',
                communal_type_id: '',
                active_value:     '',
                active_amount:    '',
                active_price:     '',
                reactive_value:   0,
                reactive_amount:  0,
                reactive_price:   0,
                payment:          '',
                date:             ''
            },

            payment_deviceId_err:       false,
            payment_active_value_err:   {status: false, message:''},
            payment_active_price_err:   false,
            payment_date_err:           false,
            payment_payment_err:        false,
            is_invalid:                 false,

        }
    },
    methods: {
        async addPayment(){

            if(this.data.device_id == '' || this.data.device_id == undefined) {
                this.is_invalid             = true
                this.payment_deviceId_err   = true
            } else {
                this.payment_deviceId_err   = false
            }

            if(this.data.active_value == '') {
                this.is_invalid             = true
                this.payment_active_value_err.status = true
                this.payment_active_value_err.message = 'Тўлдириш мажбурий'
            } else {
                this.payment_active_value_err.status = false
            }

            if(this.data.payment == '') {
                this.is_invalid          = true
                this.payment_payment_err = true
            } else {
                this.payment_payment_err = false
            }

            if(this.data.active_value < this.communalTypesStore.lastPayment.active_value) {
                this.is_invalid                       = true
                this.payment_active_value_err.status  = true
                this.payment_active_value_err.message = 'Охирги кўрсаткич нотўғри'

            }

            if(this.data.active_price == '') {
                this.is_invalid         = true
                this.payment_active_price_err    = true
            } else {
                this.payment_active_price_err    = false
            }

            if(this.data.date == '') {
                this.is_invalid         = true
                this.payment_date_err    = true
            } else {
                this.payment_date_err    = false
                this.data.date = this.myDateFormat(this.data.date, 'yyyy-mm-dd')
            }

            if(this.is_invalid) {
                this.is_invalid = false
                return this.err('Барча катакларни тўлдириш мажбурий!')
            }

            if(Object.keys(this.communalTypesStore.lastPayment).length > 0) {
                this.data.active_amount = this.data.active_value - this.communalTypesStore.lastPayment.active_value
            } else {
                this.data.active_amount = 0
            }

            this.data.communal_type_id = this.communalTypesStore.communalTypeId

            const res = await this.callApi("post", "/app/add_communal_payment", this.data)

            if(res.status === 200) {
                this.communalTypesStore.payments.unshift(res.data);

                this.data.device_id     = ''
                this.data.active_value  = ''
                this.data.active_price  = ''
                this.data.payment       = ''
                this.data.date          = ''
                this.s('Хисоблагич мувоффақиятли сақланди!')

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
        onDeviceSelected(device_id){
            this.$emit('deviceSelected', device_id)

        }
    },
    created(){}
}
</script>

