<script setup lang="ts">
// Imports nécessaires
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/burgers/Layout.vue';
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
    Form,
    FormField,
    FormControl,
    FormItem,
    FormLabel,
    FormMessage,
} from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import { toast } from '@/components/ui/toast';
import { useForm as useVeeForm } from 'vee-validate';
import * as z from 'zod';
import ListBurger from "@/pages/burgers/listBurger.vue";
import { ref } from "vue";

// Schéma de validation utilisant Zod
const validationSchema = {
    name: (value: string) => {
        if (!value) return 'Name is required';
        if (value.length < 3) return 'Name must be at least 3 characters';
        if (value.length > 50) return 'Name must be less than 50 characters';
        return true;
    },
    price: (value: number) => {
        if (!value) return 'Price is required';
        if (value < 0) return 'Price must be positive';
        return true;
    },
    stock: (value: number) => {
        if (!value) return 'Stock is required';
        if (value < 0) return 'Stock must be positive';
        return true;
    },
    description: () => true,
    image: (value: File | null) => {
        if (!value) return 'Image is required';
        return true;
    }
};

// Initialisation des données du formulaire avec InertiaJS
const inertiaForm = useForm({
    name: '',        // Nom du burger
    price: 0,        // Prix du burger
    stock: 0,        // Stock disponible
    description: '', // Description optionnelle
    image: null as File | null,     // Image (fichier) optionnelle
});

const { handleSubmit, errors, setFieldValue } = useVeeForm({
    validationSchema
});

// Fonction de soumission
const onSubmit = handleSubmit((values) => {
    const formData = new FormData();
    formData.append('name', inertiaForm.name);
    formData.append('price', inertiaForm.price.toString());
    formData.append('stock', inertiaForm.stock.toString());
    formData.append('description', inertiaForm.description || '');

    // Ajouter le fichier image s'il est sélectionné
    if (inertiaForm.image) {
        formData.append('image', inertiaForm.image);
    }

    inertiaForm.post(route('storeBurger'), {
        onSuccess: () => {
            toast({
                title: 'Succès',
                description: `Burger "${inertiaForm.name}" ajouté avec succès.`,
                variant: 'success',
            });
            inertiaForm.reset();
        },
        onError: () => {
            toast({
                title: 'Erreur',
                description: 'Une erreur est survenue. Veuillez vérifier les champs saisis.',
                variant: 'destructive',
            });
        },
    });
});

const handleFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        inertiaForm.image = target.files[0];
        setFieldValue('image', target.files[0]);
    }
};
</script>

<template>
    <AppLayout :breadcrumbs="[{ title: 'Burger Management', href: '/dashboard/burger' }]">
        <Head title="Burger management" />

        <SettingsLayout>
            <Dialog>
                <DialogTrigger asChild>
                    <Button variant="outline">Add burger</Button>
                </DialogTrigger>
                <DialogContent class="sm:max-w-[425px]">
                    <DialogHeader>
                        <DialogTitle>Add a New Burger</DialogTitle>
                        <DialogDescription>
                            Fill in the fields below to add a new burger.
                        </DialogDescription>
                    </DialogHeader>
                    <form @submit.prevent="onSubmit" class="space-y-4">
                        <!-- Nom du burger -->
                        <FormField name="name">
                            <FormItem>
                                <FormLabel>Name</FormLabel>
                                <FormControl>
                                    <Input
                                        type="text"
                                        placeholder="Name of the burger"
                                        v-model="inertiaForm.name"
                                        @input="setFieldValue('name', $event.target.value)"
                                    />
                                </FormControl>
                                <FormMessage>{{ errors.name }}</FormMessage>
                            </FormItem>
                        </FormField>

                        <!-- Prix -->
                        <FormField name="price">
                            <FormItem>
                                <FormLabel>Price</FormLabel>
                                <FormControl>
                                    <Input
                                        type="number"
                                        placeholder="Price"
                                        min="0"
                                        v-model="inertiaForm.price"
                                        @input="setFieldValue('price', Number($event.target.value))"
                                    />
                                </FormControl>
                                <FormMessage>{{ errors.price }}</FormMessage>
                            </FormItem>
                        </FormField>

                        <!-- Stock -->
                        <FormField name="stock">
                            <FormItem>
                                <FormLabel>Stock</FormLabel>
                                <FormControl>
                                    <Input
                                        type="number"
                                        placeholder="Stock"
                                        min="0"
                                        v-model="inertiaForm.stock"
                                        @input="setFieldValue('stock', Number($event.target.value))"
                                    />
                                </FormControl>
                                <FormMessage>{{ errors.stock }}</FormMessage>
                            </FormItem>
                        </FormField>

                        <!-- Description -->
                        <FormField name="description">
                            <FormItem>
                                <FormLabel>Description</FormLabel>
                                <FormControl>
                                    <Input
                                        type="text"
                                        placeholder="Description"
                                        v-model="inertiaForm.description"
                                        @input="setFieldValue('description', $event.target.value)"
                                    />
                                </FormControl>
                                <FormMessage>{{ errors.description }}</FormMessage>
                            </FormItem>
                        </FormField>

                        <!-- Image (Sélecteur de fichier) -->
                        <FormField name="image">
                            <FormItem>
                                <FormLabel>Image</FormLabel>
                                <FormControl>
                                    <Input
                                        type="file"
                                        accept="image/*"
                                        class="file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-primary-foreground hover:file:bg-primary/90"
                                        @change="handleFileChange"
                                    />
                                </FormControl>
                                <FormMessage>{{ errors.image }}</FormMessage>
                            </FormItem>
                        </FormField>

                        <!-- Boutons -->
                        <DialogFooter>
                            <Button variant="outline" type="button" @click="inertiaForm.reset()">Cancel</Button>
                            <Button type="submit">Save</Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>
        </SettingsLayout>
        <ListBurger></ListBurger>
    </AppLayout>
</template>
