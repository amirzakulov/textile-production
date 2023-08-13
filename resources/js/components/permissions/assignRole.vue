<template>
    <UsersHeader />
    <!--~~~~~~~ TABLE ONE ~~~~~~~~~-->
    <div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">
        <p class="_title0">
            <!-- <Button type="primary" @click="addModal=true" class="text-white"><Icon type="md-add-circle" class="mr-1" />Q'shish</Button> -->
            <Row>
                <Col span="6">
                    <Form ref="formValidate" :label-width="50">
                        <FormItem label="* Turi">
                            <Select v-model="data.id" placeholder="Turi"  @on-change="changeRole">
                                <Option v-for="(role, i) in roles" :key="i" :value="role.id">{{ role.roleName }}</Option>
                            </Select>
                        </FormItem>
                    </Form>
                </Col>
            </Row>
            
        </p>

        <div class="_overflow _table_div">
            <table class="_table">
                <thead>
                <tr class="blue_thead bordered">
                    <th>Resouce Name</th>
                    <th><Checkbox size="small" v-model="checkboxsRead" :disabled="selected_role_id == 1" @on-change="checkAllRead">Read</Checkbox></th>
                    <th><Checkbox size="small" v-model="checkboxsWrite" :disabled="selected_role_id == 1" @on-change="checkAllWrite">Write</Checkbox></th>
                    <th><Checkbox size="small" v-model="checkboxsUpdate" :disabled="selected_role_id == 1" @on-change="checkAllUpdate">Update</Checkbox></th>
                    <th><Checkbox size="small" v-model="checkboxsDelete" :disabled="selected_role_id == 1"  @on-change="checkAllDelete">Delete</Checkbox></th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(permission, i) in permissions" :key="i" v-if="permissions.length">
                    <td>{{ permission.resourceName }}</td>
                    <td><Checkbox size="small" v-model="permission.read" :disabled="selected_role_id == 1" /></td>
                    <td><Checkbox size="small" v-model="permission.write" :disabled="selected_role_id == 1" /></td>
                    <td><Checkbox size="small" v-model="permission.update" :disabled="selected_role_id == 1" /></td>
                    <td><Checkbox size="small" v-model="permission.delete" :disabled="selected_role_id == 1" /></td>
                </tr>
                </tbody>
            </table>
            <div class="text-center mt-5">
                <Button type="primary" :loading="isSending" :disabled="isSending" @click="assignRoles">Saqlash</Button>
            </div>
        </div>
    </div>
</template>

<script>
import UsersHeader from './parts/header.vue'
export default {
    name: "Roles",
    components: {
        UsersHeader:UsersHeader
    },
    data(){
        return {
            isSending: false,
            data: {
                id: null,
                roleName: '',
            },
            roles: [],
            permissions: [],
            defaultPermissions: [
                {resourceName: "Маълумотлар",  read: false, write: false, update: false, delete: false, name: '/',         subPage: false, icon:'md-home'},
                {resourceName: "Махсулотлар",  read: false, write: false, update: false, delete: false, name: '/products',  subPage: false, icon:'ios-construct'},
                {resourceName: "Харидолар",    read: false, write: false, update: false, delete: false, name: '/clients',   subPage: false, icon: 'ios-people'},
                {resourceName: "Омборлар",     read: false, write: false, update: false, delete: false, name: '/stocks',   subPage: false, icon: 'md-list-box'},
                {resourceName: "Таралар",      read: false, write: false, update: false, delete: false, name: '/tares',   subPage: false, icon: 'train'},
                {resourceName: "Фойдаланувчилар",      read: false, write: false, update: false, delete: false, name: '/users',     subPage: false, icon: 'md-people'},
                {resourceName: "Roles",        read: false, write: false, update: false, delete: false, name: '/users/roles', subPage: true, icon: ''},
                {resourceName: "Assign Role",  read: false, write: false, update: false, delete: false, name: '/users/assignRole', subPage: true, icon: ''},
                {resourceName: "Хисоботлар",   read: false, write: false, update: false, delete: false, name: '/reports',   subPage: false, icon: 'md-calculator'},
                {resourceName: "Созламалар",   read: false, write: false, update: false, delete: false, name: '/settings',  subPage: false, icon: 'md-settings'},
            ],
            role: {},
            selected_role_id: 1,
            role_name_err: false,
            checkboxsRead: false,
            checkboxsWrite: false,
            checkboxsUpdate: false,
            checkboxsDelete: false,

        }
    },
    methods : {
        async assignRoles(){
            let data = JSON.stringify(this.permissions)
            const res = await this.callApi("post", "/app/assign_roles", {permission: data, id: this.data.id})

            if(res.status == 200) {
                this.s("Roles has been assigned sucessfully!")
                let index = this.roles.findIndex(role => role.id == this.data.id)
                this.roles[index].permission = data
            } else {
                this.swr()
            }

        },

        async changeRole(){
            let index = this.roles.findIndex(role => role.id == this.data.id)
            let permission = this.roles[index].permission


            console.log(this.roles[index]);
            this.selected_role_id = this.roles[index].id
            
            if(!permission) {
                this.permissions = this.defaultPermissions
            } else {
                this.permissions = JSON.parse(permission)
            }
        },
        checkAllRead(){
            this.permissions.forEach((permission, index) => {
                if(this.permissions.length) {
                    permission.read = this.checkboxsRead
                }
            })
        },
        checkAllWrite(){
            this.permissions.forEach((permission, index) => {
                if(this.permissions.length) {
                    permission.write = this.checkboxsWrite
                }
            })
        },
        checkAllUpdate(){
            this.permissions.forEach((permission, index) => {
                if(this.permissions.length) {
                    permission.update = this.checkboxsUpdate
                }
            })
        },
        checkAllDelete(){
            this.permissions.forEach((permission, index) => {
                if(this.permissions.length) {
                    permission.delete = this.checkboxsDelete
                }
            })
        }
    },

    async created(){
        console.log(this.permissions);
        const roles = await this.callApi('get', '/app/get_roles') 
        console.log(roles);
        if(roles.status == 200) {
            this.roles = roles.data
            if(roles.data.length) { 
                this.data.id = roles.data[0].id
                if(roles.data[0].permission) {
                    this.permissions = JSON.parse(roles.data[0].permission)
                } else {
                    this.permissions = this.defaultPermissions
                }
            } 
        }

        this.permissions.forEach((permission, index) => {
            if (permission.read == false){
                return this.checkboxsRead = false
            }

            // checkboxsWrite: false,
            // checkboxsUpdate: false,
            // checkboxsDelete: false,

        })
    },
}
</script>
