<template>
    <CommunalExpenseHeader />
    <div class="_1adminOverveiw_table_recent _border_radious _mar_b30 _p20 pt-0">

        <Row>
            <Col span="18">
                <Card>
                    <PaymentAddForm1 v-if="communalTypesStore.communalTypeId != 2"
                        @device-selected="getLastPayment"
                    ></PaymentAddForm1>

                    <PaymentAddForm2 v-if="communalTypesStore.communalTypeId == 2"
                        @device-selected="getLastPayment"
                    ></PaymentAddForm2>

                    <Divider />

                    <Table size="small" :columns="columns" :data="communalTypesStore.payments" :loading="loading">
                        <template #date="{row}">
                            {{ myDateFormat(row.date, "MM.DD.YYYY") }}
                        </template>
                        <template #payment="{row}">
                            {{ row.payment.toLocaleString() }}
                        </template>
                    </Table>
                </Card>
            </Col>
            <Col span="6">
                <Card class="ml-1">
                    <div>
                        <Button type="primary" icon="md-add" @click="showDeviceAddModal"> Хисоблагич қўшиш</Button>
                    </div>
                    <Divider />
                    <template v-if="communalTypesStore.devices.length">
                        <Row class="bg-light p-2 mb-1"
                        v-for="(device, i) in communalTypesStore.devices" :key="i">
                            <Col span="12"><strong>{{device.name}}</strong></Col>
                            <Col span="4">{{device.measurement}}</Col>
                            <Col span="8" class="text-right">
                                <ButtonGroup>
                                    <Button size="small" type="info" @click="showDeviceEditModal(device, i)"><Icon type="md-create" /></Button>
                                    <Button size="small" type="error" @click="showDeviceDeletingModal(device, i)" :loading="device.isDeleting"><Icon type="md-close" /></Button>
                                </ButtonGroup>
                            </Col>
                        </Row>
                    </template>
                </Card>
            </Col>
        </Row>
    </div>

    <!-- Xisoblagich qushish -->
    <CommunalDeviceAddModal></CommunalDeviceAddModal>

    <!-- Xisoblagich tahrirlash -->
    <CommunalDeviceEditModal></CommunalDeviceEditModal>

    <!-- Xisoblagich uchirish -->
    <CommunalDeviceDeleteModal/>

    <!-- Tulovni tahrirlash -->
    <PaymentEditModal/>

    <!-- Xisoblagich uchirish -->
    <PaymentDeleteModal/>

</template>

<script>
import CommunalExpenseHeader from './parts/header_communal.vue';

import CommunalDeviceAddModal from "./parts/communal/devices/addModal.vue";
import CommunalDeviceEditModal from "./parts/communal/devices/editModal.vue";
import CommunalDeviceDeleteModal from "./parts/communal/devices/deleteModal.vue";

import PaymentAddForm1 from "./parts/communal/payments/addForm1.vue";
import PaymentAddForm2 from "./parts/communal/payments/addForm2.vue";
import PaymentEditModal from "./parts/communal/payments/editModal.vue";
import PaymentDeleteModal from "./parts/communal/payments/deleteModal.vue";

import { useCommunalTypesStore } from '../../stores/CommunalTypesStore';
import {resolveComponent} from "vue";
export default {
    name: "Communal Devices",
    setup() {
        const communalTypesStore = useCommunalTypesStore()

        return { communalTypesStore }
    },
    components: {
        CommunalExpenseHeader: CommunalExpenseHeader,

        PaymentAddForm1: PaymentAddForm1,
        PaymentAddForm2: PaymentAddForm2,

        CommunalDeviceAddModal: CommunalDeviceAddModal,
        CommunalDeviceEditModal: CommunalDeviceEditModal,
        CommunalDeviceDeleteModal: CommunalDeviceDeleteModal,

        PaymentEditModal: PaymentEditModal,
        PaymentDeleteModal: PaymentDeleteModal,
    },
    data() {
        return {
            loading: true,
            columns: [
                {
                    type: 'expand',
                    width: 30,
                    render: (h, { row: { reactive_value, reactive_amount, reactive_price } }) => {

                        return h('div', 'Охирги кўрсаткич (Реактив): '+reactive_value+', Ишлатилди (Реактив): '+reactive_amount +', Нарх (Реактив): ' + reactive_price)
                    }
                },
                {
                    title: "Хисоблагич №",
                    key: 'device',
                    width: "130",
                    sortable: true,
                    className: 'font-11'
                },
                {
                    title: "О.кўрсаткич (Актив)",
                    key: 'active_value',
                    className: 'font-11'
                },
                {
                    title: "Ишлатилди (Актив)",
                    key: 'active_amount',
                    className: 'font-11'
                },
                {
                    title: "Нарх (Актив)",
                    key: 'active_price',
                    className: 'font-11'
                },
                {
                    title: "Сана",
                    slot: 'date',
                    className: 'font-11'
                },
                {
                    title: "Тўлов",
                    slot: 'payment',
                    className: 'font-11'
                },
                {
                    title: " ",
                    key: 'action',
                    width: 120,
                    align: 'right',
                    className: 'font-11',
                    render: (h, params) => {

                        return h('div', {
                                class: 'ivu-btn-group ivu-btn-group-default'
                            },
                            [
                                h(resolveComponent('Button'),
                                    {
                                        title:"Taxrirlash",
                                        type: 'primary',
                                        size: 'small',

                                        onClick: () => {
                                            this.showPaymentEditModal(params.row, params.index)
                                        }
                                    }, {
                                        default() {
                                            return h(resolveComponent('Icon'), {
                                                type: 'md-create'
                                            })
                                        }
                                    }),

                                h(resolveComponent('Button'),
                                    {
                                        title:"O'chirish",
                                        type: 'error',
                                        size: 'small',
                                        loading: params.row.isDeleting,

                                        onClick: () => {
                                            this.showPaymentDeletingModal(params.row, params.index)
                                        }
                                    }, {
                                        default() {
                                            return h(resolveComponent('Icon'), {
                                                type: 'md-close'
                                            })
                                        }
                                    }),

                            ])
                    }
                },

            ],
        }
    },
    methods: {
        showDeviceAddModal(){
            this.communalTypesStore.device                  = {}
            this.communalTypesStore.deviceAddModal          = true
            this.communalTypesStore.device.communal_type_id = this.communalTypesStore.communalTypeId

        },
        showDeviceEditModal(device, index){
            let deviceObj = {
                id:   device.id,
                name: device.name,
            }

            this.communalTypesStore.device          = deviceObj
            this.communalTypesStore.deviceEditModal = true
            this.communalTypesStore.rowIndex        = index
        },
        showDeviceDeletingModal(device, i){
            this.communalTypesStore.device              = device
            this.communalTypesStore.rowIndex            = i
            this.communalTypesStore.deviceDeleteModal   = true
        },

        showPaymentEditModal(payment, index){

            let paymentObj = {
                id:             payment.id,
                device_id:      payment.device_id,
                device:         payment.device,
                communal_type_id: payment.communal_type_id,
                active_value:   payment.active_value,
                active_amount:  payment.active_amount,
                active_price:   payment.active_price,
                reactive_value: payment.reactive_value,
                reactive_amount:payment.reactive_amount,
                reactive_price: payment.reactive_price,
                payment:        payment.payment,
                date:           payment.date
            }

                this.communalTypesStore.payment = paymentObj
                this.communalTypesStore.paymentEditModal = true
                this.communalTypesStore.rowIndex = index
        },
        showPaymentDeletingModal(payment, i){

            this.communalTypesStore.payment       = payment
            this.communalTypesStore.rowIndex    = i
            this.communalTypesStore.paymentDeleteModal = true

        },
        async getLastPayment(device_id){

            const lastPayment = await this.callApi('get', '/app/last_communal_payment/'+this.communalTypesStore.communalTypeId+'/'+device_id);
            this.communalTypesStore.lastPayment = lastPayment.data

            const d = new Date(this.communalTypesStore.lastPayment.date);
            const options1 = {
                    disabledDate (date) {
                        return date && date.valueOf() < d.getTime() - 86400000;
                    }
                }

            this.communalTypesStore.restrictDaysOption = options1
        },
    },
    async created(){
        this.communalTypesStore.communalTypeId = this.$route.params.communal_type_id

        const communalTypeData = await this.callApi('get', '/app/get_communal_type/'+this.communalTypesStore.communalTypeId)
        this.communalTypesStore.communalTypeData = communalTypeData.data

        const devices = await this.callApi('get', '/app/get_communal_devices/'+this.communalTypesStore.communalTypeId)
        this.communalTypesStore.devices = devices.data

        const payments = await this.callApi('get', '/app/get_devices_payments/'+this.communalTypesStore.communalTypeId)
        this.communalTypesStore.payments = payments.data
        this.loading = false

    },
}
</script>
