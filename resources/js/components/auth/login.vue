<template>
    <div class="container">
        <div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20 col-md-4">
            <div class="_overflow _table_div p-4">

                <h2 class="text-center ivu-mb text-info"><Icon type="md-log-in" /> Tizimga kirish</h2>
                <Form ref="formValidate">
                    <FormItem class="ivu-mb">
                        <Input placeholder="Username" v-model="data.username"></Input>
                    </FormItem>
                    <FormItem class="ivu-mb">
                        <Input type="password" password placeholder="Parol" v-model="data.password"></Input>
                    </FormItem>
                </Form>
                <div class="login-footer">
                    <Button type="primary" long class="ivu-pl ivu-pr" @click="login" :disabled="isLogging" :loding="isLogging">{{isLogging ? 'Iltimos kuting':'Kirish'}}</Button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "login",
    data(){
        return {
            data: {
                username: '',
                password: ''
            },
            isLogging: false
        }
    },
    methods: {
        async login(){
            if(this.data.username.trim() === '')    return this.err('Username majburiy.')
            if(this.data.password === '')           return this.err('Parol majburiy.')
            this.isLogging = true
            const res = await this.callApi('post', '/app/login', this.data)
            // console.log(res.data)
            if(res.status === 200){
                this.s(res.data.msg)
                window.location = '/'
            } else {
                if(res.data === 401) {
                    this.i(res.data.msg)
                } else {
                    this.swr("Username yoki parol noto'g'ri");
                }
            }
            this.isLogging = false
        },
    }
}
</script>

<style scoped>
._1adminOverveiw_table_recent {
    margin: 0 auto;
    margin-top: 100px;
}
.login-footer {
    text-align: center;
}
</style>
