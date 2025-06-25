<template>
    <div class="p-6">
        <form @submit.prevent="submit">
            <div class="grid grid-cols-1 gap-6 mt-4 sm:grid-cols-2">
                <!-- Name -->
                <div>
                    <InputLabel for="name" value="Name" />
                    <TextInput
                        id="name"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.name"
                        required
                        autofocus
                    />
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>

                <!-- Email -->
                <div>
                    <InputLabel for="email" value="Email" />
                    <TextInput
                        id="email"
                        type="email"
                        class="mt-1 block w-full"
                        v-model="form.email"
                        required
                    />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <!-- Phone -->
                <div>
                    <InputLabel for="phone" value="Phone" />
                    <TextInput
                        id="phone"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.phone"
                    />
                    <InputError class="mt-2" :message="form.errors.phone" />
                </div>

                <!-- Designation -->
                <div>
                    <InputLabel for="designation" value="Designation" />
                    <TextInput
                        id="designation"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.designation"
                    />
                    <InputError class="mt-2" :message="form.errors.designation" />
                </div>

                <!-- Password -->
                <div>
                    <InputLabel for="password" :value="editing ? 'New Password (optional)' : 'Password'" />
                    <TextInput
                        id="password"
                        type="password"
                        class="mt-1 block w-full"
                        v-model="form.password"
                        :required="!editing"
                        autocomplete="new-password"
                    />
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <!-- Confirm Password -->
                <div>
                    <InputLabel for="password_confirmation" :value="editing ? 'Confirm New Password' : 'Confirm Password'" />
                    <TextInput
                        id="password_confirmation"
                        type="password"
                        class="mt-1 block w-full"
                        v-model="form.password_confirmation"
                        :required="!!form.password"
                        autocomplete="new-password"
                    />
                    <InputError class="mt-2" :message="form.errors.password_confirmation" />
                </div>

                <!-- Status -->
                <div class="col-span-2">
                    <label class="flex items-center">
                        <Checkbox v-model:checked="form.is_enabled" />
                        <span class="ml-2 text-sm text-gray-600">Account Active</span>
                    </label>
                    <InputError class="mt-2" :message="form.errors.is_enabled" />
                </div>

                <!-- Roles -->
                <div class="col-span-2">
                    <InputLabel value="Roles" />
                    <div class="mt-2 space-y-2">
                        <label v-for="role in roles" :key="role.id" class="flex items-center">
                            <Checkbox
                                :checked="form.roles.includes(role.id)"
                                @change="toggleRole(role.id)"
                            />
                            <span class="ml-2 text-sm text-gray-600">{{ role.name }}</span>
                        </label>
                    </div>
                    <InputError class="mt-2" :message="form.errors.roles" />
                </div>
            </div>

            <div class="flex items-center justify-end mt-6">
                <Link
                    :href="route('users.index')"
                    class="text-gray-600 hover:text-gray-900 mr-4"
                >
                    Cancel
                </Link>
                <PrimaryButton
                    class="ml-4"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    {{ editing ? 'Update' : 'Create' }} User
                </PrimaryButton>
            </div>
        </form>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Checkbox from '@/Components/Checkbox.vue';

const props = defineProps({
    user: {
        type: Object,
        default: () => ({
            name: '',
            email: '',
            phone: '',
            designation: '',
            is_enabled: true,
            roles: []
        })
    },
    roles: {
        type: Array,
        required: true
    }
});

const editing = computed(() => props.user.id);

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    phone: props.user.phone,
    designation: props.user.designation,
    password: '',
    password_confirmation: '',
    is_enabled: props.user.is_enabled ?? true,
    roles: props.user.roles || []
});

const toggleRole = (roleId) => {
    const index = form.roles.indexOf(roleId);
    if (index === -1) {
        form.roles.push(roleId);
    } else {
        form.roles.splice(index, 1);
    }
};

const submit = () => {
    if (editing.value) {
        form.put(route('users.update', props.user.id));
    } else {
        form.post(route('users.store'));
    }
};
</script>
