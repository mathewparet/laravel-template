<script setup>
import { ref, reactive, nextTick } from 'vue';
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
import InputError from './InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';

const emit = defineEmits(['confirmed']);

defineProps({
    
});

const confirmingPassword = ref(false);

const form = reactive({
    password: '',
    error: '',
    processing: false,
});

const passwordInput = ref(null);

const startConfirmingPassword = () => {
    axios.get(route('password.confirmation')).then(response => {
        if (response.data.confirmed) {
            emit('confirmed');
        } else {
            confirmingPassword.value = true;

            setTimeout(() => passwordInput.value.focus(), 250);
        }
    });
};

const confirmPassword = () => {
    form.processing = true;

    axios.post(route('password.confirm'), {
        password: form.password,
    }).then(() => {
        form.processing = false;

        closeModal();
        nextTick().then(() => emit('confirmed'));

    }).catch(error => {
        form.processing = false;
        form.error = error.response.data.errors.password[0];
        passwordInput.value.focus();
    });
};

const closeModal = () => {
    form.password = '';
    form.error = '';
    confirmingPassword.value = false;
};
</script>

<template>
    <span>
        <span @click="startConfirmingPassword">
            <slot />
        </span>

        <Dialog v-model:open="confirmingPassword">
            <DialogContent>
                <div class="space-y-6">
                    <DialogHeader class="space-y-3">
                        <DialogTitle>Authorize action?</DialogTitle>
                        <DialogDescription>
                            This action needs to be authorized using your password.
                        </DialogDescription>
                    </DialogHeader>

                    <div class="grid gap-2">
                        <Label for="password" class="sr-only">Password</Label>
                        <Input id="password" type="password" name="password" ref="passwordInput" @keyup.enter="confirmPassword" v-model="form.password" placeholder="Password" />
                        <InputError :message="form.error" />
                    </div>

                    <DialogFooter class="gap-2">
                        <DialogClose as-child>
                            <Button variant="outline" @click="closeModal"> Cancel </Button>
                        </DialogClose>

                        <Button :disabled="form.processing" @click.prevent="confirmPassword">
                            Confirm    
                        </Button>
                    </DialogFooter>
                </div>
            </DialogContent>
        </Dialog>
    </span>
</template>
