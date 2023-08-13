<template>
    <UsersHeader />
    <!--~~~~~~~ TABLE ONE ~~~~~~~~~-->
    <div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">
        <p class="_title0">
            <Button type="primary" @click="usersStore.addModal = true" class="text-white"><Icon type="md-add-circle" class="mr-1" />Қўшиш</Button>
        </p>

        <Row>
            <Col span="14">
                <Table size="small" :columns="columns" :data="usersStore.users" :loading="loading"></Table>
            </Col>
        </Row>

    </div>

    <AddUserModal />
    <EditUserModal />
    <DeleteUserModal />

</template>

<script>
import UsersHeader from './parts/header.vue'
import {useUsersStore} from "../../stores/UsersStore";
import {resolveComponent} from "vue";

import AddUserModal from './parts/addModal'
import EditUserModal from './parts/editModal'
import DeleteUserModal from './parts/deleteModal'
export default {
    name: "users",
    // props: ['loggedUser'],
    setup() {
        const usersStore = useUsersStore()

        return { usersStore }
    },
    components: {
        UsersHeader:UsersHeader,
        AddUserModal:AddUserModal,
        EditUserModal:EditUserModal,
        DeleteUserModal:DeleteUserModal,
    },
    data(){
        return {
            loading: true,
            columns: [
                {
                    title: "Исм",
                    key: 'fullName',
                    sortable: true
                },
                {
                    title: "Роль",
                    key: 'roleName',
                },
                {
                    title: "Фойдаланувчи",
                    key: 'username',
                },
                {
                    title: " ",
                    key: 'action',
                    width: 180,
                    align: 'right',
                    render: (h, params) => {

                        return h('div', {
                                class: 'ivu-btn-group ivu-btn-group-default'
                            },
                            [
                                h(resolveComponent('Button'),
                                    {
                                        title:"Тахрирлаш",
                                        type: 'primary',
                                        size: 'small',

                                        onClick: () => {
                                            this.showEditModal(params.row, params.index)
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
                    }
                },
            ],
        }
    },
    methods : {


        async editUser(){ //edit product
            if(this.editData.fullName.trim() === '')    return this.err('Ism majburiy.')
            if(this.editData.store_id === '')           return this.err("Do'kon majburiy.")
            if(this.editData.username.trim() === '')    return this.err('Username majburiy.')
            if(this.editData.role_id === '')            return this.err('Turi majburiy.')


            const res = await this.callApi('post', '/app/edit_user', this.editData)

            if(res.status === 200) {
                this.users[this.index].fullName  = res.data.fullName
                this.users[this.index].username  = res.data.username
                this.users[this.index].role_id  = res.data.role_id
                this.users[this.index].roleName  = res.data.roleName
                this.users[this.index].store_id  = res.data.store_id
                this.users[this.index].storeName  = res.data.storeName

                this.s('Foydalanuvchi ma\'lumotlari muvoffaqiyatli saqlandi!')
                this.editModal = false
            } else {
                if(res.status == 422) {
                    if(res.data.errors.fullName) {
                        this.err(res.data.errors.fullName[0])
                    }

                    if(res.data.errors.username) {
                        this.err(res.data.errors.username[0])
                    }

                    if(res.data.errors.password) {
                        this.err(res.data.errors.password[0])
                    }
                } else {
                    this.swr()
                }
            }
        },

        async showEditModal(user, index) {
            let userObj = {
                id:          user.id,
                fullName:    user.fullName,
                role_id:     user.role_id,
                username:    user.username,

            }

            this.usersStore.user = userObj
            this.usersStore.rowIndex = index
            this.usersStore.editModal = true
        },
        async showDeletingModal(user, i){
            this.usersStore.user        = user
            this.usersStore.rowIndex    = i
            this.usersStore.deleteModal = true
        },
    },

    async created(){
        const [users, roles, loggedUser] = await Promise.all([
            this.callApi('get', '/app/get_users'),
            this.callApi('get', '/app/get_roles'),
            this.callApi('get', '/app/logged_user'),

        ])

        this.usersStore.users = users.data
        this.usersStore.roles = roles.data
        this.usersStore.loggedUser = loggedUser.data

        this.loading = false
    },
}
</script>

<style scoped>

</style>
