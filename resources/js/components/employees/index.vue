<template>
    <EmployeesHeader />
    <!--~~~~~~~ TABLE ONE ~~~~~~~~~-->
    <div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">
        <Row :gutter="8" border="bottom" class="mb-3">
            <Col span="3">
                <Button type="primary" @click="employeesStore.addModal = true" class="text-white"><Icon type="md-add" class="mr-1" />Ходим қўшиш</Button>
            </Col>
            <Col span="6"></Col>
        </Row>

        <Table size="small" :columns="columns" :data="employeesStore.employees" :loading="loading">
            <template #hired_date="{row}">
                {{ myDateFormat(row.hired_date, "MM.DD.YYYY") }}
            </template>
            <template #fullName="{row}">
                {{ row.last_name + " " + row.first_name + " " + row.middle_name }}
            </template>
        </Table>
    </div>


    <!-- Add modal -->
    <EmployeeAddModal />

    <!-- Edit modal -->
    <EmployeeEditModal />

    <!-- Delete modal -->
    <EmployeeDeleteModal />

    <!-- Change Employment Status modal -->
    <EmployeeStatusModal />


</template>

<script>
import EmployeesHeader from './parts/header.vue'
import {useEmployeesStore} from "../../stores/EmployeesStore";
import {resolveComponent} from "vue";

import EmployeeAddModal from "./parts/addModal.vue"
import EmployeeEditModal from "./parts/editModal.vue"
import EmployeeDeleteModal from "./parts/deleteModal.vue"
import EmployeeStatusModal from "./parts/employmentStatusModal.vue"

export default {
    name: "Employees",
    components: {
        EmployeesHeader: EmployeesHeader,

        EmployeeAddModal:EmployeeAddModal,
        EmployeeEditModal:EmployeeEditModal,
        EmployeeDeleteModal:EmployeeDeleteModal,
        EmployeeStatusModal:EmployeeStatusModal,
    },
    setup() {
        const employeesStore = useEmployeesStore()

        return { employeesStore }
    },
    data() {
        return {
            loading: true,
            columns: [
                {
                    title: "ФИШ",
                    slot: 'fullName',
                    width: "300",
                    sortable: true
                },
                {
                    title: "Манзил",
                    key: 'address',
                    sortable: true
                },
                {
                    title: "Қабул сана",
                    slot: 'hired_date',
                    sortable: true
                },
                {
                    title: "Телефон",
                    key: 'phone',
                    sortable: true
                },
                {
                    title: "Бўлим",
                    key: 'department_name',
                    width: "160",
                    sortable: true
                },
                {
                    title: "action",
                    key: 'action',
                    width: 180,
                    align: 'right',
                    render: (h, params) => {
                        return h('div', [
                                    //birinchi button group
                                    h('div', {
                                            class: 'ivu-btn-group ivu-btn-group-default ivu-mr'
                                        },
                                        [
                                            h(resolveComponent('Button'),
                                                {
                                                    title:"Ишдан бўшатиш",
                                                    type: 'error',
                                                    size: 'small',

                                                    onClick: () => {
                                                        this.showEmployeeStatusModal(params.row, params.index)
                                                    }
                                                }, {
                                                    default() {
                                                        return h(resolveComponent('Icon'), {
                                                            type: 'ios-hammer'
                                                        })
                                                    }
                                                }
                                            ),
                                        ]),

                                    //ikkinchi button group
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

        showEditModal(employee, index) {
            let employeeObj = {
                id:             employee.id,
                first_name:     employee.first_name,
                last_name:      employee.last_name,
                middle_name:    employee.middle_name,
                department_id:  employee.department_id,
                phone:          employee.phone,
                address:        employee.address,
                description:    employee.description,
                type:           employee.type,
            }

            this.employeesStore.employee  = employeeObj
            this.employeesStore.editModal = true
            this.employeesStore.rowIndex  = index
        },

        showDeletingModal(employee, i){
            this.employeesStore.employee    = employee
            this.employeesStore.rowIndex    = i
            this.employeesStore.deleteModal = true
        },

        showEmployeeStatusModal(employee, i){
            let hired_date = this.employeesStore.getToday()
            let fired_date = this.employeesStore.getToday()

            if(employee.hired_date != null) {
                hired_date = employee.hired_date
            }

            if(employee.fired_date != null) {
                fired_date = employee.fired_date
            }

            let employeeObj = {
                id:             employee.id,
                first_name:     employee.first_name,
                last_name:      employee.last_name,
                middle_name:    employee.middle_name,
                hired_date:     hired_date,
                fired_date:     fired_date,
                active:         2,
            }

            this.employeesStore.employee    = employeeObj
            this.employeesStore.rowIndex    = i
            this.employeesStore.employmentStatusModal   = true

        },
    },
    async created(){
        const departments = await this.callApi('get', '/app/get_departments')
        this.employeesStore.departments = departments.data

        const employees = await this.callApi('get', '/app/get_active_employees')
        this.employeesStore.employees = employees.data

        this.loading = false

    }

}
</script>



