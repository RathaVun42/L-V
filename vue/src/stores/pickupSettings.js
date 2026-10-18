import { defineStore } from "pinia";
import { getPickupSettings, updatePickupSettings } from "@/services/pickupSetting";

export const usePickupSettingsStore = defineStore('pickupSettings', {
    state: () =>({
        settings: null,
        loading: false,
        saving: false,
        error: null
    }),
    actions: {
        async fetchSettings(){
            this.loading = true
            this.error = null

            try{
                this.settings = await getPickupSettings()
                return this.settings
            }catch(err){
                this.error = err.response?.data?.message || 'failed to load pickup settings.'
            }finally{
                this.loading = false
            }
        },
        async saveSettings(data){
            this.saving = true
            this.error = null
            try{
                this.settings = await updatePickupSettings(data)
                return this.settings
            }catch(err){
                this.error = err.response?.data?.message || 'failed to save pickup settings.'
            }finally{
                this.saving = false
            }
        }
    }
})