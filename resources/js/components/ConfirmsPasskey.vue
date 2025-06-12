<script setup>
    import {browserSupportsWebAuthn, startAuthentication } from "@simplewebauthn/browser";
    import { ref } from 'vue';
    import { useForm, usePage } from '@inertiajs/vue3';

    const emit = defineEmits(['confirmed', 'cancelled']);

    const confirmingPasskey = ref(false);

    const authorityConfirmed = ref(null);

    const props = defineProps({
        remember: {
            type: Boolean,
            default: false
        },
        title: {
            type: String,
            default: 'Confirm Passkey',
        },
        content: {
            type: String,
            default: 'For your security, please confirm your passkey to continue.',
        },
        email: {
            type: String,
            default: '',
        },
        mode: {
            type: String,
            default: 'verify',
            validate: (value) => ['login', 'verify'].indexOf(value) !== -1
        }
    })

    const passkeyForm = useForm({
        passkey: '',
        email: props.email,
        remember: false,
    });

    const operationCancelled = (failed=false) => {
        console.log('Operation cancelled')
        emit('cancelled', failed);
        confirmingPasskey.value = false;
    }

    const askForPasskey = () => {
        startAuthentication(JSON.parse(JSON.stringify(usePage().props.system.flash.options)))
            .then((res) =>{
                console.log('pre-post')
                passkeyForm.passkey = res;
                console.log('posting');
                console.log(props.mode)
                passkeyForm.transform(data => ({
                        ...data,
                        remember: props.remember ? 'on' : '',
                    })).post(route(props.mode == 'login' ? 'passkeys.login' : 'passkeys.verify'), {
                            preserveScroll: true,
                            preserveState: true,
                            onSuccess: () => {
                                console.log('onSuccess')
                                if(props.mode == 'login' || usePage().props.system.flash.verified) {
                                    console.log('if condition')
                                    authorityConfirmed.value = true;
                                    operationSuccess();
                                }
                            },
                            onError: (e) => {
                                console.error(e);
                                authorityConfirmed.value = false;
                                operationCancelled();
                            }
                });
            })
            .catch((e) => {
                console.log(e);
                authorityConfirmed.value = false;
                operationCancelled();
            })
    }

    defineExpose({
        passkeyForm: passkeyForm,
        start: (email = null) => {
            if(!browserSupportsWebAuthn()) {
                emit('cancelled');
            }
            if(email)
                passkeyForm.email = email;
                passkeyForm.post(route('passkeys.authentication-options'), {
                preserveScroll: true,
                preserveState: true,
                onSuccess: () => {
                    if(!usePage().props.system.flash.options) {
                        emit('cancelled');
                    }
                    else
                    {
                        authorityConfirmed.value = null;
                        confirmingPasskey.value = true;
                        askForPasskey();
                    }
                },
                onError: (e) => {
                    operationCancelled(true);
                }
            });
        },
    });


    const operationSuccess = () => {
        console.log('operationSuccess')
        confirmingPasskey.value = false;
        emit('confirmed');
    }

</script>
<template>
   
</template>