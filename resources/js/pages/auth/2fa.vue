<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import InputHelp from '@/components/InputHelp.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();

const form = useForm({
    otp: '',
});

const submit = () => {
    form.post(route('2fa.attempt'), {
        onFinish: () => form.reset('otp'),
    });
};

</script>

<template>
    <AuthBase title="Two factor authentication" description="Enter the one time code from your Authenticator app">
        <Head title="Two factor authentication" />

        <div v-if="status" class="mb-4 text-center text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="flex flex-col gap-6">
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="otp">Two factor code</Label>
                    <Input
                        id="otp"
                        type="text"
                        required
                        autofocus
                        :tabindex="1"
                        autocomplete="otp"
                        v-model="form.otp"
                    />
                    <InputError :message="form.errors.otp" />
                    <InputHelp>If you lost access to your two factor device, please enter one of your unused backup codes.</InputHelp>
                </div>

                <Button type="submit" class="mt-4 w-full" :tabindex="4" :disabled="form.processing">
                    <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                    Verify
                </Button>
                <Link class="block w-full" method="post" :href="route('logout')" as="button">
                    Log out
                </Link>
            </div>
        </form>
    </AuthBase>
</template>
