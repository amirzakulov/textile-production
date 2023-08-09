<template>
    <EmployeesHeader />
    <!--~~~~~~~ TABLE ONE ~~~~~~~~~-->
    <div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">
        <Row :gutter="16" class="ivu-mb">
            <Col span="12">
                <Card>
                    <Form ref="incomeProducts" label-position="left" class="ivu-mb">
                        <Row>
                            <Col span="4">
                                <FormItem label="">
                                    <Select v-model="currentYear" placeholder="Йил...">
                                        <Option v-for="year in employeeSalariesStore.years" :value="year.id" :key="year.id">{{ year.label }}</Option>
                                    </Select>
                                </FormItem>
                            </Col>
<!--                            <Col span="4" class="ivu-ml">-->
<!--                                <FormItem label="">-->
<!--                                    <Select v-model="currentMonth" placeholder="Ой...">-->
<!--                                        <Option v-for="month in employeeSalariesStore.monthes" :value="month.id" :key="month.id">{{ month.label }}</Option>-->
<!--                                    </Select>-->
<!--                                </FormItem>-->
<!--                            </Col>-->
                            <Col span="4" class="ivu-ml">
                                <Button type="primary" @click="showSalary">Кўрсатиш</Button>
                            </Col>
                        </Row>
                    </Form>

                </Card>
            </Col>
            <Col span="12">
                <Card >
                    <Col span="24" class="ivu-mb text-right">
                        <Button type="primary" @click="employeeSalariesStore.addModal = true" class="ivu-mr"><Icon type="md-add" />Маош қўшиш</Button>
                    </Col>
                </Card>
            </Col>
        </Row>

        <Table height="450" size="small" :columns="columns" :data="employeeSalariesStore.paidEmployees" :loading="loading" border>
            <template #fullName="{row}">
                {{ row.last_name + " " + row.first_name + " " + row.middle_name }}
            </template>
        </Table>
    </div>

    <!-- Add modal -->
    <SalaryAddModal />


</template>

<script>
import EmployeesHeader from './parts/header.vue'
import {resolveComponent} from "vue";
import {useEmployeeSalariesStore} from "../../stores/EmployeeSalariesStore";

import SalaryAddModal from "./parts/salary/addModal.vue"
export default {
    name: "payroll",
    components: {
        EmployeesHeader: EmployeesHeader,
        SalaryAddModal: SalaryAddModal,
    },
    setup() {
        const employeeSalariesStore = useEmployeeSalariesStore()

        return { employeeSalariesStore }
    },
    data() {
        return {
            date: new Date(),
            currentYear: '',
            loading: true,
            columns: [
                {
                    title: "ФИШ",
                    slot: 'fullName',
                    width: 300,
                    fixed: "left",
                    sortable: true
                },
                {
                    title: "Январь",
                    key: 'january',
                    width: 120,
                    sortable: true
                },
                {
                    title: "Февраль",
                    key: 'february',
                    width: 120,
                    sortable: true
                },
                {
                    title: "Март",
                    key: 'march',
                    width: 120,
                    sortable: true
                },
                {
                    title: "Апрель",
                    key: 'april',
                    width: 120,
                    sortable: true
                },
                {
                    title: "Май",
                    key: 'may',
                    width: 120,
                    sortable: true
                },
                {
                    title: "Июнь",
                    key: 'june',
                    width: 120,
                    sortable: true
                },
                {
                    title: "Июль",
                    key: 'july',
                    width: 120,
                    sortable: true
                },
                {
                    title: "Август",
                    key: 'august',
                    width: 120,
                    sortable: true
                },
                {
                    title: "Сентябрь",
                    key: 'september',
                    width: 120,
                    sortable: true
                },
                {
                    title: "Октябрь",
                    key: 'october',
                    width: 120,
                    sortable: true
                },
                {
                    title: "Ноябрь",
                    key: 'november',
                    width: 120,
                    sortable: true
                },
                {
                    title: "Декабрь",
                    key: 'december',
                    width: 120,
                    sortable: true
                },
                {
                    title: "action",
                    key: 'action',
                    width: 120,
                    fixed: "right",
                    align: 'right',
                    render: (h, params) => {
                        return h('div', [
                            h('div', {class: 'ivu-btn-group ivu-btn-group-default'},
                                [
                                    h(
                                        resolveComponent('Button'),
                                        {
                                            title:"Тахрирлаш",
                                            type: 'primary',
                                            size: 'small',

                                            onClick: () => {
                                                this.showEditModal(params.row, params.index)
                                            }
                                        },
                                        {
                                            default() {
                                                return h(resolveComponent('Icon'), {
                                                    type: 'md-create'
                                                })
                                            }
                                        }
                                    ),

                                    h(resolveComponent('Button'),
                                        {
                                            title:"Ўчириш",
                                            type: 'error',
                                            size: 'small',
                                            loading: params.row.isDeleting,

                                            onClick: () => {
                                                this.showDeletingModal(params.row, params.index)
                                            }
                                        }, {
                                            default() {
                                                return h(resolveComponent('Icon'), {
                                                    type: 'md-close'
                                                })
                                            }
                                        }),
                                ])
                        ])
                    }
                },
            ],

        }
    },
    methods: {
        async showSalary(){
            const paidEmployees = await this.callApi('post', '/app/get_employees_salaries', {year: this.currentYear})
            this.employeeSalariesStore.paidEmployees  = paidEmployees.data
        }
    },
    async created(){
        this.currentYear = this.date.getFullYear()
        const paidEmployees = await this.callApi('post', '/app/get_employees_salaries', {year: this.currentYear})
        this.employeeSalariesStore.paidEmployees  = paidEmployees.data

        const allEmployees = await this.callApi('get', '/app/get_employees')
        this.employeeSalariesStore.allEmployees  = allEmployees.data

        this.loading = false
    }
}
</script>



