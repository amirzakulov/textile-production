<template>
    <Modal
        v-model="employeesStore.employmentStatusModal"
        title="Ишдан бўшатиш"
        :mask-closable = "false"
        :closable = "false"
        width="480"
        @on-visible-change="employeesStore.modalVisibility"
    >

        <Form ref="formValidate"  :label-width="180">
            <Text class="ivu-mb" strong>{{employeesStore.employee.last_name +" "+ employeesStore.employee.first_name +" "+ employeesStore.employee.middle_name}}</Text>
            <FormItem label="* Қабул сана" class="ivu-mb">
                <DatePicker v-model="employeesStore.employee.hired_date" format="yyyy-MM-dd" type="date" placeholder="сана" style="width: 200px" />
                <br>
                <Text :class="{ 'd-none': !hired_date_err }" type="danger">Тўлдириш мажбурий!</Text>
            </FormItem>
            <FormItem label="* Бўшатилган сана" class="ivu-mt ivu-mb">
                <DatePicker v-model="employeesStore.employee.fired_date" format="yyyy-MM-dd" type="date" placeholder="сана" style="width: 200px" />
                <br>
                <Text :class="{ 'd-none': !fired_date_err }" type="danger">Тўлдириш мажбурий!</Text>
            </FormItem>

        </Form>
        <template #footer>
            <Button @click="employeesStore.fireModal = false">Беркитиш</Button>
            <Button type="primary" @click="fireEmployee" :disabled="isEditing" :loading="isEditing">{{isEditing ? 'Ходим...':'Сақлаш'}}</Button>
        </template>
    </Modal>
</template>

<script>
    import {useEmployeesStore} from "../../../stores/EmployeesStore";

    export default {
        name: "Fire Employee",
        setup() {
            const employeesStore = useEmployeesStore()

            return { employeesStore }
        },


        data(){
            return {
                isEditing: false,
                employee_name: '',

                is_invalid: false,
                hired_date_err: false,
                fired_date_err: false,

            }
        },
        methods: {
            async fireEmployee(){
                if(this.employeesStore.employee.hired_date == '') {
                    this.is_invalid     = true
                    this.hired_date_err = true
                    return this.err('Барча катакларни тўлдириш мажбурий!')
                } else {

                    this.hired_date_err  = false
                }

                if(this.employeesStore.employee.fired_date == '') {
                    this.is_invalid     = true
                    this.fired_date_err = true
                    return this.err('Барча катакларни тўлдириш мажбурий!')
                } else {

                    this.fired_date_err  = false
                }

                const res = await this.callApi('post', '/app/change_employment_status', this.employeesStore.employee)

                if(res.status == 200) {
                    this.employeesStore.employees.splice(this.employeesStore.rowIndex, 1)

                    this.s('Ходим мувоффақиятли ўзгартирилди!')
                    this.employeesStore.fireModal = false
                    this.employeesStore.employee = {}

                } else {
                    if(res.status == 422) {
                        if(res.data.errors.last_name) {
                            this.err(res.data.errors.last_name[0])
                        }
                    } else {
                        this.swr()
                    }
                }
            },
        }
    }
</script>
