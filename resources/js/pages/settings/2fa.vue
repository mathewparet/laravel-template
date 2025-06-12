<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

import HeadingSmall from '@/components/HeadingSmall.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { type BreadcrumbItem } from '@/types';
import ConfirmsPasswordOrPasskey from '@/components/ConfirmsPasswordOrPasskey.vue';

interface Props {
    className?: string;
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
        title: 'Two Factor Authentication',
        href: '/settings/password',
    },
];

const totpInput = ref<HTMLInputElement | null>(null);

const totpData = ref<Totp | null>(null)

const form = useForm({
    
});

const disable2FAForm = useForm({

})

const twoFactorActivationForm = useForm({
    code: '',
})


const disable2FA = () => {
    form.delete(route('2fa.disable'), {
        preserveScroll: true,
        preserveState: false,
    })
}

const request2FA = () => {
    form.get(route('2fa.request'), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            totpData.value = usePage().props.system?.flash?.totp;
            if(totpInput.value instanceof HTMLInputElement) {
                totpInput.value?.focus();
            }
        }
    })
}

const activate2FA = () => {
    twoFactorActivationForm.post(route('2fa.activate'), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            totpData.value = usePage().props.system?.flash?.totp;
        }
    })
}

const regenerateBackupCodes = () => {
    disable2FAForm.post(route('2fa.regenerate'), {
        preserveScroll: true,
        onSuccess: () => {
            totpData.value = usePage().props.system?.flash?.totp;
        }
    })
}

const showBackupCodes = () => {
    disable2FAForm.get(route('2fa.codes'), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            totpData.value = usePage().props.system?.flash?.totp;
        }
    })
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Two Factor Authentication" />

        <SettingsLayout>
            <div class="space-y-6">
                <HeadingSmall title="Two factor authentication" description="Add additional security to your account using two factor authentication." />

                <div class="grid gap-2">
                    <h1 class="scroll-m-20 text-xl font-semibold tracking-tight" v-if="!$page.props.auth.user.is_two_factor_enabled && !totpData?.qrcode">You have <span class="text-red-500">not enabled</span> two factor authentication.</h1>
                    <h1 class="scroll-m-20 text-xl font-semibold tracking-tight" v-else-if="totpData?.qrcode"><span class="text-blue-500">Just one more step</span> to finish enabling two factor authentication.</h1>
                    <h1 class="scroll-m-20 text-xl font-semibold tracking-tight" v-else>You have <span class="text-green-500">enabled</span> two factor authentication.</h1>
                    <p class="leading-1 [&:not(:first-child)]:mt-2">When two factor authentication is enabled, you will be prompted for a secure, random token during authentication. You may retrieve this token from your phone's Authenticator application.</p>
                </div>

                <div v-if="!$page.props.auth.user.is_two_factor_enabled">
                    <div v-if="!totpData" class="space-y-6">
                        <div class="flex items-center gap-4">
                            <ConfirmsPasswordOrPasskey key="request2FA" @confirmed="request2FA">
                                <Button :disabled="form.processing">Enable</Button>
                            </ConfirmsPasswordOrPasskey>
                        </div>
                    </div>

                    <div v-else>
                        <div v-if="totpData?.qrcode" class="space-y-6">
                            <div class="grid gap-2">
                                <div class="font-semibold text-sm">To finish enabling two factor authentication, scan the following QR code using your phone's authenticator application or enter the setup key and provide the generated OTP code.</div>
                                <img :src="totpData?.qrcode" class="size-60">
                            </div>

                            <div class="grid gap-2">
                                <Label>Setup Key</Label>
                                <div class="text-sm text-muted-foreground">{{ totpData?.secret }}</div>
                            </div>

                            <div class="grid gap-2">
                                <Label for="code">Code</Label>
                                <Input
                                    id="code"
                                    ref="totpInput"
                                    v-model="twoFactorActivationForm.code"
                                    type="text"
                                    class="mt-1 block w-full"
                                    autocomplete="otp"
                                />
                                <InputError :message="twoFactorActivationForm.errors.code" />
                            </div>

                            <div class="flex items-center gap-4">
                                <ConfirmsPasswordOrPasskey key="activate2FA" @confirmed="activate2FA">
                                    <Button :disabled="twoFactorActivationForm.processing">Confirm</Button>
                                </ConfirmsPasswordOrPasskey>
                                <Button variant="outline" :disabled="twoFactorActivationForm.processing" @click.prevent="() => totpData = null">Cancel</Button>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="$page.props.auth.user.is_two_factor_enabled" class="space-y-6">
                    <div v-if="totpData?.backup_codes" class="space-y-6">
                        <div class="grid gap-2">
                            <Label>Store these recovery codes in a secure password manager. They can be used to recover access to your account if your two factor authentication device is lost.</Label>
                            <div class="grid gap-1 max-w-xl mt-4 px-4 py-4 font-mono text-sm bg-gray-100 dark:bg-gray-900 dark:text-gray-100 rounded-lg">
                                <div v-for="backup_code in totpData.backup_codes" :key="backup_code">
                                    <span :class="{'line-through text-gray-400': backup_code.used}">{{ backup_code.code }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <span v-if="totpData?.backup_codes">
                            <ConfirmsPasswordOrPasskey key="regenerateBackupCodes" @confirmed="regenerateBackupCodes">
                                <Button variant="outline" :disabled="disable2FAForm.processing">Regenerate Backup Codes</Button>
                            </ConfirmsPasswordOrPasskey>
                        </span>
                        <span v-else>
                            <ConfirmsPasswordOrPasskey key="showBackupCodes" @confirmed="showBackupCodes">
                                <Button variant="outline" :disabled="disable2FAForm.processing">Show Backup Codes</Button>
                            </ConfirmsPasswordOrPasskey>
                        </span>
                        <ConfirmsPasswordOrPasskey key="disable2FA" @confirmed="disable2FA">
                            <Button variant="destructive" :disabled="disable2FAForm.processing">Disable</Button>
                        </ConfirmsPasswordOrPasskey>
                    </div>
                </div>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
