export default class User{

    constructor(data = {}){
        this.id = data.id ?? null
        this.name = data.name ?? ''
        this.email = data.email ?? ''
        this.password = data.password ?? null
        this.password_confirmation = data.password_confirmation ?? null
    }
}