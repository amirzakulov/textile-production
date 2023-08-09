<template>

    <Modal
        v-model="employeeSalariesStore.addModal"
        title="Маош қўшиш"
        :mask-closable = "false"
        :closable = "false"
        width="768"
    >

        <Form ref="formValidate" :label-width="180">

<!--            <Card class="bg-primary ivu-mb">-->
<!--                <Row :gutter="24">-->
<!--                    <Col span="12">-->
<!--                        <Select v-model="year" placeholder="Йил...">-->
<!--                            <Option v-for="year in years" :value="year.id" :key="year.id">{{ year.label }}</Option>-->
<!--                        </Select>-->
<!--                    </Col>-->
<!--                    <Col span="12">-->
<!--                        <Select v-model="month" placeholder="Ой...">-->
<!--                            <Option v-for="month in monthes" :value="month.id" :key="month.id">{{ month.label }}</Option>-->
<!--                        </Select>-->
<!--                    </Col>-->
<!--                </Row>-->
<!--            </Card>-->


            <FormItem label="* Йил" class="ivu-mb">
                <Select v-model="employeeSalariesStore.employee.year" placeholder="Йил...">
                    <Option v-for="year in employeeSalariesStore.years" :value="year.id" :key="year.id">{{ year.label }}</Option>
                </Select>
                <Text :class="{ 'd-none': !year_err }" class="invalid">Тўлдириш мажбурий!</Text>
            </FormItem>
            <FormItem label="* Ой" class="ivu-mb">
                <Select v-model="employeeSalariesStore.employee.month" placeholder="Ой...">
                    <Option v-for="month in employeeSalariesStore.monthes" :value="month.id" :key="month.id">{{ month.label }}</Option>
                </Select>
                <Text :class="{ 'd-none': !month_err }" class="invalid">Тўлдириш мажбурий!</Text>
            </FormItem>
            <FormItem label="* Ходим" class="ivu-mb">
                <Select v-model="employeeSalariesStore.employee.employee_id" filterable placeholder="Ходим...">
                    <Option v-for="employee in employeeSalariesStore.allEmployees" :value="employee.id" :key="employee.id">{{ employee.last_name + " " + employee.first_name + " " + employee.middle_name }}</Option>
                </Select>
                <Text :class="{ 'd-none': !employee_id_err }" class="invalid">Тўлдириш мажбурий!</Text>
            </FormItem>
            <FormItem label="* Тўлов" class="ivu-mb">
                <Input v-model="employeeSalariesStore.employee.amount" placeholder="Тўлов"></Input>
                <Text :class="{ 'd-none': !amount_err }" type="danger">Тўлдириш мажбурий!</Text>
            </FormItem>

        </Form>
        <template #footer>
            <Button @click="employeeSalariesStore.addModal = false">Беркитиш</Button>
            <Button type="primary" @click="addSalary" :disabled="isAdding" :loading="isAdding">{{isAdding ? 'Махсулот...':'Сақлаш'}}</Button>
        </template>
    </Modal>

</template>

<script>
    import {useEmployeeSalariesStore} from "../../../../stores/EmployeeSalariesStore";

    export default {
        name: "addModal",
        setup() {
            const employeeSalariesStore = useEmployeeSalariesStore()

            return { employeeSalariesStore }
        },
        data(){
            return {
                isAdding: false,

                is_invalid: false,
                year_err:   false,
                month_err:  false,
                employee_id_err: false,
                amount_err: false,
            }
        },
        methods: {
            async addSalary () {
                this.isAdding = true
                if(this.employeeSalariesStore.employee.year == '') {
                    this.is_invalid     = true
                    this.year_err  = true
                } else {
                    this.year_err  = false
                }

                if(this.employeeSalariesStore.employee.month == '') {
                    this.is_invalid     = true
                    this.month_err  = true
                } else {
                    this.month_err  = false
                }

                if(this.employeeSalariesStore.employee.employee_id == '') {
                    this.is_invalid     = true
                    this.employee_id_err  = true
                } else {
                    this.employee_id_err  = false
                }

                if(this.employeeSalariesStore.employee.amount == '') {
                    this.is_invalid     = true
                    this.amount_err  = true
                } else {
                    this.amount_err  = false
                }

                if(this.is_invalid) {
                    this.is_invalid = false
                    return this.err('Барча катакларни тўлдириш мажбурий!')
                }

                const res = await this.callApi('post', '/app/add_salary', this.employeeSalariesStore.employee)

                if(res.status == 200) {
                    this.isAdding = false
                    const paidEmployees = await this.callApi('post', '/app/get_employees_salaries', {year: this.employeeSalariesStore.employee.year})
                    this.employeeSalariesStore.paidEmployees  = paidEmployees.data

                    this.s('Ходим мувоффақиятли қўшилди!')
                    this.employeeSalariesStore.addModal = false
                    this.employeeSalariesStore.employee = {}

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
