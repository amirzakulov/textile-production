<template>
    <Modal
        v-model="employeesStore.editModal"
        title="Ходимни тахрирлаш"
        :mask-closable="false"
        :closable = "false"
        :width = "768"
        @on-visible-change="employeesStore.modalVisibility"
    >

        <Form ref="formValidate" :label-width="180">

            <FormItem label="* Бўлим" class="ivu-mb">
                <Select v-model="employeesStore.employee.department_id" placeholder="Бўлимни танланг...">
                    <Option v-for="department in employeesStore.departments" :value="department.id" :key="department.id">{{ department.name }}</Option>
                </Select>
                <Text :class="{ 'd-none': !department_id_err }" class="invalid">Тўлдириш мажбурий!</Text>
            </FormItem>

            <FormItem label="* Бўлим тури" class="ivu-mb">
                <Select v-model="employeesStore.employee.type" placeholder="Бўлим турини танланг...">
                    <Option value="1">АУП</Option>
                    <Option value="2">Ишлаб чиқариш</Option>
                </Select>
                <Text :class="{ 'd-none': !type_err }" type="danger">Тўлдириш мажбурий!</Text>
            </FormItem>

            <FormItem label="* Фамилия" class="ivu-mb">
                <Input v-model="employeesStore.employee.last_name" placeholder="Фамилия"></Input>
                <Text :class="{ 'd-none': !last_name_err }" type="danger">Тўлдириш мажбурий!</Text>
            </FormItem>
            <FormItem label="* Исм" class="ivu-mb">
                <Input v-model="employeesStore.employee.first_name" placeholder="Исм"></Input>
                <Text :class="{ 'd-none': !first_name_err }" type="danger">Тўлдириш мажбурий!</Text>
            </FormItem>
            <FormItem label="Отаси" class="ivu-mb">
                <Input v-model="employeesStore.employee.middle_name" placeholder="Отаси"></Input>
            </FormItem>
            <FormItem label="Телефон" class="ivu-mb">
                <Input v-model="employeesStore.employee.phone" placeholder="Телефон"></Input>
            </FormItem>
            <FormItem label="Манзил" class="ivu-mb">
                <Input v-model="employeesStore.employee.address" placeholder="Манзил"></Input>
            </FormItem>

            <FormItem label="Қўшимча маълумотлар" class="ivu-mb">
                <Input v-model="employeesStore.employee.description" type="textarea" :rows="4" placeholder="Қўшимча маълумотлар"></Input>
            </FormItem>
        </Form>
        <template #footer>
            <Button @click="employeesStore.editModal = false">Беркитиш</Button>
            <Button type="primary" @click="editEmployee" :disabled="isEditing" :loading="isEditing">{{isEditing ? 'Сақлаш...':'Сақлаш'}}</Button>
        </template>
    </Modal>
</template>

<script>
    import {useEmployeesStore} from "../../../stores/EmployeesStore";

    export default {
        name: "isEditing",
        setup() {
            const employeesStore = useEmployeesStore()

            return { employeesStore }
        },
        data(){
            return {
                isEditing: false,

                is_invalid: false,
                department_id_err: false,
                type_err:       false,
                first_name_err: false,
                last_name_err:  false,
                hired_date_err: false,

            }
        },
        methods: {
            async editEmployee(){ //edit Employee
                this.isEditing = true
                if(this.employeesStore.employee.department_id == '') {
                    this.is_invalid     = true
                    this.department_id_err  = true
                } else {
                    this.department_id_err  = false
                }

                if(this.employeesStore.employee.type == '') {
                    this.is_invalid     = true
                    this.type_err  = true
                } else {
                    this.type_err  = false
                }

                if(this.employeesStore.employee.last_name == '') {
                    this.is_invalid     = true
                    this.last_name_err  = true
                } else {
                    this.last_name_err  = false
                }

                if(this.employeesStore.employee.first_name == '') {
                    this.is_invalid     = true
                    this.first_name_err  = true
                } else {
                    this.first_name_err  = false
                }

                if(this.employeesStore.employee.hired_date == '') {
                    this.is_invalid     = true
                    this.hired_date_err  = true
                } else {
                    this.hired_date_err  = false
                }

                if(this.is_invalid) {
                    this.is_invalid = false
                    return this.err('Барча катакларни тўлдириш мажбурий!')
                }

                const res = await this.callApi('post', '/app/edit_employee', this.employeesStore.employee)
                if(res.status === 200) {

                    this.employeesStore.employees[this.employeesStore.rowIndex] = res.data

                    this.s('Ходимнинг ўзгаришлари мувоффақиятли сақланди!')
                    this.employeesStore.editModal = false
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
