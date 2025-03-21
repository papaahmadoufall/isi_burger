<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import {
    Form,
    FormField,
    FormControl,
    FormItem,
    FormLabel,
    FormMessage,
} from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import { useToast } from '@/components/ui/toast/use-toast';
import { Toaster } from '@/components/ui/toast';
import { ref, onMounted, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';

const { toast } = useToast();
const page = usePage<{ flash: { message?: string } }>();

const props = defineProps<{
    payments: Array<{
        id: number;
        type: string;
    }>;
}>();

const editingPayment = ref<{ id: number; type: string } | null>(null);
const isDialogOpen = ref(false);

const form = useForm({
    type: '',
});

const editForm = useForm({
    type: '',
});

// Watch for flash messages
watch(() => page.props.flash?.message, (message) => {
    if (message) {
        toast({
            title: "Success",
            description: message,
        });
    }
}, { immediate: true });

const handleSubmit = () => {
    form.post(route('payments.store'), {
        onSuccess: () => {
            isDialogOpen.value = false;
            form.reset();
            toast({
                title: "Success",
                description: "Payment method added successfully",
            });
        },
        preserveScroll: true
    });
};

const startEdit = (payment: { id: number; type: string }) => {
    editingPayment.value = payment;
    editForm.type = payment.type;
};

const handleEdit = () => {
    if (!editingPayment.value) return;
    
    editForm.put(route('payments.update', { payment: editingPayment.value.id }), {
        onSuccess: () => {
            editingPayment.value = null;
            editForm.reset();
            toast({
                title: "Success",
                description: "Payment method updated successfully",
            });
        },
        preserveScroll: true
    });
};

const deletePayment = (id: number) => {
    if (confirm('Are you sure you want to delete this payment method?')) {
        useForm({}).delete(route('payments.destroy', { payment: id }), {
            onSuccess: () => {
                toast({
                    title: "Success",
                    description: "Payment method deleted successfully",
                });
            },
            preserveScroll: true
        });
    }
};
</script>

<template>
    <AppLayout :breadcrumbs="[{ title: 'Payment Methods', href: '/dashboard/payments' }]">
        <Head title="Payment Methods" />
        <Toaster />

        <div class="container mx-auto p-6">
            <div class="mb-6 flex justify-between items-center">
                <h1 class="text-2xl font-bold">Payment Methods</h1>
                <Dialog v-model:open="isDialogOpen">
                    <DialogTrigger asChild>
                        <Button variant="outline">Add Payment Method</Button>
                    </DialogTrigger>
                    <DialogContent>
                        <DialogHeader>
                            <DialogTitle>Add Payment Method</DialogTitle>
                            <DialogDescription>
                                Enter the details for the new payment method.
                            </DialogDescription>
                        </DialogHeader>
                        <form @submit.prevent="handleSubmit" class="space-y-4">
                            <FormField name="type">
                                <FormItem>
                                    <FormLabel>Type</FormLabel>
                                    <FormControl>
                                        <Input
                                            type="text"
                                            placeholder="Enter payment type"
                                            v-model="form.type"
                                        />
                                    </FormControl>
                                    <FormMessage>{{ form.errors.type }}</FormMessage>
                                </FormItem>
                            </FormField>
                            <DialogFooter>
                                <Button type="submit" :disabled="form.processing">Save</Button>
                            </DialogFooter>
                        </form>
                    </DialogContent>
                </Dialog>
            </div>

            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>Type</TableHead>
                        <TableHead class="text-right">Actions</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="payment in props.payments" :key="payment.id">
                        <TableCell>
                            <div v-if="editingPayment?.id === payment.id">
                                <Input
                                    type="text"
                                    v-model="editForm.type"
                                    class="w-full"
                                />
                            </div>
                            <div v-else>
                                {{ payment.type }}
                            </div>
                        </TableCell>
                        <TableCell class="text-right">
                            <div v-if="editingPayment?.id === payment.id">
                                <Button 
                                    @click="handleEdit" 
                                    variant="outline" 
                                    class="mr-2"
                                    :disabled="editForm.processing"
                                >
                                    Save
                                </Button>
                                <Button @click="editingPayment = null" variant="ghost">Cancel</Button>
                            </div>
                            <div v-else>
                                <Button @click="startEdit(payment)" variant="outline" class="mr-2">Edit</Button>
                                <Button @click="deletePayment(payment.id)" variant="destructive">Delete</Button>
                            </div>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>
    </AppLayout>
</template> 