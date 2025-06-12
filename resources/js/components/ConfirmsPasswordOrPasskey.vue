<script setup>
import { ref, reactive, nextTick } from 'vue';
import InputError from './InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import Label from '@/components/ui/label/Label.vue';
import ConfirmsPasskey from './ConfirmsPasskey.vue';
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

const emit = defineEmits(['confirmed', 'confirming', 'cacelled']);

const props = defineProps({
    mandatory: {
        type: Boolean,
        default: false,
    },
    seconds: {
        type: Number,
        default: 60,
    }
});

const confirmingPassword = ref(false);

const passkeyConfirmation = ref(null);

const form = reactive({
    password: '',
    error: '',
    processing: false,
});

const passwordInput = ref(null);

const askForPassword = () => {
    console.log('askForPassword')
    confirmingPassword.value = true;
    setTimeout(() => passwordInput.value.focus(), 250);
}

const startConfirmingPassword = () => {
    emit('confirming');
    axios.get(route('password.confirmation')).then(response => {
        if (response.data.confirmed && !props.mandatory) {
            emit('confirmed');
        } else {
            passkeyConfirmation.value.start();
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
    confirmingPassword.value = false;
    form.password = '';
    form.error = '';
    emit('cancelled');
};

const confirmed = () => {
    nextTick().then(() => {
        console.log('confirmed');
        emit('confirmed')
    });
}
</script>

<template>
    <span>
        <span @click="startConfirmingPassword">
            <slot />
        </span>

        <ConfirmsPasskey :email="$page.props.auth.user.email" ref="passkeyConfirmation" @confirmed="confirmed" @cancelled="askForPassword"/>

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
