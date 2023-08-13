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
                {resourceName:"Маълумотлар тахтаси",read:true,"write":true,update:true,delete:true,name:"/",subPage:false,icon:"md-home"},
                {resourceName:"Хомашё омбори",read:true,"write":true,update:true,delete:true,name:"/raw-materials",subPage:false,icon:"logo-dropbox"},
                {resourceName:"Ишлаб чиқариш",read:true,"write":true,update:true,delete:true,name:"/production",subPage:false,icon:"ios-cube"},
                {resourceName:"Тайёр махсулот",read:true,"write":true,update:true,delete:true,name:"/finished_products",subPage:true,icon:"ios-cart"},
                {resourceName:"Моделлар",read:true,"write":true,update:true,delete:true,name:"/fashions",subPage:false,icon:"ios-shirt"},
                {resourceName:"Фойдаланувчилар",read:true,"write":true,update:true,delete:true,name:"/users",subPage:false,icon:"md-people"},
                {resourceName:"Roles",read:true,"write":true,update:true,delete:true,name:"/roles",subPage:false,icon:"ios-key"},
                {resourceName:"Ходимлар",read:true,"write":true,update:true,delete:true,name:"/employees",subPage:false,icon:"ios-people"},
                {resourceName:"Харажатлар",read:true,"write":true,update:true,delete:true,name:"/expenses",subPage:false,icon:"md-calculator"},
                {resourceName:"Валюталар",read:true,"write":true,update:true,delete:true,name:"/currency_rates",subPage:false,icon:"logo-usd"},
                {resourceName:"Хисоботлар",read:true,"write":true,update:true,delete:true,name:"/reports",subPage:false,icon:"md-people"}
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
