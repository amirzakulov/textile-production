<template>
    <Modal
        v-model="usersStore.addModal"
        title="Фойдаланувчи қўшиш"
        :mask-closable = "false"
        :closable = "false"
        width="600"
    >

        <Form ref="formValidate" :label-width="150">
            <FormItem label="* Исм" class="ivu-mb">
                <Input v-model="usersStore.user.fullName" placeholder="* Исм"></Input>
                <Text :class="{ 'd-none': !fullName_err }" type="danger">Тўлдириш мажбурий!</Text>
            </FormItem>
            <FormItem label="* Роль" class="ivu-mb">
                <Select v-model="usersStore.user.role_id" placeholder="Роль">
                    <template v-for="(role, i) in usersStore.roles" :key="i" >
                        <Option v-if="usersStore.loggedUser.role_id == 1" :value="role.id">{{ role.roleName }}</Option>
                        <Option v-else-if="usersStore.loggedUser.role_id != 1 && role.id != 1" :value="role.id">{{ role.roleName }}</Option>
                    </template>
                </Select>
                <Text :class="{ 'd-none': !role_id_err }" type="danger">Тўлдириш мажбурий!</Text>
            </FormItem>
            <FormItem label="* Фойдаланувчи" class="ivu-mb">
                <Input v-model="usersStore.user.username" placeholder="Фойдаланувчи"></Input>
                <Text :class="{ 'd-none': !username_err }" type="danger">Тўлдириш мажбурий!</Text>
            </FormItem>
            <FormItem label="* Пароль" class="ivu-mb">
                <Input v-model="usersStore.user.password" placeholder="Пароль"></Input>
                <Text :class="{ 'd-none': !password_err }" type="danger">Тўлдириш мажбурий!</Text>
            </FormItem>

        </Form>
        <template #footer>
            <Button type="error" @click="usersStore.addModal=false">Беркитиш</Button>
            <Button type="primary" @click="addUser" :disabled="isAdding" :loading="isAdding">{{isAdding ? 'Махсулот...':'Сақлаш'}}</Button>
        </template>
    </Modal>
</template>

<script>
    import {useUsersStore} from "../../../stores/UsersStore";

    export default {
        name: "addModal",
        setup() {
            const usersStore = useUsersStore()

            return { usersStore }
        },
        data(){
            return {
                isAdding: false,

                fullName_err  : false,
                role_id_err  : false,
                username_err  : false,
                password_err  : false,
                is_invalid: false,
            }
        },
        methods: {
            async addUser(){ //Add product

                this.isAdding = true
                if(this.usersStore.user.fullName == '') {
                    this.is_invalid = true
                    this.fullName_err   = true
                } else {
                    this.fullName_err   = false
                }

                if(this.usersStore.user.role_id == '') {
                    this.is_invalid = true
                    this.role_id_err   = true
                } else {
                    this.role_id_err   = false
                }

                if(this.usersStore.user.username == '') {
                    this.is_invalid = true
                    this.username_err   = true
                } else {
                    this.username_err   = false
                }

                if(this.usersStore.user.password == '') {
                    this.is_invalid = true
                    this.password_err   = true
                } else {
                    this.password_err   = false
                }

                if(this.is_invalid) {
                    this.is_invalid = false
                    this.isAdding = false
                    return this.err('Барча катакларни тўлдириш мажбурий!')
                }

                const res = await this.callApi('post', '/app/add_user', this.usersStore.user)
                console.log(res.data)
                if(res.status === 200) {
                    this.usersStore.users.unshift(res.data);
                    this.s('Мувоффақиятли қўшилди!')
                    this.usersStore.addModal = false
                    this.usersStore.user = {}

                } else {
                    if(res.status == 422) {
                        if(res.data.errors.fullName) {
                            this.err(res.data.errors.fullName[0])
                        }

                        if(res.data.errors.username) {
                            this.err(res.data.errors.username[0])
                        }

                        if(res.data.errors.role_id) {
                            this.err(res.data.errors.role_id[0])
                        }

                        if(res.data.errors.password) {
                            this.err(res.data.errors.password[0])
                        }
                    } else {
                        this.swr()
                    }
                }
            },
        }
    }
</script>

<style scoped>

</style>
