export { default as Alert } from './Alert.vue';

export enum AlertType 
{
    info = "Info",
    danger = "Danger",
    success = "Success",
    warning = "Warning",
}

export interface AlertProps
{
    type: AlertType,
    message?: string,
}