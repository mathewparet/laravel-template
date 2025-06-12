<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { TransitionRoot } from '@headlessui/vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

import HeadingSmall from '@/components/HeadingSmall.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { type BreadcrumbItem } from '@/types';
import ConfirmsPasswordOrPasskey from '@/components/ConfirmsPasswordOrPasskey.vue';
import { DateTime } from 'luxon'
import {startRegistration} from "@simplewebauthn/browser";
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { LoaderCircle } from 'lucide-vue-next';

interface Props {
    
}

interface Totp {
    secret?: string,
    qrcode?: string,
    backup_codes?: Array<string>,
}

const props = defineProps<Props>();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Settings',
        href: '/settings',
    },
    {
        title: 'Passkey',
        href: '/settings/passkey',
    },
];

const registeringNewPasskey = ref<boolean>(false)

const registrationInProgress = ref<boolean>(false)

const nameInput = ref<HTMLInputElement | null>(null)

const form = useForm({
    passkey: <any> '',
    name: <string> '',
    locked: <boolean> false,
});

const closeModal = () => {
    registeringNewPasskey.value = false;
    registrationInProgress.value = false
    form.reset();
    form.passkey = '';
    form.name = '';
    form.locked = false;
    form.clearErrors();
}

const showModal = () => {
    registeringNewPasskey.value = true;
    setTimeout(() => nameInput.value?.focus(), 250);
}

const register = () => {
    registrationInProgress.value = true
    form.locked = true;
    form.post(route('passkeys.registration-options'), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            console.log('from server', usePage().props.system.flash.options);
            startRegistration(JSON.parse(JSON.stringify(usePage().props.system.flash.options)))
                .then((res) =>{
                    form.passkey = res;
                    form.post(route('passkeys.store'), {
                        preserveScroll: true
                    })
                })
                .catch((err) => console.log(err))
                .finally(() => closeModal());
        }
    })
}

const unregister = (passkey) => {
    form.delete(route('passkeys.destroy', {passkey: passkey.id}), {
        preserveScroll: true
    })
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Passkey" />

        <SettingsLayout>
            <div class="space-y-6">
                <HeadingSmall title="Passkey" description="Passkeys are a secure form of authentication which enables you to authenticate with your device's authentication mechanism. With passkeys you will not need to login using your password or 2FA, instead you could just use your passkey." />

                <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-6 py-3">
                                    Name
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Created At
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Last Access
                                </th>
                                <th scope="col" class="px-6 py-3 text-right">
                                    Action
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr :key="passkey" v-for="passkey in $page.props.auth.user.passkeys" class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ passkey.name }}
                                </th>
                                <td class="px-6 py-4">
                                    {{ DateTime.fromISO(passkey.created_at).toLocaleString(DateTime.DATETIME_MED) }}
                                </td>
                                <td class="px-6 py-4">
                                    <span v-if="JSON.parse(passkey.public_key)?.date?.lastAccesTs">
                                        {{ DateTime.fromSeconds(JSON.parse(passkey.public_key).date.lastAccesTs).toLocaleString(DateTime.DATETIME_MED) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <ConfirmsPasswordOrPasskey @confirmed="unregister(passkey)" :key="passkey.id+'-unregister'">
                                        <div class="text-right">
                                            <span class="inline-flex items-center font-medium text-red-600 dark:text-red-500 hover:underline cursor-pointer focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 dark:focus:ring-offset-red-800">Unregister</span>
                                        </div>
                                    </ConfirmsPasswordOrPasskey>
                                </td>
                            </tr>
                            <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                                <td colspan="4">
                                    <ConfirmsPasswordOrPasskey @confirmed="showModal" key="showModal">
                                        <div class="text-center">
                                            <span class="cursor-pointer inline-flex items-center font-medium text-blue-600 dark:text-blue-500 hover:underline focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-blue-800">Create a Passkey?</span>
                                        </div>
                                    </ConfirmsPasswordOrPasskey>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </SettingsLayout>
        <Dialog v-model:open="registeringNewPasskey">
            <DialogContent>
                <div class="space-y-6">
                    <DialogHeader class="space-y-3">
                        <DialogTitle>Passkey Device Name</DialogTitle>
                    </DialogHeader>

                    <div class="grid gap-2">
                        <Label for="deviceName" class="sr-only">Device Name</Label>
                        <Input id="deviceName" type="deviceName" deviceName="deviceName" ref="nameInput" @keyup.enter="register" v-model="form.name" />
                        <InputError :message="form.error" />
                    </div>

                    <DialogFooter class="gap-2">
                        <DialogClose as-child>
                            <Button variant="outline" @click="closeModal"> Cancel </Button>
                        </DialogClose>

                        <Button :disabled="form.processing || form.locked" @click.prevent="register">
                            <LoaderCircle v-if="form.processing || form.locked" class="h-4 w-4 animate-spin" />
                            Register Passkey    
                        </Button>
                    </DialogFooter>
                </div>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
